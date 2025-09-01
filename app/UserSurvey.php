<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
class UserSurvey extends Model
{
    protected $table = 'surverys';

    static function getInfo($path_url='',$survey_id=''){

        if(empty($survey_id)) {
            $url=explode('/',$path_url);
            $survey_slug=$url[0];
        }
        else
        {
            $survey_slug=DB::table('surverys')->where('id',$survey_id)->pluck('url');
        }

	       $info=DB::table('surverys')
	      ->select('surverys.title as survey_name','surverys.survey_theme_id',
	      'surverys.logo','surverys.url','themes.file','themes.title','surverys.right_logo','surverys.header_text','surverys.footer_text','surverys.id as survey_id','surverys.shuffle_questions','surverys.dimension_hide','surverys.send_email_from','participant_rater_manage','show_relationship','question_per_page','show_relationship','surverys.sender_name')
	      ->leftjoin('themes','surverys.survey_theme_id','=','themes.id')
	      ->where('surverys.url',$survey_slug)
	      ->first();

	    return $info;
    }


    static function getSurveyInfoByRater($participant_id,$respondent_id){


    	$query=DB::table('user_survey_respondent')

        ->select('rater.rater','surverys.title','surverys.start_date','surverys.end_date','surverys.participant_rater_manage','user_survey_respondent.*','users.id as user_id','users.fname','users.lname','users.email')

        ->leftjoin('surverys','user_survey_respondent.survey_id','=','surverys.id')

        ->leftjoin('users','user_survey_respondent.participant_id','=','users.id')

        ->leftjoin('rater','user_survey_respondent.rater_id','=','rater.id')

        ->where('survey_id',config('site.survey_id'));

        if($participant_id > 0){
              $query->where('participant_id',$participant_id)
              ->where('respondent_id',0);
        }
        if($respondent_id > 0)
        {
            $query->where('respondent_id',$respondent_id);
        }

        $query->whereRaw('end_date > now()');
        // $query->whereRaw('(now() between start_date and end_date)');
        $user_survey_info_participant=$query->get()->toArray();

        return $user_survey_info_participant;
    }



}
