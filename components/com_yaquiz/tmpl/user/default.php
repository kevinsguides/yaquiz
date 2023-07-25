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

$model = $this->getModel();
$quizModel = new QuizModel();




$results = $model->getUserResults();

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
            $quizTitle = $quizModel->getItem($result->quiz_id)->title;
            if($result->passed == 1){
                $result->passed = Text::_('COM_YAQ_YES');
            } else {
                $result->passed = Text::_('COM_YAQ_NO');
            }

            $formatted_score = $result->points . "/" . $result->total_points . " (" . $result->score . "%)";

            //format $result->submitted in human readable format
            $result->submitted = date('F j, Y, g:i a', strtotime($result->submitted));

            $resultsLink = Text::_('COM_YAQ_UNAVAILABLE');
            if($result->full_results != ''){
                $resultsLink = '<a href="index.php?option=com_yaquiz&view=user&layout=singleresult&resultid='.$result->id.'">'.Text::_('COM_YAQ_VIEW_RESULTS').'</a>';
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
<?php endif; ?>

