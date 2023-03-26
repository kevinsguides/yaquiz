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

        //get params from the menu item
        $this->params = $active->getParams();
        $this->quiz_id = $this->params->get('quiz_id');
        $model = $this->getModel();
        $this->item = $model->getItem($this->quiz_id);

        $quizAccess = $this->item->access;
        $user = Factory::getUser();
        $userGroups = $user->getAuthorisedViewLevels();
        if(!in_array($quizAccess, $userGroups)){
            $app->enqueueMessage(Text::_('COM_SIMPLEQUIZ_VIEW_QUIZ_DENIED'), 'error');
            $app->redirect('index.php');
        }
        
    
        parent::display($tpl);
        
    }
}