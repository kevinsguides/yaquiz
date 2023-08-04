<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\View\Question;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
defined ( '_JEXEC' ) or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView{


    public function display($tpl = null)
    {
        $app = Factory::getApplication();
        //hide the main menu
        $app->getInput()->set('hidemainmenu', true);
  
        //get model
        $model = $this->getModel();

        if(isset($_GET['id'])){
            $question_id = $_GET['id'];
        }
        else{
            $question_id = null;
        }



        //if question_id is not set, set to zero
        if($question_id == null)
        {
            $question_id = 0;
        }
        //get the question
        $this->item = $model->getItem($question_id);
        //set the form
        $this->form  = $model->getForm($this->item, false);

        ToolbarHelper::title(Text::_('COM_YAQUIZ_PAGETITLE_QUESTIONEDITOR'));
        ToolbarHelper::apply('Question.edit');
        ToolbarHelper::save('Question.saveclose');
        ToolbarHelper::cancel('Question.cancel');

        //display the view
        return parent::display($tpl);

    }


}