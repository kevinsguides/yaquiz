<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
 * 
 * This layout displays basic info about the quiz, including title, desc, and info
 * If a timer is set, this shows the timer info, so the user knows how much time they will have before they begin
 * 
*/

namespace KevinsGuides\Component\Yaquiz\Site\View\Quiz;

defined('_JEXEC') or die;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Uri\Uri;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Factory;
use KevinsGuides\Component\Yaquiz\Site\Model\QuizModel;
use KevinsGuides\Component\Yaquiz\Site\Helper\ThemeHelper;

//if $this-> item is already set
if (isset($this->item)) {
    $quiz = $this->item;
} else {
    $quiz = $this->get('Item');
}

$model = new QuizModel();
$attempts_left = $model->quizAttemptsLeft($quiz->id);
$quiz_params = $model->getQuizParams($quiz->id);

$app = Factory::getApplication();

//get config from component
$globalParams = $app->getParams('com_yaquiz');
if ($globalParams->get('get_mathjax') === '1') {
    $wam->registerAndUseScript('com_yaquiz.mathjax', 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js', [], ['defer' => true]);
}
if ($globalParams->get('get_mathjax') === '2') {
    Log::add('Loading local mathjax', Log::INFO, 'com_yaquiz');
    $wam->registerAndUseScript('com_yaquiz.mathjaxlocal', 'components/com_yaquiz/js/mathjax/es5/tex-svg.js', [], ['defer' => true]);
}



//config for attempts left display
$showAttemptsLeft = false;
$showAttemptsLeft = $globalParams->get('show_attempts_left', '1');
if($showAttemptsLeft == '1'){
    $showAttemptsLeft = true;
}


//todo: check if user is already in the middle of a session and has time left, we should just redirect them automatically from htmlview.


?>

<?php 

include ThemeHelper::findFile('singlepage_intro.php');
include ThemeHelper::findFile('singlepage_timer_startinfo.php');

?>

