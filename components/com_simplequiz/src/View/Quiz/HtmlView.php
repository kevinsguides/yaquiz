<?php 
namespace KevinsGuides\Component\SimpleQuiz\Site\View\Quiz;
use Joomla\CMS\Log\Log;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

//use language helper
use Joomla\CMS\Language\Text;


class HtmlView extends BaseHtmlView
{
    public function display($tpl = null)
    {

        $app = Factory::getApplication();
        $active = $app->getMenu()->getActive();
        $globalParams = Factory::getApplication()->getParams('com_simplequiz');
        $model = $this->getModel();
       

        //get params from the menu item
        $this->params = $active->getParams();
        $this->quiz_id = $this->params->get('quiz_id');
        
        $this->item = $model->getItem($this->quiz_id);

        //check if quiz is published
        if($this->item->published == 0 && ($app->getIdentity()->authorise('core.edit', 'com_simplequiz') != true)){
            $app->enqueueMessage(Text::_('COM_SIMPLEQUIZ_VIEW_QUIZ_NOT_PUBLISHED'), 'error');
            $app->redirect('index.php');
        }

        $quizparams = $model->getQuizParams($this->item->id);
        $quizAccess = $this->item->access;
        $user = Factory::getUser();
        $userGroups = $user->getAuthorisedViewLevels();
        if(!in_array($quizAccess, $userGroups)){
            $app->enqueueMessage(Text::_('COM_SIMPLEQUIZ_VIEW_QUIZ_DENIED'), 'error');
            $app->redirect('index.php');
        }


        $wam = $app->getDocument()->getWebAssetManager();
        //load the style.css file for the template being used for this quiz, if it exists
        //the file is in this component's tmpl folder

        $styleFile =  'components/com_simplequiz/tmpl/quiz/' . $globalParams->get('theme','default') . '/style.css';
        Log::add('style file: ' . $styleFile, Log::INFO, 'com_simplequiz');
        if(file_exists($styleFile)){
            Log::add('style file exists', Log::INFO, 'com_simplequiz');
            $wam->registerAndUseStyle('com_simplequiz.quizstyle', $styleFile);
        }


        //if we're not on the results layout

        if ($this->getLayout() != 'results'){
        //if quiz displaymode is default 
        if($quizparams->quiz_displaymode === 'individual'){
            $this->setLayout('quiztype_individual');
            $this->currPage = $app->input->get('page', 0);
        }
        else{
            $this->setLayout('default');
        }
        }



        
    
        parent::display($tpl);
        
    }
}