<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

namespace KevinsGuides\Component\Yaquiz\Site\View\Quiz;
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use KevinsGuides\Component\Yaquiz\Site\Helper\QuestionBuilderHelper;
use KevinsGuides\Component\Yaquiz\Site\Model\QuizModel;



$app = Factory::getApplication();
$wam = $app->getDocument()->getWebAssetManager();
$wam->registerAndUseScript('com_yaquiz.jsquiz', 'components/com_yaquiz/js/jsquiz.js', [], ['defer' => true]);
$wam->registerAndUseStyle('com_yaquiz.quiz', 'components/com_yaquiz/src/Style/quiz.css');
$gConfig = $app->getParams('com_yaquiz');

if ($gConfig->get('get_mathjax') === '1') {
    $wam->registerAndUseScript('com_yaquiz.mathjax', 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js', [], ['defer' => true]);
}
if ($gConfig->get('get_mathjax') === '2') {
    Log::add('Loading local mathjax', Log::INFO, 'com_yaquiz');
    $wam->registerAndUseScript('com_yaquiz.mathjaxlocal', 'components/com_yaquiz/js/mathjax/es5/tex-svg.js', [], ['defer' => true]);
}

$theme = $gConfig->get('theme','default');
$stylefile = '/components/com_yaquiz/tmpl/' . $theme . '/style.css';
//if file exists
if (file_exists(JPATH_ROOT . $stylefile)) {
    $wam->registerAndUseStyle('com_yaquiz.quizstyle', $stylefile);
}



$qbHelper = new QuestionBuilderHelper();


//if $this-> item is already set
if (isset($this->item)) {
    $quiz = $this->item;
} else {
    $quiz = $this->get('Item');
}

//get quiz params
$quiz_params = $quiz->params;
//decode
$quiz_params = json_decode($quiz_params);

$uses_point_system = false;

if($quiz_params->quiz_use_points === "1"){
    $uses_point_system = true;
}

?>

<input id="uses_point_system" type="hidden" value="<?php echo ($uses_point_system==true)?'true':'false';?>">
<input id="quiz_showfeedback" type="hidden" value="<?php echo ($quiz_params->quiz_showfeedback==1)?'true':'false';?>">
<input id="quiz_feedback_showcorrect" type="hidden" value="<?php echo ($quiz_params->quiz_feedback_showcorrect==1)?'true':'false';?>">
<input id="passing_score" type="hidden" value="<?php echo $quiz_params->passing_score;?>">
<input type="hidden" id="shortans_ignore_trailing" value="<?php echo $gConfig->get('shortans_ignore_trailing','1'); ?>" />
<div class="container p-2">

<div id="jsquiz-intro" class="card" data-jsquiz-page="0">
    <div class="card-header">
        <h3>
            <?php echo $quiz->title; ?>
        </h3>
    </div>
    <div class="card-body">

        <?php echo $quiz->description; ?>

    </div>
    <div class="card-footer">
        <a class="btn btn-primary" id="jsquiz-btn-start"><?php echo \JText::_('COM_YAQ_START_QUIZ');?></a>
    </div>
</div>


<div class="card d-none mb-2" id="jsquiz-results">
    <h3 class="card-header"><?php echo \JText::_('COM_YAQ_RESULTS');?></h3>
    <div class="card-body">
        <p>Your Score: <span id="jsquiz-score"></span></p>
        <p id="jsquiz-passfail-feedback"></p>
        <div id="jsquiz-feedback-passed" class="bg-light text-success  p-2 rounded d-none"><i class="fas fa-clipboard-check"></i> <?php echo $gConfig->get('lang_pass');?></div>
        <div id="jsquiz-feedback-failed" class="bg-light text-danger p-2 rounded d-none"><i class="fas fa-sad-cry"></i> <?php echo $gConfig->get('lang_fail');?></div>
    </div>
</div>


<?php
$model = new QuizModel();
//loop through all questions
$questions = $model->getQuestions($quiz->id);
$questionCount = count($questions);

$i = 0;
foreach ($questions as $question):
    $i++;

    if($uses_point_system == true)
    {
        $points = $question->params->points;
    }
    else{
        $points = 1;
    }


    $numbering = "";
    if($quiz_params->quiz_question_numbering == "1"){
        $numbering = $i . ". ";
    }




    ?>
    <div class="card d-none jsquiz-questioncard mb-2" data-jsquiz-page="<?php echo $i; ?>" data-qtype="<?php echo $question->params->question_type;?>" data-pointvalue="<?php echo $points;?>" data-iscorrect="0">
        <div class="card-header">
            <h3>
                <?php echo $numbering.$question->question; ?> <i class="fas fa-question-circle float-end"></i>
            </h3>
        </div>
        <div class="card-body">
                <?php echo $question->details; ?>

                <?php if($question->params->question_type == 'multiple_choice'): 
                        //get the answers
                        $answers = $question->answers;
                        //decode
                        $answers = json_decode($answers);
                    ?>
                    <form data-correctans="<?php echo $question->correct;?>">
                        <?php 
                        $x = 0;
                        foreach ($answers as $answer):
                             ?>
                             <input class="d-none" type="radio" name="useranswer" id="answer<?php echo $i.'-'.$x; ?>" value="<?php echo $x; ?>">
                             <label class="form-check-label mchoice btn btn-dark text-start" for="answer<?php echo $i.'-'.$x; ?>"><?php echo $answer; ?></label>
                             <br/>
                        <?php $x++; endforeach;?>
                    </form>
                <?php endif;?>
                <?php if($question->params->question_type == 'fill_blank'):
                    //get the answers
                    $answers = $question->answers;
                    //escape json for html
                    $answers = htmlspecialchars($answers, ENT_QUOTES, 'UTF-8');
                    ?>
                    
                    <form id="test" data-correctans="<?php echo $answers;?>" data-casesense="<?php echo $question->params->case_sensitive; ?>">
                        <input type="text" name="useranswer" id="answer<?php echo $i; ?>" value="">
                    </form>

                <?php endif;?>
                <?php if($question->params->question_type == 'true_false'):?>
                    <form data-correctans="<?php echo $question->correct;?>">
                        <input class="d-none" type="radio" name="useranswer" id="answer<?php echo $i.'-T'; ?>" value="1">
                        <label class="form-check-label mchoice btn btn-dark text-start" for="answer<?php echo $i.'-T'; ?>"><?php echo \JText::_('COM_YAQ_TRUE');?></label>
                        <input class="d-none" type="radio" name="useranswer" id="answer<?php echo $i.'-F'; ?>" value="0">
                        <label class="form-check-label mchoice btn btn-dark text-start" for="answer<?php echo $i.'-F'; ?>"><?php echo \JText::_('COM_YAQ_FALSE');?></label>  
                    </form>                      
                <?php endif; ?>
                    <?php if($quiz_params->quiz_showfeedback==1):?>
                        <div class="jsquiz-question-feedback-correct d-none">
                            <?php 
                            //if there is any actual feedback to give
                            if($question->feedback_right != ''){
                                echo $question->feedback_right;
                            }
                            else{
                                echo \JText::_('COM_YAQ_CORRECTANS');
                            }
                            ?>
                        </div>
                        <div class="jsquiz-question-feedback-incorrect d-none">
                            <?php
                            //if there is any actual feedback to give
                            if($question->feedback_wrong != ''){
                                echo $question->feedback_wrong;
                            }
                            else{
                                echo \JText::_('COM_YAQ_INCORRECTANS');
                            }

                            //if we are showing the correct answer
                            if($quiz_params->quiz_feedback_showcorrect=='1'){
                                echo '<br/>'.$model->getCorrectAnswerText($question);
                            }
                            ?>


                            
                        </div>
                    <?php endif;?>

        </div>
        <div class="card-footer">
            <span class="float-end">Points: <?php echo $points; ?></span>                    
            <?php if($i > 1):?>
                <a class="btn btn-secondary jsquiz-btn-prev" ><?php  echo \JText::_('COM_YAQ_PREV');?></a>
            <?php endif; ?>
            <?php if($i == $questionCount): ?>
                <a class="btn btn-primary" id="jsquiz-btn-finish"><?php  echo \JText::_('COM_YAQ_FINISH');?></a>
            <?php else : ?>
                <a class="btn btn-primary jsquiz-btn-next"><?php  echo \JText::_('COM_YAQ_NEXT');?></a>
            <?php endif; ?>
            </div>
    </div>
<?php endforeach; ?>



            </div>