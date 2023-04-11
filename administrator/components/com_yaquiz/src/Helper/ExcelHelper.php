<?php
/*
 * @copyright   (C) 2023 KevinsGuides.com
 * @license     GNU General Public License version 2 or later;
*/


namespace KevinsGuides\Component\Yaquiz\Administrator\Helper;

defined ( '_JEXEC' ) or die;
$autoload = JPATH_ROOT . '/administrator/components/com_yaquiz/vendor/autoload.php';
require_once $autoload;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;



//this helper uses PhpSpreadsheet to read an Excel file and return an array of questions

class ExcelHelper{

    public function __construct(){
        //nothing to do here
    }

    public function loadQuestions($file){
        $reader = new Xlsx();
        $spreadsheet = $reader->load($file);
        $worksheet = $spreadsheet->getActiveSheet();
        $rows = $worksheet->toArray();

        if(count($rows) < 2){
            return false;
        }
        if(count($rows[0]) != 6){
            return false;
        }
        //if too many rows
        if(count($rows) > 100){
            return false;
        }

        //loop through all rows after the first
        $questions = array();
        $i = 0;
        foreach($rows as $row){
            if($i > 0){
                $question = array();
                $question['question'] = $row[0];
                $question['details'] = $row[1];
                $question['type'] = $row[2];
                $question['answers'] = $row[3];
                $question['answers'] = explode('|', $question['answers']);
                $question['correct'] = $row[4];
                $question['pointvalue'] = $row[5];
                $questions[] = $question;
            }
            $i++;
        }
        return $questions;

    }

    public function getQuestionsPreview($questions){
        //this function displays all the questions in a table
        $html = '<table class="table table-striped">';
        $html .= '<thead>
        <tr>
            <th>Question</th>
            <th>Details</th>
            <th>Type</th>
            <th>Answers</th>
            <th>Correct Value(s)</th>
            <th>Point Value</th>
        </tr></thead><tbody>';
        foreach($questions as $question){

            if($question['type'] == 'mchoice'){
                $answers = $question['answers'];
                $previewAnswers = '<ul>';
                foreach ($answers as $panswer){
                    $previewAnswers .= '<li>' . $panswer . '</li>';
                }
                $previewAnswers .= '</ul>';

                //correct answer is the index of the correct answer
                $correct = $question['correct'];
                $correct = $answers[$correct];
            }

            else if($question['type'] == 'shortans'){
                $answers = $question['answers'];
                $previewAnswers = '<ul>';
                foreach ($answers as $panswer){
                    $previewAnswers .= '<li>' . $panswer . '</li>';
                }
                $previewAnswers .= '</ul>';
                $correct = 'All answers listed are correct';

            }

            else if($question['type'] == 'tf'){
                if($question['correct'] == 't'){
                    $correct = 'True';
                }
                else{
                    $correct = 'False';
                }
                $previewAnswers = 'True or False';
            }

            else {
                $question['question'] = '<span class="badge bg-danger text-white">ERROR</span>'.$question['question'];
                $question['type'] = 'Unknown';
                $previewAnswers = 'Unknown';
                $correct = 'Unknown';
            }


            $html .= '<tr>';
            $html .= '<td>' . $question['question'] . '</td>';
            $html .= '<td>' . $question['details'] . '</td>';
            $html .= '<td>' . $question['type'] . '</td>';
            $html .= '<td>' . $previewAnswers . '</td>';
            $html .= '<td>' . $correct . '</td>';
            $html .= '<td>' . $question['pointvalue'] . '</td>';
            $html .= '</tr>';
        }
        $html .= '</tbody></table>';
        return $html;

    }

}