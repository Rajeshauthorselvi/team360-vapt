@extends('layouts.default')



@section('content')



<link rel="stylesheet" href="{{ asset('css/bootstrap3-wysihtml5.min.css') }}">
<script src="{{ asset('script/bootstrap3-wysihtml5.js') }}"></script>




<!-- <form class="container"> -->

 <form class="form-horizontal container">



    <div class="row setup-content" id="step-2">

         <div class="col-xs-12">

        <div class="form-wrapper">

        <div class="form-steps-wizard step2"> </div>





            <div class="col-md-12 well">

                <h3 class="need-margin-bottom-forstrip text-center">Build Questions</h3>
                           @if ($errors->any())
                                <div class="alert alert-danger fade in">
                                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                                        <strong>Error!</strong> A problem has been occurred while submitting form.<br>
                                        <ul>
                                            {!! implode('', $errors->all('<li class="text-danger">:message</li>')) !!}
                                        </ul>
                                </div>
                            @endif

    <div class="container col-xs-12 text-center">

        <div class="row clearfix">
            <div class="col-md-3 column {{ ($response>0) ? 'disableselections' : ''}} ">
            <ul class="column">
                <li class="field-type intro {{ ($welcome!='') ? 'disableselections' : ''}}" rel="textarea" id="welcome_txt"><img src="{{ asset('images/welcome-icon.png') }}" alt="">
                    Welcome Screen</li>
            </ul>

            <ul class="column" id="drag-input-fields">
                <li class="field-type fields" rel="radio">
                    <img src="{{ asset('images/single-choice-icon.png') }}" alt=""> Single Choice
                </li>
                <li class="field-type fields" rel="checkbox">
                    <img src="{{ asset('images/multiple-choice-icon.png') }}" alt=""> Multiple Choice
                </li>
                <li class="field-type fields" rel="dropdown">
                    <img src="{{ asset('images/dropdown-icon.png') }}" alt=""> Dropdown
                </li>
                <li class="field-type fields" rel="text">
                    <img src="{{ asset('images/short-text-icon.png') }}" alt=""> Single Line Text
                </li>
                <li class="field-type fields" rel="textarea">
                    <img src="{{ asset('images/long-text-icon.png') }}" alt=""> Multi Line Text
                </li>
                <li class="field-type fields" rel="grid">
                    <img src="{{ asset('images/grid-icon.png') }}" alt=""> Grid Questions
                </li>
            </ul>


             <ul class="column">
                <li class="field-type question-import" rel="question-import" id="question-import">Question Import</li>
            </ul>
            <ul class="column">
               <li class="field-type outro {{ ($thank_u!='') ? 'disableselections' : ''}}" rel="textarea" id="thankyou_txt"> <img src="{{ asset('images/thanks-icon.png') }}" alt="">                Thank you Screen</li>
            </ul>
		    </div>
            <div class="col-md-9 question-drop-drag" id="question-drop-drag">

            <?php

            if(!empty($questions))

            {

              $welcome_txt='';

              $thankyou_txt='';

              $question_txt=[];

              foreach ($questions as $key => $value) {

               if($value->display_order=="0")  $welcome_txt=[$value->id=>$value->question_text];

               if($value->display_order=="-1")  $thankyou_txt=[$value->id=>$value->question_text];

               if(intval($value->display_order) >0 )$question_txt[$value->id]=$value->question_type.'|'.$value->question_text."|".$value->question_dimension;

              }

            }

            ?>
            <?php

                    $check_exists_question=DB::table('user_survey_respondent')
                                        ->whereNotNull('last_submitted_date')
                                        ->where('survey_id',$survey_id)
                                        ->count();

            ?>

            @if(count($questions) > 0 && $check_exists_question==0)

            <div class="pull-right delete-button">
              <a href="javascript:void(0)" data-action="{{route('delete.questions',['survey_id'=>$survey_id])}}" class="btn btn-danger" title="Delete All Questions" id="delete-all"><i class="fa fa-trash"></i>
                Delete All Questions
              </a>
            </div>
            @endif

