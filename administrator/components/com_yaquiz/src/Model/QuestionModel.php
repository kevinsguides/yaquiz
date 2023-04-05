<?php
namespace KevinsGuides\Component\Yaquiz\Administrator\Model;

use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\MVC\Model\BaseModel;
use KevinsGuides\Component\Yaquiz\Site\Model\QuizModel;

defined('_JEXEC') or die;


use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;


//this is a model for multiple question operations

class QuestionModel extends AdminModel
{

    public function __construct($config = array())
    {
        parent::__construct($config);
    }

    //get a single question
    public function getItem($id = null)
    {
        //get the database driver
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from('#__com_yaquiz_questions');
        $query->where('id = ' . $id);
        $db->setQuery($query);
        $item = $db->loadObject();
        return $item;
    }

    public function getId($id = null)
    {
        return $id;
    }

    //form getter
    public function getForm($data = [], $loadData = true)
    {

        $app = Factory::getApplication();

        // Get the form.
        $form = $this->loadForm('com_yaquiz.question', 'question', ['control' => 'jform', 'load_data' => $loadData]);

        if (empty($form)) {
            return false;
        }


        if (isset($data)) {
            //if the form is not new, disable the question_type field
            if ($data->id != null && $data->id != 0) {
                //$form->setFieldAttribute('question_type', 'disabled', 'true');

                //make question_type readonly
                $form->setFieldAttribute('question_type', 'readonly', 'true');

                //json params to object
                $params = json_decode($data->params);
                $data->question_type = $params->question_type;
                $data->randomize_mchoice = $params->randomize_mchoice;
                $data->points = $params->points;
                $data->case_sensitive = $params->case_sensitive;
                $data->modified_by = $app->getIdentity()->id;

                //if question type is not multiple_choice
                if ($data->question_type != 'multiple_choice') {
                    //hide the randomize_mchoice field
                    $form->setFieldAttribute('randomize_mchoice', 'type', 'hidden');
                }
            }
        }
        else{

            $data = new \stdClass();
            $data->question_type = 'multiple_choice';
            $data->randomize_mchoice = 0;
            $data->points = 1;
            $data->case_sensitive = 0;
            //set created_by to current user
            $data->created_by = $app->getIdentity()->id;
            //set created to current time
            $data->created= date('Y-m-d H:i:s');
            //set modified_by to current user
            $data->modified_by = $app->getIdentity()->id;
            //set modified to current time
            $data->modified = date('Y-m-d H:i:s');
            //set checked out to this users id
            $data->checked_out = $app->getIdentity()->id;
            //set checked_out_time to current time
            $data->checked_out_time = date('Y-m-d H:i:s');

            
        }








        //populate details with details
        $form->bind($data);
        return $form;
    }

    //save the form
    public function save($data)
    {

        //log everything in $data array
        $data_array_to_string = '';
        foreach ($data as $key => $value) {
            $data_array_to_string .= $key . ' => ' . $value . ', ';
        }
        Log::add('QuestionModel::save() called with data ' . $data_array_to_string, Log::INFO, 'com_yaquiz');


        $qtype = $data['question_type'];
        if ($qtype == 'multiple_choice') {
            //get possible answers from data
            $answers = $data['answers'];

            //turn into json where key is index and value is answer
            $json_answers = json_encode($answers);
            $data['answers'] = $json_answers;
        }

        if ($qtype == 'true_false') {

            //get correct answer from data
            $correct = $data['tfcorrect'];
            Log::add('tf correct answer is ' . $correct, Log::INFO, 'com_yaquiz');
        }

        if($qtype == 'fill_blank'){
            //get correct answer from data
            $answers = $data['answers'];
            //remove any blank answers ""
            //if not null
            if(isset($answers)){
                $answers = array_filter($answers, function($value) { return $value !== ''; });
            }
            $data['answers'] = json_encode($answers);

        }



        Log::add('save called from q model', Log::INFO, 'com_yaquiz');
        Log::add('save question type is ' . $data['question_type'], Log::INFO, 'com_yaquiz');

        //see if we are updating or creating
        if ($data['id'] != null && $data['id'] != 0) {
            //update
            return $this->update($data);
        } else {
            //create
            return $this->create($data);
        }

    }

