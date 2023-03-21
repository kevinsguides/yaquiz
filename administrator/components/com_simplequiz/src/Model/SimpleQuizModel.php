<?php
namespace KevinsGuides\Component\SimpleQuiz\Administrator\Model;
use JFactory;
use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\MVC\Model\BaseModel;

defined ( '_JEXEC' ) or die;

//this is a model for a single quiz



class SimpleQuizModel extends AdminModel
{

    //get the quiz
    public function getQuiz($qid)
    {
        Log::add('try get quiz with qid '.$qid, Log::INFO, 'com_simplequiz');
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__simplequiz_quizzes');
        Log::add('trynna open quiz with id: ' . $qid, Log::INFO, 'com_simplequiz');
        $query->where('id = ' . $qid);
        $db->setQuery($query);
        $item = $db->loadObject();
        return $item;
    }


    //get the quiz form
    public function getForm($data = [], $loadData = true)
    {

        Log::add('getform called in quizmodel', Log::INFO, 'com_simplequiz');
        $app  = Factory::getApplication();

        //if layout is edit
        if($app->input->get('layout') == 'edit')
        {
// Get the form.
$form = $this->loadForm('com_simplequiz.quiz', 'quiz', ['control' => 'jform', 'load_data' => $loadData]);
if (empty($form)) {
    return false;
}
//if new quiz
if($data->id == 0 || $data->id == null)
{
    //set created_by to current user
    $data['created_by'] = $app->getIdentity()->id;
    //set created to current time
    $data['created'] = date('Y-m-d H:i:s');
    //set modified_by to current user
    $data['modified_by'] = $app->getIdentity()->id;
    //set modified to current time
    $data['modified'] = date('Y-m-d H:i:s');
    //set checked out to this users id
    $data['checked_out'] = $app->getIdentity()->id;
    //set checked_out_time to current time
    $data['checked_out_time'] = date('Y-m-d H:i:s');
    $data['quiz_displaymode'] = 'default';
}
else{
    $params = $this->getParams($data->id);
    //get 'quiz_displaymode' from params
    Log::add('params: ' . $params['quiz_displaymode'], Log::INFO, 'com_simplequiz');
    $data->quiz_displaymode = $params['quiz_displaymode'];
    $data->quiz_showfeedback = $params['quiz_showfeedback'];
    $data->quiz_feedback_showcorrect = $params['quiz_feedback_showcorrect'];
    $data->quiz_question_numbering = $params['quiz_question_numbering'];
    $data->quiz_use_points = $params['quiz_use_points'];
    $data->passing_score = $params['passing_score'];

}


//bind data to form
$form->bind($data);



return $form;
        }
        else{
            Log::add('try to get simplequiz default filter form', Log::INFO, 'com_simplequiz');
            //get the simplequiz form
            $form = $this->loadForm('com_simplequiz.simplequiz', 'simplequiz', ['control' => 'filters', 'load_data' => $loadData]);
            if (empty($form)) {
                return false;
            }
            return $form;



        }



        
    }

    public function save($data){
        //see if is new quiz or update

        //if new quiz
        if($data['id'] == 0 || $data['id'] == null)
        {
            //call insert
            return $this->insert($data);
        }
        else{
            //call update
            return $this->update($data);

        }
    }

    public function insert($data){
        //insert quiz
        $db = Factory::getContainer()->get('DatabaseDriver');
        $app = Factory::getApplication();
        $query = $db->getQuery(true);
        $query->insert('#__simplequiz_quizzes');
        $data['created_by'] = $app->getIdentity()->id;
        $data['modified_by'] = $app->getIdentity()->id;
        $data['checked_out'] = 0;
        $query->columns('title, description, published, created_by, created, modified_by, modified, checked_out, checked_out_time, params, access, hits, catid');
        $query->values($db->quote($data['title']) . ', ' . $db->quote($data['description']) . ', ' . $db->quote($data['published']) . ', ' . $db->quote($data['created_by']) . ', ' . $db->quote($data['created']) . ', ' . $db->quote($data['modified_by']) . ', ' . $db->quote($data['modified']) . ', ' . $db->quote($data['checked_out']) . ', ' . $db->quote($data['checked_out_time']) . ', ' . $db->quote($this->dataToParams($data)) . ', ' . $db->quote($data['access']) . ', ' . $db->quote($data['hits']) . ', ' . $db->quote($data['catid']));
        $db->setQuery($query);
        $db->execute();
        return true;
    }

