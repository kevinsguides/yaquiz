<?php
namespace KevinsGuides\Component\SimpleQuiz\Administrator\View\SimpleQuiz;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarHelper;
defined ( '_JEXEC' ) or die;

//this view for 1 quiz
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView

{

    public function display($tpl = null)

    {

        Log::add('HtmlView::display() called', Log::INFO, 'com_simplequiz');

        $toolbar = Toolbar::getInstance('toolbar');
        //add component options
        $toolbar->appendButton('Link', 'options', 'Options', 'index.php?option=com_config&view=component&component=com_simplequiz');
        

        //get id from url
        $id = $_GET['id'];

        Log::add("i found this id: $id", Log::INFO, 'com_simplequiz');


        //get quiz from the model
        $model = $this->getModel();

        //if view is default
        if($this->getLayout() == 'default')
        {
        //set this item to that quiz
            $this->item = $model->getQuiz($id);
            return parent::display($tpl);
        }

        //if view is edit
        if($this->getLayout() == 'edit')
        {
            //we need a toolbar
            $this->addEditToolbar();

            //if id is not 0
            if($id != 0)
            {
                //get data for that quiz
                $data = $model->getQuiz($id);
                //get the form
                $this->form = $model->getForm($data, false);
                return parent::display($tpl);
            }
            else{
                //get the form
                $this->form = $model->getForm(null, false);
                //hide the randomize_mchoice field by default
                return parent::display($tpl);
            }
        }

    }

    protected function addEditToolbar(){
        
        //set controller to SimpleQuizController
        $controller = 'SimpleQuizController';

        $app = Factory::getApplication();
        $input = $app->input;
        $input->set('controller', $controller);
        //set title
        $title = Text::_('COM_SIMPLEQUIZ_SIMPLEQUIZ');

        //add title to toolbar
        ToolbarHelper::title($title, 'simplequiz');


        $toolbar = Toolbar::getInstance('toolbar');
        $toolbar->appendButton('Link', 'cancel', 'Cancel', 'index.php?option=com_simplequiz&view=simplequizzes');
        ToolbarHelper::save('SimpleQuiz.saveclose');
        ToolbarHelper::apply('SimpleQuiz.save');
    }

}