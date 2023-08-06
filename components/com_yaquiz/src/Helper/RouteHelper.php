<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_foos
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace KevinsGuides\Component\Yaquiz\Site\Helper;

\defined('_JEXEC') or die;

use Joomla\CMS\Categories\CategoryNode;
use Joomla\CMS\Language\Multilanguage;

/**
 * Foos Component Route Helper
 *
 * @static
 * @package     Joomla.Site
 * @subpackage  com_foos
 * @since       __DEPLOY_VERSION__
 */
abstract class RouteHelper
{
	/**
	 * Get the URL route for a foos from a foo ID, foos category ID and language
	 *
	 * @param   integer  $id        The id of the foos
	 * @param   integer  $catid     The id of the foos's category
	 * @param   mixed    $language  The id of the language being used.
	 *
	 * @return  string  The link to the foos
	 *
	 * @since   __DEPLOY_VERSION__
	 */
	public static function getQuizRoute($id)
	{
		// Create the link
		$link = 'index.php?option=com_yaquiz&view=quiz&id=' . $id;


		return $link;
	}

}
