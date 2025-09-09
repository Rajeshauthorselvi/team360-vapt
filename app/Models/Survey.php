<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Survey extends Model
{
  protected $table = 'surverys';

protected $fillable = ['title','url','logo','right_logo','header_text','footer_text','send_email_from','survey_theme_id','start_date','end_date','dimension_hide','participant_rater_manage','admin_survey_flag'];

}
