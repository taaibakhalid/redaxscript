<?php
namespace Redaxscript\Model;

use function array_filter;
use function implode;
use function is_array;

/**
 * parent class to provide the article model
 *
 * @since 3.3.0
 *
 * @package Redaxscript
 * @category Model
 * @author Henry Ruhs
 */

class Article extends ContentAbstract
{
	/**
	 * name of the table
	 *
	 * @var string
	 */

	protected $_table = 'articles';

	/**
	 * get the article by alias
	 *
	 * @since 4.0.0
	 *
	 * @param string $articleAlias alias of the article
	 *
	 * @return object|null
	 */

	public function getByAlias(string $articleAlias = null) : ?object
	{
		return $this->query()->where('alias', $articleAlias)->findOne() ? : null;
	}

	/**
	 * count the articles by category and language
	 *
	 * @since 4.0.0
	 *
	 * @param int $categoryId identifier of the category
	 * @param string $language
	 *
	 * @return int|null
	 */

	public function countByCategoryAndLanguage(int $categoryId = null, string $language = null) : ?int
	{
		return $this
			->query()
			->where(
			[
				'category' => $categoryId,
				'status' => 1
			])
			->whereLanguageIs($language)
			->count() ? : null;
	}

	/**
	 * get the siblings by category and language and order and step
	 *
	 * @since 4.0.0
	 *
	 * @param int $categoryId identifier of the category
	 * @param string $language
	 * @param string $orderColumn
	 * @param int $limitStep
	 *
	 * @return object|null
	 */

	public function getSiblingByCategoryAndLanguageAndOrderAndStep(int $categoryId = null, string $language = null, string $orderColumn = 'rank', int $limitStep = null) : ?object
	{
		$categoryModel = new Category();
		$query = $this
			->query()
			->whereIn('category', $categoryModel->getSiblingArrayById($categoryId))
			->where('status', 1)
			->whereLanguageIs($language)
			->orderBySetting($orderColumn);
		if ($limitStep)
		{
			$query->limitBySetting($limitStep);
		}
		return $query->findMany() ? : null;
	}

	/**
	 * get the article route by id
	 *
	 * @since 3.3.0
	 *
	 * @param int $articleId identifier of the article
	 *
	 * @return string|null
	 */

	public function getRouteById(int $articleId = null) : ?string
	{
		if ($articleId)
		{
			$routeArray = $this
				->query()
				->tableAlias('article')
				->leftJoinPrefix('categories', 'article.category = category.id', 'category')
				->leftJoinPrefix('categories', 'category.parent = parent.id', 'parent')
				->select('parent.alias', 'parentAlias')
				->select('category.alias', 'categoryAlias')
				->select('article.alias', 'articleAlias')
				->where('article.id', $articleId)
				->findArray();

			/* handle route */

			if (is_array($routeArray[0]))
			{
				return implode('/', array_filter($routeArray[0]));
			}
		}
		return null;
	}
}
