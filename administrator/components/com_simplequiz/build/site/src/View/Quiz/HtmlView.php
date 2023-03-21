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

        $this->item = $this->get('Item');

        parent::display($tpl);
        
    }
}