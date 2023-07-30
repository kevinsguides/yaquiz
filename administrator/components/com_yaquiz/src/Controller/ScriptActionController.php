<?php
namespace KevinsGuides\Component\Yaquiz\Administrator\Controller;

defined ( '_JEXEC' ) or die ();

use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Language\Text;

use Joomla\CMS\MVC\Controller\BaseController;
use KevinsGuides\Component\Yaquiz\Administrator\Model\YaquizModel;

class ScriptActionController extends BaseController {


    public function saveQuestionOrdering() {

        //user has edit permissions
        $user = Factory::getApplication()->getIdentity();
        if (!$user->authorise('core.edit', 'com_yaquiz')) {
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }

        $input = json_decode(file_get_contents('php://input'), true);

        $quiz_id = $input['quizId'];
        $question_ids = $input['questionIds'];

        $model = new YaquizModel();
        if($model->changeQuestionOrder($quiz_id, $question_ids)){
            $response = 'success';
            echo json_encode($response);
        }
        else{
            $response = 'fail';
            echo json_encode($response);
        }



    }


}