<?php
namespace Redaxscript\Model;

/**
 * parent class to provide the user model
 *
 * @since 3.3.0
 *
 * @package Redaxscript
 * @category Model
 * @author Henry Ruhs
 */

class User extends ModelAbstract
{
	/**
	 * name of the table
	 *
	 * @var string
	 */

	protected $_table = 'users';

	/**
	 * get the user by user
	 *
	 * @since 4.0.0
	 *
	 * @param string $user name of the user
	 *
	 * @return object|null
	 */

	public function getByUser(string $user = null) : ?object
	{
		return $this->query()->where('user', $user)->findOne() ? : null;
	}

	/**
	 * create the user by array
	 *
	 * @since 3.3.0
	 *
	 * @param array $createArray array of the create
	 *
	 * @return bool
	 */

	public function createByArray(array $createArray = []) : bool
	{
		return $this
			->query()
			->create()
			->set($createArray)
			->save();
	}

	/**
	 * reset the password by id
	 *
	 * @since 3.3.0
	 *
	 * @param int $userId identifier of the user
	 * @param string $password
	 *
	 * @return bool
	 */

	public function resetPasswordById(int $userId = null, string $password = null) : bool
	{
		return $this
			->query()
			->whereIdIs($userId)
			->findOne()
			->set('password', $password)
			->save();
	}
}
