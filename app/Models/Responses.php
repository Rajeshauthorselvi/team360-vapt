<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Responses extends Model
{
protected $table = 'responses';

protected $fillable = ['user_survey_respondent_id','question_id','option_id','text_response'];

public $timestamps = false;
}
