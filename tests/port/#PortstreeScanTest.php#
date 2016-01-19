<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../config.inc.php';
require_once PORTSTREE_LIB_PATH . '/config.inc.php';

define('TEST_PORTSTREE', __DIR__ . '/portstree');

/**
  * scan existing portstree and check
  * if we only found categories containing ports
  *
  **/
function testScanPortsTree() {

  $arrExpected = array('categorywithport' => array('port'),
                       'anothercategorywithport' => array('port', 'port2', 'port3'));

  $arrReal = \Portstree\get_portlist_from(TEST_PORTSTREE);

  assertEquals($arrReal, $arrExpected);
  
  
}

/**
  * check for exception in case of wrong parameter datatype
  *
  **/
function testScannedPortsTreeIsNotAString() {

  $cloScanWithException = function() {
    \Portstree\get_portlist_from(23);
  };
  expectException($cloScanWithException, '\Exception');

  $cloScanWithException = function() {
    \Portstree\get_portlist_from(NULL);
  };
  expectException($cloScanWithException, '\Exception');

  $cloScanWithException = function() {
    \Portstree\get_portlist_from(array());
  };
  expectException($cloScanWithException, '\Exception');

}

/**
  * check for exception if given portstree is not a dir
  *
  **/
function testScannedPortsTreeIsNotADir() {

  // create nonexisting dir-path
  while(true) {
    $strNoneExistingDir = '/' . uniqid();
    if(!is_dir($strNoneExistingDir) && !is_file($strNoneExistingDir))
      break;
  }

  $cloScanWithException = function() use ($strNoneExistingDir) {
    \Portstree\get_portlist_from($strNoneExistingDir);
  };
  expectException($cloScanWithException, '\Exception');
  
}

/**
  * check for exception if given portstree is a file in reality
  *
  **/
function testScannedPortsTreeIsAFile() {

  $strFile = tempnam(sys_get_temp_dir(), 'asap-testcase');

  $cloScanWithException = function() use ($strFile) {
    \Portstree\get_portlist_from($strFile);
  };
  expectException($cloScanWithException, '\Exception');

  unlink($strFile);

}

/**
  * check for exception if given portstree is not readable
  *
  **/
function testScannedPortsTreeIsNotReadable() {

  // generate tmp-dir name
  while(true) {
    $strDirName = sys_get_temp_dir() . 'asap-testcase-' . uniqid();
    if(!is_dir($strDirName) && !file_exists($strDirName))
      break;
  }

  mkdir($strDirName, 0000);

  $cloScanWithException = function() use ($strDirName) {
    \Portstree\get_portlist_from($strDirName);
  };
  expectException($cloScanWithException, '\Exception');

  rmdir($strDirName);
  
}

/**
  * check if scan result is persisted correctly
  *
  **/
function testScannedPortPersistence() {

}