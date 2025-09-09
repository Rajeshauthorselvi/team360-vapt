<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
class UserEmail extends Model
{
    protected $table = 'users';

    static function getRespondentBasedOnSurveyStatus($participant_id,$survey_id,$remainder=0){
    	
       $users_info=array();

       $query = DB::table('users')
        ->select('users.email','users.id','users.fname','users.lname','user_survey_respondent.survey_status','surverys.participant_rater_manage')
        ->join('user_survey_respondent','users.id','=','user_survey_respondent.respondent_id' )
        ->join('surverys','surverys.id','=','user_survey_respondent.survey_id' )
        ->where('users.id','>',1)
        ->where('user_survey_respondent.survey_id',$survey_id)
        ->where('user_survey_respondent.participant_id',$participant_id);

        if($remainder==1){
            $query->where('user_survey_respondent.notify_email_date','<>',NULL)
                  ->whereIn('user_survey_respondent.survey_status',[1,2]);
        }
        else
        {
            $query->where('user_survey_respondent.notify_email_date',NULL)
                  ->where('user_survey_respondent.survey_status','=',1);
        }
       
       $users_info = $query->where('user_survey_respondent.respondent_id','>',0)->get()->toArray();

	   return $users_info;
    }



    


}
