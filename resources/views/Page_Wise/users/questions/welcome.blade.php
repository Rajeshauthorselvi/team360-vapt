<?php $rcount=$response_count;?>
@if(isset($welcome_text) AND $rcount==0)
<div class="col-sm-12  col-md-8 col-md-offset-2 col-xs-12" id="welcome-section">
  <div class="site-content">
  <div class="welcome-box">
    <div class="welcome-body">
    {!! $welcome_text !!}
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
