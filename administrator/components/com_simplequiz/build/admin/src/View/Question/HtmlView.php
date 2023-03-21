<?php
namespace KevinsGuides\Component\SimpleQuiz\Administrator\View\Question;
use Joomla\CMS\Log\Log;
defined ( '_JEXEC' ) or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView{


    public function display($tpl = null)
    {
  
        //get model
        $model = $this->getModel();

        $question_id = $_GET['qnid'];

        //if question_id is not set, set to zero
        if($question_id == null)
        {
            $question_id = 0;
        }
        //get the question
        $this->item = $model->getItem($question_id);
        //set the form
        $this->form  = $model->getForm($this->item, false);

        //display the view
        return parent::display($tpl);

    }


}