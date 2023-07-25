<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\View\Question;
use Joomla\CMS\Language\Text;
use JHtml;
use Joomla\CMS\Log\Log;

defined('_JEXEC') or die;

//get WAM
$app = \JFactory::getApplication();

//get web asset manager
$wa = $app->getDocument()->getWebAssetManager();
$jsfile =  'administrator/components/com_yaquiz/src/Scripts/question-edit.js';
Log::add('try to load web asset'.$jsfile, Log::INFO, 'com_yaquiz');
$wa->registerAndUseScript('yaquiz-admin-questioneditscript', $jsfile);
$wa->registerAndUseStyle('yaquiz-admin-questioneditstyle', 'administrator/components/com_yaquiz/src/Style/question-edit.css');


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
Log::add('question type is '.$question_type, Log::INFO, 'com_yaquiz');

$correct_answer = $item->correct;

if(isset($params->case_sensitive)){
    $case_sensitive = $params->case_sensitive;
}
else{
    $case_sensitive = 0;
}


//this form is the question.xml form
// $form = $this->form;
//get this form from model
$form = $this->getModel()->getForm($item, false);

function load_mchoice_editor($answers, $correct_answer){

    //if answers is null, set default values
    if($answers == null){
        $answers = array('Answer 1', 'Answer 2');
        $correct = 0;
    }

    $html = '';
    $html .= '<div class="mchoice-editor">';
    $html .= '<div class="mchoice-answers">';
    $html .= '<h3>'.Text::_('COM_YAQUIZ_ANSWERS').'</h3>';
    $html .= '<ul id="mchoice-answer-list">';
    $i = 0;
    foreach($answers as $answer){
        $correct = '';
        if($i == (int)$correct_answer){
            $correct = 'correct';
        }
        $html .= '<li data-ansid="'.$i.'" class="mchoice-answer '.$correct.'">';
        $html .= '<button class="btn btn-danger mchoice-delete-btn btn-sm float-end">'.Text::_('COM_YAQUIZ_DELETE').'</button>';
        $html .= '<button class="btn btn-success mchoice-correct-btn btn-sm"><i class="far fa-check-square"></i> '.Text::_('COM_YAQUIZ_MARK_CORRECT').'</button>';
        $html .= '<input class="mt-1" type="text" name="jform[answers][]" value="'.$answer.'">';
        $html .= '</li>';
        $i++;
    }
    $html .= '</ul>';
    $html .= '</div>';
    $html .= '<div class="mchoice-add-answer">';
    $html .= '<button type="button" class="btn btn-success" id="mchoice-add-btn">'.Text::_('COM_YAQUIZ_ADDANSWER').'</button>';
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
                    '.Text::_('COM_YAQUIZ_TRUE').'
                    </label>
            </div>
            <div class="form-check">
                <input class="form-check-input" type="radio" name="jform[correct]" id="radioTF2" value="0" '.($correct_answer==0?'checked':'').'>
                <label class="form-check-label" for="radioTF2">
                '.Text::_('COM_YAQUIZ_FALSE').'
                </label>
            </div>
            </div>
            </div>
            
            ';


    $html .= '</div>';
     
    return $html;

}


// all answers are considered correct
function load_fill_blank($answers, $case_sensitive = 0){

    if ($answers == null){
        $answers = array('answer'); //add blank answers
    }

    $html = '<div class="control-group">
                <div class="control-label">
                <h3>'.Text::_('COM_YAQUIZ_CASESENSITIVE').'</h3>
                <p>'.Text::_('COM_YAQUIZ_CASESENSITIVE_DESC').'</p>
                </div>
                <div class="controls">
                    <input type="checkbox" name="jform[case_sensitive]" value="1" '.($case_sensitive==1?'checked':'').'>
                </div>
            </div>
                ';


    $html .= '<div class="control-group">
                <div class="control-label">
                <h3>'.Text::_('COM_YAQUIZ_ANSWERS').'</h3>
                <p>'.Text::_('COM_YAQUIZ_ENTER_TEXT_ANSWERS_DESC').'</p>
                </div>
                <div class="controls">
                <div class="fill-blank-editor">
                <ul id="fill-blank-answer-list">';
    $i = 0;
    foreach($answers as $answer){
        $i++;
        $html .= '<li data-ansid="'.$i.'" class="fill-blank-answer">';
        $html .= '<button class="btn btn-sm float-end btn-danger fill-blank-delete-btn">'.Text::_('COM_YAQUIZ_DELETE').'</button>';
        $html .= '<input type="text" name="jform[answers][]" value="'.$answer.'">';
        $html .= '</li>';
    }
    $html .= '</ul>';
    $html .= '<button type="button" class="btn btn-success" id="fill-blank-add-btn">'.Text::_('COM_YAQUIZ_ADDANSWER').'</button>';
    $html .= '</div>
            </div>
            </div>
            ';
    return $html;

}


?>

<div class="container">
<h1><?php echo Text::_('COM_YAQUIZ_QUESTION_EDITOR'); ?></h1>

<form id="adminForm" action="index.php?option=com_yaquiz&task=question.edit" method="post">

    <!-- load the question fieldset -->
    <?php echo $form->renderFieldset('question'); ?>
    <?php if ($item->id != '' && $item->id != 0){

         if ($question_type == 'multiple_choice'){
            echo load_mchoice_editor($answers, $correct_answer); 
         }
         if ($question_type == 'true_false'){
            echo load_truefalse($correct_answer);
         }
         if ($question_type == 'fill_blank'){
            echo load_fill_blank($answers, $case_sensitive);
         }
    }
    else{
        echo Text::_('COM_YAQUIZ_SAVE_QUESTION_BEFORE_ANSWERS');
    }
    
 ?>
<input name="task" type="hidden">
    <?php JHtml::_('form.token'); ?>
</form>
<br/>
<div class="text-center">
------<span class="p-1 text-white rounded " style="background: #505a5e;" ><?PHP echo Text::_('COM_YAQUIZ_DONTFORGETSAVE') ;?></span>------
</div>

<div style="display:none">
        <li id="mchoice-answer-template" class="mchoice-answer">
        <button class="btn btn-danger mchoice-delete-btn btn-sm float-end"><?php echo Text::_('COM_YAQUIZ_DELETE');?></button>
        <button class="btn btn-success mchoice-correct-btn btn-sm"><i class="far fa-check-square"></i> <?php echo Text::_('COM_YAQUIZ_MARK_CORRECT');?></button>
            <input type="text" name="jform[answers][]" value="">
        </li>

        <li id="fill-blank-answer-template" class="fill-blank-answer">
        <button class="btn btn-danger fill-blank-delete-btn"><?php echo Text::_('COM_YAQUIZ_DELETE');?></button>
            <input type="text" name="jform[answers][]" value="">
        </li>

    </div>


<div id="yaquiz-toast" role="alert" aria-live="assertive" aria-atomic="true">
  <div class="toast-header">
    <button type="button" id="yaquiz-toast-close" class="btn-close yaquiz-toast-close" aria-label="Close"></button>
  </div>
  <div class="toast-body">
    <p id="yaquiz-toast-message"></p>
  </div>
</div>

</div>