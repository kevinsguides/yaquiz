<?php 
namespace KevinsGuides\Component\SimpleQuiz\Administrator\Controller;
use Joomla\CMS\Application\CMSApplication;
use Joomla\CMS\Log\Log;
use Joomla\CMS\MVC\Controller\BaseController;
use Joomla\CMS\MVC\Factory\MVCFactoryInterface;
use Joomla\Input\Input;


defined ( '_JEXEC' ) or die;

/**
 * Summary of QuestionsController
 */
class QuestionsController extends BaseController
{
    /**
     * Summary of display
     * @param mixed $cachable
     * @param mixed $urlparams
     * @return void
     */
    public function __construct($config = [], MVCFactoryInterface $factory = null, ?CMSApplication $app = null, ?Input $input = null)
    {
        parent::__construct($config, $factory, $app, $input);

        $this->registerTask('deleteQuestion', 'deleteQuestion');
        $this->registerTask('display', 'display');

    }

    public function display($cachable = false, $urlparams = false)
    {
        Log::add('QuestionsController::display() called', Log::INFO, 'com_simplequiz');
        $this->setRedirect('index.php?option=com_simplequiz&view=questions');
    }

    public function deleteQuestion()
    {
        $model = $this->getModel('Question');
        $delete = $this->input->get('delete', '0');
        if($model->deleteQuestion($delete)){
            $this->setMessage('Question deleted');
        }
        else{
            $this->setMessage('Error: Question not deleted');
        }
        $this->setRedirect('index.php?option=com_simplequiz&view=questions');
    }

    public function newQuestion(){
        $this->setRedirect('index.php?option=com_simplequiz&view=Question&layout=edit');
    }


    

}