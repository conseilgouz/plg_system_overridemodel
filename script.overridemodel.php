<?php
/**
 * @package		Override Model system plugin
 * @author		ConseilGouz
 * @copyright	Copyright (C) 2025 ConseilGouz. All rights reserved.
 * license      https://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 *
 * from https://joomla.stackexchange.com/questions/31922/change-the-default-articles-list-ordering-overriding-the-model/32099#32099
 **/
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\Database\DatabaseInterface;
use Joomla\Filesystem\File;

class plgSystemoverridemodelInstallerScript
{
    public function postflight($type, $parent)
    {
        if (($type != 'install') && ($type != 'update')) {
            return true;
        }
        $db = Factory::getContainer()->get(DatabaseInterface::class);
        // remove obsolete update infos
        $query = $db->getQuery(true)
            ->delete('#__update_sites')
            ->where($db->quoteName('location') . ' like "%432473037d.url-de-test.ws/%"');
        $db->setQuery($query);
        $db->execute();

        // Enable plugin
        $conditions = array(
            $db->qn('type') . ' = ' . $db->q('plugin'),
            $db->qn('folder') . ' = '. $db->q('system'),
            $db->qn('element') . ' = ' . $db->quote('overridemodel')
        );
        $fields = array($db->qn('enabled') . ' = 1');
        $query = $db->getQuery(true);
        $query->update($db->quoteName('#__extensions'))->set($fields)->where($conditions);
        $db->setQuery($query);
        try {
            $db->execute();
        } catch (\RuntimeException $e) {
            Log::add('unable to enable Override plugin', Log::ERROR, 'jerror');
        }
        File::copy(
            __DIR__.'/model/CguserModel.php',
            JPATH_ADMINISTRATOR.'/components/com_users/src/Model/CguserModel.php'
        );
        return true;
    }
}
