<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\Helper;
use JHtmlSidebar;
use Joomla\CMS\Factory;
use Joomla\CMS\Helper\CMSHelper;
use Joomla\CMS\Log\Log;
use JText;
use JToolbarHelper;

defined ( '_JEXEC' ) or die;

class YaquizHelper{

    public function getCategoryName($id){
        if($id > 0){
            $db = Factory::getContainer()->get('DatabaseDriver');
            $query = $db->getQuery(true);
            $query->select('title');
            $query->from('#__categories');
            $query->where('id = ' . $id);
            $db->setQuery($query);
            $result = $db->loadResult();
            return $result;
        }
        else{
            return 'Uncategorized';
        }
        
    }

}