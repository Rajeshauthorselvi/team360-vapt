<?php

namespace App\Exports;

use DB;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AllQuestionExport implements FromArray, WithHeadings
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
        $questions = DB::table('questions')->select('*', DB::raw("GROUP_CONCAT(question_text SEPARATOR '|') AS question_text"))
            ->where('survey_id', $survey_id)
            ->where('display_order', '>', '0')
            ->orderBy('display_order')
            ->groupBy('display_order')
            ->get();
        $question_arr = [];
        $letters = range('A', 'Z');
        foreach ($questions as $key => $question) {
            $kkey = '';
            $question_text = explode('|', $question->question_text);
            if (count($question_text) > 1) {
                foreach ($question_text as $k => $v) {
                    $qtext = $letters[$k] . ") " . trim($v);
                    if ($k == 0) {
                        $kkey = $key + 1;
                    }

                    $question_arr[] = [$kkey, trim($qtext)];
                }
            } else {
                $question_arr[] = [$key + 1, trim($question->question_text)];
            }
        }
        return $question_arr;
    }
    public function headings(): array
    {
        return [
            'Question No',
            'Question Text',

        ];
    }
}
