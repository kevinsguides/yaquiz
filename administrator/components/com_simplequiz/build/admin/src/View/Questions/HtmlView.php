<?php
namespace KevinsGuides\Component\SimpleQuiz\Administrator\View\Questions;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Toolbar\Toolbar;
defined ( '_JEXEC' ) or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView{


    public function display($tpl = null)
    {
  
        //get model
        $model = $this->getModel();

        //get all questions
        $this->items = $model->getItems();

        $toolbar = Toolbar::getInstance('toolbar');
        //add component options
        $toolbar->appendButton('Link', 'options', 'Options', 'index.php?option=com_config&view=component&component=com_simplequiz');
        

        //display the view
        return parent::display($tpl);

    }


}