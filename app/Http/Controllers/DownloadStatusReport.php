<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Excel;
use Session;
use App\Survey;
use App\Exports\StatusExport;
use App\Exports\StatusSummary;
class DownloadStatusReport extends Controller
{
    public function SurveyReport(Request $request)
    {

       if ($request->has('key')) {
       		$survey_id=decrypt($request->get('key'));
       }
       else{
       		$survey_id=$request->get('survey_id');
       }
       $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');
       $sheet_name = 'Status_Report-' . str_replace(' ', '', $survey_name) . '-' . strtotime("now");
       return Excel::download(new StatusExport($survey_id), $sheet_name . '.xlsx');

    }
public function GetStatusSurveyDetails($survey_id,$role=0,$participant_id='',$status=null)
    {

        $result=array();

        $user_details=DB::table('user_survey_respondent');

        if($role==0) $user_details->join('users','users.id','=','user_survey_respondent.participant_id');

        if($role==1) $user_details->join('users','users.id','=','user_survey_respondent.respondent_id');

        $user_details->join('users as par','par.id','=','user_survey_respondent.participant_id');

        $user_details->join('rater','rater.id','=','user_survey_respondent.rater_id' );

        $user_details->where('users.email','not like','%kjsaneesh%');
        $user_details->where('users.email','not like','%ascendus%');
        $user_details->where('users.email','not like','%authorselvi%');


            // $user_details->where('survey_status',3);


        $user_details->where('survey_id', $survey_id);

        if($role==0) $user_details->where('respondent_id', 0);

        if(is_array($participant_id) && count($participant_id) > 0 )
           $user_details->whereIn('participant_id',$participant_id);
        else if($participant_id !="")
            $user_details->where('participant_id',$participant_id);

        $user_details->select('participant_id','respondent_id','user_survey_respondent.id as user_survey_id','rater.rater',DB::raw('CONCAT(users.fname, " ", users.lname) AS user_name'),'users.email','user_survey_respondent.survey_id','user_survey_respondent.survey_status', 'users.fname', 'users.lname','last_submitted_date','notify_email_date',DB::raw('CONCAT(par.fname, " ", par.lname) AS part_name'));


       if($role==0)   $user_details->orderBy('participant_id');

        if($role==1)  $user_details->orderBy('rater.id');

        $result=$user_details->get();

        return $result;
    }
    public function SummaryReport(Request $request)
    {
        $survey_id=base64_decode($request->key);
        $data = ['survey_id' => $survey_id,'users' => $request->get('users')];
        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');
        $sheet_name = 'StatusSummary-' . str_replace(' ', '', $survey_name) . '-' . strtotime("now");
        return Excel::download(new StatusSummary($data), $sheet_name . '.xlsx');
    }
}
