<?php
namespace KevinsGuides\Component\Yaquiz\Site\View\User;
use Joomla\CMS\Log\Log;
use KevinsGuides\Component\Yaquiz\Site\Helper\QuestionBuilderHelper;
use KevinsGuides\Component\Yaquiz\Site\Model\QuizModel;

defined('_JEXEC') or die;

$model = $this->getModel();
$dbresults = $model->getIndividualResult();
$quizModel = new QuizModel();
$quizTitle = $quizModel->getItem($dbresults->quiz_id)->title;

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

Log::add('$resultsquestions  look like this from singleresult: ' . print_r($resultsquestions, true), Log::INFO, 'com_yaquiz');


$results->passfail = $passfail;


$final_feedback = $qbHelper->buildResultsArea($dbresults->quiz_id, $results);

?>

<h1><?php echo $quizTitle; ?></h1>
<h2>Your Results</h2>
<?php echo $final_feedback; ?>

