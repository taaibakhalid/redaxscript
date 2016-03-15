<?php

/**
 * comments
 *
 * @since 1.2.1
 * @deprecated 2.0.0
 *
 * @package Redaxscript
 * @category Comments
 * @author Henry Ruhs
 *
 * @param integer $article
 * @param string $route
 */

function comments($article = '', $route = '')
{
	$output = Redaxscript\Hook::trigger('commentStart');

	/* query comments */

	$comments = Redaxscript\Db::forTablePrefix('comments')
		->where(array(
			'status' => 1,
			'article' => $article
		))
		->whereRaw('(language = ? OR language is ?)', array(
				Redaxscript\Registry::get('language'),
				null
		))
		->orderGlobal('rank');

	/* query result */

	$result = $comments->findArray();
	if ($result)
	{
		$num_rows = count($result);
		$sub_maximum = ceil($num_rows / Redaxscript\Db::getSettings('limit'));
		$sub_active = LAST_SUB_PARAMETER;

		/* sub parameter */

		if (LAST_SUB_PARAMETER > $sub_maximum || LAST_SUB_PARAMETER == '')
		{
			$sub_active = 1;
		}
		else
		{
			$offset_string = ($sub_active - 1) * Redaxscript\Db::getSettings('limit') . ', ';
		}
	}
	$comments->limit($offset_string . Redaxscript\Db::getSettings('limit'));

	/* query result */

	$result = $comments->findArray();
	$num_rows_active = count($result);

	/* handle error */

	if ($result == '' || $num_rows == '')
	{
		$error = Redaxscript\Language::get('comment_no');
	}

	/* collect output */

	else if ($result)
	{
		$accessValidator = new Redaxscript\Validator\Access();
		$output .= '<div class="rs-box-line"></div>';
		foreach ($result as $r)
		{
			$access = $r['access'];

			/* access granted */

			if ($accessValidator->validate($access, MY_GROUPS) === Redaxscript\Validator\ValidatorInterface::PASSED)
			{
				if ($r)
				{
					foreach ($r as $key => $value)
					{
						$$key = stripslashes($value);
					}
				}

				/* collect headline output */

				$output .= Redaxscript\Hook::trigger('commentFragmentStart', $r) . '<h3 id="comment-' . $id . '" class="rs-title-comment">';
				if ($url)
				{
					$output .= '<a href="' . $url . '" class="rs-link-default" rel="nofollow">' . $author . '</a>';
				}
				else
				{
					$output .= $author;
				}
				$output .= '</h3>';

				/* collect box output */

				$output .= infoline('comments', $id, $author, $date);
				$output .= '<div class="rs-box-comment">' . $text . '</div>' . Redaxscript\Hook::trigger('commentFragmentEnd', $r);

				/* admin dock */

				if (LOGGED_IN == TOKEN && FIRST_PARAMETER != 'logout')
				{
					$output .= admin_dock('comments', $id);
				}
			}
			else
			{
				$counter++;
			}
		}

		/* handle access */

		if ($num_rows_active == $counter)
		{
			$error = Redaxscript\Language::get('access_no');
		}
	}

	/* handle error */

	if ($error)
	{
		$output = '<div class="rs-box-comment-error">' . $error . Redaxscript\Language::get('point') . '</div>';
	}
	$output .= Redaxscript\Hook::trigger('commentEnd');
	echo $output;

	/* call pagination as needed */

	if ($sub_maximum > 1 && Redaxscript\Db::getSettings('pagination') == 1)
	{
		pagination($sub_active, $sub_maximum, $route);
	}
}