<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\Model;
use Exception;
use JFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Model\BaseModel;

defined ( '_JEXEC' ) or die;

//this is a model for multiple question operations

class QuestionsModel extends AdminModel
{

    //get all the Items
    public function getItems($filter_title = null, $filter_categories = null, $page = 0, $filter_limit = 10)
    {
        //get the database driver
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__com_yaquiz_questions');
        if($filter_title){
            Log::add('attempt filter by title '.$filter_title, Log::INFO, 'com_yaquiz');
            $query->where($db->quoteName('question') . ' LIKE ' . $db->quote('%'.$filter_title.'%'));
        }
        if($filter_categories){
            Log::add('attempt filter by category '.$filter_categories, Log::INFO, 'com_yaquiz');
            $query->where($db->quoteName('catid') . ' = ' . $db->quote($filter_categories));
        }
        if($filter_limit == null){
            $filter_limit = 10;
        }
        $query->setLimit($filter_limit, ($page)*$filter_limit);
        $db->setQuery($query);
        $items = $db->loadObjectList();
        return $items;
    }

    //get items by their category
    public function getItemsByCategory($category)
    {
        //get the database driver
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__com_yaquiz_questions');
        $query->where('catid = ' . $category);
        $db->setQuery($query);
        $items = $db->loadObjectList();
        return $items;
    }

    //get items in groups of 10
    public function getItemsByPage($page)
    {
        //get the database driver
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__com_yaquiz_questions');
        $query->setLimit(10);
        $query->setOffset($page * 10);
        $db->setQuery($query);
        $items = $db->loadObjectList();
        return $items;
    }

    public function getCategories(){
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('id, title');
        $query->from('#__categories');
        $query->where('extension = "com_yaquiz"');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        return $items;
    }

    
    public function getForm($data = [], $loadData = true)
    {

        $app = Factory::getApplication();
        if ($app->input->get('layout') == 'insertmulti') {
            $form = $this->loadForm('com_yaquiz.insertmulti', 'insertmulti');
        }
        else{
            $form = $this->loadForm('com_yaquiz.questionsfilter', 'questionsfilter', ['control' => 'filters', 'load_data' => $loadData]);
        }

        
        if (empty($form)) {
            return false;
        }
        return $form;

    }

    public function getPageCount($filter_categories = null, $filter_title = null, $filter_limit = 10){
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('COUNT(*)');
        $query->from('#__com_yaquiz_questions');
        if($filter_title){
            $query->where($db->quoteName('question') . ' LIKE ' . $db->quote('%'.$filter_title.'%'));
        }
        if($filter_categories){
            $query->where($db->quoteName('catid') . ' = ' . $db->quote($filter_categories));
        }
        if($filter_limit == null){
            $filter_limit = 10;
        }

        $db->setQuery($query);
        $count = $db->loadResult();
        return ceil($count / $filter_limit);
    }

    public function insertMultiQuestions($questions, $catid){


        $db = Factory::getContainer()->get('DatabaseDriver');
        $app = Factory::getApplication();
        $userid = $app->getIdentity()->id;

        if($app->getIdentity()->authorise('core.edit', 'com_yaquiz') != true){
            $app->enqueueMessage('Edit permissions required', 'error');
            return;
        }

        foreach($questions as $question){
            Log::add('attempt insert question '.$question['question'], Log::INFO, 'com_yaquiz');
            //loop through all elems in question and log them
            if($question['type'] == 'mchoice'){
                $query = $db->getQuery(true);
                $query->insert('#__com_yaquiz_questions');
                $query->columns('id, question, params, published, created_by, modified_by, details, answers, correct, catid');
                $id = 'null';
                $questiontitle = $db->quote($question['question']);
                $params = [];
                    $params['question_type'] = 'multiple_choice';
                    $params['randomize_mchoice'] = 0;
                    $params['case_sensitive'] = null;
                    $params['points'] = $question['pointvalue'];
                $dbparams = $db->quote(json_encode($params));
                $published = $db->quote(1);
                $created_by = $db->quote($userid);
                $modified_by = $db->quote($userid);
                $details = $db->quote($question['details']);
                $answers = $db->quote(json_encode($question['answers']));
                $correct = $db->quote($question['correct']);
                $query->values("$id, $questiontitle, $dbparams, $published, $created_by, $modified_by, $details, $answers, $correct, $catid");
                $db->setQuery($query);
                $db->execute();
            }
            if($question['type'] == 'tf'){
                $query = $db->getQuery(true);
                $query->insert('#__com_yaquiz_questions');
                $query->columns('id, question, params, published, created_by, modified_by, details, answers, correct, catid');
                $id = 'null';
                $questiontitle = $db->quote($question['question']);
                $params = [];
                    $params['question_type'] = 'true_false';
                    $params['randomize_mchoice'] = 0;
                    $params['case_sensitive'] = null;
                    $params['points'] = $question['pointvalue'];
                $dbparams = $db->quote(json_encode($params));
                $published = $db->quote(1);
                $created_by = $db->quote($userid);
                $modified_by = $db->quote($userid);
                $details = $db->quote($question['details']);
                $answers = $db->quote(json_encode($question['answers']));
                if($question['correct'] == 't' || $question['correct'] == 'true'){
                    $correct = $db->quote(1);
                }
                    
                else{
                      $correct = $db->quote(0);
                }
                  
                
                $query->values("$id, $questiontitle, $dbparams, $published, $created_by, $modified_by, $details, $answers, $correct, $catid");
                $db->setQuery($query);
                $db->execute();
            }
            if($question['type'] == 'shortans'){
                $query = $db->getQuery(true);
                $query->insert('#__com_yaquiz_questions');
                $query->columns('id, question, params, published, created_by, modified_by, details, answers, correct, catid');
                $id = 'null';
                $questiontitle = $db->quote($question['question']);
                $params = [];
                    $params['question_type'] = 'fill_blank';
                    $params['randomize_mchoice'] = 0;
                    $params['case_sensitive'] = 0;
                    $params['points'] = $question['pointvalue'];
                $dbparams = $db->quote(json_encode($params));
                $published = $db->quote(1);
                $created_by = $db->quote($userid);
                $modified_by = $db->quote($userid);
                $details = $db->quote($question['details']);
                $answers = $db->quote(json_encode($question['answers']));
                $correct =  $db->quote(" ");
                $query->values("$id, $questiontitle, $dbparams, $published, $created_by, $modified_by, $details, $answers, $correct, $catid");
                $db->setQuery($query);
                $db->execute();
            }



        }
                
    }

    public function deleteQuestions($question_ids){

        //user needs permissions
        $app = Factory::getApplication();
        if($app->getIdentity()->authorise('core.delete', 'com_yaquiz') != true){
            $app->enqueueMessage('Delete permissions required to delete', 'error');
            return;
        }

        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->delete('#__com_yaquiz_questions');
        $query->where('id IN ('.implode(',', $question_ids).')');
        $db->setQuery($query);
        $db->execute();

        //also delete from question quiz map
        $query = $db->getQuery(true);
        $query->delete('#__com_yaquiz_question_quiz_map');
        $query->where('question_id IN ('.implode(',', $question_ids).')');
        $db->setQuery($query);
        $db->execute();
        
        
    }


}