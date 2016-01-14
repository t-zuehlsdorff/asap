<?php

namespace APHPUnit\Testcases;

/**
  * @param $mixValue - the value to test
  *
  * test if the given value matches the
  * boolean value "true".
  *
  * create the assertion-environment and send
  * the result of the test to the queue
  *
  **/
function assertTrue($mixValue) {

  $arrEnv = getAssertionEnvironment();
  $arrEnv['success'] = (true === $mixValue) ? true : false;
  
  $arrEnv['result_real']      = var_export($mixValue, true);
  $arrEnv['result_expected']  = var_export(true, true);
  
  $objQueue = new \SimpleIPC(QUEUE_IDENTIFIER);
  $objQueue->send(serialize($arrEnv));

}

/**
  * @param $mixValue - the value to test
  *
  * test if the given value matches the
  * boolean value "false".
  *
  * create the assertion-environment and send
  * the result of the test to the queue
  *
  **/
function assertFalse($mixValue) {

  $arrEnv = getAssertionEnvironment();
  $arrEnv['success'] = (false === $mixValue) ? true : false;
  
  $arrEnv['result_real']      = var_export($mixValue, true);
  $arrEnv['result_expected']  = var_export(false, true);
  
  $objQueue = new \SimpleIPC(QUEUE_IDENTIFIER);
  $objQueue->send(serialize($arrEnv));

}

/**
  * @param $mixReal     - the value to test against the expectation
  * @param $mixExpected - the expected value
  *
  * test if the given value matches the
  * expected value
  *
  * create the assertion-environment and send
  * the result of the test to the queue
  *
  **/
function assertEquals($mixReal, $mixExpected) {

  $arrEnv = getAssertionEnvironment();
  $arrEnv['success'] = ($mixReal === $mixExpected) ? true : false;
  
  $arrEnv['result_real']      = var_export($mixReal, true);
  $arrEnv['result_expected']  = var_export($mixExpected, true);
  
  $objQueue = new \SimpleIPC(QUEUE_IDENTIFIER);
  $objQueue->send(serialize($arrEnv));

}

/**
  * @param $mixReal     - the value to test against the expectation
  * @param $mixExpected - the expected value
  *
  * test if the given value do NOT matches the
  * expected value
  *
  * create the assertion-environment and send
  * the result of the test to the queue
  *
  **/
function assertNotEquals($mixReal, $mixExpected) {

  $arrEnv = getAssertionEnvironment();
  $arrEnv['success'] = ($mixReal !== $mixExpected) ? true : false;
  
  $arrEnv['result_real']      = var_export($mixReal, true);
  $arrEnv['result_expected']  = var_export($mixExpected, true);
  
  $objQueue = new \SimpleIPC(QUEUE_IDENTIFIER);
  $objQueue->send(serialize($arrEnv));

}

/**
  * @param $mixValue - the value to test
  *
  * test if the given value matches empty()-definition
  *
  * create the assertion-environment and send
  * the result of the test to the queue
  *
  **/
function assertEmpty($mixValue) {

  $arrEnv = getAssertionEnvironment();
  $arrEnv['success'] = (empty($mixValue)) ? true : false;
  
  $objQueue = new \SimpleIPC(QUEUE_IDENTIFIER);
  $objQueue->send(serialize($arrEnv));

}

/**
  * @param $mixValue - the value to test
  *
  * test if the given value is NULL
  *
  * create the assertion-environment and send
  * the result of the test to the queue
  *
  **/
function assertNull($mixValue) {

  $arrEnv = getAssertionEnvironment();
  $arrEnv['success'] = (is_null($mixValue)) ? true : false;
  
  $arrEnv['result_real']      = var_export($mixValue, true);
  $arrEnv['result_expected']  = var_export(null, true);
  
  $objQueue = new \SimpleIPC(QUEUE_IDENTIFIER);
  $objQueue->send(serialize($arrEnv));

}

/**
  * @param $mixKey   - the key to check for its existence
  * @param $arrArray - the array to check
  *
  * check if the $arrArray has a key $mixKey
  *
  * create the assertion-environment and send
  * the result of the test to the queue
  *
  **/
function assertArrayHasKey($mixKey, $arrArray) {

  $arrEnv = getAssertionEnvironment();
  $arrEnv['success'] = (is_array($arrArray) && array_key_exists($mixKey, $arrArray)) ? true : false;
  
  $objQueue = new \SimpleIPC(QUEUE_IDENTIFIER);
  $objQueue->send(serialize($arrEnv));

}

/**
  * @param $strFunction          - the function to check
  * @param $intParameterPosition - the position of the parameter to check
  *
  * check if the parameter at the given position (start counting with 0)
  * has an array-constraint.
  *
  * create the assertion-environment and send
  * the result of the test to the queue
  *
  **/
function assertParameterHasArrayConstraint($strFunction, $intParameterPosition) {

  $objReflector = new \ReflectionFunction($strFunction);
  $arrParameter = $objReflector->getParameters();
  
  $arrEnv = getAssertionEnvironment();
  $arrEnv['success'] = (isset($arrParameter[$intParameterPosition]) && $arrParameter[$intParameterPosition]->isArray()) ? true : false;
  
  $objQueue = new \SimpleIPC(QUEUE_IDENTIFIER);
  $objQueue->send(serialize($arrEnv));

}

/**
  * @param $cloThrower           - closure which should throw an exception
  * @param $strExpectedException - the expected exception
  *
  * check if the $cloThrower throws the expected exception
  * if an unexpected exception is thrown, it will be also catched,
  * to allow further execution of the test-case.
  *
  * the assertion environment will additionally contains:
  * - exception_uncatched
  * - exception_expected
  * - exception_thrown
  *
  * the result of the test ist send to the queue
  *
  **/
function expectException($cloThrower, $strExpectedException) {

  $arrEnv = getAssertionEnvironment();
  $arrEnv['exception_expected'] = $strExpectedException;
  
  try {
  
    $cloCatcher = create_function('$cloThrower',
                                   'try { $cloThrower(); return false; } 
                                    catch ('.$strExpectedException.' $objException) { return true; }');

    $arrEnv['success'] = $cloCatcher($cloThrower);
    $arrEnv['exception_thrown'] = $strExpectedException;
  
  } catch (\Exception $objException) {
  
    $arrEnv['success'] = false;
    $arrEnv['exception_uncatched'] = get_class($objException);
  
  }
  
  $objQueue = new \SimpleIPC(QUEUE_IDENTIFIER);
  $objQueue->send(serialize($arrEnv));

}

/**
  * @return (array) the assertion environment
  *
  * create an array with the assertion environment. this will
  * contain:
  * - test-function
  * - test-case
  * - line of assertion
  * - file with test-case
  * - flag, if its an assertion
  * - flag, if its an exception
  *
  **/
function getAssertionEnvironment() {

  $arrBacktrace = debug_backtrace();
  
  $arrEnv['test-function'] = $arrBacktrace[1]['function'];
  $arrEnv['test-case']     = $arrBacktrace[2]['function'];
  $arrEnv['file']          = $arrBacktrace[1]['file'];
  $arrEnv['line']          = $arrBacktrace[1]['line'];
  
  if(fnmatch('aphpunit\testcases\assert*', strtolower($arrBacktrace[1]['function']), FNM_NOESCAPE))
    $arrEnv['is_assertion'] = true;
  
  if('aphpunit\testcases\expectexception' === strtolower($arrBacktrace[1]['function']))
    $arrEnv['is_exception'] = true;
  
  return $arrEnv;

}
