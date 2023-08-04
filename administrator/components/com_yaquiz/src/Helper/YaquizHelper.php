<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\Helper;

use Joomla\CMS\Factory;

use KevinsGuides\Component\Yaquiz\Administrator\Model\YaquizModel;

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


    public static function canEditQuiz($id = null){


        $user = Factory::getApplication()->getIdentity();

        // Check edit
        if ($user->authorise('core.edit', 'com_yaquiz')) {
            return true;
        }

        if($id === null){
            $app = Factory::getApplication();
            $id = $app->input->getInt('id');
        }
        

        // Check edit own
        if ($user->authorise('core.edit.own', 'com_yaquiz')) {
            $userId = $user->id;

            // Check for existing quiz
            if ($id) {
                // Get the user who created the article
                $model = new YaquizModel();
                $createdBy = (int) $model->getQuiz($id)->created_by;
                // If the article is yours to edit, allow it.
                if ($createdBy === $userId) {
                    return true;
                }
            }

        }



        return  false;

    }

}