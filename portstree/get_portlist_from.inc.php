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

  // steps:
  // - check for exceptional cases
  // - read portstree
  // - build result

  // side-notes: parallel scan of categories for faster results?

}
