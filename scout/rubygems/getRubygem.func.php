<?php

namespace asap\scout\rubygems;

/**
  *
  * @param $strApiEndpoint - contains the URI to the endpoint
  *
  * @return array - an empty array in case the API returns an error or an empty result
  * @return array - the found port as JavaScript object
  *
  * @return null  - if $strApiEndpoint is not a string, empty, or is not a valid URL (http/https schemes only)
  *
  * this function will call an API and returns the result in JSON
  *
  **/
function getRubygem($strApiEndpoint) {

  if(!\is_string($strApiEndpoint) || '' === $strApiEndpoint)
    return null;

  if('http' !== \strtolower(\substr($strApiEndpoint, 0, 4)))
    return null;

  if(\filter_var($strApiEndpoint, FILTER_VALIDATE_URL) === false)
    return null;

  $mixApiCallResult = @\file_get_contents($strApiEndpoint);

  return false === $mixApiCallResult ? [] : \json_decode($mixApiCallResult, true);

}