    public function paramsToJson($data)
    {
        $params = new \stdClass();
        $params->question_type = $data['question_type'];
        $params->randomize_mchoice = $data['randomize_mchoice'];
        $params->points = $data['points'];
        $params->case_sensitive = $data['case_sensitive'];
        $json = json_encode($params);
        Log::add('trynna save these params as json: ' . $json, Log::INFO, 'com_yaquiz ');
        return $json;
    }


    public function update($data)
    {
        $app = Factory::getApplication();
        Log::add('update function called', Log::INFO, 'com_yaquiz');
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->update('#__com_yaquiz_questions');
        $query->set('question = ' . $db->quote($data['question']));
        $query->set('details = ' . $db->quote($data['details']));
        $query->set('params = ' . $db->quote($this->paramsToJson($data)));
        $query->set('answers = ' . $db->quote($data['answers']));
        $query->set('correct = ' . $db->quote($data['correct']));
        $query->set('catid = ' . $db->quote($data['catid']));
        $query->set('modified_by = ' . $app->getIdentity()->id);
        $query->set('modified = CURRENT_TIMESTAMP');
        $query->set('feedback_right = ' . $db->quote($data['feedback_right']));
        $query->set('feedback_wrong = ' . $db->quote($data['feedback_wrong']));
        $query->where('id = ' . $data['id']);
        $db->setQuery($query);
        $db->execute();

        return true;
    }

    public function create($data)
    {
        Log::add('create function called', Log::INFO, 'com_yaquiz');
        $app = Factory::getApplication();
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->insert('#__com_yaquiz_questions');
        $query->columns('id, question, details, params, catid, created_by, created, modified_by, modified');
        $data['created_by'] = $app->getIdentity()->id;
        $data['modified_by'] = $app->getIdentity()->id;
        $data['checked_out'] = 0;
        $query->values('null, ' . $db->quote($data['question']) . ', ' . $db->quote($data['details']) . ', ' . $db->quote($this->paramsToJson($data)) . ', ' . $db->quote($data['catid']) . ', ' . $db->quote($data['created_by']) . ', ' . $db->quote($data['created']) . ', ' . $db->quote($data['modified_by']) . ', ' . $db->quote($data['modified']));
        $db->setQuery($query);
        $db->execute();
        return true;

    }

    public function getLastInsertedId()
    {
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('id');
        $query->from('#__com_yaquiz_questions');
        $query->order('id DESC');
        $db->setQuery($query);
        $id = $db->loadResult();
        return $id;
    }


    public function deleteQuestion($pk)
    {
        $app = Factory::getApplication();
        if($app->getIdentity()->authorise('core.delete', 'com_yaquiz') != true){
            //cue message
            $app->enqueueMessage('You don\'t have permission to delete questions.', 'error');
            throw new \Exception(Text::_('JERROR_ALERTNOAUTHOR'), 403);
        }
        



        //find all quiz ids that this question is in
        $db = Factory::getContainer()->get('DatabaseDriver');
        $query = $db->getQuery(true);
        $query->select('quiz_id');
        $query->from('#__com_yaquiz_question_quiz_map');
        $query->where('question_id = ' . $pk);
        $db->setQuery($query);
        $quiz_ids = $db->loadColumn();

        //delete from questions
        $query = $db->getQuery(true);
        $query->delete('#__com_yaquiz_questions');
        $query->where('id = ' . $pk);
        $db->setQuery($query);
        $db->execute();

        //delete from question_quiz_map
        $query = $db->getQuery(true);
        $query->delete('#__com_yaquiz_question_quiz_map');
        $query->where('question_id = ' . $pk);
        $db->setQuery($query);
        $db->execute();



        //reorder existing quizzes
        $quizModel = new YaquizModel();
        foreach ($quiz_ids as $quiz_id) {
            $quizModel->reorderQns($quiz_id);
        }



        return true;
    }



}