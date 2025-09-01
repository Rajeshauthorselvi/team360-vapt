<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Contracts\View\View;
use DB;
class TextResponseExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct($data)
    {
        $this->data = $data;
    }
    public function view(): View
    {
        $survey_id = $this->data['survey_id'];
        $users = $this->data['users'];

        if ($users != "undefined" && isset($users) && $users != 'null') {
            $participant_ids = $users;
            $participant_details = $this->GetUserSurveyDetails($survey_id, 0, $participant_ids);
            $user_id = $participant_ids;
        } else {
            $participant_details = $this->GetUserSurveyDetails($survey_id, 0);
            $user_id = '';
        }
        $question_id = array();

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

        foreach ($question_dimensions as $each_question_dimension) {
            $question_id[$each_question_dimension] = DB::table('questions')
                ->select(DB::raw('questions.id,questions.display_order'))
                ->join('options', 'options.question_id', '=', 'questions.id')
                ->where('questions.survey_id', $survey_id)
                ->where('questions.question_dimension', $each_question_dimension)
                ->where('questions.display_order', '>', '0')
                ->where('options.option_weight', '=', '0')
                ->groupBy('question_id')
                ->pluck('questions.id', 'display_order')->toArray();
        }
        $result_set = array();
        if (count($participant_details) > 0) {

            foreach ($participant_details as $participant) {
                $participant->responses = $this->GetUserTextResponse($participant->user_survey_id, $question_id);

                $respondent_details = $this->GetUserSurveyDetails($survey_id, 1, $participant->participant_id);

                array_push($result_set, $participant);

                if (count($respondent_details) > 0) {
                    foreach ($respondent_details as $respondent) {
                        $respondent->responses = $this->GetUserTextResponse($respondent->user_survey_id, $question_id);

                        array_push($result_set, $respondent);
                    }
                }

            }
        }
        return view('admin.report.export_blade.text_response')
            ->with('text_responses', $result_set)
            ->with('question_id', $question_id)
            ->with('text_question_dimension', $question_dimensions);
    }
    public function GetUserTextResponse($user_survey_id, $question_id)
    {

        $result = array();
        foreach ($question_id as $dimension => $value) {
            foreach ($value as $each_value) {
                $result[] = DB::table('responses')
                    ->join('questions', 'responses.question_id', 'questions.id')
                    ->join('options', 'options.question_id', '=', 'questions.id')
                    ->where('responses.user_survey_respondent_id', $user_survey_id)
                    ->where('responses.question_id', $each_value)
                    ->where('responses.text_response', '!=', '')
                    ->orderBy('questions.display_order')
                    ->pluck('responses.text_response', 'questions.id')->toArray();

            }
        }

        return $result;
    }
    public function GetUserSurveyDetails($survey_id, $role = 0, $participant_id = '')
    {

        $result = array();
        $user_details = DB::table('user_survey_respondent');
        if ($role == 0) {
            $user_details->join('users', 'users.id', '=', 'user_survey_respondent.participant_id');
        }

        if ($role == 1) {
            $user_details->join('users', 'users.id', '=', 'user_survey_respondent.respondent_id');
        }

        $user_details->join('rater', 'rater.id', '=', 'user_survey_respondent.rater_id');

        if ($participant_id != 0) {
            $user_details->where('survey_status', 3);
        }

        $user_details->where('survey_id', $survey_id);

        if ($role == 0) {
            $user_details->where('respondent_id', 0);
        }

        if (is_array($participant_id) && count($participant_id) > 0) {
            $user_details->whereIn('participant_id', $participant_id);
        } else if ($participant_id != "") {
            $user_details->where('participant_id', $participant_id);
        }

        $user_details->select('participant_id', 'respondent_id', 'user_survey_respondent.id as user_survey_id', 'rater.rater', 'users.fname','users.lname' ,DB::raw('CONCAT(users.fname, " ", users.lname) AS user_name'), 'users.email', 'user_survey_respondent.survey_id')
            ->orderBy('participant_id','ASC')
            ->orderBy('respondent_id','ASC');

        $result = $user_details->get();

        return $result;
    }
}
