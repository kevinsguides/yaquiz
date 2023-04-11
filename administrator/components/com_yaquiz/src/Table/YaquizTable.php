<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\Table;

use Joomla\CMS\Table\Table;

//table class

class YaquizTable extends Table {

    public function __construct(&$db) {
        parent::__construct('#__com_yaquiz_quizzes', 'id', $db);
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