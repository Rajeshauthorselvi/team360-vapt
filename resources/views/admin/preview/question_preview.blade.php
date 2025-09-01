<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- <link rel="shortcut icon"  href={{ URL::asset('asset/images/fav_icon.png') }} type="image/png" /> -->

  <title>@if(isset($title)){{$title}}@endif</title>
  
   {!! HTML::script('script/jquery.js') !!}
   {!! HTML::script('script/jqueryui.js') !!}
   {!! HTML::script('script/bootstrap.min.js') !!}
   {!! HTML::style('css/bootstrap.min.css') !!}
   {!! HTML::style('css/jqueryui.css') !!}
   {!! HTML::script('script/bootstrapValidator.min.js') !!}
   {!! HTML::style('css/bootstrapValidator.min.css') !!}
   {!! HTML::style('css/font-awesome/css/font-awesome.min.css') !!}
   
   {!! HTML::style('css/user-common.css') !!}
   {!! HTML::script('script/user-common.js') !!}
 {!! HTML::style('css/themes/'.$themes) !!}



</head>
<body>



<header>
<div class="inner-header">



@if(!empty($survey_details))
     <div class="navbar navbar-default ">
   <div  class="col-sm-3 logo"> {!! HTML::image('storage/surveys/'.$survey_details->logo,'Logo',['class'=>'img-responsive']) !!}</div>
  <div  class="col-sm-6 header_text">{!! $survey_details->header_text !!}</div>
  <div  class="col-sm-3 logo  right_logo">{!! HTML::image('storage/surveys/'.$survey_details->right_logo,' ',['class'=>'img-responsive']) !!}</div>

    </div>
@endif


<br/>
<div class="container-fluid">

<div class="row">

<div class="welcome-strip text-center">Welcome <strong>User</strong></div>

</div>

</div>


</div>
</header>


<?php 

function yieldoptionforgrid($question_id,$i_option,$key)
{
  $checked=false;
  $result='';
//if(in_array($question_id,$responses))
//{
   // $responses=DB::table('responses')->where('option_id',$i_option)->value('question_id');
//if($responses==$question_id)
//{
//$checked=false;
  $result ='<div class="option-subsection  option-subsection-grid" >'.Form::radio("_".$question_id,$i_option,$checked,["class"=>"grid-required op_radio",'id'=>'radiolabel_'.$question_id.'_'.$key,'data-type'=>'grid']).'<span class="grid_label" >'.Form::label('radiolabel_'.$question_id.'_'.$key,$key).'</span></div>';
  print $result;


//}
/*
else
{
$checked=false;
  $result ='<div class="option-subsection" >'.Form::radio("_".$question_id,$i_option,$checked,["class"=>"grid-required op_radio",'id'=>'radiolabel_'.$question_id.'_'.$key,'data-type'=>'grid']).'</div>';
  print $result;

}*/
//}



}



?>
<div class="container parent">
<div class="row">
@if(isset($welcome_text))
<div class="col-sm-12  col-md-8 col-md-offset-2 col-xs-12" id="welcome-section">
  <div class="welcome-body">
  {!! $welcome_text !!}
  </div>
  <div class="welcome-footer">
    {{Form::button('Take Survey',['class'=>'btn btn-primary btn-lg','id'=>'take-survey'])}}
  </div>
<style type="text/css">
  #question-container,#fixed-footer{display: none;}
</style>
</div>
@endif
<div class="col-sm-12  col-md-8 col-md-offset-2 col-xs-12 welcome-txt" id="question-container">
{!! Form::open(array('route'=>['ostore',$survey_details->url],'method'=>'POST','id'=>'survey-user-form')) !!}


<?php $no=1;?>

<div class="bs-example" data-example-id="contextual-panels">
<ul class="question-container">
@if(count($questions)>0)
@foreach($questions as $key=>$question)

@if($question->question_type=="grid")
<li class="group active @if($no==1) {{'focus'}} @endif" data-id="{{$no}}">
<div class="panel panel-primary question" rel={{++$key}} data-question-type="{{$question->question_type}}">
<input type="hidden" name="question_id[]" value="{{$question->question_id}}">

<div class="panel-heading">

  <div class="question-label"> {!!$no.". ". $question->question_dimension!!}
    @if($question->question_required==1)
    <span class="qtn-required">*</span>
    @endif
  </div>
</div>

<?php 

$qtext=explode('|', $question->question_text);
$qqid=explode('|', $question->question_id);
$c=array_combine($qqid,$qtext);

