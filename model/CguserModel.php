<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   (C) 2025 Conseilgouz
 * @license     GNU General Public License version 3 or later; see LICENSE.txt
 *
 * En création d'utilisateur, ajout d'un mot de passe de la longueur définie dans la config.
 *
 */

namespace Joomla\Component\Users\Administrator\Model;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Component\Users\Administrator\Model\UserModel;
use Joomla\CMS\User\UserHelper;

\defined('_JEXEC') or die;

class CguserModel extends UserModel
{
    /**
         * Constructor.
         *
         * @param   array                 $config   An optional associative array of configuration settings.
         * @param   ?MVCFactoryInterface  $factory  The factory.
         *
         * Note : en cas de modification d'un utilisateur, user.id doit être initialisée.
         *
         */
    public function __construct($config = [], ?MVCFactoryInterface $factory = null)
    {
        $input = Factory::getApplication()->getInput();
        $id = $input->getInt('id');
        if ($id) {// edit one user
            $this->setState('user.id', $id);
        }
        parent::__construct($config, $factory);
    }
    /**
    * Method to save the form data.
    *
    * En création d'un utilisateur, si le mot de passe n'a pas été saisi, un mot de
    * passe de la longueur mini. définie dans la configuration est créé car, en standard,
    * le mot de passe créé fait 32 caractères.
    *
    * @param   array  $data  The form data.
    *
    * @return  boolean  True on success.
    *
    */
    public function save($data)
    {
        $pk   = (!empty($data['id'])) ? $data['id'] : (int) $this->getState('user.id');
        $user = $this->getUserFactory()->loadUserById($pk);

        if (empty($user->id)) {
            // Check the password and create the crypted password
            if (empty($data['password'])) {
                $params = ComponentHelper::getParams('com_users');
                $minimumLength      = (int)$params->get('minimum_length', 32);
                $data['password']  = UserHelper::genRandomPassword($minimumLength);
                $data['password2'] = $data['password'];
            }
        }
        return parent::save($data);
    }
}
