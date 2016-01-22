<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../config.inc.php';

#
# version schema
#
# 1. Default
# 2. 
#
#
#
#

# could be a class?

const NOT_SUPPORTED = 0; # ???
const ERROR = -1; # ???
const DEFAULT_SCHEMA = 1;
const FOO = 2;
const BAR = 3;

$cloDefaultSchema = function($strVersion) {
  return preg_match('', $strVersion);
};

$cloFoo = function($strVersion) {
  return preg_match('', $strVersion);
};

$arrSchema = [ DEFAULT_SCHEMA => $cloDefaultSchema, $cloFoo ];

/**
  * @param $strVersion - the version number of a FreeBSD port
  * @param $arrVersionSchemes - an array holding closures that try to identify a pattern
  *
  * @return (int) - the matched version pattern
  *
  * where the return value can be one of the following:
  *
  * -1 ERRROR
  *  0 NOT_SUPPORTED
  *  1 DEFAULT_SCHEMA
  *  ...
  *
  * this function takes a version number and tries to recognize
  * the version schema.
  * the pattern(s) to match can be configured and can be passed as an argument.
  * the passed pattern schould somehow be sorted from high to most unlikly due to
  * performance.
  * 
  *
  **/
function detectVersionSchema($strVersion, array $arrVersionSchemes) {
}

/**
  *
  *
  *
  *
  *
  *
  **/
function testDetectVersionSchema() {

  assertEquals(detectVersionSchema([], []), -1);
  assertEquals(detectVersionSchema(123, []), -1);
  assertEquals(detectVersionSchema('', []), -1);
  
  assertEquals(detectVersionSchema('1.2.3', []), 1);
  assertEquals(detectVersionSchema('1', []), 1);
  assertEquals(detectVersionSchema('1.0', []), 1);
  assertEquals(detectVersionSchema('1.03', []), 1);
  assertEquals(detectVersionSchema('0.9.33.2', []), 1);
  assertEquals(detectVersionSchema('1.3.110', []), 1);
  assertEquals(detectVersionSchema('193', []), 1);
  assertEquals(detectVersionSchema('0.10.23', []), 1);
  assertEquals(detectVersionSchema('47846', []), 1);
  assertEquals(detectVersionSchema('0.003', []), 1);
  assertEquals(detectVersionSchema('0.03000', []), 1);
  assertEquals(detectVersionSchema('1.5.2.1.5.4', []), 1);

  assertNotEquals(detectVersionSchema('3.0.2009060901', []), 1); # ???
  assertNotEquals(detectVersionSchema('20020228', []), 1);
  assertNotEquals(detectVersionSchema('2010.07.22', []), 1);

}
