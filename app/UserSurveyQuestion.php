<?php

namespace App;

use DB;
use Illuminate\Database\Eloquent\Model;

class UserSurveyQuestion extends Model
{
    //

    public static function getQuestions($survey_id = 0, $rater_id = 0, $textarea = 0)
    {

        $questions = self::Questions($survey_id, $rater_id, $textarea);
        foreach ($questions as $key => $question) {

            $exploded = explode('|', $question->question_id);

            if ($question->question_type == "grid") {

                foreach ($exploded as $key => $iquestion_id) {

                    $option_values[$iquestion_id] = DB::table('options')->where('question_id', $iquestion_id)->orderBy('option_weight', 'ASC')->pluck('id', 'option_text');
                }
                $question->optionth = DB::table('options')->where('question_id', $iquestion_id)->orderBy('option_weight', 'asc')->pluck('option_text');
            } else {
                $option_values = DB::table('options')
                    ->orderBy('id', 'ASC')
                    ->where('question_id', $exploded)
                    ->pluck('id', 'option_text');

                $question->optionth = DB::table('options')
                    ->where('question_id', $exploded)
                    ->orderBy('option_weight', 'asc')
                    ->pluck('option_text');

                // $option_values=DB::table('options')->orderBy('id','ASC')->whereIn('question_id',$exploded)->get();
            }

            $question->options = $option_values;

            unset($option_values);

        }

        // if (config('site.theme') == "Dimension_Wise_Page") {

        //     $questions = self::DimensionBasedQuestions($questions);

        // }
        return $questions;
    }

    public static function DimensionBasedQuestions($questions)
    {
        $data = array();

        foreach ($questions as $key => $question) {
            $data[$question->question_dimension][] = $question;
        }
        // dd($data);
        return $data;
    }

    public static function Questions($survey_id = 0, $rater_id = 0, $textarea = 0)
    {

        $query = DB::table('questions')
            ->select(DB::raw('surverys.id,GROUP_CONCAT(question_text order by question_id ASC SEPARATOR "|") as question_text,GROUP_CONCAT(questions.id SEPARATOR "|") as question_id,questions.question_type,questions.question_required,questions.question_dimension,questions.question_sub_dimension,questions.question_preamble'), 'display_order')
            ->join('surverys', 'questions.survey_id', '=', 'surverys.id')
            ->join('question_grouping', 'question_grouping.question_id', '=', 'questions.id')
            ->where('questions.survey_id', $survey_id)
            ->where('question_grouping.rater_id', $rater_id)
            ->where('display_order', '>', 0); //added "order by question_id in question_text concat"

        if (config('site.theme') != "Dimension_Wise_Page" && config('site.theme') != "Page_Wise") {
            if ($textarea == 1) {
                $query->where('questions.question_type', 'textarea');
            } else {
                $query->where('questions.question_type', '<>', 'textarea');
            }
            $query->orderBy('display_order', 'DESC');
        } else {
            //$query->orderBy('questions.id','ASC'); //Code commented by Raj
            if ($textarea == 1) {
                $query->where('questions.question_type', 'textarea');
            } else {
                $query->where('questions.question_type', '<>', 'textarea');
            }
        }

        if (config('site.shuffle_questions') == 1) {
            $query->orderBy(DB::raw('RAND()'));
        }

        // else $query->orderBy('display_order','ASC');

        $questions = $query->groupBy('display_order')->get();
        //$questions=$query->groupBy('display_order')->toSql();
        //dd($questions);
        return $questions;

    }
    public static function getWelcomeText($survey_id = 0)
    {

        $welcome_text = DB::table('questions')->where('survey_id', $survey_id)->where('display_order', 0)->value('question_text');
        return $welcome_text;

    }

    public static function getUserSurveyId($survey_id = 0, $participant_id, $rater, $user_id = 0)
    {

        $respondent_id = 0;
        if (($participant_id > 0 and $user_id > 0) and $participant_id != $user_id) {
            $respondent_id = $user_id;
        }

        $user_survey_respondent_id = DB::table('user_survey_respondent')->where('participant_id', $participant_id)->where('respondent_id', $respondent_id)->where('rater_id', $rater)->where('survey_id', $survey_id)->value('id');
        return $user_survey_respondent_id;
    }

    public static function getUserSurveyResponses($user_survey_respondent_id = 0)
    {
        $responses = DB::table('responses')->where('user_survey_respondent_id', $user_survey_respondent_id)->pluck('question_id', 'id')->toArray();
        return $responses;
    }

    public static function getUserSurveyResponsesCount($user_survey_respondent_id = 0, $survey_id = 0)
    {

        $response_not_grid = DB::table('responses')->join('questions', 'questions.id', '=', 'responses.question_id')
            ->select(DB::raw('GROUP_CONCAT(question_text,"") as question_text,GROUP_CONCAT(questions.id,"") as question_id,questions.question_type,questions.question_required,questions.question_dimension'))
            ->groupBy('display_order')
            ->where('questions.question_type', '!=', 'grid')
            ->where('user_survey_respondent_id', $user_survey_respondent_id)->get()->toArray();

        $count_not_grid = count($response_not_grid);

        $response_grid = DB::table('responses')->join('questions', 'questions.id', '=', 'responses.question_id')
            ->select(DB::raw('GROUP_CONCAT(questions.id  ORDER BY  questions.id ASC) as question_id '))
            ->groupBy('display_order')->where('questions.question_type', '=', 'grid')
            ->where('user_survey_respondent_id', $user_survey_respondent_id)
            ->pluck('question_id')->toArray();

        $questions_grid = DB::table('questions')->join('surverys', 'questions.survey_id', '=', 'surverys.id')->select(DB::raw('GROUP_CONCAT(questions.id ORDER BY  questions.id ASC) as question_id', 'orderBy  questions.question_id'))
            ->groupBy('display_order')
            ->where('questions.survey_id', $survey_id)
            ->where('questions.question_type', 'grid')
            ->pluck('question_id')->toArray();

        $fully_answered_grid = array_intersect($questions_grid, $response_grid);
        $fully_answered_grid_count = count($fully_answered_grid);

        $response_count = $count_not_grid + $fully_answered_grid_count;

        return $response_count;

    }
}
