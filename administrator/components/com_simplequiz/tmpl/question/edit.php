<?php
namespace KevinsGuides\Component\SimpleQuiz\Administrator\View\Question;
use Joomla\CMS\Language\Text;
use JHtml;
use Joomla\CMS\Log\Log;

defined('_JEXEC') or die;

//get WAM
$app = \JFactory::getApplication();

//get web asset manager
$wa = $app->getDocument()->getWebAssetManager();
$jsfile =  'administrator/components/com_simplequiz/src/Scripts/question-edit.js';
Log::add('try to load web asset'.$jsfile, Log::INFO, 'com_simplequiz');
$wa->registerAndUseScript('simplequiz-admin-questioneditscript', $jsfile);
$wa->registerAndUseStyle('simplequiz-admin-questioneditstyle', 'administrator/components/com_simplequiz/src/Style/question-edit.css');


//get items
$item = $this->item;

//if item is null, set default values
if($item == null || $item->id == 0){
    $item = new \stdClass();
    $item->question = 'New Question';
    $item->details = '';
    $item->answers = '';
    $item->correct = '';
    $item->params = '{"question_type":"multiple_choice"}';
    $item->id = '';
}

$question = $item->question;
$details = $item->details;
$answers = $item->answers;
//decode json answers
$answers = json_decode($answers);
$params = $item->params;
//decode json params
$params = json_decode($params);
$question_type = $params->question_type;
Log::add('question type is '.$question_type, Log::INFO, 'com_simplequiz');

$correct_answer = $item->correct;

//this form is the question.xml form
// $form = $this->form;
//get this form from model
$form = $this->getModel()->getForm($item, false);

function load_mchoice_editor($answers, $correct_answer){
    $html = '';
    $html .= '<div class="mchoice-editor">';
    $html .= '<div class="mchoice-answers">';
    $html .= '<h3>Answers</h3>';
    $html .= '<ul id="mchoice-answer-list">';
    $i = 0;
    foreach($answers as $answer){
        $correct = '';
        if($i == (int)$correct_answer){
            $correct = 'correct';
        }
        $html .= '<li data-ansid="'.$i.'" class="mchoice-answer '.$correct.'">';
        $html .= '<button class="btn btn-danger mchoice-delete-btn">Delete</button>';
        $html .= '<button class="btn btn-success mchoice-correct-btn">Correct</button>';
        $html .= '<input type="text" name="jform[answers][]" value="'.$answer.'">';
        $html .= '</li>';
        $i++;
    }
    $html .= '</ul>';
    $html .= '</div>';
    $html .= '<div class="mchoice-add-answer">';
    $html .= '<button type="button" class="btn btn-success" id="mchoice-add-btn">Add Answer</button>';
    $html .= '</div>';
    $html .= '</div>';
    return $html;
}


// $correct_answer is either 1 true or 0 false
function load_truefalse($correct_answer = 1){
    $html = '<div class="control-group">
                <div class="control-label">
                <h3>Select Correct Answer</h3>
                </div>
                <div class="controls">
                <div class="truefalse-editor">
                <div class="form-check">
                <input class="form-check-input" type="radio" name="jform[correct]" id="radioTF1" value="1" '.($correct_answer==1?'checked':'').'>
                    <label class="form-check-label" for="radioTF1">
                    True
                    </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="jform[correct]" id="radioTF2" value="0" '.($correct_answer==0?'checked':'').'>
                <label class="form-check-label" for="radioTF2">
                False
                </label>
            </div>
            </div>
            </div>
            
            ';


    $html .= '</div>';
     
    return $html;

}

?>

<h1>Question Editor</h1>

<form id="adminForm" action="index.php?option=com_simplequiz&task=question.edit" method="post">

    <!-- load the question fieldset -->
    <?php echo $form->renderFieldset('question'); ?>
    <?php if ($item->id != '' && $item->id != 0){

         if ($question_type == 'multiple_choice'){
            echo load_mchoice_editor($answers, $correct_answer); 
         }
         if ($question_type == 'true_false'){
            echo load_truefalse($correct_answer);
         }
    }
    else{
        echo '<p>You must save the question and lock its type before answer options can be used</p>';
        echo '<p>Note that you cannot change the question type after saving!!!</p>';
    }
    
 ?>
<input name="task" type="hidden">
    <?php JHtml::_('form.token'); ?>
</form>

<div style="display:none">
        <li id="mchoice-answer-template" class="mchoice-answer">
        <button class="btn btn-danger mchoice-delete-btn">Delete</button>
        <button class="btn btn-success mchoice-correct-btn">Correct</button>
            <input type="text" name="jform[answers][]" value="">
        </li>
    </div>


<div id="simplequiz-toast" role="alert" aria-live="assertive" aria-atomic="true">
  <div class="toast-header">
    <button type="button" id="simplequiz-toast-close" class="btn-close simplequiz-toast-close" aria-label="Close"></button>
  </div>
  <div class="toast-body">
    <p id="simplequiz-toast-message"></p>
  </div>
</div>