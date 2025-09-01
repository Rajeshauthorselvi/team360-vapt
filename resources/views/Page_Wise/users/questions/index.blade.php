@extends('Page_Wise.users.layouts.default')

@section('content')


<div class="paginate">
<div class="container parent">
<div class="row">

@include('Page_Wise.users.questions.functions')
@if($welcome_text!='')
@include('Page_Wise.users.questions.welcome')
@endif
<div class="col-sm-12  col-md-10 col-md-offset-1 col-xs-12 welcome-txt" id="question-container">

  {!! Form::open(array('route'=>['user.store',config('site.survey_slug')],'method'=>'POST','id'=>'survey-user-form')) !!}


  <div class="bs-example" data-example-id="contextual-panels">

      <ul class="question-container ">
      @if(count($questions)>0)
       <?php $count=1 ?>
            <div class="items">
          @foreach($questions as $key=>$question)
              <div class="item">
                    @if($question->question_type=="grid")
                         @include('Page_Wise.users.questions.gridquestions')
                        @php
                          $qtext=explode('|', $question->question_text);
                          $count+= count($qtext);
                        @endphp

                    @else

                        @include('Page_Wise.users.questions.alltypequestions')
                        <?php $count++; ?>
                    @endif
              </div>
          @endforeach
          </div>
      @else
        <div class="title m-b-md">
          <div class="text-center">NO QUESTIONS FOUND</div>
        </div>

      @endif
      </ul>
    </div>
  </div>

  {{Form::hidden('currentli',1,['id'=>'currentli'])}}
  {{Form::hidden('formaction','submit',['id'=>'formaction'])}}
  <input type="hidden" class="perpage" value="{{ $question_per_page }}">
  <input type="hidden" name="user_survey_respondent_id" value="{{$user_survey_id}}">
  {{Form::close()}}

</div>

</div>

<?php $rcount=$response_count;?>
@if(($rcount==0) && ($welcome_text==''))
<style type="text/css">
  #question-container,#fixed-footer{display: inherit;}
</style>
@endif
@if($rcount>0)
<style type="text/css">
  #question-container,#fixed-footer{display: inherit;}
</style>
<script type="text/javascript">

$(document).ready(function(){

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
    $(this).find('.message').show();
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


 if(liposition.length > 0)
 {
    var height=$('li[data-id="'+liposition[0]+'"]').offset().top-$('.inner-header').outerHeight()-10;
    $('html, body').animate({scrollTop:height}, 'slow');

 }

});


</script>
@endif
@include('Page_Wise.users.questions.fixedfooter')
</div>
<script type="text/javascript">
  $(".paginate").paginga({
    itemsPerPage:{{ $question_per_page }}
  });
</script>
@endsection
