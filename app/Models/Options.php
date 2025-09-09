<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Options extends Model
{
protected $table = 'responses';

protected $fillable = ['question_id','option_text','option_weight'];

public $timestamps = false;
}
