<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Questions extends Model
{
protected $table = 'questions';

protected $fillable = ['survey_id','question_text','question_type','question_required','question_dimension','display_order'];

public $timestamps = false;
}
