   <?php $dimension_hide=DB::table('surverys')->where('id',$survey_id)->value('dimension_hide');?>
<li class="group active @if($no==1) {{'focus'}} @endif" data-id="{{$no}}">
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
      <div class="question-label">{{-- {{$no.' '}} --}} {!! str_replace('~', ' | ', $question->question_text) !!}
        @if($question->question_required==1)
            <span class="qtn-required" style="{{ ($survey_id==38)?'visibility:hidden':'' }}">*</span>
        @endif
      </div>
    </div>
    <?php
    $user_response="";
    $datanottouch=0;
    if(!empty($responses)) $user_response = array_keys($responses,$question->question_id);
    if(!empty($user_response)) $datanottouch=1;
    $responses=DB::table('responses')->find($user_response);

    $tresponse=isset($responses->text_response)?$responses->text_response:'';
    $tresponse=explode(' ',$tresponse);
    if (count($tresponse) >= 20) {
        $status="valid";
    } else {
        $status="invalid";
    }
    $display="display:none";
    if (!empty($responses)) {
        if (count($tresponse) >= 20) {
            $display="display:none";
        } else {
            $display="display:block";
        }
    }
    ?>

      <div class="panel-body question-options" data-nottouch="{{$datanottouch}}">

        <?php yield_input($question->question_type,$question->question_id,$question->options,$user_response);?>
        @if($question->question_type=="text" ||  $question->question_type =="textarea")
            @if ($survey_id==11)
                <div class="col-sm-12 pl-0 pr-0">
                    <div class="col-sm-6 pull-left pl-0">Minumum words 20</div>
                    <div class="col-sm-6 pull-right  pr-0 text-right">Word count <span class="word_count">0</span></div>
                </div>
                <div class="clearfix"></div>
                <div class="count_message {{ $status }}" style="{{ $display }}"><span><strong>Ooops!</strong> You must write minimum 20 words.</span></div>
            @endif
            <div class="message"><span><strong>Ooops!</strong> You must write few lines.</span></div>
        @else
           {{--  @if ($survey_id==37)
                <div class="count_message {{ $status }}" style="{{ $display }}"><span><strong>Ooops!</strong> You must write minimum 20 words.</span></div>
            @endif --}}
            @if ($survey_id==37)
                <div class="error-message-check hidden"><span><strong>Ooops!</strong> You must need to select minumum 2 selection</span></div>
            @endif
            <div class="message"><span><strong>Ooops!</strong> You must make a selection</span></div>
        @endif

      </div>


  </div>
</li>
