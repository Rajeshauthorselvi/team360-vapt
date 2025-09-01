<?php

namespace App\Exports;

use DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class UsersPasswordExport implements FromView
{
    /**
     * @return \Illuminate\Support\Collection
     */
    public function __construct($data)
    {
        $this->survey_id = $data;
    }
    public function view(): View
    {
        $survey_id = $this->survey_id;
        $user_id = array();
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
        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');
        return view('admin.report.export_blade.export_users_password')
            ->with('survey_details', $result_set)
            ->with('survey_name', $survey_name)
            ->with('survey_id', $survey_id);
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

        $user_details->select('participant_id', 'respondent_id', 'user_survey_respondent.id as user_survey_id', 'rater.rater', DB::raw('CONCAT(users.fname, " ", users.lname) AS user_name'), 'users.email', 'user_survey_respondent.survey_id', 'user_survey_respondent.survey_status', 'users.fname', 'users.lname', 'last_submitted_date', 'notify_email_date', 'users.password');

        if ($role == 0) {
            $user_details->orderBy('participant_id');
        }

        if ($role == 1) {
            $user_details->orderBy('rater.id');
        }

        $result = $user_details->get();

        return $result;
    }
}
