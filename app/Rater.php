<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Rater extends Model
{
protected $table = 'rater';
protected $fillable = ['rater'];

public $timestamps = false;
}
