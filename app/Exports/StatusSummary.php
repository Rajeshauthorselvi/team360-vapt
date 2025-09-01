<?php

namespace App\Exports;

use DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class StatusSummary implements FromView
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
        $survey_id = $this->survey_id['survey_id'];

        $all_participant = DB::table('users')
            ->select('users.email', 'users.id', 'users.fname', 'users.lname', 'user_survey_respondent.survey_status')
            ->join('user_survey_respondent', 'users.id', '=', 'user_survey_respondent.participant_id')
            ->where('users.id', '>', 1)
            ->where('user_survey_respondent.survey_id', $survey_id)
            ->where('user_survey_respondent.respondent_id', '=', 0)
            ->where('users.email', 'not like', '%kjsaneesh%')
            ->where('users.email', 'not like', '%saneesh%')
            ->where('users.email', 'not like', '%ascendus%')
            ->where('users.email', 'not like', '%authorselvi%')
            ->get()->toArray();

        $total_summary = array();
        $survey_raters = DB::table('survey_rater as sr')
            ->leftjoin('rater as r', 'r.id', 'sr.rater_id')
            ->where('survey_id', $survey_id)
            ->where('r.rater', '<>', 'Self')
            ->pluck('r.rater', 'r.id');

        foreach ($all_participant as $key => $participant) {

            $temp_summary = [
                'name' => $participant->fname . ' ' . $participant->lname,
                'email' => $participant->email,
                'self_status' => $participant->survey_status
            ];
            foreach ($survey_raters as $rater_id => $rater) {
                $completed_respondent = DB::table('user_survey_respondent')
                    ->where('survey_id', $survey_id)
                    ->where('rater_id', $rater_id)
                    ->where('survey_status', 3)
                    ->where('participant_id', $participant->id)
                    ->where('respondent_id', '<>', 0)
                    ->count();
                $total_respondent = DB::table('user_survey_respondent')
                    ->where('survey_id', $survey_id)
                    ->where('rater_id', $rater_id)
                    ->where('participant_id', $participant->id)
                    ->where('respondent_id', '<>', 0)
                    ->count();

                $temp_summary['raters_completed'][$rater_id] = [
                    'completed' => $completed_respondent,
                    'total_respondent' => $total_respondent,
                ];
            }

            array_push($total_summary, $temp_summary);
        }
        $data = array();
        $data['summary_report'] = $total_summary;
        $data['all_raters'] = $survey_raters;
        $data['survey_name'] = DB::table('surverys')->where('id', $survey_id)->value('title');
        $data['survey_id'] = $survey_id;
        $data['title'] = "Status Summary";
        return view('admin.report.export_blade.status_summary', $data);
    }
}
