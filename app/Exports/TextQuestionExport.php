<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromArray;

class TextQuestionExport implements FromArray
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct($data)
    {
        $this->survey_id = $data;
    }
    function array(): array
    {
        $survey_id = $this->survey_id;
        $question_dimensions = DB::table('questions')
            ->select(DB::raw('questions.id,questions.question_dimension'))
            ->join('options', 'options.question_id', '=', 'questions.id')
            ->where('questions.survey_id', $survey_id)
            ->where('questions.display_order', '>', '0')
            ->where('options.option_weight', '=', '0')
        // ->whereIN('options.option_text',['others'])
            ->whereNOTIn('question_type', ['checkbox', 'grid', 'dropdown'])
            ->groupBy('questions.question_dimension')
            ->orderBy('questions.id')
            ->pluck('question_dimension', 'questions.id')->toArray();
        $question_id = array();
        $questions = array();
        foreach ($question_dimensions as $each_question_dimension) {
            $question_id = DB::table('questions')
                ->select(DB::raw('questions.id,questions.display_order'))
                ->join('options', 'options.question_id', '=', 'questions.id')
                ->where('questions.survey_id', $survey_id)
                ->where('questions.question_dimension', $each_question_dimension)
                ->where('questions.display_order', '>', '0')
                ->where('options.option_weight', '=', '0')
                ->groupBy('question_id')
                ->pluck('questions.id', 'display_order')->toArray();

            foreach ($question_id as $each_question_id) {
                $questions[] = DB::table('questions')
                    ->select('*', DB::raw("GROUP_CONCAT(question_text SEPARATOR '|') AS question_text"))
                    ->where('questions.survey_id', $survey_id)
                    ->where('questions.id', $each_question_id)
                    ->where('questions.display_order', '>', '0')
                    ->get()->toArray();

            }
        }
        $d = [];
        foreach ($questions as $key => $each_question) {
            foreach ($each_question as $keys => $question) {

                $question_text = $question->question_text;
                $question_dimension = $question->question_dimension;

                $question_arr['Question No'] = $key + 1 . ' ';
                $question_arr['Question Dimension'] = strip_tags($question_dimension);
                $question_arr['Question Text'] = strip_tags(trim($question->question_text));
                array_push($d, $question_arr);

            }
        }

        return $d;
    }
}
