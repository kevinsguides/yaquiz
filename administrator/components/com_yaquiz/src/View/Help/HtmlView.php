<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\View\Help;


defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
class HtmlView extends BaseHtmlView
{
    public function display($tpl = null)
    {
        ToolbarHelper::title(Text::_('COM_YAQUIZ_PAGETITLE_HELP'));

        $app = Factory::getApplication();
        if($app->getIdentity()->authorise('core.admin', 'com_yaquiz')){
            ToolbarHelper::preferences('com_yaquiz');
        }

        return parent::display($tpl);


    }
}