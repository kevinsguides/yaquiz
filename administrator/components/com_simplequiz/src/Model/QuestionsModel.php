<?php
namespace KevinsGuides\Component\SimpleQuiz\Administrator\Model;
use Exception;
use JFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\MVC\Model\BaseModel;

defined ( '_JEXEC' ) or die;

//this is a model for multiple question operations

class QuestionsModel extends BaseDatabaseModel
{

    //get all the Items
    public function getItems()
    {
        //get the database driver
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__simplequiz_questions');
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


}