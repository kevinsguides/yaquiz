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
     * gets everything except full_results
     * @param pk user id
     * @param limit # of results to return
     * @param page page of results to return
     */
    public function getUserResults($pk = null, $limit = null, $page = null){

        //if pk null, assume it's the id of current user
        if ($pk == null){
            $user = Factory::getApplication()->getIdentity();
            $pk = $user->id;
        }

        if($limit == null){
            $limit = 10;
        }

        if($page == null){
            $page = 1;
        }

        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        //$query->select('id, quiz_id, user_id, score, points, total_points, submitted, passed');
        //$query->select('r.id, r.quiz_id, r.user_id, r.score, r.points, r.total_points, r.submitted, r.passed, q.title');
        $query->select('r.*, q.title');
        $query->from($db->quoteName('#__com_yaquiz_results', 'r'));
        $query->join('LEFT', $db->quoteName('#__com_yaquiz_quizzes', 'q') . ' ON (' . $db->quoteName('r.quiz_id') . ' = ' . $db->quoteName('q.id') . ')');
        $query->where($db->quoteName('r.user_id') . ' = ' . $db->quote($pk));
        $query->setLimit($limit, ($page-1)*$limit);
        //order by newest first
        $query->order('submitted DESC');
        $db->setQuery($query);
        $results = $db->loadObjectList();
        Log::add('Loading results for user: ' . $pk . ' results: ' . 
            print_r($results, true)
        , Log::INFO, 'yaquiz');
        return $results;
    }

    /**
     * count # of records for user pk
     * @param pk user id
     */
    public function countTotalResults($pk = null){
            
            //if pk null, assume it's the id of current user
            if ($pk == null){
                $user = Factory::getApplication()->getIdentity();
                $pk = $user->id;
            }
    
            $db = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->getQuery(true);
            $query->select('COUNT(*)');
            $query->from($db->quoteName('#__com_yaquiz_results'));
            $query->where($db->quoteName('user_id') . ' = ' . $db->quote($pk));
            $db->setQuery($query);
            $count = $db->loadResult();
            return $count;

    }


    /**
     * load a single result with timer data
     * @param pk result id
     */
    public function getIndividualResult($pk = null){

        $app = Factory::getApplication();
        $input = $app->input;

        if($pk == null){
            $pk = $input->get('resultid', null, 'INT');
        }

        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('r.*, t.start_time');
        $query->from($db->quoteName('#__com_yaquiz_results', 'r'));
        $query->join('LEFT', $db->quoteName('#__com_yaquiz_user_quiz_times', 't') . ' ON (' . $db->quoteName('r.id') . ' = ' . $db->quoteName('t.result_id') . ')');
        $query->where($db->quoteName('r.id') . ' = ' . $db->quote($pk));
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