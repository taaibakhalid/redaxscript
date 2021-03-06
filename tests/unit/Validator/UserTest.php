<?php
namespace Redaxscript\Tests\Validator;

use Redaxscript\Tests\TestCaseAbstract;
use Redaxscript\Validator;

/**
 * UserTest
 *
 * @since 4.3.0
 *
 * @package Redaxscript
 * @category Tests
 * @author Henry Ruhs
 *
 * @covers Redaxscript\Validator\User
 */

class UserTest extends TestCaseAbstract
{
	/**
	 * testGetPattern
	 *
	 * @since 4.3.0
	 */

	public function testGetPattern() : void
	{
		/* setup */

		$validator = new Validator\User();

		/* actual */

		$actual = $validator->getPattern();

		/* compare */

		$this->assertIsString($actual);
	}

	/**
	 * testValidate
	 *
	 * @since 4.3.0
	 *
	 * @param string $user
	 * @param bool $expect
	 *
	 * @dataProvider providerAutoloader
	 */

	public function testValidate(string $user = null, bool $expect = null) : void
	{
		/* setup */

		$validator = new Validator\User();

		/* actual */

		$actual = $validator->validate($user);

		/* compare */

		$this->assertEquals($expect, $actual);
	}
}
