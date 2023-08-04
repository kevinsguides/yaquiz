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

        $app = Factory::getApplication();

        
        $toolbar = Factory::getContainer()->get(ToolbarFactoryInterface::class)->createToolbar('toolbar');
        ToolbarHelper::title(Text::_('COM_YAQUIZ_PAGETITLE_QUIZLIST'), 'yaquiz');
        ToolbarHelper::link('index.php?option=com_yaquiz&view=yaquiz&layout=edit', 'COM_YAQUIZ_NEWQUIZ', 'new', 'COM_YAQUIZ_NEWQUIZ', false);
        ToolbarHelper::link('index.php?option=com_categories&extension=com_yaquiz', 'JCATEGORIES', 'folder', 'JCATEGORIES', false);
        ToolbarHelper::custom('Questions.display', 'checkbox', 'checkbox', 'COM_YAQUIZ_QUESTION_MGR', false);
        if($app->getIdentity()->authorise('core.admin', 'com_yaquiz')){
            ToolbarHelper::preferences('com_yaquiz');
        }

        return parent::display($tpl);
    }




}
