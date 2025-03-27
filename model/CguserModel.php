<?php

/**
 * @package     Joomla.Administrator
 * @subpackage  com_users
 *
 * @copyright   (C) 2008 Open Source Matters, Inc. <https://www.joomla.org>
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * En création d'utilisateur, ajout d'un mot de passe de la longueur définie dans la config.
 *
 */

namespace Joomla\Component\Users\Administrator\Model;

use Joomla\CMS\Component\ComponentHelper;
use Joomla\Component\Users\Administrator\Model\UserModel;
use Joomla\CMS\User\UserHelper;

\defined('_JEXEC') or die;

class CguserModel extends UserModel
{
    /**
    * Method to save the form data.
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
    /**
     * Strange behaviour : return an object when expecting Array
     *
     * @param   integer  $userId  The user ID to retrieve the groups for
     *
     * @return  array  An array of assigned groups
     */
    public function getAssignedGroups($userId = null)
    {
        $groupsIDs = parent::getAssignedGroups($userId);
        if (!empty($groupsIDs)) {
            $ret = [];
            if (is_object($groupsIDs)) {
                foreach ($groupsIDs as $key => $item) {
                    if (is_int($item)) {
                        $ret[$key] = $item;
                    }
                }
                $groupsIDs = $ret;
            }
        }
        return $groupsIDs;
    }

}
