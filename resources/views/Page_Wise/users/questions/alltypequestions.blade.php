   <?php $dimension_hide=DB::table('surverys')->where('id',$survey_id)->value('dimension_hide');?>
<li class="group active @if($count==1) {{'focus'}} @endif" data-id="{{$count}}">
    <div class="panel panel-primary question" rel="{{++$key}}" data-question-type="{{$question->question_type}}">
    <input type="hidden" name="question_id[]" value="{{$question->question_id}}">
    <div class="panel-heading">
      @if(!empty($question->question_dimension))
@if($dimension_hide==0)
        <div class="question-dimension">
          {!!$question->question_dimension!!}
        </div>
@endif
      @endif
      <div class="question-label">{{$count.'. '}} {!!$question->question_text!!}
        @if($question->question_required==1)
        <span class="qtn-required">*</span>
        @endif
      </div>
    </div>
    <?php
    $user_response=[];
    $datanottouch=0;
    if(!empty($responses)) $user_response = array_keys($responses,$question->question_id);
    if(!empty($user_response)) $datanottouch=1;
    ?>

      <div class="panel-body question-options" data-nottouch="{{$datanottouch}}">


        <?php yield_input($question->question_type,$question->question_id,$question->options,$user_response);?>

        @if($question->question_type=="text" ||  $question->question_type =="textarea")
          <div class="message"><span><strong>Ooops!</strong> You must write few lines.</span></div>
        @else
          <div class="message"><span><strong>Ooops!</strong> You must make a selection</span></div>
        @endif

      </div>


  </div>
</li>
