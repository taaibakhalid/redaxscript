<?php
namespace Redaxscript\Server;

use function basename;

/**
 * children class to get the file
 *
 * @since 2.4.0
 *
 * @package Redaxscript
 * @category Server
 * @author Henry Ruhs
 */

class File extends ServerAbstract
{
	/**
	 * get the output
	 *
	 * @since 2.4.0
	 *
	 * @return string
	 */

	public function getOutput() : string
	{
		return basename($this->_request->getServer('SCRIPT_NAME'));
	}
}