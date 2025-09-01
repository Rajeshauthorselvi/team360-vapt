<?php

namespace App\Http\Controllers;

use App\Exports\RawScoreExport;
use App\Exports\StatusExport;
use App\Exports\TextResponseExport;
use App\Exports\StatusSummary;
use DB;
use Excel;
use Illuminate\Http\Request;

class ExportStatusController extends Controller
{
    public function ExportStatus(Request $request)
    {

        $survey_id = $request->get('survey_id');
        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');
        $sheet_name = 'Status_Report-' . str_replace(' ', '', $survey_name) . '-' . strtotime("now");
        return Excel::download(new StatusExport($survey_id), $sheet_name . '.xlsx');
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
        $user_details->where('users.email', 'not like', "%ascendus%");

        if ($role == 0) {
            $user_details->where('respondent_id', 0);
        }

        if (is_array($participant_id) && count($participant_id) > 0) {
            $user_details->whereIn('participant_id', $participant_id);
        } else if ($participant_id != "") {
            $user_details->where('participant_id', $participant_id);
        }

        $user_details->select('participant_id', 'respondent_id', 'user_survey_respondent.id as user_survey_id', 'rater.rater', DB::raw('CONCAT(users.fname, " ", users.lname) AS user_name'), 'users.email', 'user_survey_respondent.survey_id', 'user_survey_respondent.survey_status', 'users.fname', 'users.lname', 'last_submitted_date', 'notify_email_date', DB::raw('CONCAT(par.fname, " ", par.lname) AS part_name'));

        if ($role == 0) {
            $user_details->orderBy('participant_id');
        }

        if ($role == 1) {
            $user_details->orderBy('rater.id');
        }

        $result = $user_details->get();

        return $result;
    }

    public function RawscoreExport(Request $request)
    {
        $survey_id = $request->get('survey_id');
        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');

        $sheet_name = 'Rawscore-' . str_replace(' ', '', $survey_name) . '-' . strtotime("now");
        $data = [
            'survey_id' => $survey_id,
            'users' => $request->get('users'),
        ];
        return Excel::download(new RawScoreExport($data), $sheet_name . '.xlsx');
    }
    public function text_response(Request $request)
    {
        $survey_id = $request->get('survey_id');
        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');
        $sheet_name = 'TextResponse-' . str_replace(' ', '', $survey_name) . '-' . strtotime("now");
        $data = [
            'survey_id' => $survey_id,
            'users' => $request->get('users'),
        ];
        return Excel::download(new TextResponseExport($data), $sheet_name . '.xlsx');
    }
    public function staus_summary(Request $request)
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

        return view('admin.report.export_blade.status_summary',$data);
    }
    public function StatusSummary(Request $request)
    {
        $survey_id = $request->get('survey_id');

        $data = [
            'survey_id' => $survey_id,
            'users' => $request->get('users'),
        ];
        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');
        $sheet_name = 'StatusSummary-' . str_replace(' ', '', $survey_name) . '-' . strtotime("now");
        return Excel::download(new StatusSummary($data), $sheet_name . '.xlsx');
    }
}
