<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\View\Yaquiz;


defined ( '_JEXEC' ) or die;
use KevinsGuides\Component\Yaquiz\Administrator\Model\YaquizModel;
use Joomla\CMS\Factory;

$app = Factory::getApplication();

$quiz_id = 0;

if(isset($_GET['id'])){
    $quiz_id = $_GET['id'];
}

if($quiz_id == 0){
    $app->enqueueMessage("No quiz ID provided.", "error");
    return;
}

//get results from model
$model = new YaquizModel;
$results = $model->getAllSavedResults($quiz_id);
$result_count = count($results);



?>

<h1>Quiz Results: </h1>
<?php echo $result_count; ?> saved user results found for this quiz.<br><br>

<table class="table table-striped">
    <thead>
        <tr>
            <th>Username/ID</th>
            <th>Pass/Fail</th>
            <th>Score</th>
            <th>Submitted On</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($results as $result): ?>
        
            <?php 
                $userid = $result->user_id;
                $user = Factory::getApplication()->getIdentity($userid);
                $username = $user->username; 
            ?>

        <tr>
            <td>
                <?php echo $username; ?>
                [<?php echo $userid; ?>]

            </td>
            <td><?php 
                if($result->passed == 1){
                    echo "Passed";
                }
                else{
                    echo "Failed";
                }
            
            ?></td>
            <td><?php echo $result->score; ?></td>
            <td><?php echo $result->submitted; ?></td>
            <!-- <a href="index.php?option=com_yaquiz&view=yaquiz&layout=results&quiz_id=<?php //echo $quiz_id; ?>&result_id=<?php //echo $result->id; ?>">View</a> -->
            <td>Coming soon...</td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<p>Note: Viewing individual detailed results about each question users picked does not work yet.</p>
