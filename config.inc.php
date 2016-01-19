<?php

/***************************/
/* configuration constants */
/***************************/

date_default_timezone_set('Europe/Berlin');

/*************************/
/* define path constants */
/*************************/

const PROJECT_PATH = __DIR__ . '/';
const CONFIG_PATH  = PROJECT_PATH . 'config/';
const SQL_PATH     = PROJECT_PATH . 'database/sql';
const SCOUT_PATH   = PROJECT_PATH . 'scout/';
const LIB_PATH     = PROJECT_PATH . 'lib/';

const SCOUT_RUBYGEMS_PATH = SCOUT_PATH . 'rubygems/';
const LIB_SCOUT_PATH      = LIB_PATH   . 'scout/';

/***************************/
/* scout related constants */
/***************************/

const RUBYGEMS_API_HOST            = 'https://rubygems.org/api/v1';
const RUBYGEM_GET_GEM              = RUBYGEMS_API_HOST . DIRECTORY_SEPARATOR . 'gems';
const RUBYGEMS_API_RESPONSE_FORMAT = '.json'; # supported api response formats: .json, .yaml
const FREEBSD_RUBYGEM_PORT_PREFIX  = 'rubygem-';

define('PORTSTREE_LIB_PATH', PROJECT_PATH . 'portstree/');

// redefine include path to add FreeBSD share path
set_include_path('.:/usr/local/share/pear:/usr/local/share');

/****************/
/* define DDDBL */
/****************/

require_once 'dddbl/dddbl.php';

// store configuration and all sql definition
\DDDBL\storeDBFileContent(CONFIG_PATH . 'database.ddef');
\DDDBL\loadQueryDefinitionsInDir(SQL_PATH, '*.sql');

// etablish connection if there is no one
if(!\DDDBL\isConnected())
  if(!\DDDBL\connect())
    throw new \Exception ("could not connect to database");
