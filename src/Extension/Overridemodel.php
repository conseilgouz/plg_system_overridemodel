<?php
/**
 * @package		Override Model System plugin
 * @author		ConseilGouz
 * @copyright	Copyright (C) 2025 ConseilGouz. All rights reserved.
 * license      https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 *
 * from https://joomla.stackexchange.com/questions/31922/change-the-default-articles-list-ordering-overriding-the-model/32099#32099
 **/
/*                 -------------- Override User Model ---------------------------          */
/*     create a model called MyModel in administrator/components/com_user/src/Model        */
/*                          this plugin extends UserModel.php                              */
/*                 ---------------------------------------------------------------         */
namespace Conseilgouz\Plugin\System\OverrideModel\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Extension\ComponentInterface;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\Component\Users\Administrator\Model\UserModel;
use Joomla\CMS\User\UserFactoryAwareTrait;
use Joomla\CMS\User\UserFactoryInterface;
use Joomla\DI\Container;
use Joomla\Event\EventInterface;

final class Overridemodel extends CMSPlugin
{
    use UserFactoryAwareTrait;
    private $container;
    public function onAfterExtensionBoot(EventInterface $event)
    {
        // Test that a component is being booted.
        if ($event->getArgument('type') !== ComponentInterface::class) {
            return;
        }

        // Test that this is com_users component.
        if ($event->getArgument('extensionName') !== 'users') {
            return;
        }

        // Get the service container.
        $container = $event->getArgument('container');

        if (!($container instanceof Container)) {
            return;
        }

        // Check that MVC factory is used and can be overridden.
        if (!$container->has(MVCFactoryInterface::class) || $container->isProtected(MVCFactoryInterface::class)) {
            return;
        }
        // UserFactory might be lost : reload it
        $this->setUserFactory($container->get(UserFactoryInterface::class));
        $container->extend(
            MVCFactoryInterface::class,
            static fn () =>
            new class ('Joomla\\Component\\Users') extends MVCFactory {
                protected $container;
                protected function getClassName(string $suffix, string $prefix)
                {
                    $class = parent::getClassName($suffix, $prefix);
                    // UserFactory is lost : reload it
                    $this->container = Factory::getContainer();
                    $this->setUserFactory($this->container->get(UserFactoryInterface::class));
                    if ($class === UserModel::class) {
                        return 'Joomla\Component\Users\Administrator\Model\CguserModel';
                    }
                    return $class;
                }
            }
        );
    }
}
