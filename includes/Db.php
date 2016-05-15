<?php
namespace Redaxscript;

use ORM;
use PDO;
use PDOException;

/**
 * children class to handle the database
 *
 * @since 2.2.0
 *
 * @package Redaxscript
 * @category Db
 * @author Henry Ruhs
 *
 * @method _addJoinSource()
 * @method _addOrderBy()
 * @method _addWhere()
 * @method _setupDb()
 * @method clearCache()
 * @method deleteMany()
 * @method deleteOne()
 * @method findArray()
 * @method findMany()
 * @method findOne()
 * @method forTable()
 * @method getDb()
 * @method rawExecute()
 * @method rawQuery()
 * @method orderByAsc()
 * @method orderByDesc()
 * @method tableAlias()
 * @method whereGt()
 * @method whereIdIn()
 * @method whereIdIs()
 * @method whereIn()
 * @method whereLt()
 * @method whereRaw()
 */

class Db extends ORM
{
	/**
	 * instance of the config class
	 *
	 * @var object
	 */

	protected static $_config;

	/**
	 * constructor of the class
	 *
	 * @since 2.6.0
	 *
	 * @param Config $config instance of the config class
	 */

	public static function construct(Config $config)
	{
		self::$_config = $config;
	}

	/**
	 * init the class
	 *
	 * @since 2.6.0
	 */

	public static function init()
	{
		$dbType = self::$_config->get('dbType');
		$dbHost = self::$_config->get('dbHost');
		$dbName = self::$_config->get('dbName');
		$dbUser = self::$_config->get('dbUser');
		$dbPassword = self::$_config->get('dbPassword');
		$dbSocket = strstr($dbHost, '.sock');

		/* mysql and pgsql */

		if ($dbType === 'mysql' || $dbType === 'pgsql')
		{
			if ($dbType === 'mysql')
			{
				self::configure('connection_string', 'mysql:' . ($dbSocket ? 'unix_socket' : 'host') . '=' . $dbHost . ';dbname=' . $dbName . ';charset=utf8');
			}
			else
			{
				self::configure('connection_string', 'pgsql:' . ($dbSocket ? 'unix_socket' : 'host') . '=' . $dbHost . ';dbname=' . $dbName . ';options=--client_encoding=utf8');
			}

			/* username and password */

			self::configure(array(
				'username' => $dbUser,
				'password' => $dbPassword
			));
		}

		/* sqlite */

		if ($dbType === 'sqlite')
		{
			self::configure('sqlite:' . $dbHost);
		}

		/* general */

		self::configure(array(
			'caching' => true,
			'caching_auto_clear' => true,
			'return_result_sets' => true
		));
	}

	/**
	 * get the database status
	 *
	 * @since 2.4.0
	 *
	 * @return integer
	 */

	public static function getStatus()
	{
		$output = 0;

		/* has connection */

		try
		{
			if (self::$_config->get('dbType') === self::getDb()->getAttribute(PDO::ATTR_DRIVER_NAME))
			{
				$output = self::countTablePrefix() > 7 ? 2 : 1;
			}
		}
		catch (PDOException $exception)
		{
			$output = 0;
		}
		return $output;
	}

	/**
	 * raw instance helper
	 *
	 * @since 2.4.0
	 *
	 * @return Db
	 */

	public static function rawInstance()
	{
		self::_setup_db();
		return new self(null);
	}

	/**
	 * count table with prefix
	 *
	 * @since 2.4.0
	 *
	 * @return Db
	 */

	public static function countTablePrefix()
	{
		if (self::$_config->get('dbType') === 'mysql')
		{
			return self::rawInstance()->rawQuery('SHOW TABLES LIKE \'' . self::$_config->get('dbPrefix') . '%\'')->findMany()->count();
		}
		if (self::$_config->get('dbType') === 'pgsql')
		{
			return self::forTable('pg_catalog.pg_tables')->whereLike('tablename', '%' . self::$_config->get('dbPrefix') . '%')->whereNotLike('tablename', '%pg_%')->whereNotLike('tablename', '%sql_%')->count();
		}
		if (self::$_config->get('dbType') === 'sqlite')
		{
			return self::forTable('sqlite_master')->where('type', 'table')->whereLike('name', '%' . self::$_config->get('dbPrefix') . '%')->whereNotLike('name', '%sqlite_%')->count();
		}
	}

	/**
	 * for table with prefix
	 *
	 * @since 2.2.0
	 *
	 * @param string $table name of the table
	 * @param string $connection which connection to use
	 *
	 * @return Db
	 */

	public static function forTablePrefix($table = null, $connection = self::DEFAULT_CONNECTION)
	{
		self::_setupDb($connection);
		return new self(self::$_config->get('dbPrefix') . $table, array(), $connection);
	}

	/**
	 * left join with prefix
	 *
	 * @since 2.2.0
	 *
	 * @param string $table name of the table
	 * @param string $constraint constraint as needed
	 * @param string $tableAlias alias of the table
	 *
	 * @return Db
	 */

	public function leftJoinPrefix($table = null, $constraint = null, $tableAlias = null)
	{
		return $this->_addJoinSource('LEFT', self::$_config->get('dbPrefix') . $table, $constraint, $tableAlias);
	}

	/**
	 * where like with many
	 *
	 * @since 3.0.0
	 *
	 * @param array $columnArray array of column names
	 * @param array $likeArray array of the like
	 *
	 * @return Db
	 */

	public function whereLikeMany($columnArray = null, $likeArray = null)
	{
		return $this->_addWhere('(' . implode($columnArray, ' LIKE ? OR ') . ' LIKE ? )', $likeArray);
	}

	/**
	 * find a flat array
	 *
	 * @since 3.0.0
	 *
	 * @param string $key key of the item
	 *
	 * @return array
	 */

	public function findFlatArray($key = 'id')
	{
		$output = array();
		foreach ($this->findArray() as $value)
		{
			if (array_key_exists($key, $value))
			{
				$output[] = $value[$key];
			}
		}
		return $output;
	}

	/**
	 * get the setting
	 *
	 * @since 3.0.0
	 *
	 * @param string $key key of the item
	 *
	 * @return string
	 */

	public function getSetting($key = null)
	{
		$settings = self::forTablePrefix('settings')->findMany();

		/* process settings */

		if ($key)
		{
			foreach ($settings as $setting)
			{
				if ($setting->name === $key)
				{
					return $setting->value;
				}
			}
		}
		else if (!$key)
		{
			return $settings;
		}
		return false;
	}

	/**
	 * set the setting
	 *
	 * @since 3.0.0
	 *
	 * @param string $key key of the item
	 * @param string $value value of the item
	 */

	public function setSetting($key = null, $value = null)
	{
		self::forTablePrefix('settings')->where('name', $key)->findOne()->set('value', $value)->save();
	}

	/**
	 * order according to global setting
	 *
	 * @since 2.2.0
	 *
	 * @param string $column name of the column
	 *
	 * @return Db
	 */

	public function orderGlobal($column = null)
	{
		return $this->_addOrderBy($column, $this->getSetting('order'));
	}

	/**
	 * limit according to global setting
	 *
	 * @since 2.2.0
	 *
	 * @return Db
	 */

	public function limitGlobal()
	{
		$this->_limit = $this->getSetting('limit');
		return $this;
	}
}