<br>
<br>
            <ul id="intro">
                <?php $css_welcome="inherit";?>

                @if(!empty($welcome_txt))
                    <?php
                        list($qtnid) = array_keys($welcome_txt);
                        $welcome_text=strip_tags($welcome_txt[$qtnid]);
                        if(strlen(trim($welcome_text))>110)
                        {
                            $welcome_text=substr($welcome_text, 0,110)."...";
                        }
                        $css_welcome="none";
                    ?>
                    <li rel="{{$qtnid}}" class="response-area  intro" rel="{{$qtnid}}" ><div class="response-label">{{$welcome_text}}</div><div class="response-action"><a class="delete-question pull-right" href="#" title="Delete" style="display: none;"><span class="glyphicon glyphicon-trash"></span></a></div></li>
                @endif
                <li class="drop-space-intro" style="display:{{$css_welcome}}">Add Welcome Text Here & Write Welcome Message</li>
            </ul>

            <ul id="fields">
            <li class="drop-space-questions">Add Questions Here</li>
            <?php $qcnt=1;  $temp="";?>

            @if(!empty($question_txt))

            @foreach($question_txt as $qtnid=>$qtnvalue)
            <?php
                $exp=explode('|',$qtnvalue);
                $question_nature_format=$exp[0];
                $qtn_txt=$exp[1];
                $question_dimension=$exp[2];
            ?>



            @if(!empty($question_dimension) && $temp!=$question_dimension)

           <!--  <div class="question-dimension" >

           <div class="response-label-db">{{$question_dimension}}</div><div class="response-action"><a class="delete-question pull-right" href="#" title="Delete" style="display: none;"><span class="glyphicon glyphicon-trash"></span></a></div>



           </div> -->

            @endif

           <?php

           $qtn_txt=strip_tags($qtn_txt);

           if(strlen(trim($qtn_txt))>100)

            {

                 $qtn_txt=substr($qtn_txt, 0,80)."...";

            }
            $qdimension=$question_dimension;
            if(strlen(trim($qdimension))>100)
            {
              $qdimension=substr($qdimension, 0,80)."...";
            }

          ?>

            <li id="item_{{$qtnid}}"  title="{{$question_dimension}}"  rel="{{$qtnid}}" class="response-area-db  questiontxt-db" data-question-type="{{$question_nature_format}}" data-preamble="{{$question_dimension}}">

            <div class="response-order-list">{{$qcnt}}


        @if($question_nature_format=='radio')
            <img src="{{ asset('images/single-choice-icon.png') }}" alt="Single Choice">

        @elseif ($question_nature_format=='checkbox')
            <img src="{{ asset('images/multiple-choice-icon.png') }}" alt="Multiple Choice">

        @elseif ($question_nature_format=='dropdown')
            <img src="{{ asset('images/dropdown-icon.png') }}" alt="Dropdown">

        @elseif ($question_nature_format=='text')
            <img src="{{ asset('images/short-text-icon.png') }}" alt="Single Line Text">

        @elseif ($question_nature_format=='textarea')
            <img src="{{ asset('images/long-text-icon.png') }}" alt="Multi Line Text">

        @elseif  ($question_nature_format=='grid')
            <img src="{{ asset('images/grid-icon.png') }}" alt="Grid Questions">
        @endif


</div><div class="response-label-db">{{$qtn_txt}}</div>
<div class="question-dimension-section" >{!!$qdimension!!}</div>
<div class="response-action"><a class="delete-question pull-right" href="#" title="Delete" style="display: none;"><span class="glyphicon glyphicon-trash"></span></a></div>
</li>



            <?php $qcnt++;   $temp=$question_dimension;?>

            @endforeach

            @endif



            </ul>








            <ul id="outro">

            <?php $css_thankyou="inherit"; ?>

             @if(!empty($thankyou_txt))

            <?php  list($qtnid) = array_keys($thankyou_txt);



             $thankyou_text=strip_tags($thankyou_txt[$qtnid]);

             if(strlen(trim($thankyou_text))>110)

             {

                $thankyou_text=substr($thankyou_text, 0,110)."...";

             }

             $css_thankyou="none";

            ?>



               <li rel="{{$qtnid}}" class="response-area  outro" rel="{{$qtnid}}" ><div class="response-label">{{$thankyou_text}}</div><div class="response-action"><a class="delete-question pull-right" href="#" title="Delete" style="display: none;"><span class="glyphicon glyphicon-trash"></span></a></div></li>

            @endif

             <li class="drop-space-outro" style="display:{{$css_thankyou}}" >Add Thank you Text Here</li>



            </ul>


        <div class=" text-center">

                <a href="{{URL::route('survey.edit',$survey_id)}}" class="btn btn-danger btn-md">Cancel</a>

                <button type="button" data-migrate="{{URL::route('questions_group','survey_id='.$survey_id)}}" id="activate-step-2" class="btn btn-success btn-md">Save & Next</button>

   </div>



	        </div>



    </div>



            </div>

        </div>

    </div>

    </div>

    </div>





