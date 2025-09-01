<?php $rcount=$response_count;?>
@if(isset($welcome_text) AND $rcount==0)
<div class="col-sm-12  col-md-10 col-md-offset-1 col-xs-12" id="welcome-section">
  <div class="site-content">
  <div class="welcome-box">
    <div class="welcome-body">
      <?php
        $rater_type = Request::get('rater_type');

        //Code added by Raj for Self & Respondent Welcome Msg
        if($welcome_text!='')
        {
          $welcome_message = explode("##<<<#>>>##",$welcome_text);
          if(count($welcome_message)==2)
          {
              $welcomeMsgSelf = $welcome_message[0];
              $welcomeMsgResp = $welcome_message[1];
              if($rater_type == 'self')
                echo $welcomeMsgSelf ;
              else
                echo $welcomeMsgResp ;
          }
          else
          {
              echo $welcome_message[0];
          }

        }

        //Code End by Raj for Self & Respondent Welcome Msg

      ?>

    </div>
    <div class="welcome-footer text-center">
      {{Form::button('Take Survey',['class'=>'btn btn-submit ','id'=>'take-survey'])}}
    </div>
  </div>
</div>
</div>
<style type="text/css">
	footer{
		position: relative;
	}

</style>
@endif