$optext=$question->options;

$optionth=(isset($question->optionth)) ? $question->optionth : array();


 $user_response=""; 
 $datanottouch=0;
    /*if(!empty($responses))
      $user_response = array_keys($responses,$question->question_id);
    if(!empty($user_response)) $datanottouch=1;*/
    

?>

<div class="panel-body question-options" data-nottouch="{{$datanottouch}}">
 <table class="table table-bordered">
 <tr>
   
   @if(count($optionth)>0)
   <th class="question-dimension"></th>
   @foreach($optionth as $optionthi)
   <th class="text-center question-dimension">{!! $optionthi !!}</th>
   @endforeach
   @endif

 </tr>

@if(count($c)>0)
   @foreach($c as $question_id => $question_text)
   <tr class="data-question-type-grid">
   <td class=" question-dimension">{!!$question_text!!}</td>
   <?php $i_options=(isset($optext[$question_id])) ? $optext[$question_id] : array();?>
   @foreach($i_options as $key=>$i_option)
   <td ><?php 
//var_dump("question_id".$question_id."\n"."option".$i_option."\n"."key".$key);

   yieldoptionforgrid($question_id,$i_option,$key); ?></td>
   @endforeach
   </tr>
   @endforeach


@endif
  <?php unset($c);?>  
</table>
 <div class="message"><span><strong>Ooops!</strong> You must make a selection</span><div>

</div>
</div>


</li>

@else



<li class="group active @if($no==1) {{'focus'}} @endif" data-id="{{$no}}">
<div class="panel panel-primary question" rel={{++$key}} data-question-type="{{$question->question_type}}">
<input type="hidden" name="question_id[]" value="{{$question->question_id}}">
<div class="panel-heading">
@if($dimension_hide==0)
	@if(!empty($question->question_dimension)&& ($question->question_dimension!='&nbsp;'))
		<div class="question-dimension">
		   {!!$question->question_dimension!!}
		</div>
	@endif
@endif
	<div class="question-label"> {!! $no.". ". $question->question_text !!}
	  @if($question->question_required==1)
	  <span class="qtn-required">*</span>
	  @endif
	</div>
</div>
<?php
 $user_response=""; 
 $datanottouch=0;
    if(!empty($responses))
      $user_response = array_keys($responses,$question->question_id);
     // $user_response=(isset($responses[$question->question_id])) ? $responses[$question->question_id] : "";
    if(!empty($user_response)) $datanottouch=1;
    
?>
	<div class="panel-body question-options" data-nottouch="{{$datanottouch}}">
	  <?php yield_input($question->question_type,$question->question_id,$question->options,$user_response);?>
    <div class="message"><span><strong>Ooops!</strong> You must make a selection</span><div></div></div>

	</div>

</div>
</li>

@endif
<?php $no++; ?>
@endforeach
@else
               <div class="title m-b-md">
                    <div class="text-center">NO QUESTIONS FOUND</div>
                </div>
  
@endif
</ul>
</div>
{{Form::hidden('survey_id',$survey_details->id)}}
{{Form::hidden('currentli',1,['id'=>'currentli'])}}
{{Form::hidden('formaction','submit',['id'=>'formaction'])}}

{{Form::close()}}

</div>


<?php

