<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_foos
 *
 * @copyright   Copyright (C) 2005 - 2020 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

namespace KevinsGuides\Component\Yaquiz\Administrator\Extension;

defined('JPATH_PLATFORM') or die;

use Joomla\CMS\User\UserFactoryInterface;
use Joomla\CMS\Association\AssociationServiceInterface;
use Joomla\CMS\Association\AssociationServiceTrait;
use Joomla\CMS\Categories\CategoryServiceInterface;
use Joomla\CMS\Categories\CategoryServiceTrait;
use Joomla\CMS\Extension\BootableExtensionInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\HTML\HTMLRegistryAwareTrait;
use FooNamespace\Component\Foos\Administrator\Service\HTML\AdministratorService;
use FooNamespace\Component\Foos\Administrator\Service\HTML\Icon;
use Psr\Container\ContainerInterface;
use Joomla\CMS\Helper\ContentHelper;
use Joomla\CMS\Component\Router\RouterServiceInterface;
use Joomla\CMS\Component\Router\RouterServiceTrait;

/**
 * Component class for com_foos
 *
 * @since  __BUMP_VERSION__
 */
class YaquizComponent extends MVCComponent implements BootableExtensionInterface, CategoryServiceInterface, AssociationServiceInterface, RouterServiceInterface
{
	use CategoryServiceTrait;
	use AssociationServiceTrait;
	use HTMLRegistryAwareTrait;
	use RouterServiceTrait;

	/**
	 * Booting the extension. This is the function to set up the environment of the extension like
	 * registering new class loaders, etc.
	 *
	 * If required, some initial set up can be done from services of the container, eg.
	 * registering HTML services.
	 *
	 * @param   ContainerInterface  $container  The container
	 *
	 * @return  void
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function boot(ContainerInterface $container)
	{

    }

	/**
	 * Adds Count Items for Category Manager.
	 *
	 * @param   \stdClass[]  $items    The category objects
	 * @param   string       $section  The section
	 *
	 * @return  void
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function countItems(array $items, string $section)
	{
		try {
			$config = (object) [
				'related_tbl'   => $this->getTableNameForSection($section),
				'state_col'     => 'published',
				'group_col'     => 'catid',
				'relation_type' => 'category_or_group',
			];

			ContentHelper::countRelations($items, $config);
		} catch (\Exception $e) {
			// Ignore it
		}
	}

	/**
	 * Returns the table for the count items functions for the given section.
	 *
	 * @param   string  $section  The section
	 *
	 * @return  string|null
	 *
	 * @since   __BUMP_VERSION__
	 */
	protected function getTableNameForSection(string $section = null)
	{
		return ($section === 'category' ? 'com_yaquiz_quizzes' : 'com_yaquiz_quizzes');
	}

	/**
	 * Returns the state column for the count items functions for the given section.
	 *
	 * @param   string  $section  The section
	 *
	 * @return  string|null
	 *
	 * @since   __BUMP_VERSION__
	 */
	protected function getStateColumnForSection(string $section = null)
	{
		return 'published';
	}
}
