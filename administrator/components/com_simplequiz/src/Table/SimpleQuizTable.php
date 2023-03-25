<?php
namespace KevinsGuides\Component\SimpleQuiz\Administrator\Table;

use Joomla\CMS\Table\Table;

//table class

class SimpleQuizTable extends Table {

    public function __construct(&$db) {
        parent::__construct('#__simplequiz_quizzes', 'id', $db);
    }

    public function bind($array, $ignore = '') {
        return parent::bind($array, $ignore);
    }

    public function store($updateNulls = false) {
        return parent::store($updateNulls);
    }

    public function check() {
        return parent::check();
    }

}