function yield_input($type,$qid,$qoptions,$user_response)
{
  $tresponse=null;
  $oresponse=null;
  $tstyle='display:none';
  if(!empty($user_response) AND count($user_response)==1) {
    $responses=DB::table('responses')->find($user_response);
    $tresponse=$responses->text_response;
    $oresponse=$responses->option_id;
   
  }
  else if(count($user_response)>1)
  {
    foreach ($user_response as $key => $value) {
       $responses=DB::table('responses')->find($value);
       $oresponse[]=$responses->option_id;
    }
  }

  if(!empty($tresponse)) $tstyle='display:inherit';


  $result='';
  switch ($type) {
    case 'text':
      $result=Form::text("_".$qid,$tresponse,["class"=>"form-control required",'data-type'=>'text']);
      break;
      case 'textarea':
        $result=Form::textarea("_".$qid,$tresponse,["class"=>"form-control required",'data-type'=>'textarea']);
        break;
        case 'dropdown':
        $options=[''=>'Please Select'];
        foreach ($qoptions as $key => $qoption) {
          $options[$qoption->id]=$qoption->option_text;
        }
          $result=Form::select("_".$qid,$options,$oresponse,["class"=>"form-control required",'data-type'=>'dropdown']);
          break;
          case 'radio':
          foreach ($qoptions as $key => $qoption) {
              $checked=($qoption->id==$oresponse) ? 'checked':null;
              if(strtolower($qoption->option_text)=="others"){
        
                $result .='<div class="option-subsection" >'.Form::radio("_".$qid,$qoption->id,$checked,["class"=>"required op_radio",'id'=>'radiolabel_'.$qid.'_'.$key,'data-type'=>'radio']);
                $result .=Form::label('radiolabel_'.$qid.'_'.$key,$qoption->option_text);
                $result .=Form::textarea("others_".$qid, $tresponse, ['class'=>'others-textarea required form-control','rows'=>'5','style'=>$tstyle]).'</div>';
                
              }
              else {
                $result .='<div class="option-subsection" >'.Form::radio("_".$qid,$qoption->id,$checked,["class"=>"required op_radio",'id'=>'radiolabel_'.$qid.'_'.$key,'data-type'=>'radio']);
                $result .=Form::label('radiolabel_'.$qid.'_'.$key,$qoption->option_text).'</div>';
              }
              
          }
          //echo Form::textarea("_".$qid, null, ['size' => '30x5','class'=>'textarea form-control ']);
            break;
            case 'checkbox':
            foreach ($qoptions as $key => $qoption) {
          $checked=(is_array($oresponse)) ? ((in_array($qoption->id, $oresponse)) ? 'checked':null) : (($qoption->id==$oresponse) ? 'checked':null);
                $result .='<div class="option-subsection" >'.Form::checkbox("_".$qid.'[]',$qoption->id,$checked,["class"=>"required",'id'=>'checkboxlabel_'.$qid.'_'.$key,'data-type'=>'checkbox']);
                $result .=Form::label('checkboxlabel_'.$qid.'_'.$key,$qoption->option_text).'</div>';
            }
            break;

    default:
      break;
  }
  echo $result;
}

?>

	<script type="text/javascript">
