<?php

/***************************/
/* configuration constants */
/***************************/

date_default_timezone_set('Europe/Berlin');

/*************************/
/* define path constants */
/*************************/

define('PROJECT_PATH',   __DIR__ . '/');
define('CONFIG_PATH',    PROJECT_PATH . 'config/');
define('SQL_PATH',       PROJECT_PATH . 'database/sql/');

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