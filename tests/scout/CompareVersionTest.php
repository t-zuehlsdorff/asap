<?php

namespace APHPUnit\Testcases;

require_once __DIR__ . '/../config.inc.php';

require_once SCOUT_RUBYGEMS_PATH . 'getRubygem.func.php';
require_once SCOUT_RUBYGEMS_PATH . 'getRubygemApiRequestUrl.func.php';
require_once LIB_SCOUT_PATH      . 'compareVersions.func.php';

/*var_dump(\asap\scout\rubygems\getRubygemApiRequestUrl('foo') === RUBYGEM_GET_GEM . '/foo.json');
var_dump(\asap\scout\rubygems\getRubygemApiRequestUrl() === null);
var_dump(\asap\scout\rubygems\getRubygemApiRequestUrl(1) === null);
var_dump(\asap\scout\rubygems\getRubygemApiRequestUrl('') === null);
var_dump(\asap\scout\rubygems\getRubygemApiRequestUrl('rubygem-foo') === RUBYGEM_GET_GEM . '/foo.json');
var_dump(\asap\scout\rubygems\getRubygemApiRequestUrl('rubygems-foo') === RUBYGEM_GET_GEM . '/rubygems-foo.json');
var_dump(\asap\scout\rubygems\getRubygemApiRequestUrl('ruby-hmac') === RUBYGEM_GET_GEM . '/ruby-hmac.json');

var_dump(\asap\scout\rubygems\getRubygem('') === null);
var_dump(\asap\scout\rubygems\getRubygem('HTTP://RUBYGEMS.ORG/API/V1/GEMS/FOO.JSON') === []);
var_dump(\asap\scout\rubygems\getRubygem('http://rubygems.org/api/v1/gems/FOO.json') === []);
var_dump(\asap\scout\rubygems\getRubygem('HTTP://RUBYGEMS.ORG/API/V1/GEMS/mail_room.json') === []);
var_dump(sizeof(\asap\scout\rubygems\getRubygem('http://rubygems.org/api/v1/gems/mail_room.json')) > 0);
*/

function testCompareVersions() {
  
  assertTrue(\asap\lib\scout\compareVersions('1.1', '1.1.1'));
  assertTrue(\asap\lib\scout\compareVersions('1.1.1', '2.1'));
  assertFalse(\asap\lib\scout\compareVersions('4.2.4.rc1', '4.2.4.rc2'));
  assertFalse(\asap\lib\scout\compareVersions('4.2.4.rc1', '5.0.0.beta1'));
  assertFalse(\asap\lib\scout\compareVersions('4.2.0.rc1', '4.2.0.beta4'));
  assertTrue(\asap\lib\scout\compareVersions('4.2.0.rc1', '4.2.0'));
  assertTrue(\asap\lib\scout\compareVersions('4.2.0.beta5', '4.2.0'));
  assertTrue(\asap\lib\scout\compareVersions('3', '3.0.0.1'));
  assertFalse(\asap\lib\scout\compareVersions('3.0.0.1', '3.0.0.1'));
  assertTrue(\asap\lib\scout\compareVersions('0.0.3', '0.1.0'));
  assertFalse(\asap\lib\scout\compareVersions('4.9.27', '5.0.rc1'));
  assertFalse(\asap\lib\scout\compareVersions('beta5', 'rc1'));
  assertTrue(\asap\lib\scout\compareVersions('1.1', 'v1.1.1'));
  assertFalse(\asap\lib\scout\compareVersions('1.1', 'v1.0.1'));
  assertFalse(\asap\lib\scout\compareVersions('1.1', 'v1.1'));

}