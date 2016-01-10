<?php

namespace asap\scout\rubygems;

/**
  *
  * @param $strRubygemName - the name of the rubygem port
  *
  * @return string - the API request for $strRubygemName
  *
  * @return null   - if $strRubygemName is not a string or empty
  *
  * this function returns the URL to call against the rubygems.org API.
  * since rubygems have *usually* the 'rubygems-' prefix in the FreeBSD ports tree,
  * the function will take care and remove that if necessary.
  * the response format will be whatever RUBYGEMS_API_RESPONSE_FORMAT const is defined to.
  *
  **/
function getRubygemApiRequestUrl($strRubygemName = '') {

  if(!\is_string($strRubygemName) || '' === $strRubygemName)
    return null;

    if(\substr($strRubygemName, 0, strlen(FREEBSD_RUBYGEM_PORT_PREFIX)) === FREEBSD_RUBYGEM_PORT_PREFIX)
      $strRubygemName = \substr($strRubygemName, strlen(FREEBSD_RUBYGEM_PORT_PREFIX));

  return RUBYGEM_GET_GEM . DIRECTORY_SEPARATOR . $strRubygemName . RUBYGEMS_API_RESPONSE_FORMAT;

}
