<?php

namespace KevinsGuides\Component\SimpleQuiz\Administrator\View\SimpleQuizzes;
use JLoader;
use KevinsGuides\Component\SimpleQuiz\Administrator\Helper\SimpleQuizHelper;
defined('_JEXEC') or die;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Toolbar\Toolbar;
use Joomla\CMS\Toolbar\ToolbarFactoryInterface;
use Joomla\CMS\Toolbar\ToolbarHelper;



use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Log\Log;

class HtmlView extends BaseHtmlView
{
    public function display($tpl = null)
    {


        $toolbar = Toolbar::getInstance('toolbar');
        $toolbar->appendButton('Link', 'new', 'New Quiz', 'index.php?option=com_simplequiz&view=simplequiz&layout=edit');
        //add component options
        $toolbar->appendButton('Link', 'options', 'Options', 'index.php?option=com_config&view=component&component=com_simplequiz');
        
        return parent::display($tpl);
    }




}
