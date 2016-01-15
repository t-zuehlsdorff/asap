<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../config.inc.php';

/**
  * scan existing portstree and check
  * if we only found categories containing ports
  *
  **/
function testScanPortsTree() {

  $arrExpected = array('categorywithport' => array('port'),
                       'anothercategorywithport' => array('port', 'port2', 'port3'));

  $arrReal = \Portstree\get_portlist_from();

  assertEquals($arrReal, $arrExpected);
  
  
}

/**
  * check for exception in case of wrong parameter datatype
  *
  **/
function testScannedPortsTreeIsNotAString() {

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