<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

namespace KevinsGuides\Component\Yaquiz\Site\View\User;
use KevinsGuides\Component\Yaquiz\Site\Model\QuizModel;
//render user quiz profile
// For now lets just have this link to all results...

defined('_JEXEC') or die;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;

//we might make this changeable later
$filter_limit = 10;

$model = $this->getModel();
$quizModel = new QuizModel();

$app = Factory::getApplication();

$filter_page = $app->input->get('page', 1, 'INT');
Log::add('filter_page: '.$filter_page, Log::DEBUG, 'com_yaquiz');

$page_count = ceil($model->countTotalResults(null) / $filter_limit);

if($filter_page > $page_count){
    $filter_page = $page_count;
}

$results = $model->getUserResults(null, $filter_limit, $filter_page);

// TODO: add filter by quiz title (only show user quizzes theyve actually taken in list)

?>

<h1><?php echo Text::_('COM_YAQ_USERRESULTS');?></h1>

<?php //if there are no results to display
if (count($results) == 0) : ?>
    <p><?php echo Text::_('COM_YAQ_NOQUIZYET');?></p>
<?php else: ?>

<table class="table table-striped table-hover">
    <thead>
        <tr>
            <th scope="col"><?php echo Text::_('COM_YAQ_USERRESULTS_QNAME');?></th>
            <th scope="col"><?php echo Text::_('COM_YAQ_USERRESULTS_PASSED');?></th>
            <th scope="col"><?php echo Text::_('COM_YAQ_USERRESULTS_URSCORE');?></th>
            <th scope="col"><?php echo Text::_('COM_YAQ_USERRESULTS_QUIZDATE');?></th>
            <th scope="col"><?php echo Text::_('COM_YAQ_USERRESULTS_MOREDETS');?></th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($results as $result) : 
            $quizTitle = $result->title;
            if($result->passed == 1){
                $result->passed = Text::_('COM_YAQ_YES');
            } else {
                $result->passed = Text::_('COM_YAQ_NO');
            }

            $formatted_score = $result->points . "/" . $result->total_points . " (" . $result->score . "%)";

            //format $result->submitted in human readable format
            $submitted_date = date('F j, Y', strtotime($result->submitted));
            $submitted_time = date('g:i a', strtotime($result->submitted));

            $resultsLink = Text::_('COM_YAQ_UNAVAILABLE');
            if($result->full_results != ''){
                $resultsLink = '<a href="index.php?option=com_yaquiz&view=user&layout=singleresult&resultid='.$result->id.'">'.Text::_('COM_YAQ_VIEW_RESULTS').'</a>';
            }
            
            ?>
            <tr>
                <td><strong><?php echo $quizTitle; ?></strong></td>
                <td><?php echo $result->passed; ?></td>
                <td><?php echo $formatted_score; ?></td>
                <td title="<?php echo $submitted_date.' '.$submitted_time; ?>"><?php echo $submitted_date; ?></td>
                <td><?php echo $resultsLink; ?></td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
<?php endif; ?>

<?php if ($page_count > 1) : ?>
<nav aria-label="user results page navigation">
    <ul class="pagination">
        <?php //display up to 10 pages at a time
        $start_page = $filter_page - 4;
        $end_page = $filter_page + 5;
        if($start_page < 1){
            $start_page = 1;
            if($page_count > 10){
                $end_page = 10;
            } else {
                $end_page = $page_count;
            }
            
        }
        if($end_page > $page_count){
            $end_page = $page_count;
            if($page_count - 10 > 0){
                $start_page = $page_count - 10;
            } else {
                $start_page = 1;
            }

        }
        ?>
        <?php if($start_page > 1) : ?>
            <li class="page-item"><a class="page-link" title="<?php echo Text::_('COM_YAQ_FIRST_PAGE') ;?>" href="index.php?option=com_yaquiz&view=user&layout=default&page=1?>">
            <i class="fas fa-chevron-left"></i></a></li>
            </a></li>
        <?php endif; ?>


        <?php for($i = $start_page; $i <= $end_page; $i++) : ?>
            <?php if($i == $filter_page) : ?>
                <li class="page-item active"><a class="page-link" href="index.php?option=com_yaquiz&view=user&layout=default&page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
            <?php else: ?>
                <li class="page-item"><a class="page-link" href="index.php?option=com_yaquiz&view=user&layout=default&page=<?php echo $i; ?>"><?php echo $i; ?></a></li>
            <?php endif; ?>
        <?php endfor; ?>

        <?php 
            //if total pages > end_page, display last page link
            if($page_count > $end_page) : ?>
                <li class="page-item"><a class="page-link" title="<?php echo Text::_('COM_YAQ_LAST_PAGE') ;?>" href="index.php?option=com_yaquiz&view=user&layout=default&page=<?php echo $page_count; ?>"><i class="fas fa-chevron-right"></i></a></li>
        <?php endif; ?>
        

    </ul>
</nav>

<?php endif; ?>


