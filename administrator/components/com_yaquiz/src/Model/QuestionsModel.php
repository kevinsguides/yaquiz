<?php
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
    public function getItems($filter_title = null, $filter_categories = null, $page = 0)
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
        $query->setLimit(10, $page * 10);
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

    public function getPageCount($filter_categories = null, $filter_title = null){
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
        $db->setQuery($query);
        $count = $db->loadResult();
        return ceil($count / 10);
    }

    public function insertMultiQuestions($questions, $catid){
        $db = Factory::getContainer()->get('DatabaseDriver');
        $app = Factory::getApplication();
        $userid = $app->getIdentity()->id;


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



        }
        
        //log the entire query
        
    }


}