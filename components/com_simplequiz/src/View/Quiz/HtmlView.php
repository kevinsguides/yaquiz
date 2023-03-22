<?php 
namespace KevinsGuides\Component\SimpleQuiz\Site\View\Quiz;
use Joomla\CMS\Log\Log;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;

use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;

class HtmlView extends BaseHtmlView
{
    public function display($tpl = null)
    {

        $app = Factory::getApplication();
        $active = $app->getMenu()->getActive();
        $qid = '1';
        if($qid === '0'){
            $this->quiz_id = $qid_manual;
        }
        else{
            $this->quiz_id = $qid;
        }

        $model = $this->getModel();
        $this->item = $model->getItem($this->quiz_id);
    
        parent::display($tpl);
        
    }
}