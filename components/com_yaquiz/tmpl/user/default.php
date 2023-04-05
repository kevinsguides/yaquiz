<?php
namespace KevinsGuides\Component\Yaquiz\Site\View\User;
use KevinsGuides\Component\Yaquiz\Site\Model\QuizModel;
//render user quiz profile
// For now lets just have this link to all results...

defined('_JEXEC') or die;

$model = $this->getModel();
$quizModel = new QuizModel();
$results = $model->getUserResults();

?>

<h1>Your Results</h1>

<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th scope="col">Quiz Name</th>
            <th scope="col">Passed</th>
            <th scope="col">Your Score</th>
            <th scope="col">Submitted</th>
            <th scope="col">More Details...</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($results as $result) : 
            $quizTitle = $quizModel->getItem($result->quiz_id)->title;
            if($result->passed == 1){
                $result->passed = "Yes";
            } else {
                $result->passed = "No";
            }

            $formatted_score = $result->points . "/" . $result->total_points . " (" . $result->score . "%)";

            //format $result->submitted in human readable format
            $result->submitted = date('F j, Y, g:i a', strtotime($result->submitted));

            $resultsLink = 'Not Available';
            if($result->full_results != ''){
                $resultsLink = '<a href="meh">View Results</a>';
            }
            
            ?>
            <tr>
                <td><strong><?php echo $quizTitle; ?></strong></td>
                <td><?php echo $result->passed; ?></td>
                <td><?php echo $formatted_score; ?></td>
                <td><?php echo $result->submitted; ?></td>
                <td><?php echo $resultsLink; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>


