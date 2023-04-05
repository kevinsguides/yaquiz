<?php
namespace KevinsGuides\Component\Yaquiz\Site\Model;
defined ('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Model\BaseModel;

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



}