<?php

namespace KevinsGuides\Component\SimpleQuiz\Administrator\Model;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

defined ( '_JEXEC' ) or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Log\Log;

class SimpleQuizzesModel extends AdminModel
{
    
    //get all the Items
    public function getItems($titlefilter = null, $catfilter=null)
    {
        //get the database driver
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__simplequiz_quizzes');
        if($titlefilter){
            $query->where('title LIKE "%'.$titlefilter.'%"');
        }
        if($catfilter){
            $query->where('catid = '.$catfilter);
        }
        $db->setQuery($query);
        $items = $db->loadObjectList();
        return $items;
    }



    public function getName(){
        return 'SimpleQuizzesModel';
    }

	/**
	 * Method for getting a form.
	 *
	 * @param array $data Data for the form.
	 * @param bool $loadData True if the form is to load its own data (default case), false if not.
	 * @return \Joomla\CMS\Form\Form
	 */
	public function getForm($data = array(), $loadData = true) {
        //get the simplequizzes form
        $form = $this->loadForm('com_simplequiz.simplequizzes', 'simplequizzes', array('control' => 'filters', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;

	}
}