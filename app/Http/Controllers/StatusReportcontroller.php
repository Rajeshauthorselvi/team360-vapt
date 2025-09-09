<?php

namespace App\Http\Controllers;

use App\Exports\RawscoreQuestionExport;
use DB;
use Excel;
use Illuminate\Http\Request;

class StatusReportcontroller extends Controller
{
    public function ReportController(Request $request)
    {

        $survey_id = $request->get('survey_id');

        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');

        $user_id = array();
        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');

        $participant_details = $this->GetStatusSurveyDetails($survey_id, 0);

        $result_set = array();
        if (count($participant_details) > 0) {

            foreach ($participant_details as $participant) {

                $respondent_details = $this->GetStatusSurveyDetails($survey_id, 1, $participant->participant_id, 'status');
                array_push($result_set, $participant);

                if (count($respondent_details) > 0) {
                    foreach ($respondent_details as $respondent) {
                        array_push($result_set, $respondent);
                    }
                }

            }

        }

        return view('admin.report.survey_status')
            ->with('survey_name', $survey_name)
            ->with('survey_details', $result_set)
            ->with('survey_id', $survey_id)
            ->with('title', 'Survey Status');

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
                    ->where('responses.text_response', '!=', '')
                    ->where('responses.question_id', $each_value)
                    ->orderBy('questions.display_order')
                    ->pluck('responses.text_response', 'questions.id')->toArray();

            }
        }

        return $result;
    }
    public function GetStatusSurveyDetails($survey_id, $role = 0, $participant_id = '', $status = null)
    {

        $result = array();

        $user_details = DB::table('user_survey_respondent');

        if ($role == 0) {
            $user_details->join('users', 'users.id', '=', 'user_survey_respondent.participant_id');
        }

        if ($role == 1) {
            $user_details->join('users', 'users.id', '=', 'user_survey_respondent.respondent_id');
        }

        $user_details->join('users as par', 'par.id', '=', 'user_survey_respondent.participant_id');

        $user_details->join('rater', 'rater.id', '=', 'user_survey_respondent.rater_id');

        // $user_details->where('survey_status',3);

        $user_details->where('survey_id', $survey_id);

        if ($role == 0) {
            $user_details->where('respondent_id', 0);
        }

        if (is_array($participant_id) && count($participant_id) > 0) {
            $user_details->whereIn('participant_id', $participant_id);
        } else if ($participant_id != "") {
            $user_details->where('participant_id', $participant_id);
        }

        $user_details->select('participant_id', 'respondent_id', 'user_survey_respondent.id as user_survey_id', 'rater.rater','users.fname','users.lname', DB::raw('CONCAT(users.fname, " ", users.lname) AS user_name'), 'users.email', 'user_survey_respondent.survey_id', 'user_survey_respondent.survey_status', 'users.fname', 'users.lname', 'last_submitted_date', 'notify_email_date','par.fname as par_fname','par.lname as par_lname', DB::raw('CONCAT(par.fname, " ", par.lname) AS part_name'));

        if ($role == 0) {
            $user_details->orderBy('participant_id');
        }

        if ($role == 1) {
            $user_details->orderBy('rater.id');
        }

        $result = $user_details->get();

        return $result;
    }
    public function GetUserSurveyDetails($survey_id, $role = 0, $participant_id = '')
    {

        // dd($status);
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

        $user_details->select('participant_id', 'respondent_id', 'user_survey_respondent.id as user_survey_id', 'rater.rater','fname','lname', DB::raw('CONCAT(users.fname, " ", users.lname) AS user_name'), 'users.email', 'user_survey_respondent.survey_id')
            ->orderBy('participant_id', 'ASC')
            ->orderBy('respondent_id', 'ASC');
        $result = $user_details->get();

        return $result;
    }

    public function textresponseIndex(Request $request)
    {
        $survey_id = $request->get('survey_id');

        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');

        $filter_participants = DB::table('user_survey_respondent')
            ->join('users', 'users.id', '=', 'user_survey_respondent.participant_id')
            ->where('survey_status', 3)->where('respondent_id', 0)->where('survey_id', $survey_id)->select('participant_id','fname','lname', DB::raw('CONCAT(fname, " ", lname) AS participant_name'))->pluck('participant_name', 'participant_id')->toArray();

        if ($request->has('users')) {
            $participant_ids = $request->get('users');
            $participant_details = $this->GetUserSurveyDetails($survey_id, 0, $participant_ids);
            $user_id = $participant_ids;
        } else {
            $participant_details = $this->GetUserSurveyDetails($survey_id, 0);
            $user_id = '';
        }

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

        return view('admin.report.text_response')
            ->with('responses', $result_set)
            ->with('survey_name', $survey_name)
            ->with('survey_id', $survey_id)
            ->with('user_name', $user_id)
            ->with('filter_participants', $filter_participants)
            ->with('question_dimensions', $question_dimensions)
            ->with('question_id', $question_id)
            ->with('title', 'Text Response');

    }

    public function RawscoreController(Request $request)
    {

        $survey_id = $request->get('survey_id');
        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');

        $participant_details1 = DB::table('responses')
        // ->select('responses.text_response','users.id as participant_id','users.fname','users.lname','users.email','user_survey_respondent.id as user_survey_respondent_id','rater.rater','user_survey_respondent.respondent_id')
            ->join('user_survey_respondent', 'user_survey_respondent.id', '=', 'responses.user_survey_respondent_id')
            ->join('users', 'users.id', '=', 'user_survey_respondent.participant_id')
            ->join('rater', 'rater.id', '=', 'user_survey_respondent.rater_id')
            ->where('user_survey_respondent.survey_id', $survey_id)
            ->where('user_survey_respondent.respondent_id', '=', '0')
        // ->where('user_survey_respondent.survey_status', '=', '3')
            ->groupBy('user_survey_respondent.id')
            ->orderBy('user_survey_respondent.participant_id')
            ->pluck('users.id as participant_id', DB::raw('CONCAT(fname, " ", lname) AS fname'))
            ->toArray();

        // var_dump($request->get('users'));

        // $f_l_name = DB::table('users')->selectRaw('GROUP_CONCAT(COALESCE(CONCAT(fname, " ", lname), fname)) AS user_name')->where('id', $response->participant_id)->value('user_name');
        $participant_details1 = DB::table('responses')
        // ->selectRaw('GROUP_CONCAT(COALESCE(CONCAT(fname, " ", lname), fname)) AS user_name,users.id as participant_id')
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
            $responses =$question_id=$user_id=$participant_details= array();
            if ($request->get('users')) {
                $user_id = $request->get('users');
                foreach ($user_id as $key => $single_par) {

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
                            $f_l_name = DB::table('users')->where('id', $response->participant_id)->value(DB::raw('CONCAT(fname, " ",lname) AS user_name'));
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

        $question_dimension = isset($data_dimension_implode)?$data_dimension_implode:array();

        return view('admin.report.rawscore')
            ->with('responses', $responses)
            ->with('question_dimension', $question_dimension)
            ->with('question_id', $question_id)
            ->with('survey_name', $survey_name)
            ->with('survey_id', $survey_id)
            ->with('user_name', $user_id)
            ->with('participant_details', $participant_details)
            ->with('participant_id', $participant_details1)
            ->with('title', 'Raw Score');

    }
    public function download_raw_score_QuesController(Request $request)
    {
        $survey_id = $request->get('survey_id');
        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');
        $sheet_name = 'Question-' . str_replace(' ', '', $survey_name) . '-' . strtotime("now");
        return Excel::download(new RawscoreQuestionExport($survey_id), $sheet_name . '.xlsx');

    }
    public function download_text_response_QuesController(Request $request)
    {
        $survey_id = $request->get('survey_id');
        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');
        $sheet_name = 'Question-' . str_replace(' ', '', $survey_name) . '-' . strtotime("now");
        return Excel::download(new RawscoreQuestionExport($survey_id), $sheet_name . '.xlsx');
    }
    public function StatusSummaryController(Request $request)
    {
        $survey_id = $request->survey_id;

        $all_participant = DB::table('users')
            ->select('users.email', 'users.id', 'users.fname', 'users.lname', 'user_survey_respondent.survey_status')
            ->join('user_survey_respondent', 'users.id', '=', 'user_survey_respondent.participant_id')
            ->where('users.id', '>', 1)
            ->where('user_survey_respondent.survey_id', $survey_id)
            ->where('user_survey_respondent.respondent_id', '=', 0)
            ->get()->toArray();

        $total_summary = array();
        $survey_raters = DB::table('survey_rater as sr')
            ->leftjoin('rater as r', 'r.id', 'sr.rater_id')
            ->where('survey_id', $survey_id)
            ->where('r.rater','<>','Self')
            ->pluck('r.rater', 'r.id');

        foreach ($all_participant as $key => $participant) {

            $temp_summary = [
                'name' => $participant->fname . ' ' . $participant->lname,
                'email' => $participant->email,
                'self_status'=>$participant->survey_status
            ];
            foreach ($survey_raters as $rater_id => $rater) {
                $completed_respondent = DB::table('user_survey_respondent')
                    ->where('survey_id', $survey_id)
                    ->where('rater_id',$rater_id)
                    ->where('survey_status', 3)
                    ->where('participant_id',$participant->id)
                    ->where('respondent_id','<>',0)
                    ->count();
                $total_respondent = DB::table('user_survey_respondent')
                    ->where('survey_id', $survey_id)
                    ->where('rater_id',$rater_id)
                    ->where('participant_id',$participant->id)
                    ->where('respondent_id','<>',0)
                    ->count();

                $temp_summary['raters_completed'][$rater_id]=[
                    'completed'=>$completed_respondent,
                    'total_respondent'=>$total_respondent,
                ];
            }

            array_push($total_summary,$temp_summary);
        }
        $data=array();
        $data['summary_report']=$total_summary;
        $data['all_raters']=$survey_raters;
        $data['survey_name'] = DB::table('surverys')->where('id', $survey_id)->value('title');
        $data['survey_id']=$survey_id;
        $data['title']="Status Summary";
        return view('admin.report.status_summary',$data);
    }
}