</form>













<section class="welcome_text">



  <div id="questionimport-modal" class="modal fade" role="dialog">







                            <div class="modal-dialog modal-lg">







                              <!-- Modal content-->



                              <div class="modal-content">









                   <div class="modal-body">



                   <div id="show-error-import"></div>





                           <div class="form-group">

                            <p class="text-info">Upload .xls .xlsx file with following headers to update the questions for the survey. <b>(question_text,question_type,question_required,question_dimension,display_order,options,option_weight)</b>.<br>Options and Option weight should have the following format <b>value1|value2</b></p>
                            <p class="text-info" >Download sample questions <a href="{{URL::route('import-questions.show',$survey_id)}}" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> Download</a></p>

                                    <div class="">

                              <input type="file" id="question-file-import" class="filestyle"   data-buttonName="btn-primary" name="import_file"  accept=".xls, .xlsx"/>

                                  </div>

                            </div>







                    </div>







                                <div class="modal-footer">



                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>



                                <button type="button" id="questionimport-submit-btn" class="btn btn-success" >Save</button>

                                <img src="{{ asset('images/img_load.gif') }}" alt="ajax_load" class="load-ajximage">

                                </div>

                              </div>


                            </div>



</div>









<div id="questionedit-section">



</div>

<script type="text/javascript">

  $(document).ready(function(){



    $('.delete-question').tooltip();

    $('.response-area-db').tooltip();



  $('#questionimport-submit-btn').on('click',function(){



    var error='';

    var avatar = $("#question-file-import").val();

    var extension = avatar.split('.').pop().toUpperCase();

    var avatarok=0;





    if(avatar.length < 1) {

        avatarok = 0;

        error="Please select the file";

    }

    else if (extension!="XLSX" && extension!="XLS"){

        avatarok = 0;

        $("#question-file-import").val('');

        error="invalid extension " +extension;

    }

    else {

        avatarok = 1;

    }







    if(avatarok == 1) {

      var formData = new FormData();

      formData.append("survey_id",  "{{$survey_id}}");

      formData.append("import_file",  $("#question-file-import")[0].files[0]);



       $.ajax({

          url:"{{URL::route('import-questions.store')}}",

          type:'POST',

          dataType: 'json',

          processData: false,

          contentType: false,

          headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },

          data:formData,

          beforeSend: function(xhr){

            // xhr.setRequestHeader('Content-Type', 'multipart/form-data');

            $('.load-ajximage').show();

          },

          complete: function(){

              $('.load-ajximage').hide();

          },

          success:function(data){



            if(data.error)

            {

               var error='';

              $.each(data.error, function (key, val) {

                //  $.each(data.error[i], function (key, val) {

                      error =error+val+"<br>";

                      //alert(val);

                  //});

              });





             $('#show-error-import').html('<div class="alert alert-danger alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a><strong>Error!</strong> ' +error+'</div><br>');



             $('#question-file-import').filestyle('clear');

            }



            if(data.success.length>0)

            {





             $('#show-error-import').html('<div class="alert alert-success alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a><strong>success!</strong> ' +data.success+'</div><br>');

              window.location.reload();

            }



          }

        });

     }

     else

     {

          $('#show-error-import').html('<div class="alert alert-danger alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a><strong>Error!</strong> ' +error+'</div><br>');

     }



 });

  });

</script>

</section>

<script src="{{ asset('script/bootstrap-filestyle.js') }}"></script>
<script src="{{ asset('script/sweetalert.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">
<link rel="stylesheet" href="{{ asset('css/admin-addquestion.css') }}">




<script type="text/javascript">



