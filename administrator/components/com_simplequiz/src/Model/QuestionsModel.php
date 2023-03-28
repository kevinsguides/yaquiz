<?php
namespace KevinsGuides\Component\SimpleQuiz\Administrator\Model;
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
        $query->from('#__simplequiz_questions');
        if($filter_title){
            Log::add('attempt filter by title '.$filter_title, Log::INFO, 'com_simplequiz');
            $query->where($db->quoteName('question') . ' LIKE ' . $db->quote('%'.$filter_title.'%'));
        }
        if($filter_categories){
            Log::add('attempt filter by category '.$filter_categories, Log::INFO, 'com_simplequiz');
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
        $query->from('#__simplequiz_questions');
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
        $query->from('#__simplequiz_questions');
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
        $query->where('extension = "com_simplequiz"');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        return $items;
    }

    
    public function getForm($data = [], $loadData = true)
    {
        Log::add('getform called in questionsmodel', Log::INFO, 'com_simplequiz');
        $app  = Factory::getApplication();
        // Get the form.
        $form = $this->loadForm('com_simplequiz.questionsfilter', 'questionsfilter', ['control' => 'filters', 'load_data' => $loadData]);
        if (empty($form)) {
            return false;
        }
        return $form;

    }

    public function getPageCount(){
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('COUNT(*)');
        $query->from('#__simplequiz_questions');
        $db->setQuery($query);
        $count = $db->loadResult();
        return ceil($count / 10);
    }


}