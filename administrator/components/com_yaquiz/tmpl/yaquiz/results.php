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

$filters = null;
$page = 1;
$yaqresultlimit = 25;

if(isset($_POST['filterusername'])){
    $filters = array(
        'filterusername' => $_POST['filterusername']
    );
}
if(isset($_GET['page'])){
    $page = $_GET['page'];
}
if(isset($_POST['yaqresultlimit'])){
    $yaqresultlimit = $_POST['yaqresultlimit'];
    $_SESSION['yaqresultlimit'] = $yaqresultlimit;
}else{
    if(isset($_SESSION['yaqresultlimit'])){
        $yaqresultlimit = $_SESSION['yaqresultlimit'];
    }
}


//get results from model
$model = new YaquizModel;
$result_count = $model->countTotalSavedResults($quiz_id, $filters);

//if count > limit, determine pagination
$pagecount = ceil($result_count / $yaqresultlimit);

//if resultcount > 1 and page is greater than pagecount, redirect to last page
if($result_count > 1 && $page > $pagecount){
    $page = 1;
}


$results = $model->getAllSavedResults($quiz_id, $filters, $page, $yaqresultlimit);

?>

<h1><?php   
    $quiz = $model->getQuiz($quiz_id);
    echo Text::_('COM_YAQUIZ_YAQUIZRESULTS').': '.$quiz->title; ?>
</h1>
<?php echo Text::sprintf('COM_YAQUIZ_RESULTCOUNT',$result_count); ?><br><br>

<!-- filter by username field -->
<div class="card card-body" style="max-width: 400px;">
<form action="index.php?option=com_yaquiz&view=yaquiz&layout=results&id=<?php echo $quiz_id; ?>" method="post">
    <div class="form-group">
        <label for="username"><?PHP echo Text::_('COM_YAQUIZ_FILTERUSERNAME');?></label>
        <input type="text" class="form-control" id="filterusername" name="filterusername" placeholder="Enter username">
    </div>
    <?php if(isset($_POST['filterusername']) && strlen($_POST['filterusername'] > 0) ) :?>
        <span><?php echo Text::_('COM_YAQUIZ_CURRENTFILTERS').$_POST['filterusername']; ?></span>
        <br/>
    <?php endif;?>
    <button type="submit" class="btn btn-primary"><?PHP echo Text::_('COM_YAQUIZ_FILTER');?></button>
</form>
</div>
<br/>

<table class="table table-striped">
    <thead>
        <tr>
            <th><?PHP echo Text::_('COM_YAQUIZ_RESULTIDLBL');?></th>
            <th><?PHP echo Text::_('COM_YAQUIZ_USERNAMEWITHIDLBL');?></th>
            <th><?PHP echo Text::_('COM_YAQUIZ_PASSFAIL');?></th>
            <th><?PHP echo Text::_('COM_YAQUIZ_SCORE');?></th>
            <th><?PHP echo Text::_('COM_YAQUIZ_SUBMITTEDON');?></th>
            <th><?PHP echo Text::_('COM_YAQUIZ_MOREDETAILS');?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach($results as $result): ?>
        
            <?php 
                $userid = $result->user_id;
                $quiz_taker = Factory::getUser($result->user_id);
                $quiz_taker_username = $quiz_taker->username;
            ?>

        <tr>
            <td>
                <?php echo $result->id; ?>
            </td>
            <td>
                <?php echo $quiz_taker_username; ?>
                [<?php echo $userid; ?>]

            </td>
            <td><?php 
                if($result->passed == 1){
                    echo Text::_('COM_YAQUIZ_PASSED');
                }
                else{
                    echo Text::_('COM_YAQUIZ_FAILED');
                }
            
            ?></td>
            <td><?php echo $result->score; ?></td>
            <td><?php echo $result->submitted; ?></td>
            <td>
                <?php 
                
                    if($result->full_results){
                        echo ('<a href="index.php?option=com_yaquiz&view=yaquiz&layout=detailresults&id='.$quiz_id.'&result_id='.$result->id.'">'.Text::_('COM_YAQUIZ_VIEW').'</a>');
                    }
                    else{
                        echo Text::_('COM_YAQUIZ_NOTAVAILABLE');
                    }
                ?>
                
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>


<?php if ($pagecount > 1):?>
    <nav aria-label="Page navigation">
        <ul class="pagination">
            <?php for($i = 1; $i <= $pagecount; $i++): ?>
                <li class="page-item <?php if($i == $page){echo "active";} ?>"><a class="page-link" href="index.php?option=com_yaquiz&view=yaquiz&layout=results&id=<?php echo $quiz_id; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
            <?php endfor; ?>
        </ul>
    </nav>
<?php endif; ?>

<!-- page limit selector -->
<form style="max-width: 200px;" action="index.php?option=com_yaquiz&view=yaquiz&layout=results&id=<?php echo $quiz_id; ?>&page=<?php echo $page; ?>" method="post">
    <div class="form-group">
        <label for="yaqresultlimit"><?PHP echo Text::_('COM_YAQUIZ_RESULTSPERPAGE');?></label>
        <select class="form-control" id="yaqresultlimit" name="yaqresultlimit">
            <option value="5" <?php if($yaqresultlimit == 5){echo "selected";} ?>>5</option>
            <option value="10" <?php if($yaqresultlimit == 10){echo "selected";} ?>>10</option>
            <option value="25" <?php if($yaqresultlimit == 25){echo "selected";} ?>>25</option>
            <option value="50" <?php if($yaqresultlimit == 50){echo "selected";} ?>>50</option>
            <option value="100" <?php if($yaqresultlimit == 100){echo "selected";} ?>>100</option>
        </select>
    </div>
    <button type="submit" class="btn btn-primary"><?PHP echo Text::_('COM_YAQUIZ_UPDATE');?></button>
</form>