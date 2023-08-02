<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\View\Certificates;


defined('_JEXEC') or die;
use Joomla\CMS\MVC\View\HtmlView as BaseHtmlView;
use Joomla\CMS\Toolbar\ToolbarHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
class HtmlView extends BaseHtmlView
{
    public function display($tpl = null)
    {
        
        ToolbarHelper::title(Text::_('COM_YAQUIZ_CERTIFICATES'));

        $app = Factory::getApplication();
        $certfile = $app->input->get('certfile', null, 'STRING');

        $user = Factory::getApplication()->getIdentity();


        //if layout edit
        if($this->getLayout() == 'edit'){
            $model = $this->getModel();
            $form = $model->getForm();
            ToolbarHelper::cancel('Certificates.cancel');
            if($certfile != 'default.html'){
            ToolbarHelper::save('Certificates.save');
            $app->getInput()->set('hidemainmenu', true);
            }
        }else{

            if ($user->authorise('core.admin', 'com_yaquiz')) {
                ToolbarHelper::addNew('Certificates.add');
                ToolbarHelper::custom('Certificates.rebuildVerifyCodes', 'refresh', '', 'COM_YAQUIZ_REBUILD_VERIFY_CODES', false);
                ToolbarHelper::preferences('com_yaquiz');
            }    
        }


        return parent::display($tpl);
    }
}