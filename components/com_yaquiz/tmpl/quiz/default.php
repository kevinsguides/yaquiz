<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/

namespace KevinsGuides\Component\Yaquiz\Site\View\Quiz;

use Joomla\CMS\Log\Log;
use Joomla\CMS\Uri\Uri;
use KevinsGuides\Component\Yaquiz\Site\Helper\QuestionBuilderHelper;
use JHtml;
use Joomla\CMS\Factory;
use KevinsGuides\Component\Yaquiz\Site\Model\QuizModel;




defined('_JEXEC') or die;


$app = Factory::getApplication();
$wam = $app->getDocument()->getWebAssetManager();
$style = 'components/com_yaquiz/src/Style/quiz.css';
$wam->registerAndUseStyle('com_yaquiz.quiz', $style);



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

$theme = $globalParams->get('theme','default');
$stylefile = '/components/com_yaquiz/tmpl/' . $theme . '/style.css';
//if file exists
if (file_exists(JPATH_ROOT . $stylefile)) {
    $wam->registerAndUseStyle('com_yaquiz.quizstyle', $stylefile);
}


if ($app->input->get('status') == 'retry') {
    //get their old answers from the session
    $session = Factory::getSession();
    $oldanswers = $session->get('sq_retryanswers');
} else {
    $oldanswers = null;
}


JHtml::_('behavior.keepalive');


//if $this-> item is already set
if (isset($this->item)) {
    $quiz = $this->item;
} else {
    $quiz = $this->get('Item');
}

$model = new QuizModel();
$attempts_left = $model->quizAttemptsLeft($quiz->id);
$quiz_params = $model->getQuizParams($quiz->id);


//get the questions (a list of objects)
$questions = $model->getQuestions($quiz->id);
$questionBuilder = new QuestionBuilderHelper();

$quizparams = $model->getQuizParams($quiz->id);
$quizparams->quiz_id = $quiz->id;

//record hits
if ($globalParams->get('record_hits') === '1') {
    $model->countAsHit($quiz->id);
}


//if the quiz is null, show error
if ($quiz == null):
    ?>
    <div class="card m-3">
        <div class="card-body">
            <h1><?php echo \JText::_('COM_YAQ_NOTFOUND') ?></h1>
            <p><?php echo \JText::_('COM_YAQ_NOTFOUND_MORE') ?></p>
        </div>
    </div>
    <?php
else:

    
    $template_intro = (JPATH_SITE . '/components/com_yaquiz/tmpl/quiz/' . $theme . '/singlepage_intro.php');
    

    ?>
    <?php include($template_intro); ?>

        <?php if ($quizparams->quiz_displaymode == 'default'): ?>

            <form action="<?php echo Uri::root(); ?>index.php?option=com_yaquiz&task=quiz.submitquiz" method="post">
                <input type="hidden" name="quiz_id" value="<?php echo $quiz->id; ?>" />
                <?php $i = 0;?>
                <?php foreach ($questions as $question): ?>
                    <?php
                    if($question->params->question_type != 'html_section'){
                        $i++;
                    }
                    
                    $actualid = $question->id;
                    //check if $oldanswers is set
                    if ($oldanswers) {
                        //see if this question is in the old answers
                        foreach ($oldanswers as $oldanswer) {
                            if ($oldanswer['question_id'] == $actualid) {
                                $question->defaultanswer = $oldanswer['answer'];
                            }
                        }
                        if (!isset($question->defaultanswer) && ($app->input->get('status') == 'retry')) {
                            $question->defaultanswer = 'missing';
                        }
                    }
                    $question->question_number = $i;
                    echo $questionBuilder->buildQuestion($question, $quizparams);
                    ?>
                <br />
                <?php endforeach; ?>

                <?php echo JHtml::_('form.token'); ?>
                <button type="submit" class="btn btn-success btn-lg"><?php echo \JText::_('COM_YAQ_SUBMITQUIZ') ?></button>
            </form>

        <?php endif; ?>


    <?php
endif;
?>