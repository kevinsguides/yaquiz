<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/



namespace KevinsGuides\Component\Yaquiz\Administrator\View\Yaquizzes;
use JLoader;
use KevinsGuides\Component\Yaquiz\Administrator\Helper\YaquizHelper;
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
        $toolbar->appendButton('Link', 'new', 'COM_YAQUIZ_NEWQUIZ', 'index.php?option=com_yaquiz&view=yaquiz&layout=edit');
        //add component options
        $toolbar->appendButton('Link', 'options', 'COM_YAQUIZ_COMPSETTINGS', 'index.php?option=com_config&view=component&component=com_yaquiz');
        //link to com_categories
        $toolbar->appendButton('Link', 'folder', 'JCATEGORIES', 'index.php?option=com_categories&extension=com_yaquiz');
        ToolbarHelper::custom('Questions.display', 'checkbox', 'checkbox', 'COM_YAQUIZ_QUESTION_MGR', false);

        ToolbarHelper::title(Text::_('COM_YAQUIZ_PAGETITLE_QUIZLIST'), 'yaquiz');
           


        return parent::display($tpl);
    }




}
