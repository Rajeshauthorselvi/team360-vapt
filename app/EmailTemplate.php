<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class EmailTemplate extends Model
{
    protected $table="email_templates";

    public $timestamps=false;

    protected $fillable=['id','survey_id','subject','content','type'];
}
