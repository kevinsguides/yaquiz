<?php

namespace KevinsGuides\Component\Yaquiz\Administrator\Model;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;
use Joomla\CMS\Factory;

defined ( '_JEXEC' ) or die;

use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Model\ListModel;
use Joomla\CMS\Log\Log;

class YaquizzesModel extends AdminModel
{
    
    //get all the Items
    public function getItems($titlefilter = null, $catfilter=null, $page=1, $limit=10)
    {
        //get the database driver
        $db = $this->getDatabase();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__com_yaquiz_quizzes');
        if($titlefilter){
            $query->where('title LIKE "%'.$titlefilter.'%"');
        }
        if($catfilter){
            $query->where('catid = '.$catfilter);
        }
        $query->setLimit($limit, ($page)*$limit);
        $db->setQuery($query);
        $items = $db->loadObjectList();
        return $items;
    }

    public function getTotalPages($limit, $titlefilter = null, $catfilter=null){
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('count(*)');
        $query->from('#__com_yaquiz_quizzes');
        if($titlefilter){
            $query->where('title LIKE "%'.$titlefilter.'%"');
        }
        if($catfilter){
            $query->where('catid = '.$catfilter);
        }
        $db->setQuery($query);
        $total = $db->loadResult();
        return ceil($total/$limit);
    }



    public function getName(){
        return 'YaquizzesModel';
    }

	/**
	 * Method for getting a form.
	 *
	 * @param array $data Data for the form.
	 * @param bool $loadData True if the form is to load its own data (default case), false if not.
	 * @return \Joomla\CMS\Form\Form
	 */
	public function getForm($data = array(), $loadData = true) {
        //get the yaquizzes form
        $form = $this->loadForm('com_yaquiz.yaquizzes', 'yaquizzes', array('control' => 'filters', 'load_data' => $loadData));
        if (empty($form)) {
            return false;
        }
        return $form;

	}
}