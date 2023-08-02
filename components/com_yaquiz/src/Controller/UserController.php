<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

namespace KevinsGuides\Component\Yaquiz\Site\Controller;

defined('_JEXEC') or die;
//require composer autoloader
require_once JPATH_ROOT . '/components/com_yaquiz/vendor/autoload.php';
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\Factory;
use KevinsGuides\Component\Yaquiz\Site\Model\UserModel;
use KevinsGuides\Component\Yaquiz\Site\Model\QuizModel;
use Dompdf\Dompdf;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Component\ComponentHelper;


class UserController extends BaseController
{

    // Force this view to be component-only
    //index.php?option=com_yaquiz&task=User.generateQuizCert&format=raw
    public function generateQuizCert()
    {

        $app = Factory::getApplication();
        $user = $app->getIdentity();
        $result_id= $app->input->get('result_id', 0, 'INT');
        $user_id = $user->id;

        $model = new UserModel;
        $result = $model->getIndividualResult($result_id);


        if ($result->passed != 1){
            $app->enqueueMessage(Text::_('COM_YAQUIZ_ERROR_NOT_PASSED').'result is '.print_r($result), 'error');
            $app->redirect('index.php?option=com_yaquiz&view=results');
        }

        $quiz_model = new QuizModel;
        $quiz = $quiz_model->getItem($result->quiz_id);

        $submitdate_short=date('Y-m-d', strtotime($result->submitted));
        $submitdate_time = date('g:i a', strtotime($result->submitted));

        $pdf_filename = $quiz->title . '_' . $user->username . '_' . $submitdate_short . '.pdf';

        $quiz_params = $quiz_model->getQuizParams($result->quiz_id);
        $comp_params = ComponentHelper::getParams('com_yaquiz');

        //figure out what cert file to use
        if(isset($quiz_params->certificate_file)){
            $certificate_file = $quiz_params->certificate_file;
        }
        else{
            $certificate_file = "global";
        }
        if($certificate_file == "global"){
            $certificate_file = $comp_params->get('certificate_file', 'default.html');
        }

        //get html for pdf
        //load from html file at components/com_yaquiz/certificates/default.html
        $html_to_pdf = file_get_contents(JPATH_ROOT . '/components/com_yaquiz/certificates/'.$certificate_file);

        //replace placeholders with actual values
        $html_to_pdf = str_replace('QUIZ_NAME', $quiz->title, $html_to_pdf);
        $html_to_pdf = str_replace('USER_FULLNAME', $user->name, $html_to_pdf);
        $html_to_pdf = str_replace('QUIZ_SCORE', $result->points, $html_to_pdf);
        $html_to_pdf = str_replace('QUIZ_TOTAL', $result->total_points, $html_to_pdf);
        $html_to_pdf = str_replace('QUIZ_TIME', $submitdate_time, $html_to_pdf);
        $html_to_pdf = str_replace('QUIZ_DATE', $submitdate_short, $html_to_pdf);
        $site_name = Factory::getConfig()->get('sitename');
        $html_to_pdf = str_replace('QUIZ_COPYRIGHT', date('Y') .'  '. $site_name, $html_to_pdf);

        //create an 8 digit hash of the user id and result id
        $cert_code = substr(md5($user_id . $result_id), 0, 8);

        $html_to_pdf = str_replace('CERT_CODE', $cert_code, $html_to_pdf);



        // instantiate and use the dompdf class
        $dompdf = new Dompdf();
        $dompdf->loadHtml($html_to_pdf);

        // (Optional) Setup the paper size and orientation
        $dompdf->setPaper('A4', 'landscape');

        // Render the HTML as PDF
        $dompdf->render();

        // Output the generated PDF to Browser
        $dompdf->stream($pdf_filename);
    }

}