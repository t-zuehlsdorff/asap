<?php

namespace APHPUnit;

function printTestResults($arrTestResults) {

  $arrGlobalSummary = array('test-cases' => 0, 'success' => 0, 'failed' => 0);
  $arrFileSummary   = array();
  
  $arrFailedCases = array();

  foreach($arrTestResults AS $strFile => $arrTestCases) {
  
    $arrGlobalSummary['test-cases'] += count($arrTestCases);
  
    $arrFileSummary[$strFile]['test-cases'] = count($arrTestCases);
    $arrFileSummary[$strFile]['success']    = 0;
    $arrFileSummary[$strFile]['failed']     = 0;
    
    foreach($arrTestCases AS $arrTestCase) {
    
      if(true === $arrTestCase['success']) {
        $arrFileSummary[$strFile]['success'] ++;
        $arrGlobalSummary['success'] ++;
        continue;
      }
      
      $arrFileSummary[$strFile]['failed'] ++;
      $arrGlobalSummary['failed'] ++;
      $arrFailedCases[] = $arrTestCase;
    
    }
  
  }
  
  print "\nTestcase-Summary\n================\n\n";
  
  foreach($arrFileSummary AS $strFile => $arrSummary)
    print "$strFile:\t {$arrSummary['success']}/{$arrSummary['test-cases']} successfull\n";
  
  print "\n{$arrGlobalSummary['success']} from {$arrGlobalSummary['test-cases']} successfull, {$arrGlobalSummary['failed']} failures reported\n";
  
  if(empty($arrFailedCases))
    return;
  
  print "\nFailed Testcases\n================\n\n";
  
  foreach($arrFailedCases AS $arrCase) {
  
    print "File:\t{$arrCase['file']}\n";
    print "Line:\t{$arrCase['line']}\n";
    print "Case:\t{$arrCase['test-case']}\n";
    
    if(isset($arrCase['is_exception']) && true === $arrCase['is_exception']) {
      print "Expected Exception:\t{$arrCase['exception_expected']}\n";
      print "Uncatched Exception:\t{$arrCase['exception_uncatched']}\n\n";
    }
    
    if(isset($arrCase['is_assertion']) && true === $arrCase['is_assertion']) {
      
      if(isset($arrCase['result_expected']) && isset($arrCase['result_real'])) {
        print "Expected:\t{$arrCase['result_expected']}\n";
        print "Real Result:\t{$arrCase['result_real']}\n\n";
      }
    }
    
  }
 
}