<?php
namespace Redaxscript\Content\Tag;

use Redaxscript\Module as BaseModule;
use function array_filter;
use function call_user_func_array;
use function explode;
use function implode;
use function in_array;
use function is_array;
use function json_decode;
use function method_exists;
use function str_replace;

/**
 * children class to parse content for module tags
 *
 * @since 3.0.0
 *
 * @package Redaxscript
 * @category Content
 * @author Henry Ruhs
 */

class Module extends TagAbstract
{
	/**
	 * options of the module tag
	 *
	 * @var array
	 */

	protected $_optionArray =
	[
		'search' =>
		[
			'<rs-module>',
			'</rs-module>'
		],
		'namespace' => 'Redaxscript\Modules',
		'delimiter' => '@@@'
	];

	/**
	 * process the class
	 *
	 * @since 3.0.0
	 *
	 * @param string $content content to be parsed
	 *
	 * @return string
	 */

	public function process(string $content = null) : string
	{
		$output = str_replace($this->_optionArray['search'], $this->_optionArray['delimiter'], $content);
		$partArray = array_filter(explode($this->_optionArray['delimiter'], $output));
		$modulesLoaded = BaseModule\Hook::getModuleArray();

		/* parse as needed */

		foreach ($partArray as $key => $value)
		{
			if ($key % 2)
			{
				$partArray[$key] = null;
				$json = json_decode($value, true);

				/* call with parameter */

				if (is_array($json))
				{
					foreach ($json as $moduleName => $parameterArray)
					{
						if (in_array($moduleName, $modulesLoaded))
						{
							$partArray[$key] = $this->_call($moduleName, $parameterArray);
						}
					}
				}

				/* else simple call */

				else if (in_array($value, $modulesLoaded))
				{
					$partArray[$key] = $this->_call($value);
				}
			}
		}
		$output = implode($partArray);
		return $output;
	}

	/**
	 * call the module
	 *
	 * @since 3.2.0
	 *
	 * @param string $moduleName
	 * @param array $parameterArray
	 *
	 * @return string|null
	 */

	protected function _call(string $moduleName = null, array $parameterArray = []) : ?string
	{
		$moduleClass = $this->_optionArray['namespace'] . '\\' . $moduleName . '\\' . $moduleName;
		$methodName = 'render';
		if (method_exists($moduleClass, $methodName))
		{
			$module = new $moduleClass($this->_registry, $this->_request, $this->_language, $this->_config);
			return call_user_func_array(
			[
				$module,
				$methodName,
			], $parameterArray);
		}
		return null;
	}
}
