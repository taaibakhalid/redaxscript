<?php
namespace Redaxscript;

/**
 * parent class to minify styles and scripts
 *
 * @since 2.2.0
 *
 * @package Redaxscript
 * @category Minifier
 * @author Henry Ruhs
 */

class Minifier
{
	/**
	 * shortcut to minify styles
	 *
	 * @since 2.2.0
	 *
	 * @param $input styles input
	 *
	 * @return string
	 */

	public function styles($input = null)
	{
		return $this->_minify($input, 'styles');
	}

	/**
	 * shortcut to minify scripts
	 *
	 * @since 2.2.0
	 *
	 * @param $input scripts input
	 *
	 * @return string
	 */

	public function scripts($input = null)
	{
		return $this->_minify($input, 'scripts');
	}

	/**
	 * minify styles and scripts
	 *
	 * @since 2.2.0
	 *
	 * @param $input styles and scripts input
	 * @param $type related type of input
	 *
	 * @return string
	 */

	protected function _minify($input = null, $type = null)
	{
		/* replace comments */

		$output = preg_replace('/\/\*([\s\S]*?)\*\//', '', $input);

		/* replace tabs and newlines */

		$output = preg_replace('/\t+/', '', $output);
		$output = preg_replace('/\r+/', PHP_EOL, $output);
		$output = preg_replace('/\n+/', PHP_EOL, $output);

		/* general minify */

		$output = str_replace(array(
			' {',
			'{ '
		), '{', $output);
		$output = str_replace(array(
			' }',
			'} ',
		), '}', $output);
		$output = str_replace(array(
			' :',
			': '
		), ':', $output);
		$output = str_replace(array(
			' ;',
			'; '
		), ';', $output);
		$output = str_replace(array(
			' ,',
			', '
		), ',', $output);

		/* additional minify for script */

		if ($type == 'scripts')
		{
			$output = str_replace(array(
				' (',
				'( '
			), '(', $output);
			$output = str_replace(array(
				' +',
				'+ '
			), '+', $output);
			$output = str_replace(array(
				' -',
				'- '
			), '-', $output);
			$output = str_replace(array(
				' =',
				'= '
			), '=', $output);
			$output = str_replace(array(
				' ||',
				'|| '
			), '||', $output);
			$output = str_replace(array(
				' &&',
				'&& '
			), '&&', $output);
		}

		/* trim output */

		$output = trim($output);
		return $output;
	}
}