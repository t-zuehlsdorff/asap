<?php

namespace Portstree;

/**
  * @param $strTree - root of portstree path
  *
  * @throws \Exception - if given path is not a string
  * @throws \Exception - if given path is not a directory
  * @throws \Exception - if given path is not readable
  *
  * @returns (array) - list of categories and their ports found
  *
  * get a list of all categories and their ports from the given
  * ports-tree path.
  * it is assumed, that the given path is the root of the portstree
  * the case of the ports is not modified in any way
  *
  * there are a number of found entries ignored:
  * - a directory named 'distfiles' is ignored, because it contains
  *   downloaded distfiles and not ports
  * - every directory starting with uppercase is ignored,
  *   because they contain infrastructure and not ports
  * - every file is ignored
  *
  * the result is an array returned in the following format:
  * [category]   => [port, port-n, ..],
  * [category-n] => [port, port-n, ..]
  * ..
  *
  **/
function get_portlist_from($strTree) {

  if(!is_string($strTree))
    throw new \Exception ("given parameter must be a string but it is: " . gettype($strTree));

  if(!is_dir($strTree))
    throw new \Exception ("given path is not a directory: $strTree");

  if(!is_readable($strTree))
    throw new \Exception ("given path is not readable: $strTree");

  // normalize path to contain a slash at the end in very case
  $strTree = ('/' === $strTree[mb_strlen($strTree) - 1]) ? $strTree : $strTree . '/';
  
  $arrPortList = array();

  $objPortsTree = new \DirectoryIterator($strTree);

  foreach ($objPortsTree AS $objCategory) {

    if($objCategory->isDot() || !$objCategory->isDir())
      continue;

    $strCategoryName = $objCategory->getFilename();

    // ignore every director starting with an uppercase char
    $strFirstChar = $strCategoryName[0];
    if($strFirstChar === strtoupper($strFirstChar))
      continue;

    // ignore distfile directory
    if('distfiles' === $strCategoryName)
      continue;

    $objPortCategory = new \DirectoryIterator($strTree . $strCategoryName);

    foreach($objPortCategory AS $objPortDir) {
      
      if($objPortDir->isDot() || !$objPortDir->isDir())
        continue;
      
      $arrPortList[$strCategoryName][] = $objPortDir->getFilename();

    }

  }

  return $arrPortList;
  
}
