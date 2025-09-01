<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class User_survey_respondent extends Model
{
protected $table = 'user_survey_respondent';

protected $fillable = ['survey_id','participant_id','respondent_id','rater_id','survey_status','notify_email_date','remainder_email_date','last_submitted_date'];


}
