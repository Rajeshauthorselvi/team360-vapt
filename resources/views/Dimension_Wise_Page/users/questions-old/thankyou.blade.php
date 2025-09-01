@extends('default.users.layouts.default')
@section('content')


<div class="col-sm-12  col-md-8 col-md-offset-2 col-xs-12" id="welcome-section">
<div class="site-content">
<div class="welcome-box">
  <div class="welcome-body">
<?php

  	$rater_type = Request::get('rater_type');

        //Code added by Raj for Self & Respondent Welcome Msg
        if($thankyou_text!='')
        {
          $thanku_message = explode("##<<<#>>>##",$thankyou_text);
          if(count($thanku_message)==2)
          {
              $thankuMsgSelf = $thanku_message[0];
              $thankuMsgResp = $thanku_message[1];
              if($rater_type == 'self')
                echo $thankuMsgSelf ;
              else
                echo $thankuMsgResp ;
          }
          else
          {
              echo $thanku_message[0];
          }

        }

        //Code End by Raj for Self & Respondent Welcome Msg
?>
  </div>
  <div class="welcome-footer">
    <a class='btn btn-primary btn-lg' href="{{URL::route('user.dashboard',[config('site.survey_slug')])}}">Back to Home</a>
  </div>
</div>
</div>




</div>
@endsection
