<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/



defined ( '_JEXEC' ) or die;

use Joomla\CMS\Categories\CategoryFactoryInterface;
use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\Service\Provider\CategoryFactory;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\HTML\Registry;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;
use KevinsGuides\Component\Yaquiz\Administrator\Extension\YaquizComponent;
use KevinsGuides\Component\Yaquiz\Administrator\Helper\AssociationsHelper;
use Joomla\CMS\Association\AssociationExtensionInterface;
use Joomla\CMS\Component\Router\RouterFactoryInterface;
use Joomla\CMS\Extension\Service\Provider\RouterFactory;





return new class implements ServiceProviderInterface
{
	/**
	 * Registers the service provider with a DI container.
	 *
	 * @param   Container  $container  The DI container.
	 *
	 * @return  void
	 *
	 * @since   __BUMP_VERSION__
	 */
	public function register(Container $container)
	{
		$container->set(AssociationExtensionInterface::class, new AssociationsHelper);

		//$container->registerServiceProvider(new CategoryFactory('\\KevinsGuides\\Component\\Yaquiz'));
		$container->registerServiceProvider(new MVCFactory('\\KevinsGuides\\Component\\Yaquiz'));
		$container->registerServiceProvider(new ComponentDispatcherFactory('\\KevinsGuides\\Component\\Yaquiz'));
		$container->registerServiceProvider(new RouterFactory('\\KevinsGuides\\Component\\Yaquiz'));

		$container->set(
			ComponentInterface::class,
			function (Container $container) {
				$component = new YaquizComponent($container->get(ComponentDispatcherFactoryInterface::class));

				$component->setRegistry($container->get(Registry::class));
				$component->setMVCFactory($container->get(MVCFactoryInterface::class));
				//$component->setCategoryFactory($container->get(CategoryFactoryInterface::class));
				$component->setAssociationExtension($container->get(AssociationExtensionInterface::class));
				$component->setRouterFactory($container->get(RouterFactoryInterface::class));

				return $component;
			}
		);
	}
};