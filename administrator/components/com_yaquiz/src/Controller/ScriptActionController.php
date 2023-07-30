<?php
namespace KevinsGuides\Component\Yaquiz\Administrator\Controller;

defined ( '_JEXEC' ) or die ();

use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;

use Joomla\CMS\Response\JsonResponse as JResponseJson;
use Joomla\CMS\MVC\Controller\BaseController;


class ScriptActionController extends BaseController {


    public function doSomething() {

        echo 'something';
    }


}