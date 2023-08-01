<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\Controller;

defined ( '_JEXEC' ) or die ();
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Input\Input;
use Joomla\CMS\MVC\Controller\BaseController;

class CertificatesController extends BaseController
{

    public function __construct($config = [], MVCFactoryInterface $factory = null, ?CMSApplication $app = null, ?Input $input = null)
    {
        parent::__construct($config, $factory, $app, $input);
        $this->registerTask('edit', 'edit');
        $this->registerTask('cancel', 'cancel');
    }

    public function saveTemplate(){

        $app = Factory::getApplication();
        $input = $app->input;
        $user = $app->getIdentity();

        if(!$user->authorise('core.admin', 'com_yaquiz')){
            $app->enqueueMessage('You do not have permission to edit certificates', 'error');
            $app->redirect('index.php?option=com_yaquiz&view=certificates');
        }
        

    }

    public function cancel(){
        //return to certificates list
        $app = Factory::getApplication();
        $app->redirect('index.php?option=com_yaquiz&view=certificates');
    }

}