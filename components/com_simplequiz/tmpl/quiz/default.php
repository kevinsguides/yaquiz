<?php
namespace KevinsGuides\Component\SimpleQuiz\Site\View\Quiz;

use Joomla\CMS\Log\Log;
use KevinsGuides\Component\SimpleQuiz\Site\Helper\QuestionBuilderHelper;
use JHtml;
use Joomla\CMS\Factory;
use KevinsGuides\Component\SimpleQuiz\Site\Model\QuizModel;




defined('_JEXEC') or die;


$app = Factory::getApplication();
$wa = $app->getDocument()->getWebAssetManager();
$style = '/components/com_simplequiz/src/Style/quiz.css';
$wa->registerAndUseStyle('com_simplequiz.quiz', $style);

//get config from component
$globalParams = $app->getParams('com_simplequiz');
if ($globalParams->get('load_mathjax') === '1') {
    $wa->registerAndUseScript('com_simplequiz.mathjax', 'https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-svg.js');
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

//get the questions (a list of objects)
$questions = $model->getQuestions($quiz->id);
$questionBuilder = new QuestionBuilderHelper();

$quizparams = $model->getQuizParams($quiz->id);

//if the quiz is null, show error
if ($quiz == null):
    ?>
    <div class="card m-3">
        <div class="card-body">
            <h1>Quiz not found</h1>
            <p>Sorry, the quiz you are looking for could not be found.</p>
        </div>
    </div>
    <?php
else:
    ?>
    <div class="p-3">
        <div class="card">
            <h2 class="card-header">
                <?php echo $quiz->title; ?>
            </h2>
            <div class="card-body">
                <?php echo $quiz->description; ?>
            </div>
        </div>
        <br />
        <?php if ($quizparams->quiz_displaymode == 'default'): ?>

            <form action="index.php?option=com_simplequiz&task=quiz.submitquiz" method="post">
                <input type="hidden" name="quiz_id" value="<?php echo $quiz->id; ?>" />

                <?php foreach ($questions as $question): ?>
                    <?php
                    $actualid = $question->id - 1;
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
                    echo $questionBuilder->buildQuestion($question, $quizparams);
                    ?>
                <br />
                <?php endforeach; ?>

                <?php echo JHtml::_('form.token'); ?>
                <button type="submit" class="btn btn-success btn-lg">Submit Quiz</button>
            </form>

        <?php endif; ?>

    </div>
    <?php
endif;
?>