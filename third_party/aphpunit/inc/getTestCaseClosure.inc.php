<?php

/**
  * @param $strTestFile         - the file to check for test-cases
  * @param $arrDefinedFunctions - all user-definied functions until now
  * @param $intParentPID        - the process-pid of the parent
  * 
  * @throw \Exception - if first parameter is not a string
  * @throw \Exception - if given file is not a file nor readable
  * @throw \Exception - if given parent-id is not an integer
  *
  * create a closure for execution in a fork. it will: 
  * - include the given test-file
  * - find test-cases definied in test-file
  * - send list of test-cases to queue
  * - send SIGTERM to parent id and exit
  *
  **/
function getTestCaseClosure($strTestFile, array $arrDefinedFunctions, $intParentPID) {

  if(!is_string($strTestFile))
    throw new \Exception("first given parameter is not a string");
  
  if(!is_file($strTestFile) || !is_readable($strTestFile))
    throw new \Exception("given file is not a file or not readable: $strTestFile");
  
  if(!is_int($intParentPID))
    throw new \Exception("third given parameter is not an integer");

  return function () use ($strTestFile, $arrDefinedFunctions, $intParentPID) {
    
    include $strTestFile;

    # get test-cases
    $arrAllFunctions  = get_defined_functions();
    $arrUserFunctions = $arrAllFunctions['user'];
    $arrDefinedFunctions = array_diff($arrUserFunctions, $arrDefinedFunctions);
    
    $arrTestCases = array();
    
    foreach($arrDefinedFunctions AS $strFunction)
      if(fnmatch('aphpunit\testcases\test*', $strFunction, FNM_NOESCAPE))
        $arrTestCases[] = $strFunction;

    # collect all information in this structure
    $arrMsgContent = array('file'      => $strTestFile,
                           'functions' => $arrTestCases);

    # send the result to the queue
    $objQueue = new \SimpleIPC(QUEUE_IDENTIFIER);
    $objQueue->send(serialize($arrMsgContent));

    # kill thread after sending parent a SIGTERM
    posix_kill($intParentPID, SIGTERM);
    exit();
    
  };
  
}