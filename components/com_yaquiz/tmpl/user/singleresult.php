<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

namespace KevinsGuides\Component\Yaquiz\Site\View\User;
use Joomla\CMS\Log\Log;
use KevinsGuides\Component\Yaquiz\Site\Helper\QuestionBuilderHelper;
use KevinsGuides\Component\Yaquiz\Site\Model\QuizModel;
use Joomla\CMS\Language\Text;

defined('_JEXEC') or die;

$model = $this->getModel();
$dbresults = $model->getIndividualResult();
$quizModel = new QuizModel();
$quizTitle = $quizModel->getItem($dbresults->quiz_id)->title;

$app = \Joomla\CMS\Factory::getApplication();
$user = $app->getIdentity();

$qbHelper = new QuestionBuilderHelper();

if($dbresults->passed == 1){
    $passfail = "pass";
} else {
    $passfail = "fail";
}

//create a blank results object
$results = new \stdClass();
$results->correct = $dbresults->points;
$results->total = $dbresults->total_points;
Log::add('$results->correct: ' . $results->correct, Log::INFO, 'com_yaquiz');
Log::add('$results->total: ' . $results->total, Log::INFO, 'com_yaquiz');
$results->quiz_id = $dbresults->quiz_id;
$results->questions = json_decode($dbresults->full_results);

//create an empty array
$resultsquestions = array();
foreach($results->questions as $question){
    //each question is an object, we need to turn its properties into an array
    $newquestion = array();
    foreach($question as $key => $value){
        $newquestion[$key] = $value;
    }
    //add the question to the array
    $resultsquestions[] = $newquestion;
}

$results->questions = $resultsquestions;

$results->passfail = $passfail;


$final_feedback = $qbHelper->buildResultsArea($dbresults->quiz_id, $results);

$format_submitted_date = date('F j, Y, g:i a', strtotime($dbresults->submitted));

$attempt_count = $quizModel->getAttemptCount($dbresults->quiz_id, $user->id);

$quiz_params = $quizModel->getQuizParams($dbresults->quiz_id);

$max_attempts = $quiz_params->max_attempts;

$quiz_certificate = $quiz_params->quiz_certificate;

$remaining_attempts = $max_attempts - $attempt_count;

if($max_attempts == 0){
    $remaining_attempts = Text::_('COM_YAQ_UNLIMITED_ATTEMPTS');
}
else if($remaining_attempts == 0){
    $remaining_attempts = '<span class="bg-danger text-white p-1 rounded">'.Text::_('COM_YAQ_MAX_ATTEMPTS_REACHED').'</span>';
}
else if($remaining_attempts == 1){
    $remaining_attempts = Text::_('COM_YAQ_1ATTEMPT_LEFT');
}
else {
    $remaining_attempts = Text::sprintf('COM_YAQ_ATTEMPTS_REMAINING', $remaining_attempts);
}


?>

<div class="card mb-2">
<span class="card-header fs-2"><?php echo (Text::_('COM_YAQ_QUIZ_RESULT_HISTORY').$quizTitle); ?></span>
<div class="card-body">
    <p><?php echo Text::sprintf('COM_YAQ_QUIZ_SAVED_RESULT_FOR_USERNAME', $user->name); ?></p>
<p><?php echo (Text::_('COM_YAQ_ORIGINAL_SUBMIT').$format_submitted_date); ?></p>
<p><?php echo Text::sprintf('COM_YAQ_ATTEMPT_COUNT', $attempt_count); ?></p>
<p><?php echo $remaining_attempts; ?></p>

<?php if ($quiz_certificate != 'none' && $passfail == 'pass') : ?>
    <p><a href="index.php?option=com_yaquiz&task=User.generateQuizCert&format=raw&quiz_id=<?php echo $dbresults->quiz_id; ?>&result_id=<?php echo $dbresults->id; ?>" class="btn btn-success btn-lg"><i class="fas fa-certificate"></i> <?php echo Text::_('COM_YAQ_VIEW_CERTIFICATE');?></a></p>
<?php endif; ?>

</div>
<div class="card-footer">
    <a href="index.php?option=com_yaquiz&view=user" class="btn btn-primary btn-sm"><i class="fas fa-arrow-circle-left"></i> <?php echo Text::_('COM_YAQ_RETURN');?></a>
</div>
</div>
<?php echo $final_feedback; ?>

