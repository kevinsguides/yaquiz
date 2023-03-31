<?php
namespace KevinsGuides\Component\Yaquiz\Site\View\Quiz;
defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use KevinsGuides\Component\Yaquiz\Site\Helper\QuestionBuilderHelper;
use KevinsGuides\Component\Yaquiz\Site\Model\QuizModel;



$app = Factory::getApplication();
$wam = $app->getDocument()->getWebAssetManager();
$wam->registerAndUseScript('com_yaquiz.jsquiz', 'components/com_yaquiz/js/jsquiz.js', [], ['defer' => true]);
$wam->registerAndUseStyle('com_yaquiz.quiz', '/components/com_yaquiz/src/Style/quiz.css');
$globalParams = $app->getParams('com_yaquiz');

if ($globalParams->get('load_mathjax') === '1') {
    $wam->registerAndUseScript('com_yaquiz.mathjax', 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js');
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

<div class="card" data-jsquiz-page="0">
    <div class="card-header">
        <h3>
            <?php echo $quiz->title; ?>
        </h3>
    </div>
    <div class="card-body">

        <?php echo $quiz->description; ?>

    </div>
    <div class="card-footer">
        <a class="btn btn-primary" id="jsquiz-btn-start">Start Quiz</a>
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



    ?>
    <div class="card jsquiz-questioncard" data-jsquiz-page="<?php echo $i; ?>" data-qtype="<?php echo $question->params->question_type;?>" data-pointvalue="<?php echo $points;?>">
        <div class="card-header">
            <h3>
                <?php echo $question->question; ?>
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
                        <label class="form-check-label mchoice btn btn-dark text-start" for="answer<?php echo $i.'-T'; ?>">True</label>
                        <input class="d-none" type="radio" name="useranswer" id="answer<?php echo $i.'-F'; ?>" value="0">
                        <label class="form-check-label mchoice btn btn-dark text-start" for="answer<?php echo $i.'-F'; ?>">False</label>                        
                <?php endif; ?>
        </div>
        <div class="card-footer">
            <span class="float-end">Points: <?php echo $points; ?></span>                    
            <?php if($i > 1):?>
                <a class="btn btn-primary" class="jsquiz-btn-prev">Previous</a>
            <?php endif; ?>
            <?php if($i == $questionCount): ?>
                <a class="btn btn-primary" id="jsquiz-btn-finish">Finish</a>
            <?php else : ?>
                <a class="btn btn-primary" class="jsquiz-btn-next">Next</a>
            <?php endif; ?>
            </div>
    </div>
<?php endforeach; ?>
<div class="card" id="jsquiz-results">
    <h3 class="card-header">Results</h3>
    <div class="card-body">
        <p>Your Score: <span id="jsquiz-score"></span></p>
        <p id="jsquiz-passfail-feedback"></p>
    </div>
</div>


