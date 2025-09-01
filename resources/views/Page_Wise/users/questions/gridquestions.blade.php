<li class="group active @if($count==1) {{'focus'}} @endif" data-id="{{$count}}">
<div class="panel panel-primary question" rel="{{++$key}}" data-question-type="{{$question->question_type}}">
<input type="hidden" name="question_id[]" value="{{$question->question_id}}">

<div class="panel-heading">
  <div class="question-label"> 
    {{-- {{$no.' '}} --}}


  
    @if($dimension_hide==0)
      {!!$question->question_dimension!!}
    @endif
    @if($question->question_required==1)
    <span class="qtn-required">*</span>
    @endif
  </div>
</div>

<?php 

$qtext=explode('|', $question->question_text);
$qqid=explode('|', $question->question_id);
/*var_dump($qtext);
var_dump($qqid);*/

$c=array_combine($qqid,$qtext);

$optext=$question->options;

$optionth=(isset($question->optionth)) ? $question->optionth : array();

 $user_response=""; 
 $datanottouch=0;
    if(!empty($responses))
      $user_response = array_keys($responses,$question->question_id);
    if(!empty($user_response)) $datanottouch=1;
    

?>

<div class="panel-body question-options" data-nottouch="{{$datanottouch}}">
 <table class="table table-bordered">
  <thead>
     <tr>
       
       @if(count($optionth)>0)
       <th class="question-dimension"></th>
       @foreach($optionth as $optionthi)
       <th class="text-center question-dimension">{!!$optionthi!!}</th>
       @endforeach
       @endif

     </tr>
  </thead>
<tbody>
@if(count($c)>0)
   <?php $ques_count=1; ?>
   @foreach($c as $question_id => $question_text)
   <tr class="data-question-type-grid">
   <td class=" question-dimension">{{$count}}. {!!$question_text!!}</td>
   <?php 

   $i_options=(isset($optext[$question_id])) ? $optext[$question_id] : array();

   $option_count=1;

   ?>
   @foreach($i_options as $key=>$i_option)
   <td class="grid-td"><?php 
   yieldoptionforgrid($question_id,$i_option,$option_count,$user_survey_id,$key); ?></td>
    <?php $option_count++; ?>
   @endforeach
   </tr>
   <?php $count++;?>
   @endforeach


@endif
  <?php unset($c);?>  
</tbody>
</table>
</div>
 <div class="message"><span><strong>Ooops!</strong> You must make a selection</span><div>

</div>
</div>


</li>
<style type="text/css">
  .data-question-type-grid .question-dimension {
    text-align: left;
  }
</style>