$(document).ready(function(){

/*welcome section*/

       $('.field-type.intro').draggable({

        cursor      : "move",

        revert      : 'invalid',

        helper      : 'clone',

        appendTo    : 'body',

        containment : ".drop-space-intro",

        //classes     : {"ui-draggable": "intro",},

        start: function( event, ui ) {

            ui.helper.css({

              'width'     : '350px',

              'text-align': 'center'

            });

         }



        });





        $(".drop-space-intro" ).droppable({

             /*hoverClass: "ui-state-active",*/



             drop: function( event, ui ) {



              question_type=ui.draggable.attr('rel');



                 var survey_id="{{$survey_id}}";

                 var display_order=0;

                 var question_id="";

                  $.get('{{URL::route("import-questions.create")}}', {survey_id:survey_id,display_order:display_order,question_id:question_id}).done(function( data ) {



                        $('#questionedit-section').html(data);



                        $('#question-modal').modal({

                                    show:true,

                                  backdrop: 'static',

                                  keyboard: false



                        });



                      }, "html" );





            }



        });




/*start onclick welcometext*/

        $(".intro" ).click(function(){

             /*hoverClass: "ui-state-active",*/

        question_type=$(this).attr('rel');
 if(question_type=="textarea"){

                 var survey_id="{{$survey_id}}";

                 var display_order=0;

                 var question_id="";

                  $.get('{{URL::route("import-questions.create")}}', {survey_id:survey_id,display_order:display_order,question_id:question_id}).done(function( data ) {

              $('#questionedit-section').html(data);

             $('#question-modal').modal({

                                  show:true,

                                  backdrop: 'static',

                                  keyboard: false

                        });

                      }, "html" );
}

});



/*end onclick welcometext*/

/*welcome section*/





/*Thankyou section*/

       $('.field-type.outro').draggable({

        cursor      : "move",

        revert      : 'invalid',

        helper      : 'clone',

        appendTo    : 'body',

        containment : ".drop-space-outro",

        //classes     : {"ui-draggable": "intro",},

        start: function( event, ui ) {

            ui.helper.css({

              'width'     : '350px',

              'text-align': 'center'

            });

         }



        });



        $( ".drop-space-outro" ).droppable({

             hoverClass: "ui-state-active",

              drop: function( event, ui ) {



              question_type=ui.draggable.attr('rel');



                 var survey_id="{{$survey_id}}";

                 var display_order=-1;

                 var question_id="";

                  $.get('{{URL::route("import-questions.create")}}', {survey_id:survey_id,display_order:display_order,question_id:question_id}).done(function( data ) {



                        $('#questionedit-section').html(data);



                        $('#question-modal').modal({

                                    show:true,

                                  backdrop: 'static',

                                  keyboard: false



                        });



                      }, "html" );





            }



        });



/*start onclick Thankyou*/

        $(".outro" ).click(function(){

             /*hoverClass: "ui-state-active",*/

        question_type=$(this).attr('rel');
 if(question_type=="textarea"){

                 var survey_id="{{$survey_id}}";

                 var display_order=-1;

                 var question_id="";

                  $.get('{{URL::route("import-questions.create")}}', {survey_id:survey_id,display_order:display_order,question_id:question_id}).done(function( data ) {

              $('#questionedit-section').html(data);

             $('#question-modal').modal({

                                  show:true,

                                  backdrop: 'static',

                                  keyboard: false

                        });

                      }, "html" );
}

});



/*end onclick Thankyou*/







/*Thankyou  section*/



/*Question Import*/

//question-import

 $('.field-type.fields').draggable({

        cursor      : "move",

        revert      : 'invalid',

        helper      : 'clone',

        appendTo    : 'body',

        containment : ".drop-space-questions",

        //classes     : {"ui-draggable": "intro",},

        start: function( event, ui ) {

          /*  ui.helper.css({

              'width'     : '350px',

              'text-align': 'center',



            }); */

         }



        });

 //$('#question-import').draggable('disable');


 $('#question-import').click(function(){

                $('#questionimport-modal').modal({

                  show:true,

                  backdrop: 'static',

                  keyboard: false

                });
              });



$('.fields').click(function(){

	question_type=$(this).attr('rel');

              if(question_type=="question-import"){

                $('#questionimport-modal').modal({

                  show:true,

                  backdrop: 'static',

                  keyboard: false

                });

              }

              else

              {

                 var survey_id="{{$survey_id}}";

                 var display_order=$('#fields li.response-area-db').length;

                  $.get('{{URL::route("questions.create")}}', {question_type:question_type,survey_id:survey_id,display_order:display_order}).done(function( data ) {

                        $('#questionedit-section').html(data);

                        $('#questionadd-modal').modal({

                                    show:true,

                                  backdrop: 'static',

                                  keyboard: false



                        });



                      }, "html" );

              }


});



/*end of click action */

$(".drop-space-questions" ).droppable({

             /*hoverClass: "ui-state-active",*/



             drop: function( event, ui ) {



              question_type=ui.draggable.attr('rel');



              if(question_type=="question-import"){



                $('#questionimport-modal').modal({

                  show:true,

                  backdrop: 'static',

                  keyboard: false

                });

              }

              else

              {

                 var survey_id="{{$survey_id}}";

                 var display_order=$('#fields li.response-area-db').length;

                  $.get('{{URL::route("questions.create")}}', {question_type:question_type,survey_id:survey_id,display_order:display_order}).done(function( data ) {



                        $('#questionedit-section').html(data);



                        $('#questionadd-modal').modal({

                                    show:true,

                                  backdrop: 'static',

                                  keyboard: false



                        });



                      }, "html" );

              }



            }



        });





/*Question Import



/*Sortable*/
if({{$response}}=="0"){
$( "#fields" ).sortable({

        placeholder: "ui-state-highlight",

        opacity: 0.6,

        cursor:'move',

        distance:10,

        axis:'Y'



});
}


  $('#fields').on('sortupdate',function(event, ui){

    //update: function(event, ui) {



        var display_order =$(this).sortable('serialize');

        $.ajax({

          url:"{{URL::route('questions.index')}}",

          type:'GET',

          dataType: 'json',

          data:display_order,

            beforeSend: function(){

          //$('.load-image').show();

          ui.item.find('.response-order-list').css({"background-image":'url("../images/img_load.gif")',"background-repeat":"no-repeat"});

          ui.item.css('opacity',0.3);

          },

          complete: function(){

            ui.item.find('.response-order-list').css({'background-image':'none'});

            ui.item.css('opacity',1);

          },

          success:function(data){



                      $('#fields li').each(function(){



                        var id=$(this).attr('rel');



                        checkexists = id in data;

                        if(checkexists)

                        {

                          $(this).find('.response-order-list').html(data[id]);



                        }



                      });

                  }





        });





    });



   // });





$("#fields").disableSelection();







/*Sortable*/





/*closing the modal*/

var $modal = $('#question_modal');



//when hidden

$modal.on('hidden.bs.modal', function(e) {

 $('.remove-after-modal-close').remove();

});



$modal.modal('hide');

/*closing the modal*/



/*closing question import modal */

var $modal = $('#questionimport-modal');

$modal.on('hidden.bs.modal', function(e) {

 $('.remove-after-modal-close').remove();





});



$modal.modal('hide');

/*closing question import modal */











if({{$response}}=="0"){
  $('body').on('mouseenter', '.response-area', function () {



     $(this).find('.delete-question').show();



     $(this).css('box-shadow','0 1px 1px #bababa');



  }).on('mouseleave', '.response-area', function () {

     $(this).find('.delete-question').hide();


     $(this).css('box-shadow','none');

});



  $('body').on('mouseenter', '.response-area-db', function () {



     $(this).find('.delete-question').show();
       $(this).find('.question-dimension-section').hide();

     $(this).css('box-shadow','0 1px 1px #bababa');



  }).on('mouseleave', '.response-area-db', function () {

     $(this).find('.delete-question').hide();

     $(this).css('box-shadow','none');
       $(this).find('.question-dimension-section').show();

});




}








$('body').on('click','.delete-question',function(){

var question_id=$(this).parent().parent().attr('rel');

var classes=$(this).parent().parent().attr('class');



console.log(classes);



swal({

  title: "Are you sure?",

  text: "Your will not be able to recover this data!",

  type: "warning",

  showCancelButton: true,

  confirmButtonClass: "btn-danger",

  confirmButtonText: "Yes, delete it!",

  closeOnConfirm: false,

  showLoaderOnConfirm: true

},

function(){





 $.ajax({

          url:"{{URL::route('questions.destroy',0)}}",

          type:'POST',

          dataType: 'json',

          data:{question_id:question_id,_method: 'delete',"_token": "{{ csrf_token() }}"},

            beforeSend: function(){

          $('.load-image').show();

          },

          complete: function(){

              $('.load-image').hide();

          },

          success:function(data){



            if(data.status="success")

            {

                if(classes=="response-area  outro")

               {

                $('.response-area.outro').remove();

                $('.field-type.outro').draggable( "enable" );

		$('.field-type.outro').css( "pointer-events","inherit" );
		$('.field-type.outro').removeClass('disableselections');


                $('.field-type.outro').css('opacity','inherit');

                $('.drop-space-outro').show();



              }

              else if(classes=="response-area  intro")

               {

                $('.response-area.intro').remove();

                $('.field-type.intro').draggable( "enable" );
		$('.field-type.intro').css( "pointer-events","inherit" );
		$('.field-type.intro').removeClass('disableselections');

                $('.field-type.intro').css('opacity','inherit');

                $('.drop-space-intro').show();



              }



              else

              {



                $('.response-area-db').each(function(){

                  if($(this).attr('rel')==question_id) $(this).remove();

                  var count=1;

                  $('#fields li.response-area-db ').each(function(){







                          $(this).find('.response-order-list').html(count);

                          count++;



                      });

              });





              }



               swal("Deleted!", "Your imaginary file has been deleted.", "success");

            }

          }

      });



 });



});





$('body').on('click','.response-label-db,.response-order-list',function(){



  var qtnid=$(this).parent().attr('rel');



  if(qtnid!="")

  {

        $(this).parent().find('.response-order-list').css({"background-image":'url("../images/img_load.gif")',"background-repeat":"no-repeat"});



       $(this).parent().css({"opacity":0.3});



       $.get('{{URL::route("questions.edit",0)}}', {question_id:qtnid}).done(function( data ) {



        $('#questionedit-section').html(data);



        $('#questionedit-modal').modal({

                    show:true,

                  backdrop: 'static',

                  keyboard: false



        });



      }, "html" );





  }







});



$('body').on('click','.response-label',function(){





  var question_id=$(this).parent().attr('rel');

  var survey_id="{{$survey_id}}";



  $.get('{{URL::route("import-questions.create")}}', {question_id:question_id,survey_id:survey_id}).done(function( data ) {



                        $('#questionedit-section').html(data);



                        $('#question-modal').modal({

                                    show:true,

                                  backdrop: 'static',

                                  keyboard: false



                        });



                      }, "html" );





    });



});