function checkerror(obj)
{   
  var error=false;
      var input_type=obj.attr('data-type');

      var required_exists=obj.closest('.panel-primary').find('.qtn-required').length;

      if(input_type=='radio' && required_exists >0 && obj.closest('.question-options').find('input[type=radio]:checked').val()===undefined)
      { 
        error=true;
      }
      else if(input_type=='radio' && required_exists >0 && obj.closest('.question-options').find('.others-textarea:visible').val()=="")
      { 
        error=true;
      }
      else if(input_type=='checkbox' && required_exists >0 && obj.closest('.question-options').find('input[type=checkbox]:checked').val()===undefined)
      {
      error=true;
      }
      else if(input_type=='text' && required_exists >0 && obj.closest('.question-options').find('input[type=text]').val()=="")
      {
       error=true;
      }
      else if(input_type=='textarea' && required_exists >0 && obj.closest('.question-options').find('textarea').val()=="")
      {
        error=true;
      }
     else if(input_type=='dropdown' && required_exists >0 && obj.closest('.question-options').find('select').val()=="")
      {

          error=true;
      }
else if(input_type=='grid' && required_exists >0 && obj.closest('.question-options').find('input[type=radio]:checked').val()===undefined)
      { 
        error=true;
      }
      else
      {
          error=false;
      }
  return error;
      
}
$(document).ready(function(){

  $('#take-survey').click(function(){
    $('#welcome-section').fadeOut('slow').css('-webkit-transition','background 5s');
    $('#question-container').fadeIn('slow').css('-webkit-transition', 'background 1s');
     $('#fixed-footer').fadeIn('slow').css('-webkit-transition', 'background 1s');
     $('html, body').animate({scrollTop:0}, 'slow');
  })

 


$('#form-submit').click(function(){

  $('#formaction').val('submit');
  var error=0;
  var liposition=new Array();
  $('.question-container li').each(function(){
    var check_required=$(this).find('.qtn-required:visible').length;

    var closesinput=$(this).find('.grid-required , .required');


var closes=$(this).find('tr.data-question-type-grid').length;
if(closes>0)
{
var obj=$(this).find('tr.data-question-type-grid');
var checkcount=0;
$(obj).each(function(){
	checkcount += parseInt($(this).find('input[type=radio]:checked').length);
//alert(checkcount);
});
check_req = $(this).find('.qtn-required').length;
	if(check_req==1)
	{
		if(closes==checkcount)
		{

		$(this).find('.message').hide();
		}
			else
			{

			//alert('req_unanswered');

			$(this).find('.message').show();

			//$('#form-save').show();

			    //var id=$(this).attr('data-id');

			//lit_grid.push($(this).attr('data-id'));
			//lit.push(liposition);
			 liposition.push($(this).attr('data-id'));
			 
			
			return null;
			}

	}
}
else
{






    if(check_required>0 && checkerror(closesinput))
    {
      liposition.push($(this).attr('data-id'));
      $(this).find('.message').show();
    }
    else
    {
      $(this).find('.message').hide();
    }

}

  });
  //alert(liposition);

 if(liposition.length > 0)
 {
    var height=$('li[data-id="'+liposition[0]+'"]').offset().top-$('.inner-header').outerHeight()-10;
      //alert(height);

      $('html, body').animate({scrollTop:height}, 'slow');

 }
 else
 {
    $('#form-submit').fadeTo(1000, 0.4);
    $('#survey-user-form').submit();
 }

});

$('body').on('change','.required',function(){

if($(this).next('label').text()=="others" || $(this).hasClass('others-textarea')){
  $(this).parent().find('.others-textarea').show().focus();
}
else
{
  $(this).closest('.question-options').find('.others-textarea').val('').hide();
}



 if(!checkerror($(this))) 
 {
    $(this).parents('li').find('.message').hide();
    var id=$(this).parents('li').next().attr('data-id');

if(id!="" && id!=undefined){

	 var check_field=$(this).attr( "data-type" );
      if (check_field=="radio"  || check_field=="dropdown") {
          var height=$('li[data-id="'+id+'"]').offset().top-$('.inner-header').outerHeight()-10;
          $('html, body').animate({scrollTop:height}, 'slow');
      }
    }

    var datatouched=$(this).closest('.question-options').attr('data-nottouch');
    if(datatouched==0){

    var totalanswer=parseInt($('#answered').attr('data-count'))+1;
    $('#answered').attr('data-count',totalanswer);
    $('#answered').text(totalanswer);

    var totalquestions=parseInt($('#total-questions').text());
    var percentage=Math.floor((totalanswer / totalquestions) * 100);

    $('.progress-bar').css('width', percentage+'%').attr('aria-valuenow', percentage);
    $(this).closest('.question-options').attr('data-nottouch',1);
    }  

 }
 else
 {
    
    //alert($(this).closest('.question-options').find('label:last').text());
    
    $(this).parents('li').find('.message').show();
    if( $(this).closest('.question-options').find('label:last').text()!="others" ){
      $(this).closest('.question-options').attr('data-nottouch',0);
      var totalanswer=parseInt($('#answered').attr('data-count'))-1;
      $('#answered').attr('data-count',totalanswer);
      $('#answered').text(totalanswer);
      var totalquestions=parseInt($('#total-questions').text());
      var percentage=Math.floor((totalanswer / totalquestions) * 100);
      $('.progress-bar').css('width', percentage+'%').attr('aria-valuenow', percentage); 
    }
    else
    {
      var datatouched=$(this).closest('.question-options').attr('data-nottouch');
      if(datatouched==1){
        var totalanswer=parseInt($('#answered').attr('data-count'))-1;
      $('#answered').attr('data-count',totalanswer);
      $('#answered').text(totalanswer);
      var totalquestions=parseInt($('#total-questions').text());
      var percentage=Math.floor((totalanswer / totalquestions) * 100);
      $('.progress-bar').css('width', percentage+'%').attr('aria-valuenow', percentage); 
      $(this).closest('.question-options').attr('data-nottouch',0);
      }
    }
 }

});

		 $('#next-btn').on('click',function(){

      var h = window.innerHeight;
      //console.log($(this).innerHeight());
      var c= h/2;

		 	var id=$(this).val();

		 	var height=$('li[data-id="'+id+'"]').offset().top-$('.inner-header').outerHeight()-10;
		 	//alert(height);

		 	$('html, body').animate({scrollTop:height}, 'slow');

		 });

		  $('#prev-btn').click(function(){

		 	 var h = window.innerHeight;
      //console.log($(this).innerHeight());
      var c= h/2;

      var id=$(this).val();

      var height=$('li[data-id="'+id+'"]').offset().top-$('.inner-header').outerHeight()-10;

		 	$('html, body').animate({scrollTop:height}, 'slow');

		 });


	//$('#prev-btn').fadeTo("slow", 0.15);


		$(window).bind('scroll', function(e) {
 
			var h = window.innerHeight;
			//console.log($(this).innerHeight());
			var c= h/2;
			console.log("center:"+c);
      console.log("header height:"+$('.inner-header').outerHeight());

            var currentposition = $(this).scrollTop()+$('.inner-header').outerHeight()+c/2;

            var total=$('.question-container li').length;
             $('.question-container li').each(function() {
            	
            	var li = $(this);
              console.log("li height:"+$(this).outerHeight());
            	var position = li.offset().top - currentposition;
            	console.log('currentpostion'+position);
            
	            if (position <= 0) {
	                li.addClass('focus');

	                $('#next-btn').val(li.next().attr('data-id'));
	                 $('#prev-btn').val(li.prev().attr('data-id'));

	                if(li.attr('data-id')>1) 
	                {
	                	$('#prev-btn').css({'opacity':1,'pointer-events':'inherit','transition': '0.2s'});
	                	//$('#prev-btn').fadeIn("slow");
	                }
	                else
	                {
	                	$('#prev-btn').css({'opacity':0.3,'pointer-events':'none','transition': '0.2s'});
	                	//$('#prev-btn').fadeTo("slow", 0.15);


	                }

	                if(total==li.attr('data-id')){
	                	$('#next-btn').css({'opacity':0.3,'pointer-events':'none','transition': '0.2s'});
                    $('#form-submit').show();
                    $('#form-save').hide();
	                }
	                else
	                {
	                	$('#next-btn').css({'opacity':1,'pointer-events':'inherit','transition': '0.2s'});
                    $('#form-submit').show();
                     $('#form-save').show();
	                }

	            } else {
	                li.removeClass('focus');
	                $('.question-container li:first').addClass('focus');

	               // $('.question-container li:last').addClass('focus');
          //  var id=li.next().attr('data-id');
/*

if($( ".question-container" ).scrollTop( 100 ))
{
             $('.question-container li:last').addClass('focus');
}
  
*/	            }

        });
           
        });

});
    </script>

