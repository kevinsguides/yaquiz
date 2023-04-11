<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/



defined ( '_JEXEC' ) or die;

use Joomla\CMS\Dispatcher\ComponentDispatcherFactoryInterface;
use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Extension\MVCComponent;
use Joomla\CMS\Extension\Service\Provider\ComponentDispatcherFactory;
use Joomla\CMS\Extension\Service\Provider\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\DI\Container;
use Joomla\DI\ServiceProviderInterface;

return new class implements ServiceProviderInterface {

    public function register(Container $container): void {
        $container->registerServiceProvider(new MVCFactory('\\KevinsGuides\\Component\\Yaquiz'));
        $container->registerServiceProvider(new ComponentDispatcherFactory('\\KevinsGuides\\Component\\Yaquiz'));
        $container->set(
            ComponentInterface::class,
            function (Container $container) {
                $component = new MVCComponent($container->get(ComponentDispatcherFactoryInterface::class));
                $component->setMVCFactory($container->get(MVCFactoryInterface::class));
                return $component;
            }
        );
    }
};