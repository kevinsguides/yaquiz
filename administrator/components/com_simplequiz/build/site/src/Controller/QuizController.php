<?php
namespace KevinsGuides\Component\SimpleQuiz\Site\Controller;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormFactoryInterface;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Input\Input;
use JSession;
defined ('_JEXEC') or die;
use Joomla\CMS\MVC\Controller\BaseController;
use KevinsGuides\Component\SimpleQuiz\Site\Model\QuizModel;
use KevinsGuides\Component\SimpleQuiz\Site\Helper\QuestionBuilderHelper;

/**
 * Summary of QuizController
 */
class QuizController extends BaseController{

    /**
     * Summary of display
     * @param mixed $cachable
     * @param mixed $urlparams
     * @return void
     */
    public function display($cachable = false, $urlparams = array()){
        parent::display($cachable, $urlparams);
        //register tasks
        $this->registerTask('submitquiz', 'submitquiz');

    }

    public function submitquiz()
    {
        Log::add('QuizController::submitquiz() called', Log::INFO, 'com_simplequiz');

            //check for token
            if(!JSession::checkToken()){
                Log::add('SimpleQuizController::save() token failed', Log::INFO, 'com_simplequiz');
                //cue error message
                $this->setMessage('Token failed');
                //redirect to the view
                $this->setRedirect('index.php?option=com_simplequiz');
                return;
            }

        //grade the quiz by comparing the answers to the correct answers
        //get submitted answers
        $app = Factory::getApplication();
        $input = $app->input;
        $answers = $input->get('answers', array(), 'array');
        $quiz_id = $input->get('quiz_id', 0, 'int');

        //for each answer, check if its correct
        
        $correct = 0;
        $total = 0;
        $model = new QuizModel();
        $all_feedback = array();
        foreach($answers as $question_id=>$answer){
            $total++;
            $correct += $model->checkAnswer($question_id, $answer);
            //key value pair of question_id, their answer, and if its correct
            $question_feedback = array(
                'question_id' => $question_id,
                'correctanswer' => $model->getCorrectAnswerText($question_id),
                'correct' => $model->checkAnswer($question_id, $answer),
                'useranswer' => $model->getSelectedAnswerText($question_id, $answer),
            );
            $all_feedback[] = $question_feedback;
        }

        Log::add('QuizController::submitquiz() total correct: '.$correct . ' of ' .$total, Log::INFO, 'com_simplequiz');

        $qbhelper = new QuestionBuilderHelper();
        
        //create a blank results object
        $results = new \stdClass();
        $results->correct = $correct;
        $results->total = $total;
        $results->quiz_id = $quiz_id;
        $results->feedback = $all_feedback;

        //set quiz title state var
        $quiz = $model->getItem($quiz_id);
        $title = $quiz->title;
        Log::add('QuizController::submitquiz() quiz title: '.$title, Log::INFO, 'com_simplequiz');

        $buildResults = $qbhelper->buildResultsArea($title, $quiz_id, $results);

        //set app view to results
        $app->input->set('view', 'quiz');
        $app->input->set('layout', 'results');
        
        echo $buildResults;

        //$this->setRedirect('index.php?option=com_simplequiz&view=quiz&layout=results');

    }


}