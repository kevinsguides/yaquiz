<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\View\Yaquiz;


defined ( '_JEXEC' ) or die;
use KevinsGuides\Component\Yaquiz\Administrator\Model\YaquizModel;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;

$app = Factory::getApplication();

//permissions
$user = $app->getIdentity();
$canViewResults = $user->authorise('yaquiz.viewresults', 'com_yaquiz');

$quiz_id = 0;

if(!$canViewResults){
    $app->enqueueMessage("You do not have permission to view quiz results.", "error");
    return;
}

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
            <th>RID</th>
            <th>Username [UID]</th>
            <th>Pass/Fail</th>
            <th>Score</th>
            <th>Submitted On</th>
            <th>More Details</th>
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
                <?php echo $result->id; ?>
            </td>
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
            <td>
                <?php 
                
                    if($result->full_results){
                        echo ('<a href="index.php?option=com_yaquiz&view=yaquiz&layout=detailresults&id='.$quiz_id.'&result_id='.$result->id.'">View</a>');
                    }
                    else{
                        echo "N/A";
                    }
                ?>
                
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>