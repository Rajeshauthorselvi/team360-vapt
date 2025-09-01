<?php

namespace App\Http\Controllers;

use App\Imports\QuestionImport;
use Arr;
use DB;
use Excel;
use Illuminate\Http\Request;
use Response;
use Session;

class QuestionImportController extends Controller
{

    public function create(Request $request)
    {

        $display_order = $request->get('display_order');
        $survey_id = $request->get('survey_id');
        $question_id = $request->get('question_id');
        $question_text = "";
        if (!empty($question_id)) {
            $question_details = DB::table('questions')->find($question_id);
            $question_text = $question_details->question_text;
            $display_order = $question_details->display_order;

        }

        $response = 0;
        $user_survey_respondentids = DB::table('user_survey_respondent')->where('survey_id', $survey_id)->pluck('id');

        foreach ($user_survey_respondentids as $user_survey_respondentid) {
            $response += DB::table('responses')->where('user_survey_respondent_id', $user_survey_respondentid)->count();
        }

        return view('admin.question.addmessages')->with('display_order', $display_order)
            ->with('question_text', $question_text)
            ->with('survey_id', $survey_id)
            ->with('response', $response)
            ->with('question_id', $question_id);

    }

    public function show($id)
    {

        $myFile = public_path('download/questions.xls');

        $headers = ['Content-Type: application/vnd.ms-excel'];

        $newName = 'sample-question-file-' . time() . '.xls';

        return response()->download($myFile, $newName, $headers);
    }

    public function checkEmpty($id, $val, $line)
    {

        $result = [];
        if ($val['question_type'] == "") {
            $result[] = 'Column Question Type found empty at line number ' . $line;
        } else {
            $question_type = ['text', 'textarea', 'radio', 'dropdown', 'checkbox', 'grid'];
            if (!in_array($val['question_type'], $question_type)) {
                $result[] = 'Invalid question type at line number ' . $line . '. Only Allowed values are (text,textarea,radio,dropdown,checkbox). ' . $line;
            } else {
                $result = $this->checknotNull($val['options'], $val['question_type'], $line);
            }
        }

        if ($val['question_text'] == "") {
            $result[] = 'Column Question found empty at line number ' . $line;
        }

        if ($val['question_required'] == "") {
            $result[] = 'Column Question Required found empty at line number ' . $line;
        }

        /*if(isset($val['question_dimension']) && empty($val['question_dimension']))$result[]='Column Question Dimension found empty at line number '.$line;*/

        if ($val['display_order'] == "") {
            $result[] = 'Column Display Order found empty, should only have numeric values greater than 0 at line number ' . $line;
        } else if (!is_numeric($val['display_order'])) {
            $result[] = 'Column Display Order should only have numeric values greater than 0 at line number ' . $line;
        }

        $boolean_values = ['yes', 'no'];
        if (!isset($val['question_required']) && !in_array(strtolower($val['question_required']), $boolean_values)) {
            $result[] = 'Column Question Required should only have boolean values i.e(yes,no) at line number ' . $line;
        }

        $question = $val['question_text'];
        $pos = strpos($question, '|');

        if ($val['question_type'] == 'grid' && $pos == false && $val['question_text'] != '') {
            $result[] = 'You are select Question type is Grid. please enter this format <b>(question1|quesiton2)</b> at line number ' . $line;
        }

        /*Check datas from DB question exists or Not*/
        if ($val['question_type'] == 'grid' && $pos !== false) {
            $explode_question_ex = explode('|', $question);
            foreach ($explode_question_ex as $key => $value) {
                $question_check_db = DB::table('questions')->where('question_text', $value)->where('question_dimension', $val['question_dimension'])->where('survey_id', $id)->exists();
                if ($question_check_db) {
                    $result[] = 'Question already exist for this Question Dimension <b>' . $val['question_dimension'] . '</b> at line number ' . $line;
                }

            }
            /*Check question repetition for same column*/
            $count_array = count($explode_question_ex);
            $unique_count = count(array_unique($explode_question_ex));
            if ($count_array != $unique_count) {
                $result[] = 'Questions are repeated at line number ' . $line;
            }
/*End Check question repetition*/
        } else {
            $exists_question = DB::table('questions')
                ->select('question_text')
                ->where('question_dimension', $val['question_dimension'])
                ->where('survey_id', $id)
                ->get();
            foreach ($exists_question as $check_question) {
                if ($check_question->question_text == $val['question_text']) {
                    $result[] = 'Question already exist for this Qestion Dimension <b>' . $val['question_dimension'] . '</b> at line number ' . $line;
                }

            }
        }

        return $result;

    }

    public function checknotNull($value, $qtype, $line)
    {
        $option_symbol_count = substr_count($value, '|') + 1;
        $optionweight_symbol_count = substr_count($value, '~');

        $result = array();
        switch ($qtype) {
            case 'radio':
                if (is_null($value) or strpos($value, '|') == false) {
                    $result[] = 'Enter the option values seperated by pipe(|) symbol at the line number ' . $line;
                }

                if ($option_symbol_count != $optionweight_symbol_count) {
                    $result[] = "Option field is in invalid format. Please follow this format (opt1~val|opt2~val2) line no" . $line;
                }

                break;
            case 'dropdown':
                if (is_null($value) or strpos($value, '|') == false) {
                    $result[] = 'Enter the option values seperated by pipe(|) symbol at the line number ' . $line;
                }

                if ($option_symbol_count != $optionweight_symbol_count) {
                    $result[] = "Option field is in invalid format. Please follow this format (opt1~val|opt2~val2) line no" . $line;
                }
                break;
            case 'checkbox':
                if (is_null($value) or strpos($value, '|') == false) {
                    $result[] = 'Enter the option values seperated by pipe(|) symbol at the line number ' . $line;
                }

                if ($option_symbol_count != $optionweight_symbol_count) {
                    $result[] = "Option field is in invalid format. Please follow this format (opt1~val|opt2~val2) line no" . $line;
                }

                break;
            case 'text':
                if (strtolower($value) != "null") {
                    $result[] = 'Enter the option values to NULL at the line number ' . $line;
                }

                break;
            case 'textarea':
                if (strtolower($value) != "null") {
                    $result[] = 'Enter the option values to NULL at the line number ' . $line;
                }

                break;

            default:

                break;
        }

        //if(count($result)>0) $result=implode(',', $result);

        return $result;
    }

