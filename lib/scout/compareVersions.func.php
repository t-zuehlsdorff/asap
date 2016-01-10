<?php

namespace asap\lib\scout;

/**
  * @param $strPortVersion - the latest version of the port in FreeBSD ports tree
  * @param $strApiVersion  - the version of the ruby gem on rubygems.org
  *
  * @return (boolean) true  - if an update is available for port
  * @return (boolean) false - if *no* update is available for port
  *
  * @return null            - in case of unexpected input: any given argument is not a string or empty
  *
  * @link https://php.net/version_compare version_compare
  *
  * this function will compare two version numbers. it does this the "php way".
  * if the $strApiVersion contains any letter, it will prevent (returns false) the comparison,
  * because we don't want to have any [alpha, beta, dev, rc, ...] versions in the ports tree.
  * it expects the first argument to be the "current" version and the second the version to be compared to.
  *
  **/
function compareVersions($strPortVersion, $strApiVersion) {

  if(!\is_string($strPortVersion) || !\is_string($strApiVersion))
    return null;

   if('' === $strPortVersion || '' === $strApiVersion)
    return null;

  $strLowerCaseGemVersion = \strtolower($strApiVersion);
  $arrLetters             = range('a', 'z');

  # we don't want any alpha/beta/dev/rc versions in the ports tree
  foreach(\str_split($strLowerCaseGemVersion) AS $strChar)
    if(\in_array($strChar, $arrLetters))
      return false;

  return \version_compare($strPortVersion, $strLowerCaseGemVersion) === -1 ? true : false;

}
