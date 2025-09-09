<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
class Respondent extends Model
{
    protected $table = 'users';

    static function getRespondentBasedOnParticipant($participant_id){
    	
        $users_info = DB::table('users')
                    ->select('users.*','rater.rater','user_survey_respondent.survey_status','user_survey_respondent.id as user_survey_respondent_id','user_survey_respondent.notify_email_date',DB::raw("count('r.*') as rcount"))
                    ->join('user_survey_respondent','users.id','=','user_survey_respondent.respondent_id' )
                    ->join('rater','rater.id','=','user_survey_respondent.rater_id' )
                    ->leftjoin('responses as r','r.user_survey_respondent_id','=','user_survey_respondent.id')
                    ->where('users.id','>',1)
                    ->where('user_survey_respondent.respondent_id','!=',0)
                    ->where('user_survey_respondent.survey_id',config('site.survey_id'))
                    ->where('user_survey_respondent.participant_id',$participant_id)
                    ->groupby('user_survey_respondent.id')
                    ->get()->toArray();

	   return $users_info;
    }

    static function getRaterList($survey_id){
        $rater_list = DB::table('rater')
            ->select('rater.id as rater_id','rater.rater')
            ->leftjoin('survey_rater','rater.id','=','survey_rater.rater_id')
            ->where('survey_rater.survey_id',$survey_id)
            ->where('rater.rater','<>','self')
            ->get();
        return $rater_list;
    }

    static function getSurveyRaterList($survey_id){
        $survey_rater_list=DB::table('survey_rater')      
                        ->join('rater','rater.id','=','survey_rater.rater_id' )
                        ->where('survey_id',$survey_id)  
                        ->where('rater.rater','<>','self')
                        ->pluck('rater','rater_id');
        return $survey_rater_list;
    }
    
    static function getRespondentEmailBySurveyId($participant_id,$survey_id){

        $respondent_email= DB::table('user_survey_respondent')  
            ->join('users','user_survey_respondent.respondent_id','users.id')
            ->where('participant_id', $participant_id)
            ->where('respondent_id','>',0 )
            ->where('survey_id', $survey_id)
            ->pluck('email')->toArray();
        return $respondent_email;

    }

    


}
