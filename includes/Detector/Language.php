<?php
namespace Redaxscript\Detector;

use Redaxscript\Db;
use Redaxscript\Model;
use function substr;

/**
 * children class to detect the required language
 *
 * @since 2.0.0
 *
 * @package Redaxscript
 * @category Detector
 * @author Henry Ruhs
 */

class Language extends DetectorAbstract
{
	/**
	 * automate run
	 *
	 * @since 2.1.0
	 */

	protected function _autorun()
	{
		$settingModel = new Model\Setting();
		$dbStatus = $this->_registry->get('dbStatus');
		$lastTable = $this->_registry->get('lastTable');
		$lastId = $this->_registry->get('lastId');

		/* detect language */

		$this->_output = $this->_detect(
		[
			'query' => $this->_request->getQuery('l'),
			'session' => $this->_request->getSession('language'),
			'contents' => $lastTable ? Db::forTablePrefix($lastTable)->whereIdIs($lastId)->findOne()->language : null,
			'settings' => $dbStatus === 2 ? $settingModel->get('language') : null,
			'browser' => substr($this->_request->getServer('HTTP_ACCEPT_LANGUAGE'), 0, 2),
			'fallback' => 'en'
		], 'language', 'languages' . DIRECTORY_SEPARATOR . $this->_filePlaceholder . '.json');
	}
}
