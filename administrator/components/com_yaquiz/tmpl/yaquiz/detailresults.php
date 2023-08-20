<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
 * 
 * This layout is for a single user attempt results along with any feedback, correct/incorrect, etc.
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\View\Yaquiz;


defined ( '_JEXEC' ) or die;

use KevinsGuides\Component\Yaquiz\Administrator\Model\YaquizModel;
$model = new YaquizModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Uri\Uri;

$quiz_id = 0;
$result_id = 0;

if(isset($_GET['id'])){
    $quiz_id = $_GET['id'];
}
if(isset($_GET['result_id'])){
    $result_id = $_GET['result_id'];
}

$their_results = $model->getIndividualAttemptResult($result_id);
$quiz = $model->getQuiz($quiz_id);

$quiz_taker = Factory::getUser($their_results->user_id);
$quiz_taker_name = $quiz_taker->name;
$quiz_taker_username = $quiz_taker->username;

?>
<div class="container">
<div class="card">
    <h1 class="card-header">
    <?php echo Text::_('COM_YAQUIZ_RESULTS_FULLRESULTS');?>
    </h1>
    <div class="card-body">

    <div class="progress">
        <div class="progress-bar <?php 
        print ($their_results->passed==1) ? "bg-success" : "bg-danger";
        ?>" role="progressbar" style="width: <?php echo $their_results->score; ?>%;" aria-valuenow="<?php echo $their_results->score; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $their_results->score; ?>%</div>
    </div>

    <table class="table table-striped">
            <thead>
                <td><?php echo Text::_('COM_YAQUIZ_PROPERTY');?></td>
                <td><?php echo Text::_('COM_YAQUIZ_VALUE');?></td>
            </thead>
            <tr>
                <td><?php echo Text::_('COM_YAQUIZ_QUIZID');?></td>
                <td><?php echo $quiz->id; ?></td>
            </tr>
            <tr>
                <td><?php echo Text::_('COM_YAQUIZ_QUIZTITLE');?></td>
                <td><?php echo $quiz->title; ?></td>
            </tr>
            <tr>
                <td><?php echo Text::_('JUSERNAME');?></td>
                <td><a href="index.php?option=com_users&task=user.edit&id=<?php echo $quiz_taker->id; ?>" target="_blank"><?php echo $quiz_taker->username; ?></a></td>
            </tr>
            <tr>
                <td><?php echo Text::_('COM_YAQUIZ_FULLNAME');?></td>
                <td><?php echo $quiz_taker_name; ?></td>
            </tr>
            <?php if (isset($their_results->start_time)) : ?>
            <tr>
                <td><?php echo Text::_('COM_YAQUIZ_STARTTIME');?></td>
                <td><?php echo $their_results->start_time; ?></td>
            </tr>
            <?php //calculate duration
                $start_time = strtotime($their_results->start_time);
                $end_time = strtotime($their_results->submitted);
                $duration = $end_time - $start_time;
                $duration = gmdate("H:i:s", $duration);
            ?>
            <tr>
                <td><?php echo Text::_('COM_YAQUIZ_DURATION');?></td>
                <td><?php echo $duration; ?></td>
            </tr>

            <?php endif; ?>
            <tr>
                <td><?php echo Text::_('COM_YAQUIZ_SUBMITTIME');?></td>
                <td><?php echo $their_results->submitted; ?></td>
            </tr>
            <tr>
                <td><?php echo Text::_('COM_YAQUIZ_GRADE_POINTS_OVER_TOTAL');?></td>
                <td><?php echo $their_results->points.'/'.$their_results->total_points; ?></td>
            </tr>
            <tr>
                <td><?php echo Text::_('COM_YAQUIZ_SCORE');?></td>
                <td><?php echo $their_results->score; ?>%</td>
            </tr>
            <tr>
                <td><?php echo Text::_('COM_YAQUIZ_PASSFAIL');?></td>
                <td><?php
                    if($their_results->passed){
                        echo Text::_('COM_YAQUIZ_PASSED');
                    }
                    else{
                        echo Text::_('COM_YAQUIZ_FAILED');
                    }
                ?></td>
            </tr>
        </table>
    </div>
</div>

<br/>

<h2><?php echo Text::_('COM_YAQUIZ_RESULTS_USERANSWERS');?></h2>
<?php

    $detail_feedback = json_decode($their_results->full_results);
    //for each question
    foreach($detail_feedback as $item):
        $question_title = $item->question->question;
        $question_params = $item->question->params;
        $question_type = $question_params->question_type;
    
    ?>
    <div class="card mb-1">
        <div class="card-header text-white w-100 d-block
        <?php
            print ($item->iscorrect == 1) ? "bg-success" : "bg-danger";
        ?>
        ">
            <span><?php echo $question_title; ?></span>
            
            <span class="float-end"> [<?php echo Text::_('COM_YAQUIZ_POINTVALUE');?>: <?php echo $question_params->points;?>]</span>
        </div>
        <div class="card-body">
        <p><?php echo Text::_('COM_YAQUIZ_QUESTIONTYPE') . ' - '.$question_type; ?></p>
        <hr>
        <p><?php echo Text::_('COM_YAQUIZ_DETAILS');?>:</p>
        <?php
            if($item->question->details){

                $item->question->details = str_replace('src="images', 'src="'.Uri::root().'images', $item->question->details);
                
                echo $item->question->details;
            }
            else{
                echo Text::_('COM_YAQUIZ_NOEXTRAPERMS');
            }
        ?>
        <hr>
        <p><?php echo Text::_('COM_YAQUIZ_POSSIBLE_ANSWERS');?></p>
        <?php 
        $possible_answers = $item->question->answers;

        //turn json string array into array
        $possible_answers = json_decode($possible_answers);
        foreach($possible_answers as $answer){
            echo $answer . '<br/>';
        }
        
        
        
        ?>
        <hr>
        <p><?php echo Text::_('COM_YAQUIZ_USER_SELECTED_ANSWER');?></p>
        <?php echo $item->useranswer; ?>
        

        </div>


    </div>

<?php endforeach; ?>


</div>


<br/>
<div class="text-center">
<a href="https://kevinsguides.com/tips" class="btn btn-success btn-lg"><?php echo Text::_('COM_YAQUIZ_SUPPORT_THIS_PROJECT');?></a>
</div>