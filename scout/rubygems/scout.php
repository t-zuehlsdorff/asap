<?php

namespace asap\scout;

require_once '../../config.inc.php';

require_once SCOUT_RUBYGEMS_PATH . 'getRubygem.func.php';
require_once SCOUT_RUBYGEMS_PATH . 'getRubygemApiRequestUrl.func.php';
require_once LIB_SCOUT_PATH      . 'compareVersions.func.php';

// shall this be a function???
function scout(array $arrRubygems) {

  if(!$arrRubygems)
    return null;

  foreach($arrRubygems AS $arrPortRubygem) {

    $strApiCall = \asap\scout\rubygems\getRubygemApiRequestUrl($arrPortRubygem['portName']);
    $arrRubygem = \asap\scout\rubygems\getRubygem($strApiCall);

    if(!$arrRubygem) {
      \var_dump('no result for: ' . $arrPortRubygem['portName']);
      continue; # logging?
    }

    \var_dump(\asap\lib\scout\compareVersions($arrPortRubygem['version'], $arrRubygem['version']));

  }

}

$arrRubygems = [

  [ 'portId' => 1, 'portName' => 'mail_room', 'version' => '0.6.1' ], # 0.6.1
  [ 'portId' => 2, 'portName' => 'net-sftp', 'version' => '1.9' ], # 2.1.2
  [ 'portId' => 3, 'portName' => 'rubygem-bcrypt', 'version' => '3.0.1' ], # 3.1.10
  [ 'portId' => 4, 'portName' => 'ruby-hmac', 'version' => '0.3.9' ], # 0.4.0
  [ 'portId' => 5, 'portName' => 'ruby-camellia', 'version' => '1.0' ], # 1.2 *not* on rubygems.org
  [ 'portId' => 6, 'portName' => 'ruby-password', 'version' => '1.0' ], # 0.15.5 on rubygems.org but not noted in Makefile
  [ 'portId' => 7, 'portName' => 'ruby-tcpwrap', 'version' => '1.0' ] # 0.6 *not* on rubygems.org

];

scout($arrRubygems);
