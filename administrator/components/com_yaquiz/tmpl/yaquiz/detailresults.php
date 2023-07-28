<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\View\Yaquiz;


defined ( '_JEXEC' ) or die;

use KevinsGuides\Component\Yaquiz\Administrator\Model\YaquizModel;
$model = new YaquizModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$quiz_id = 0;
$result_id = 0;

if(isset($_GET['id'])){
    $quiz_id = $_GET['id'];
}
if(isset($_GET['result_id'])){
    $result_id = $_GET['result_id'];
}

$their_results = $model->getIndividualAttemptResult($quiz_id, $result_id);
$quiz = $model->getQuiz($quiz_id);

$quiz_taker = Factory::getUser($their_results->user_id);
$quiz_taker_name = $quiz_taker->name;
$quiz_taker_username = $quiz_taker->username;

?>
<div class="container">
<div class="card">
    <h1 class="card-header">
        Full Attempt Results
    </h1>
    <div class="card-body">

    <div class="progress">
        <div class="progress-bar <?php 
        print ($their_results->passed==1) ? "bg-success" : "bg-danger";
        ?>" role="progressbar" style="width: <?php echo $their_results->score; ?>%;" aria-valuenow="<?php echo $their_results->score; ?>" aria-valuemin="0" aria-valuemax="100"><?php echo $their_results->score; ?>%</div>
    </div>

    <table class="table table-striped">
            <thead>
                <td>Property</td>
                <td>Value</td>
            </thead>
            <tr>
                <td>Quiz ID</td>
                <td><?php echo $quiz->id; ?></td>
            </tr>
            <tr>
                <td>Quiz Title</td>
                <td><?php echo $quiz->title; ?></td>
            </tr>
            <tr>
                <td>Username</td>
                <td><a href="index.php?option=com_users&task=user.edit&id=<?php echo $quiz_taker->id; ?>" target="_blank"><?php echo $quiz_taker->username; ?></a></td>
            </tr>
            <tr>
                <td>Full Name</td>
                <td><?php echo $quiz_taker_name; ?></td>
            </tr>
            <tr>
                <td>Submit Time</td>
                <td><?php echo $their_results->submitted; ?></td>
            </tr>
            <tr>
                <td>Grade (points/total)</td>
                <td><?php echo $their_results->points.'/'.$their_results->total_points; ?></td>
            </tr>
            <tr>
                <td>Score</td>
                <td><?php echo $their_results->score; ?>%</td>
            </tr>
            <tr>
                <td>Pass/Fail</td>
                <td><?php
                    if($their_results->passed){
                        echo "Passed";
                    }
                    else{
                        echo "Failed";
                    }
                ?></td>
            </tr>
        </table>
    </div>
</div>

<br/>

<h2>User Answers</h2>
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
            
            <span class="float-end"> [Point Value: <?php echo $question_params->points;?>]</span>
        </div>
        <div class="card-body">
        <p>Question Type: <?php echo $question_type; ?></p>
        <hr>
        <p>Details:</p>
        <?php
            if($item->question->details){
                echo $item->question->details;
            }
            else{
                echo "No extra question details were provided to user on quiz administration.";
            }
        ?>
        <hr>
        <p>Possible Answer(s)</p>
        <?php echo $item->question->answers;?>
        <hr>
        <p>User Selected Answer</p>
        <?php echo $item->useranswer; ?>
        

        </div>


    </div>

<?php endforeach; ?>


</div>


<br/>
<a href="https://kevinsguides.com/tips" class="btn btn-success btn-lg"><?php echo Text::_('COM_YAQUIZ_SUPPORT_THIS_PROJECT');?></a>
