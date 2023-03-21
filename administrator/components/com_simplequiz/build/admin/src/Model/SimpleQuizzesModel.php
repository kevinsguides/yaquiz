<?php

namespace KevinsGuides\Component\SimpleQuiz\Administrator\Model;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Factory;

defined ( '_JEXEC' ) or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Log\Log;

class SimpleQuizzesModel extends ListModel
{
    
    //get all the Items
    public function getItems()
    {
        //get the database driver
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__simplequiz_quizzes');
        $db->setQuery($query);
        $items = $db->loadObjectList();
        return $items;
    }

}