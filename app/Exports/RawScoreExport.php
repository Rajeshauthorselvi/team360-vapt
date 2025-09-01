<?php

namespace App\Exports;

use DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class RawScoreExport implements FromView
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
        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');

        $participant_details1 = DB::table('responses')
        // ->select('responses.text_response','users.id as participant_id','users.fname','users.lname','users.email','user_survey_respondent.id as user_survey_respondent_id','rater.rater','user_survey_respondent.respondent_id')
            ->join('user_survey_respondent', 'user_survey_respondent.id', '=', 'responses.user_survey_respondent_id')
            ->join('users', 'users.id', '=', 'user_survey_respondent.participant_id')
            ->join('rater', 'rater.id', '=', 'user_survey_respondent.rater_id')
            ->where('user_survey_respondent.survey_id', $survey_id)
            ->where('user_survey_respondent.respondent_id', '=', '0')
            ->where('user_survey_respondent.survey_status', '=', '3')
            ->groupBy('user_survey_respondent.id')
            ->orderBy('user_survey_respondent.participant_id')
            ->pluck('users.id as participant_id', DB::raw('CONCAT(fname, " ", lname) AS fname'))
            ->toArray();
        if ($users != "undefined" && isset($users) && $users != 'null') {
            $users = explode(',', $users);
            foreach ($users as $key => $single_par) {
                // dd($user_id);

                $participant_details = DB::table('responses')
                    ->select('users.id as participant_id', 'users.fname as p_fname', 'users.lname as p_lname', 'users.email', 'user_survey_respondent.id as user_survey_respondent_id', 'rater.rater', 'user_survey_respondent.respondent_id')
                    ->join('user_survey_respondent', 'user_survey_respondent.id', '=', 'responses.user_survey_respondent_id')
                    ->join('users', 'users.id', '=', 'user_survey_respondent.participant_id')
                    ->join('rater', 'rater.id', '=', 'user_survey_respondent.rater_id')
                    ->where('user_survey_respondent.survey_id', $survey_id)
                    ->where('user_survey_respondent.participant_id', $single_par)
                    ->where('user_survey_respondent.respondent_id', '=', '0')
                    ->where('user_survey_respondent.survey_status', '=', '3')
                    ->groupBy('user_survey_respondent.id')
                    ->orderBy('user_survey_respondent.participant_id')
                    ->get();

                $respondent_details = DB::table('responses')
                    ->select('users.id as respondent_id', 'users.fname as r_fname', 'users.lname as r_lname', 'users.email', 'user_survey_respondent.id as user_survey_respondent_id', 'rater.rater', 'user_survey_respondent.participant_id')
                    ->join('user_survey_respondent', 'user_survey_respondent.id', '=', 'responses.user_survey_respondent_id')
                    ->join('users', 'users.id', '=', 'user_survey_respondent.respondent_id')
                    ->join('rater', 'rater.id', '=', 'user_survey_respondent.rater_id')
                    ->where('user_survey_respondent.survey_id', $survey_id)
                    ->where('user_survey_respondent.participant_id', $single_par)
                    ->where('user_survey_respondent.respondent_id', '<>', '0')
                    ->where('user_survey_respondent.survey_status', '=', '3')
                    ->groupBy('user_survey_respondent.id')
                    ->orderBy('user_survey_respondent.participant_id')
                    ->get();

                $result = $participant_details->merge($respondent_details);
                $sorted_result = $result;

                $question_dimension = DB::table('questions')
                    ->where('questions.survey_id', $survey_id)
                    ->where('questions.display_order', '>', '0')
                    ->whereNotIn('question_type', ['text', 'textarea'])
                    ->orderBy('questions.display_order')
                    ->groupBy('questions.question_dimension')
                    ->pluck('question_dimension')->toArray();
                $data_dimension = $data_dimension_implode = array();
                foreach ($question_dimension as $key => $dimension) {
                    $question_ids = DB::table('questions')
                        ->where('questions.survey_id', $survey_id)
                        ->where('questions.display_order', '>', '0')
                        ->whereNotIn('question_type', ['text', 'textarea'])
                        ->where('question_dimension', $dimension)
                        ->orderBy('questions.display_order')
                        ->pluck('id')->toArray();
                    $data_dimension[$dimension] = $question_ids;
                    $data_dimension_implode[implode('|', $question_ids)] = $dimension;
                }

                $question_id = array();

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
                        $question_id[] = DB::table('questions')
                            ->select(DB::raw('id,GROUP_CONCAT(id SEPARATOR "|") as question_id,display_order'))
                            ->where('questions.survey_id', $survey_id)
                            ->where('questions.display_order', $each_display_order)
                            ->where('questions.display_order', '>', '0')
                            ->whereNotIn('question_type', ['text', 'textarea'])
                            ->pluck('question_id', 'display_order')->toArray();

                    }
                }

                if (count($sorted_result) > 0) {
                    foreach ($sorted_result as $response) {

                        unset($responses_option_weights);
                        foreach ($data_dimension as $key => $dimensions) {

                            foreach ($dimensions as $key => $quest_id) {
                                $responses_option_weights[] = DB::table('responses')
                                    ->select(DB::raw('responses.*, GROUP_CONCAT(options.option_weight SEPARATOR ",") as option_weight,questions.*'))
                                    ->leftjoin('options', 'responses.option_id', '=', 'options.id')
                                    ->leftjoin('questions', 'responses.question_id', 'questions.id')
                                    ->where('responses.user_survey_respondent_id', $response->user_survey_respondent_id)
                                    ->where('questions.display_order', '>', '0')
                                    ->where('questions.id', $quest_id)
                                    ->whereNotIn('question_type', ['text', 'textarea'])
                                    ->groupBy('questions.id')
                                    ->pluck('option_weight', 'questions.id')->toArray();
                            }

                        }

                        if ($response->respondent_id != 0) {
                            $f_l_name = DB::table('users')->selectRaw('GROUP_CONCAT(COALESCE(CONCAT(fname, " ", lname), fname)) AS user_name')->where('id', $response->participant_id)->value('user_name');
                        } else {
                            $f_l_name = $response->p_fname . ' ' . $response->p_lname;
                        }
                        $responses[$response->user_survey_respondent_id] = [
                            'email' => $response->email,
                            'rater_type' => $response->rater,
                            'username' => $f_l_name,
                            'response' => $responses_option_weights,
                        ];

                    }

                } else {
                    $responses = '';
                }
            }
        } else {

            $participant_ids = DB::table('user_survey_respondent')->where('survey_id', $survey_id)->where('survey_status', 3)->groupBy('participant_id')->pluck('participant_id');

            foreach ($participant_ids as $key => $participant_id) {

                $user_id = '';
                $participant_details = DB::table('responses')
                    ->select('users.id as participant_id', 'users.fname as p_fname', 'users.lname as p_lname', 'users.email', 'user_survey_respondent.id as user_survey_respondent_id', 'rater.rater', 'user_survey_respondent.respondent_id')
                    ->join('user_survey_respondent', 'user_survey_respondent.id', '=', 'responses.user_survey_respondent_id')
                    ->join('users', 'users.id', '=', 'user_survey_respondent.participant_id')
                    ->join('rater', 'rater.id', '=', 'user_survey_respondent.rater_id')
                    ->where('user_survey_respondent.survey_id', $survey_id)
                    ->where('user_survey_respondent.participant_id', $participant_id)
                    ->where('user_survey_respondent.respondent_id', '=', '0')
                    ->where('user_survey_respondent.survey_status', '=', '3')
                    ->groupBy('user_survey_respondent.id')
                    ->orderBy('user_survey_respondent.participant_id')
                    ->get();

                $respondent_details = DB::table('responses')
                    ->select('users.id as respondent_id', 'users.fname as r_fname', 'users.lname as r_lname', 'users.email', 'user_survey_respondent.id as user_survey_respondent_id', 'rater.rater', 'user_survey_respondent.participant_id')
                    ->join('user_survey_respondent', 'user_survey_respondent.id', '=', 'responses.user_survey_respondent_id')
                    ->join('users', 'users.id', '=', 'user_survey_respondent.respondent_id')
                    ->join('rater', 'rater.id', '=', 'user_survey_respondent.rater_id')
                    ->where('user_survey_respondent.survey_id', $survey_id)
                    ->where('user_survey_respondent.participant_id', $participant_id)
                    ->where('user_survey_respondent.respondent_id', '<>', '0')
                    ->where('user_survey_respondent.survey_status', '=', '3')
                    ->groupBy('user_survey_respondent.id')
                    ->orderBy('user_survey_respondent.participant_id')
                    ->get();

                $result = $participant_details->merge($respondent_details);

                $sorted_result = $result;

                $question_dimension = DB::table('questions')
                    ->where('questions.survey_id', $survey_id)
                    ->where('questions.display_order', '>', '0')
                    ->whereNotIn('question_type', ['text', 'textarea'])
                    ->orderBy('questions.display_order')
                    ->groupBy('questions.question_dimension')
                    ->pluck('question_dimension')->toArray();
                $data_dimension = $data_dimension_implode = array();
                foreach ($question_dimension as $key => $dimension) {
                    $question_ids = DB::table('questions')
                        ->where('questions.survey_id', $survey_id)
                        ->where('questions.display_order', '>', '0')
                        ->whereNotIn('question_type', ['text', 'textarea'])
                        ->where('question_dimension', $dimension)
                        ->orderBy('questions.display_order')
                        ->pluck('id')->toArray();
                    $data_dimension[$dimension] = $question_ids;
                    $data_dimension_implode[implode('|', $question_ids)] = $dimension;
                }

                $question_id = array();

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
                        $question_id[] = DB::table('questions')
                            ->select(DB::raw('id,GROUP_CONCAT(id SEPARATOR "|") as question_id,display_order'))
                            ->where('questions.survey_id', $survey_id)
                            ->where('questions.display_order', $each_display_order)
                            ->where('questions.display_order', '>', '0')
                            ->whereNotIn('question_type', ['text', 'textarea'])
                            ->pluck('question_id', 'display_order')->toArray();

                    }
                }

                if (count($sorted_result) > 0) {
                    foreach ($sorted_result as $response) {
                        unset($responses_option_weights);
                        foreach ($data_dimension as $key => $dimensions) {

                            foreach ($dimensions as $key => $quest_id) {
                                $responses_option_weights[] = DB::table('responses')
                                    ->select(DB::raw('responses.*, GROUP_CONCAT(options.option_weight SEPARATOR ",") as option_weight,questions.*'))
                                    ->leftjoin('options', 'responses.option_id', '=', 'options.id')
                                    ->leftjoin('questions', 'responses.question_id', 'questions.id')
                                    ->where('responses.user_survey_respondent_id', $response->user_survey_respondent_id)
                                    ->where('questions.display_order', '>', '0')
                                    ->where('questions.id', $quest_id)
                                    ->whereNotIn('question_type', ['text', 'textarea'])
                                    ->groupBy('questions.id')
                                    ->pluck('option_weight', 'questions.id')->toArray();
                            }

                        }

                        if ($response->respondent_id != 0) {
                            $f_l_name = DB::table('users')->where('id', $response->participant_id)->first();
                            $f_l_name=$f_l_name->fname.' '.$f_l_name->lname;
                        } else {
                            $f_l_name = $response->p_fname . ' ' . $response->p_lname;
                        }
                        
                        $responses[$response->user_survey_respondent_id] = [
                            'email' => $response->email,
                            'rater_type' => $response->rater,
                            'username' => $f_l_name,
                            'response' => $responses_option_weights,
                        ];
                    }
                   
                } else {
                    $responses = [];
                }
            }
        }
        $question_dimension = $data_dimension_implode;
        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');
        return view('admin.report.export_blade.raw_score')
            ->with('raw_score_survey_id', $survey_id)
            ->with('raw_score_survey_name', $survey_name)
            ->with('raw_score_question_dimension', $question_dimension)
            ->with('raw_score_question_id', $question_id)
            ->with('raw_score_responses', $responses);

    }
}
