   <?php $dimension_hide=DB::table('surverys')->where('id',$survey_id)->value('dimension_hide');?>
<li class="group active @if($count==1) {{'focus'}} @endif" data-id="{{$count}}">
    <div class="panel panel-primary question" rel="{{++$key}}" data-question-type="{{$ques->question_type}}">
    <input type="hidden" name="question_id[]" value="{{$ques->question_id}}">
    <div class="panel-heading">

      <div class="question-label">{{$count.'. '}} {!!$ques->question_text!!}
        @if($ques->question_required==1)
        <span class="qtn-required">*</span>
        @endif
      </div>
    </div>
    <?php
    $user_response=[];
    $datanottouch=0;
    if(!empty($responses)) $user_response = array_keys($responses,$ques->question_id);
    if(!empty($user_response)) $datanottouch=1;
    ?>

      <div class="panel-body question-options" data-nottouch="{{$datanottouch}}">
        <?php yield_input($ques->question_type,$ques->question_id,$ques->options,$user_response);?>
        @if($ques->question_type=="text" ||  $ques->question_type =="textarea")
          <div class="message"><span><strong>Ooops!</strong> You must write few lines.</span></div>
        @else
          <div class="message"><span><strong>Ooops!</strong> You must make a selection</span></div>
        @endif

      </div>


  </div>
</li>
