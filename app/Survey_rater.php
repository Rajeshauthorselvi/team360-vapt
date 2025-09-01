<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Survey_rater extends Model
{
protected $table = 'survey_rater';

protected $fillable = ['rater_id','survey_id'];

public $timestamps = false;
}
