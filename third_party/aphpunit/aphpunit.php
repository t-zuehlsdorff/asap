<?php

namespace APHPUnit;

require_once __DIR__ . '/config.inc.php';
require_once __DIR__ . '/inc/getTestCaseClosure.inc.php';
require_once __DIR__ . '/inc/Assertions.inc.php';
require_once __DIR__ . '/inc/Fork.class.php';
require_once __DIR__ . '/inc/SimpleIPC.class.php';
require_once __DIR__ . '/inc/TestResultPrinting.inc.php';

######################################
# check given parameter for test-run #
######################################

if(empty($argv[1]))
  die("no directory with testcases given\n");

$strTestDir = $argv[1];

if(!is_dir($strTestDir))
  die("given path is not a directory: $strTestDir\n");

#########################
# initialize ipc-queues #
#########################

# initialize ipc-queue empty, to avoid messages from old test-cases
$objQueue = new \SimpleIPC(QUEUE_IDENTIFIER);
$objQueue->clear();

#################################
# initialize forking-management #
#################################

$objFork = new \Fork();
$objFork->setMaxForks(MAX_FORKS);

####################################
# initialize runtime-configuration #
####################################

# get id of the process - it will be the parent of all forked childs
$intParentPID = posix_getppid();

# get a list of all user-definied functions until here
# we can skip them when looking for newly definied test-cases
$arrAllFunctions = get_defined_functions();
$arrDefinedFunctions = $arrAllFunctions['user'];

############################################
# find all test-files and their test-cases #
############################################

$arrTestCases = array();

# closure to read test-cases from the queue
$cloGetCasesFromQueue = function() use ($objQueue, &$arrTestCases) {

  while(!$objQueue->isQueueEmpty()) {
  
    $strFoundTestCases = $objQueue->receive();
    $arrFoundTestCases = unserialize($strFoundTestCases);
    $arrTestCases[$arrFoundTestCases['file']] = $arrFoundTestCases['functions'];
    
  }

};

$objTestDir = new \RecursiveDirectoryIterator($strTestDir);

# iterate recursivly through given directory and execute
# all found testcases
foreach(new \RecursiveIteratorIterator($objTestDir) AS $objFile) {

  if(!$objFile->isReadable())
    continue;

  if(!fnmatch('*Test.php', $objFile->getFilename()))
    continue;

  $strTestFile = $objFile->getPathname();
  
  # avoid test-case detection for already known files
  if(isset($arrTestCases[$strTestFile]))
    continue;
  else
    $arrTestCases[$strTestFile] = array();
  
  # get closure which will detect test-cases within the given file
  $cloGetTestCases = getTestCaseClosure($strTestFile, $arrDefinedFunctions, $intParentPID);
  
  # add closure to forking-queue and try to fork a new process if possible
  $objFork->addToQueue($cloGetTestCases);
  $objFork->fork();
  
  # get found testcases from queue; otherwise the queue will become full
  $cloGetCasesFromQueue();

}

# get the test-cases from all files stored in the queue
while(!$objFork->isQueueEmpty()) {
  $objFork->fork();
  $cloGetCasesFromQueue();
}

# wait until all test-cases received
$objFork->waitUntilForksFinished(100, $cloGetCasesFromQueue);

################################################
# execute all test-cases and get their results #
################################################

$arrTestResults = array();

# closure to read result of test-cases from queue
$cloGetTestResultsFromQueue = function () use ($objQueue, &$arrTestResults) {
  
  while(!$objQueue->isQueueEmpty()) {
  
    $strResult = $objQueue->receive();
    $arrResult = unserialize($strResult);
    $arrTestResults[$arrResult['file']][] = $arrResult;
    
  }
  
};

# iterate through the test-cases; always execute all
# test-cases of a file to keep it in cache
foreach($arrTestCases AS $strTestFile => $arrCases) {

  foreach($arrCases AS $strTestFunction) {
  
    # create closure which includes test-file and execute the test-function
    $cloExecuteTestCase = function () use ($strTestFile, $strTestFunction) {
    
      include $strTestFile;
      
      try {
        $strTestFunction();
      } catch (\Exception $objException) {
        var_dump($objException);
      }
    
    };
  
    # add closure to queue and try to fork a new process if possible
    $objFork->addToQueue($cloExecuteTestCase);
    $objFork->fork();
    
    # reads the results from queue; otherwise the queue will become full
    $cloGetTestResultsFromQueue();
  
  }

}

# execute all test-cases and get their results
while(!$objFork->isQueueEmpty()) {
  $objFork->fork();
  $cloGetTestResultsFromQueue();
}

# wait until the last test-cases are finished and get their results
$objFork->waitUntilForksFinished(100, $cloGetTestResultsFromQueue);

# close and destroy queue, its no longer needed
$objQueue->close();

####################
# print the result #
####################

printTestResults($arrTestResults);