    public function update($data){
        //update quiz
        $app = Factory::getApplication();
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->update('#__simplequiz_quizzes');
        $query->set('title = ' . $db->quote($data['title']));
        $query->set('description = ' . $db->quote($data['description']));
        $query->set('published = ' . $db->quote($data['published']));
        $query->set('modified_by = ' . $app->getIdentity()->id);
        $query->set('modified = CURRENT_TIMESTAMP');
        $query->set('params = ' . $db->quote($this->dataToParams($data)));
        $query->set('catid = ' . $db->quote($data['catid']));
        $query->where('id = ' . $data['id']);
        $db->setQuery($query);
        $db->execute();

        return true;

    }

    public function addQuestionsToQuiz($quizid, $questionids){

        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);

        //check the __simplequiz_question_quiz_map table for existing questions for this quiz
        $query->select('question_id');
        $query->from('#__simplequiz_question_quiz_map');
        $query->where('quiz_id = ' . $quizid);
        $db->setQuery($query);
        $existingQuestions = $db->loadColumn();

        //return if no quiz id or questionids are given
        if($quizid == null || $questionids == null)
        {
            return;
        }

        //if there are existing questions
        if(count($existingQuestions) > 0)
        {
            //loop through the existing questions
            foreach($existingQuestions as $existingQuestion)
            {
                //check if this existingQuestion matches any of the questionids
                if(in_array($existingQuestion, $questionids))
                {
                    //if it does, remove it from the questionids array
                    $key = array_search($existingQuestion, $questionids);
                    unset($questionids[$key]);

                }

            }
        }

        //if there are no questions to add, return
        if(count($questionids) == 0)
        {
            return;
        }

        //loop through the questionids
        foreach($questionids as $questionid)
        {
            //insert the questionid and quizid into the __simplequiz_question_quiz_map table
            $query = $db->getQuery(true);
            $query->insert('#__simplequiz_question_quiz_map');
            $query->columns('quiz_id, question_id');
            $query->values($quizid . ', ' . $questionid);
            $db->setQuery($query);
            $db->execute();
        }

    }

    public function getQuestionsInQuiz($quiz_id){

        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);

        //get the questions in this quiz
        $query->select('q.id, q.question, q.details, q.answers, q.correct, q.published, q.params, q.catid');
        $query->from('#__simplequiz_questions as q');
        $query->join('INNER', '#__simplequiz_question_quiz_map as qqm ON q.id = qqm.question_id');
        $query->where('qqm.quiz_id = ' . $quiz_id);
        $db->setQuery($query);
        $questions = $db->loadObjectList();

        return $questions;

    }

    public function removeQuestionFromQuiz($quiz_id, $question_id){

        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        //delete the question from the quiz
        $query->delete('#__simplequiz_question_quiz_map');
        $query->where('quiz_id = ' . $quiz_id);
        $query->where('question_id = ' . $question_id);
        $db->setQuery($query);
        $db->execute();

    }

    public function getParams($qid){

        //get params from db
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('params');
        $query->from('#__simplequiz_quizzes');
        $query->where('id = ' . $qid);
        $db->setQuery($query);
        $params = $db->loadResult();

        //decode params
        $params = json_decode($params);

        //turn params into array
        $params = (array) $params;

        return $params;

    }

    public function setParams($qid, $params){

        //encode params
        $params = json_encode($params);

        //update params in db
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->update('#__simplequiz_quizzes');
        $query->set('params = ' . $db->quote($params));
        $query->where('id = ' . $qid);
        $db->setQuery($query);
        $db->execute();

    }

    /**
     * Takes data from form and converts it to params json
     */
    public function dataToParams($data){
        $params = array();
        //set params
        $params['quiz_displaymode'] = $data['quiz_displaymode'];
        $params['quiz_showfeedback'] = $data['quiz_showfeedback'];
        $params['quiz_feedback_showcorrect'] = $data['quiz_feedback_showcorrect'];
        $params['quiz_question_numbering'] = $data['quiz_question_numbering'];
        $params['quiz_use_points'] = $data['quiz_use_points'];
        $params['passing_score'] = $data['passing_score'];
        //encode
        $params = json_encode($params);
        return $params;
    }

    public function getCategoryName($pk){
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('title');
        $query->from('#__categories');
        $query->where('id = ' . $pk);
        $db->setQuery($query);
        $category = $db->loadResult();
        return $category;
    }



}