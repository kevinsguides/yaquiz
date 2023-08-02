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
use KevinsGuides\Component\Yaquiz\Administrator\Helper\CertHelper;
use Joomla\CMS\Uri\Uri;
require_once JPATH_ROOT . '/components/com_yaquiz/vendor/autoload.php';
use Dompdf\Dompdf;
use Dompdf\Options;
class CertificatesController extends BaseController
{

    public function __construct($config = [], MVCFactoryInterface $factory = null, ?CMSApplication $app = null, ?Input $input = null)
    {
        parent::__construct($config, $factory, $app, $input);
        $this->registerTask('cancel', 'cancel');
        $this->registerTask('add', 'add');
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

    public function add(){
        //redirect to edit with certfile=default.html
        $app = Factory::getApplication();
        $app->redirect('index.php?option=com_yaquiz&view=certificates&layout=edit');
    }

    public function save(){

        $app = Factory::getApplication();
        $input = $app->input;
        $user = $app->getIdentity();

        if(!$user->authorise('core.admin', 'com_yaquiz')){
            $app->enqueueMessage('You do not have permission to edit certificates', 'error');
            $app->redirect('index.php?option=com_yaquiz&view=certificates');
        }

        $data = $_POST['jform'];
        $certfile = $data['certfile'];
        $certfile_start =  $_POST['certfile_start'];
        //if no title, set to certificate-date-time
        if(strlen($certfile) < 1){
            $certfile = 'certificate-'.date('Y-m-d-H-i-s');
        }

        $templatehtml = ($data['templatehtml']);

        $certHelper = new CertHelper();
        if($certHelper->saveCertHtml($certfile, $certfile_start, $templatehtml)){
            $app->enqueueMessage('Certificate saved'.print_r($data), 'message');
        }
        else{
            $app->enqueueMessage('Error saving certificate'.print_r($data), 'error');
        }

        $app->redirect('index.php?option=com_yaquiz&view=certificates');

    }

    public function delete(){

        $filename = $_GET['certfile'];

        $app = Factory::getApplication();
        $input = $app->input;
        $user = $app->getIdentity();

        if(!$user->authorise('core.admin', 'com_yaquiz')){
            $app->enqueueMessage('You do not have permission to edit certificates', 'error');
            $app->redirect('index.php?option=com_yaquiz&view=certificates');
        }

        $certHelper = new CertHelper();
        if($certHelper->deleteCertHtml($filename)){
            $app->enqueueMessage('Certificate deleted', 'message');
        }
        else{
            $app->enqueueMessage('Error deleting certificate', 'error');
        }

        $app->redirect('index.php?option=com_yaquiz&view=certificates');

    }

    public function getCertPreview()
    {




        $app = Factory::getApplication();
        $user = $app->getIdentity();
        if(!$user->authorise('core.view', 'com_yaquiz')){
            $app->enqueueMessage('You do not have permission to view previews', 'error');
            $app->redirect('index.php?option=com_yaquiz&view=certificates');
        }

        $certfile = $_GET['certfile'];

        $pdf_filename = $certfile . '.pdf';

        if(!isset($certfile)){
            $app->enqueueMessage('No certificate file specified', 'error');
            $app->redirect('index.php?option=com_yaquiz&view=certificates');
        }

        $certfile = $certfile . '.html';

        //check if cert file exists
        if(!file_exists(JPATH_ROOT . '/components/com_yaquiz/certificates/'.$certfile)){
            $app->enqueueMessage('Certificate file not found', 'error');
            $app->redirect('index.php?option=com_yaquiz&view=certificates');
        }


        //get html for pdf
        //load from html file at components/com_yaquiz/certificates/default.html
        $html_to_pdf = file_get_contents(JPATH_ROOT . '/components/com_yaquiz/certificates/'.$certfile);

        //replace placeholders with actual values
        $html_to_pdf = str_replace('QUIZ_NAME', "Sample Quiz", $html_to_pdf);
        $html_to_pdf = str_replace('USER_FULLNAME',"John Doe", $html_to_pdf);
        $html_to_pdf = str_replace('QUIZ_SCORE', "95", $html_to_pdf);
        $html_to_pdf = str_replace('QUIZ_TOTAL', "100", $html_to_pdf);
        $html_to_pdf = str_replace('QUIZ_TIME', "12:30", $html_to_pdf);
        $html_to_pdf = str_replace('QUIZ_DATE', "01-02-2023", $html_to_pdf);
        $html_to_pdf = str_replace('SITEROOT_URI/', Uri::root(), $html_to_pdf);
        $site_name = Factory::getConfig()->get('sitename');
        $html_to_pdf = str_replace('QUIZ_COPYRIGHT', date('Y') .'  '. $site_name, $html_to_pdf);

        //create an 8 digit hash of the user id and result id
        $cert_code = "ABCD1234";

        $html_to_pdf = str_replace('CERT_CODE', $cert_code, $html_to_pdf);



        // instantiate and use the dompdf class
        $options = new Options();
        $options->set('isRemoteEnabled', true);
        $dompdf = new Dompdf($options);
        $dompdf->loadHtml($html_to_pdf);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream($pdf_filename);


    }

}