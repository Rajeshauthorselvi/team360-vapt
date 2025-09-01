<?php

namespace App\Exports;

use DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromArray;
class RawscoreQuestionExport implements FromArray
{
    /**
    * @return \Illuminate\Support\Collection
    */
    public function __construct($data)
    {
        $this->survey_id = $data;
    }
    public function array(): array
    {
        $survey_id = $this->survey_id;

        $question_dimension = DB::table('questions')
            ->select(DB::raw('id,GROUP_CONCAT(id SEPARATOR "|") as question_id,question_dimension'))
            ->where('questions.survey_id', $survey_id)
            ->where('questions.display_order', '>', '0')
            ->whereNotIn('question_type', ['text', 'textarea'])
            ->groupBy('questions.question_dimension')
            ->orderBy('questions.id')
            ->pluck('question_dimension', 'question_id')->toArray();

        foreach ($question_dimension as $each_question_dimension) {
            $display_orders = DB::table('questions')
                ->select('id', 'display_order')
                ->where('questions.survey_id', $survey_id)
                ->where('questions.question_dimension', $each_question_dimension)
                ->where('questions.display_order', '>', '0')
                ->whereNotIn('question_type', ['text', 'textarea'])
                ->groupBy('questions.display_order')
                ->orderBy('questions.id')
                ->pluck('display_order')->toArray();

            foreach ($display_orders as $each_display_order) {
                $questions[] = DB::table('questions')
                    ->select('*', DB::raw("GROUP_CONCAT(question_text SEPARATOR '|') AS question_text"))
                    ->where('questions.survey_id', $survey_id)
                    ->where('questions.display_order', $each_display_order)
                    ->where('questions.display_order', '>', '0')
                    ->whereNotIn('question_type', ['text', 'textarea'])
                    ->get()->toArray();

            }
            $key = 0;
            $letters = range('A', 'Z');
            $d = array();
            foreach ($questions as $key => $each_question) {
                foreach ($each_question as $keys => $question) {
                    $kkey = 0;
                    $question_text = explode('|', $question->question_text);
                    $question_dimension = $question->question_dimension;
                    if (count($question_text) > 1) {
                        foreach ($question_text as $k => $v) {
                            $qtext = $letters[$k] . ") ";
                            if ($k == 0) $kkey = $key + 1;
                            {
                                $question_arr['Question No'] = $kkey . '(' . $qtext;
                                $question_arr['Question Dimension'] = strip_tags($question_dimension);
                                $question_arr['Question Text'] = strip_tags(trim($v));
                                array_push($d, $question_arr);
                            }
                        }
                    } else {

                        $question_arr['Question No'] = $key + 1 . ' ';
                        $question_arr['Question Dimension'] = strip_tags($question_dimension);
                        $question_arr['Question Text'] = strip_tags(trim($question->question_text));
                        array_push($d, $question_arr);
                    }
                }
            }
        }
        return $d;
    }
}
