<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

namespace KevinsGuides\Component\Yaquiz\Site\Model;
use Joomla\CMS\Log\Log;
defined ('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseModel;
use Joomla\CMS\Language\Text;


class UserModel extends BaseModel
{
    /**
     * load all results... I don't think I used this.
     */
    public function getItems()
    {
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__com_yaquiz_results'));
        $db->setQuery($query);
        $results = $db->loadObjectList();
        return $results;
    }

    /**
     * load results for a specific user
     */
    public function getUserResults($pk = null){

        //if pk null, assume it's the id of current user
        if ($pk == null){
            $user = Factory::getApplication()->getIdentity();
            $pk = $user->id;
        }

        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__com_yaquiz_results'));
        $query->where($db->quoteName('user_id') . ' = ' . $db->quote($pk));
        //order by newest first
        $query->order('submitted DESC');
        $db->setQuery($query);
        $results = $db->loadObjectList();
        return $results;
    }

    public function getIndividualResult($pk = null){

        $app = Factory::getApplication();
        $input = $app->input;

        if($pk == null){
            $pk = $input->get('resultid', null, 'INT');
        }

        Log::add('Loading result: ' . $pk, Log::INFO, 'yaquiz');

        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->quoteName('#__com_yaquiz_results'));
        $query->where($db->quoteName('id') . ' = ' . $db->quote($pk));
        $db->setQuery($query);
        $result = $db->loadObject();

        //the result->user_id must be the same as the current user
        $user = Factory::getApplication()->getIdentity();
        if($result->user_id != $user->id){
            $app->enqueueMessage(Text::_('COM_YAQUIZ_VIEW_QUIZ_RESULT_DENIED'), 'error');
            return false;
        }

        return $result;

    }



}