</script>





@if($css_thankyou=="none")

<script type="text/javascript">

  $(document).ready(function(){

     $('.field-type.outro').draggable( "disable" );

                $('.field-type.outro').css("opacity",0.3);

   });

</script>

@endif



@if($css_welcome=="none")

<script type="text/javascript">

  $(document).ready(function(){

     $('.field-type.intro').draggable( "disable" );

                $('.field-type.intro').css("opacity",0.3);

   });

</script>

@endif









<script type="text/javascript">

  $(document).ready(function(){





    $('#activate-step-2').on('click',function(){



      var welcome_txt_exists=parseInt($('#intro .response-area').length);

      var qtn_txt_exists=parseInt($('#fields .response-area-db').length);

      var thankyou_txt_exists=parseInt($('#outro .response-area').length);



      if( qtn_txt_exists >0)
    //   if(welcome_txt_exists >0 && thankyou_txt_exists >0 && qtn_txt_exists >0)

      {

        window.location=$(this).attr('data-migrate');

      }

      else

      {

        var error='';

        // if(welcome_txt_exists==0) error +='Welcome Text should not be empty'+'\n';

          if(qtn_txt_exists==0) error +='Questions should not be empty'+'\n';

            // if(thankyou_txt_exists==0) error +='Thankyou Text should not be empty'+'\n';

        if(error!="") swal("Please Look!", error, "warning")

      }

    });



      $('.field-type.fields').draggable({

        helper:'clone',

        containment : "#fields",

          start: function( event, ui ) {

            ui.helper.css({

              'width'     : '350px',

              'text-align': 'center',

              'background': 'grey',

              'color'     : 'black'



            });

            $('.drop-space-questions').css('background','#FFFFFF');

         }

      });



  });


      $('#delete-all').click(function(){

        var action=$(this).attr('data-action');

         $obj=$(this).closest('form');
          swal({
          title: "Are you sure want to delete all Questions ?",
          type: "warning",
          showCancelButton: true,
          confirmButtonClass: "btn-danger",
          confirmButtonText: "Yes, delete it!",
          closeOnConfirm: false
        },
        function(isConfirm){
          if (isConfirm) {
                window.location.href =action;
          }
       });

        });







</script>

<style type="text/css">



  .field-type.question-import {

    background: #f3f3f3 none repeat scroll 0 0;

    border-radius: 3px;

    padding: 8px;

    cursor:pointer;

    border:1px solid #ddd;

}
  .field-type.question-grouping {

    background: #f3f3f3 none repeat scroll 0 0;

    border-radius: 3px;

    padding: 8px;

    cursor:pointer;

    border:1px solid #ddd;

}


.field-type.question-import:hover{
  background: #fbfbfb none repeat scroll 0 0;
}

.load-ajximage{display: none;}
.disableselections {
    pointer-events: none;
}
.field-type.fields:nth-child(2n+1) {
    clear: both;
}
ul.column:nth-child(2) {
    margin-bottom: 0;
}
.clear-fix{clear: both;}

.alert.alert-danger.alert-dismissable {
    margin-bottom: 15px !important;
}
</style>



@endsection
