<li class="group active @if($no==1) {{'focus'}} @endif" data-id="{{$no}}">
<div class="panel panel-primary question" rel="{{++$key}}" data-question-type="{{$question->question_type}}">
<input type="hidden" name="question_id[]" value="{{$question->question_id}}">

<div class="panel-heading">
  <div class="question-label"> 
    {{-- {{$no.' '}} --}}

<?php 
  
  $question_dimension=explode('split_question',$question->question_dimension);


?>
  
    @if($dimension_hide==0)

      @if(count($question_dimension)>1)
       <div class="question-dimension">
                {!! $question_dimension[0] !!}
        </div>
        {!!$question_dimension[1]!!}
      @else
        {!!$question_dimension[0]!!}
      @endif



      
    @endif
    @if($question->question_required==1)
    <span class="qtn-required">{{-- * --}}</span>
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
 <tr>
   
   @if(count($optionth)>0)
   <?php
      $head_width='27%';
      if ($survey_id==35) {
        $head_width='20%';
      }
   ?>
   <th class="question-dimension" width="{{ $head_width }}"></th>
   @foreach($optionth as $optionthi)
   <?php
      $option_width='5%';
      if ($survey_id==35) {
        $option_width='8%';
      }
   ?>
   <th class="text-center question-dimension" width="{{ $option_width }}">{!!$optionthi!!}</th>
   @endforeach
   @endif

 </tr>
<?php $gridquestiontxt='';?>
@if(count($c)>0)
   <?php $ques_count=1; ?>
   @foreach($c as $question_id => $question_text)
   <?php if($ques_count==1) $gridquestiontxt=$question_id;?>
   <tr class="data-question-type-grid">
   <td class=" question-dimension">{{-- {{$ques_count}}. --}} {!!$question_text!!}</td>
   <?php 

   $i_options=(isset($optext[$question_id])) ? $optext[$question_id] : array();

   $option_count=1;

   ?>
   @foreach($i_options as $key=>$i_option)
   <?php $option_label=$key; ?>
   <td class="grid-td"><?php 
   yieldoptionforgrid($question_id,$i_option,$option_count,$user_survey_id,$option_label); ?></td>
    <?php $option_count++; ?>
   @endforeach
   </tr>
   <?php $ques_count++;?>
   @endforeach


@endif
  <?php unset($c);?>  
</table>

<?php  

   $gridquestiontxt=str_replace("‘",'',$gridquestiontxt);
   $gridquestiontxt=str_replace("’",'',$gridquestiontxt);
   $gridquestiontxt=str_replace(' ', '', $gridquestiontxt);

   ?>

 <div class="message"><span><strong>Ooops!</strong> You must make a selection</span><div>

</div>
</div>


</li>
<style type="text/css">
  .data-question-type-grid .question-dimension {
    text-align: left;
  }
</style>
