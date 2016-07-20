<?php
namespace Redaxscript\View;

use Redaxscript\Db;
use Redaxscript\Html;
use Redaxscript\Hook;

/**
 * children class to create the comment form
 *
 * @since 3.0.0
 *
 * @package Redaxscript
 * @category View
 * @author Henry Ruhs
 */

class CommentForm extends ViewAbstract
{
	/**
	 * render the view
	 *
	 * @since 3.0.0
	 *
	 * @param integer $articleId identifier of the article
	 *
	 * @return string
	 */

	public function render($articleId = null)
	{
		$output = Hook::trigger('commentFormStart');

		/* html elements */

		$titleElement = new Html\Element();
		$titleElement
			->init('h2', array(
				'class' => 'rs-title-content'
			))
			->text($this->_language->get('comment_new'));
		$formElement = new Html\Form($this->_registry, $this->_language);
		$formElement->init(array(
			'button' => array(
				'submit' => array(
					'name' => get_class()
				)
			)
		), array(
			'captcha' => Db::getSetting('captcha') > 0
		));

		/* create the form */

		$formElement
			->append('<fieldset>')
			->legend()
			->append('<ul><li>')
			->label('* ' . $this->_language->get('author'), array(
				'for' => 'author'
			))
			->text(array(
				'id' => 'author',
				'name' => 'author',
				'readonly' => $this->_registry->get('myName') ? 'readonly' : null,
				'required' => 'required',
				'value' => $this->_registry->get('myName')
			))
			->append('</li><li>')
			->label('* ' . $this->_language->get('email'), array(
					'for' => 'email'
			))
			->email(array(
				'id' => 'email',
				'name' => 'email',
				'readonly' => $this->_registry->get('myEmail') ? 'readonly' : null,
				'required' => 'required',
				'value' => $this->_registry->get('myEmail')
			))
			->append('</li><li>')
			->label($this->_language->get('url'), array(
				'for' => 'url'
			))
			->url(array(
				'id' => 'url',
				'name' => 'url'
			))
			->append('</li><li>')
			->label('* ' . $this->_language->get('text'), array(
				'for' => 'text'
			))
			->textarea(array(
				'class' => 'rs-js-auto-resize rs-js-editor-textarea rs-field-textarea',
				'id' => 'text',
				'name' => 'text',
				'required' => 'required'
			))
			->append('</li>');
		if (Db::getSetting('captcha') > 0)
		{
			$formElement
				->append('<li>')
				->captcha('task')
				->append('</li>');
		}
		$formElement->append('</ul></fieldset>');
		if (Db::getSetting('captcha') > 0)
		{
			$formElement->captcha('solution');
		}
		$formElement
			->hidden(array(
				'name' => 'article',
				'value' => $articleId
			))
			->token()
			->submit()
			->reset();

		/* collect output */

		$output .= $titleElement . $formElement;
		$output .= Hook::trigger('commentFormEnd');
		return $output;
	}
}