</div>
</div>


<script>
$(document).ready(function(){
$('body').on('keypress','.required',function(el){

 var check_field=$(this).attr("data-type");
  if(!checkerror($(this)))
  {
    var id=$(this).parents('li').next().attr('data-id');
    var crnt_data_id=$(this).prev().attr('data-count');
    var text_id=$(this).attr('name');

    if(id!="" && id!=undefined){
      if (check_field=="textarea" || check_field=="text" ) {

       var length_data=$('[data-count="'+text_id+'"]');
        if (length_data.length==0) {
          $(this).before("<div class='on_submit_text'><input type='button' data-count='"+text_id+"' id='next_move'  value='Ok' class='btn btn-success input'></div>");
          $('.input').on('click',function(){
            var id=$(this).parents('li').next().attr('data-id');
            var height=$('li[data-id="'+id+'"]').offset().top-$('.inner-header').outerHeight()-10;
            $('html, body').animate({scrollTop:height}, 'slow');
         });
        }
      }
    }

  }
  });
});

</script>

<script>
$(document).ready(function(){

$('body').on('click','.required',function(){

 var check_field=$(this).attr("data-type");
  if(!checkerror($(this)))
  {
    var id=$(this).parents('li').next().attr('data-id');
    var crnt_data_id=$(this).prev().attr('data-count');
    var check_id=$(this).attr('name');

    if(id!="" && id!=undefined){
      if (check_field=="checkbox") {

       var length_data=$('[data-count="'+check_id+'"]');
        if (length_data.length==0) {
          $(this).after("<div class='on_submit'><input type='button' data-count='"+check_id+"' id='next_move_check'  value='Ok' class='btn btn-success input'></div>");
          $('.input').on('click',function(){
            var id=$(this).parents('li').next().attr('data-id');
            var height=$('li[data-id="'+id+'"]').offset().top-$('.inner-header').outerHeight()-10;
            $('html, body').animate({scrollTop:height}, 'slow');
         });
        }
      }
    }

  }
  });
});

</script>


