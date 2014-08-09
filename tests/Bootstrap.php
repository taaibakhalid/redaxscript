<?php
namespace Redaxscript;

/* include as needed */

include_once('includes/Autoloader.php');
include_once('hook_function.php');
include_once('hook_method.php');
/* temp */
include_once('stubs.php');
/* temp */

/* init */

Autoloader::init();
Request::init();

/* set config */

Config::set('type', 'mysql');
Config::set('host', 'redaxscript.com');
Config::set('name', 'd01ae38a');
Config::set('user', 'd01ae38a');
Config::set('password', 'travis');

/* registry and config */

$registry = Registry::getInstance();
$config = Config::getInstance();

/* database and hook */

Db::init($config);
Hook::init($registry);

/* language */

$language = Language::getInstance();
$language::init('en');