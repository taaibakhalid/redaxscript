<?php
namespace Redaxscript;

/* autoload */

include_once('includes/Autoloader.php');
include_once('TestCase.php');

/* deprecated */

include_once('includes/query.php');

/* init */

Autoloader::init();
Request::init();

/* get instance */

$registry = Registry::getInstance();
$config = Config::getInstance();

/* get environment */

$dbType = getenv('DB_TYPE');

/* mysql and pgsql */

if ($dbType === 'mysql' || $dbType === 'pgsql')
{
	if ($dbType === 'mysql')
	{
		echo 'MySQL' . PHP_EOL;
		$config->set('dbUser', 'root');
	}
	else
	{
		echo 'PostgreSQL' . PHP_EOL;
		$config->set('dbUser', 'postgres');
	}
	$config->set('dbType', $dbType);
	$config->set('dbHost', '127.0.0.1');
	$config->set('dbName', 'test');
	$config->set('dbPassword', 'test');
	$config->set('dbSalt', 'test');
}

/* sqlite */

else
{
	echo 'SQLite' . PHP_EOL;
	$config->set('dbType', 'sqlite');
}

/* database */

Db::construct($config);
Db::init();

/* installer */

$installer = new Installer($config);
$installer->init();
$installer->rawDrop();
$installer->rawCreate();
$installer->insertData(array(
	'adminName' => 'Test',
	'adminUser' => 'test',
	'adminPassword' => 'test',
	'adminEmail' => 'test@test.com'
));
Db::forTablePrefix('users')->whereIdIs(1)->findOne()->set('password', 'test')->save();

/* test module */

if (is_dir('modules/TestDummy'))
{
	$testDummy = new Modules\TestDummy\TestDummy;
	$testDummy->install();
}

/* hook */

Hook::construct($registry);
Hook::init();

/* language */

$language = Language::getInstance();
$language::init();