<script>
$(document).ready(function(){
$('.required').click(function(){

var check_field=$(this).attr("data-type");
if (check_field=="radio"){

var radio_name=$(this).attr('name');
var radio_id=$(this).attr('id');


  if(!checkerror($(this)))
  {
    var id=$(this).parents('li').next().attr('data-id');
    var crnt_data_id=$(this).prev().attr('data-count');
    var text_id=$(this).attr('name');
    if(id!="" && id!=undefined)
{

      
if($("label[for='"+radio_id+"']").text()==='others')
{
          $(this).before("<div class='on_submit_others'><input type='button' data-count='"+text_id+"' id='next_move_others'  value='Ok' class='btn btn-success input'></div>");
          $('.input').on('click',function(){
            var id=$(this).parents('li').next().attr('data-id');
            var height=$('li[data-id="'+id+'"]').offset().top-$('.inner-header').outerHeight()-10;
            $('html, body').animate({scrollTop:height}, 'slow');
         });
}
else
{

 $('.on_submit_others').hide();
}

      }
   }

}

});
});

</script>
<script>
$(document).ready(function(){
$('.grid-required').click(function(){

var total_question=parseInt($(this).closest('table').find('tr.data-question-type-grid').length);

var obj=$(this).closest('table').find('tr.data-question-type-grid');
var checkcount=0;
$(obj).each(function(){

	checkcount += parseInt($(this).find('input[type=radio]:checked').length);

});
var question_required=$(this).parents('.question').find('.qtn-required').length;

var id=$(this).parents('li').next().attr('data-id');

if(id!="" && id!=undefined){

if(total_question==checkcount)
{

 $(this).parents('li').find('.message').hide();
var height=$('li[data-id="'+id+'"]').offset().top-$('.inner-header').outerHeight()-10;
$('html, body').animate({scrollTop:height}, 'slow');
 var datatouched=$(this).closest('.question-options').attr('data-nottouch');
    if(datatouched==0){

    var totalanswer=parseInt($('#answered').attr('data-count'))+1;
    $('#answered').attr('data-count',totalanswer);
    $('#answered').text(totalanswer);

    var totalquestions=parseInt($('#total-questions').text());
    var percentage=Math.floor((totalanswer / totalquestions) * 100);

    $('.progress-bar').css('width', percentage+'%').attr('aria-valuenow', percentage);
    $(this).closest('.question-options').attr('data-nottouch',1);
    }  
}
else
{
if(question_required==1)
{
 $(this).parents('li').find('.message').show();
}
else
{
$(this).parents('li').find('.message').hide();

}
}


}
else
{
if(total_question==checkcount)
{
var datatouched=$(this).closest('.question-options').attr('data-nottouch');
    if(datatouched==0){

    var totalanswer=parseInt($('#answered').attr('data-count'))+1;
    $('#answered').attr('data-count',totalanswer);
    $('#answered').text(totalanswer);

    var totalquestions=parseInt($('#total-questions').text());
    var percentage=Math.floor((totalanswer / totalquestions) * 100);

    $('.progress-bar').css('width', percentage+'%').attr('aria-valuenow', percentage);
    $(this).closest('.question-options').attr('data-nottouch',1);
    }  
}
}
});
});



</script>
<style type="text/css">
.on_submit_others {
	float: right;
	width: 6%;

}
.on_submit_others input {
	position: absolute;
	margin-top: 32px;
	right: 32px;
}

#next_move  {
	float: right;
	margin: 0 0 -50px;
	position: relative;
 
}

.on_submit {
	float: right;
	width: 6%;
}
.on_submit input {
	position: absolute;
   
}
#next_move_check {
   
    right: 16px;
}
 .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .top-right {
                position: absolute;
                right: 10px;
                top: 18px;
            }

            .content {
                text-align: center;
            }

            .title {
                font-size: 60px;
                color: #636b6f;
            }

            .links > a {
                color: #636b6f;
                padding: 0 25px;
                font-size: 12px;
                font-weight: 600;
                letter-spacing: .1rem;
                text-decoration: none;
                text-transform: uppercase;
            }

            .m-b-md {
                margin-bottom: 30px;
            }
footer
{
display:none;
}
</style>
<div class="" id="fixed-footer">

<div class="progress-section">

</div>
<div class="action-panel">
<div class="actions pull-right">
<input type="submit" name="" value="Submit" class="btn btn-primary" id="form-submit" disabled>
<button class="btn btn-primary " id="prev-btn" value="1"><span class="glyphicon glyphicon-chevron-up" ></span></button>
<button class="btn btn-primary " id="next-btn" value="2"><span class="glyphicon glyphicon-chevron-down"></span></button>
</div>
</div>

	

</div>
<footer>
<div class="container">
<div class="row">
<div class="col-md-12 ">
<h5 class="text-center">
<strong>© 2017 Ascendus. All Rights Reserved.</strong>
</h5>
</div>
</div>
</div>
</footer>
</body>
</html> 