    public function checkduplicate($question_dimension, $question_text, $key, $data)
    {

        $result = false;

        if ($question_dimension != "" && $question_text != "" && $key != "" && count($data) > 0) {

            foreach ($data as $k => $v) {

                if ($k == $key) {
                    continue;
                }

                if ($v['question_dimension'] == $question_dimension && $v['question_text'] == $question_text) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;

    }
    public function CheckHeading($headings, $key)
    {
        return Arr::has($headings, $key);

    }
    public function store(Request $request)
    {

        if ($request->ajax()) {

            if ($request->hasFile('import_file')) {

                $import = new QuestionImport;
                Excel::import($import, $request->file('import_file'));
                $total_datas = $import->DataContainer();

                $question_text = $this->CheckHeading($total_datas[0], 'question_text');
                $question_type = $this->CheckHeading($total_datas[0], 'question_type');
                $question_required = $this->CheckHeading($total_datas[0], 'question_required');
                $question_dimension = $this->CheckHeading($total_datas[0], 'question_dimension');
                $display_order = $this->CheckHeading($total_datas[0], 'display_order');
                $options = $this->CheckHeading($total_datas[0], 'options');

                $error[] = '';
                $row_count = Session::get('count_row');
                if (count($total_datas) > 0) {
                    $i = 2;
                    $datas = $total_datas;
                    if ($question_text && $question_type && $question_required && $question_dimension && $display_order && $options) {
                        foreach ($datas as $key => $result) {
                            $checkduplicate = $this->checkduplicate($result["question_dimension"], $result["question_text"], $key, $total_datas);
                            $checkEmpty = $this->checkEmpty($request->get('survey_id'), $result, $i);
                            if ($checkduplicate) {
                                $error[] = 'Questions are repeated at line number' . $i;
                            }
                            $error = array_merge($error, $checkEmpty);
                            $i++;
                            if ($key + 2 == $row_count) {
                                break;
                            }
                        }
                    } else {
                        $error[] = "Header Mismatch at line number 1 . Please follow the format(question_text, question_type, question_required,  question_dimension, display_order, options).";
                    }
                } else {
                    $error[] = "No records found";
                }
            } else {
                $error[] = "File not valid";
            }

            $error = array_filter($error);
            if (count($error) == 0) {
                foreach ($total_datas as $key => $result) {

                    $required_enable = '1';

                    $qrequired_flag = (strtolower($result['question_required']) == "yes") ? 1 : 0;
                    $question = $result['question_text'];
                    $pos = strpos($question, '|');
                    if ($pos == false) {
                        $question_value = $result['question_text'];

                        $question_details = [
                            'survey_id' => $request->get('survey_id'),
                            'question_text' => $result['question_text'],
                            'question_type' => $result['question_type'],
                            'question_required' => $qrequired_flag,
                            // 'question_enabled'=>$qrequired_enable,
                            'question_dimension' => $result['question_dimension'],
                            'display_order' => $result['display_order'],
                        ];
                        $question_id = DB::table('questions')->insertGetId($question_details);

                    } else {
                        $question_value = $result['question_text'];
                        $explode_datas = explode('|', $question_value);
                        foreach ($explode_datas as $key => $questions) {

                            $question_details = [
                                'survey_id' => $request->get('survey_id'),
                                'question_text' => $questions,
                                'question_type' => $result['question_type'],
                                'question_required' => $qrequired_flag,
                                // 'question_enabled'=>$qrequired_enable,
                                'question_dimension' => $result['question_dimension'],
                                'display_order' => $result['display_order'],
                            ];

                            $question_id = DB::table('questions')->insertGetId($question_details);
                            $option = explode('|', $result['options']);

                            foreach ($option as $value) {
                                $optionte_optionwe = explode('~', $value);
                                $option_details = [
                                    'option_text' => $optionte_optionwe[0],
                                    'option_weight' => $optionte_optionwe[1],
                                    'question_id' => $question_id,
                                ];
                                DB::table('options')->insert($option_details);
                            }
                        }
                    }

                    //Insertion of options

                    if (strtolower($result['options']) == "null") {
                        $option_details = [
                            'option_text' => 'null',
                            'option_weight' => '0',
                            'question_id' => $question_id,
                        ];
                        DB::table('options')->insert($option_details);
                    } else {
                        if ($pos == false) {
                            $option = explode('|', $result['options']);

                            foreach ($option as $value) {
                                $optionte_optionwe = explode('~', $value);
                                $option_details = [
                                    'option_text' => $optionte_optionwe[0],
                                    'option_weight' => $optionte_optionwe[1],
                                    'question_id' => $question_id,
                                ];
                                DB::table('options')->insert($option_details);
                            }
                        }

                    }
                    //end of option insertion

                }

                $response = array(
                    'status' => 'success',
                    'success' => 'All the questions are imported successfully!',
                    'error' => '',
                );
                return \Response::json($response);
            }
            $response = array(
                'status' => 'success',
                'success' => '',
                'error' => $error,
            );
            return \Response::json($response);

        }

    }

}
