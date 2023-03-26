<?php
namespace KevinsGuides\Component\SimpleQuiz\Administrator\View\Questions;
use Joomla\CMS\Component\ComponentHelper;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
defined ( '_JEXEC' ) or die;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView{


    public function display($tpl = null)
    {
  
        //get model
        $model = $this->getModel();
        $app = Factory::getApplication();
        $wa = $app->getDocument()->getWebAssetManager();

        $cParams = ComponentHelper::getParams('com_simplequiz');
        if($cParams->get('load_mathjax')==='1'){
            $wa->registerAndUseScript('com_simplequiz.mathjax', 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js');
        }
        $this->form = $model->getForm();
        //get all questions
        $this->items = $model->getItems();

        $toolbar = Toolbar::getInstance('toolbar');
        //add component options
        $toolbar->appendButton('Link', 'options', 'Options', 'index.php?option=com_config&view=component&component=com_simplequiz');
        ToolbarHelper::title('Simple Quiz - Questions', 'list');
        ToolbarHelper::custom('SimpleQuizzes.display', 'list', 'list', 'Quizzes', false);
        ToolbarHelper::addNew('Questions.newQuestion');

        //display the view
        return parent::display($tpl);

    }


}