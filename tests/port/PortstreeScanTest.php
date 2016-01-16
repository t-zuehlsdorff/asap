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

  // cases:
  // - nonexisting dir
  // - path is file
}

/**
  * check for exception if given portstree is not readable
  *
  **/
function testScannedPortsTreeIsNotReadable() {

}

/**
  * check if scan result is persisted correctly
  *
  **/
function testScannedPortPersistence() {

}