<?php 
namespace KevinsGuides\Component\Yaquiz\Site\View\User;
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
        //user must be logged in
        $app = Factory::getApplication();
        $user = $app->getIdentity();
        if ($user->guest) {
            $app = Factory::getApplication();
            $app->enqueueMessage(Text::_('COM_YAQUIZ_LOGIN_REQUIRED'), 'error');
            $app->redirect('index.php?option=com_users&view=login');
        }

        //user_results_page_enabled must be enabled
        $globalParams = Factory::getApplication()->getParams('com_yaquiz');
        if($globalParams->get('user_results_page_enabled',"1")=="0"){
            $app->enqueueMessage(Text::_('COM_YAQUIZ_USER_RESULTS_PAGE_DISABLED'), 'error');
            $app->redirect('index.php');
        }

        parent::display($tpl);

    }
}