@extends('layouts.default')

@section('content')
	

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
                                
                                 </ul> </div>
                                @endif             
    <div class="container col-xs-12 text-center">
        <div class="row clearfix">
		    <div class="col-md-3 column">

            <ul class="column">
                <li class="field-type intro" rel="textarea" id="welcome_txt">Welcome Screen</li>
               
            </ul>
            <ul class="column" id="drag-input-fields" style="display: none;">
                <li class="field-type fields" rel="text">Heading Texts</li>
                <li class="field-type fields" rel="radio">Single Choice</li>
                <li class="field-type fields" rel="checkbox">Multiple Choice</li>
                <li class="field-type fields" rel="dropdown">Dropdown</li>
                <li class="field-type fields" rel="text">Single Line Text </li>
                <li class="field-type fields" rel="textarea">Multiple Line Text </li>
                <li class="field-type fields">Question Import</li>
               
            </ul>
             <ul class="column">
                <li class="field-type question-import" id="question-import">Question Import</li>
               
            </ul>
            <ul class="column">
               <li class="field-type outro" rel="textarea" id="thankyou_txt">Thank you Screen</li>
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
               if(intval($value->display_order) >0 )$question_txt[$value->id]=$value->question_text;
              }
            }
            ?>
            <ul id="intro">
           
            @if(!empty($welcome_txt))
            <?php  list($qtnid) = array_keys($welcome_txt); ?>
            <li rel="{{$qtnid}}" class="response-area  intro"><div class="response-label">{{$welcome_txt[$qtnid]}}</div><div class="response-action"><a class="delete-question pull-right" href="#" title="Delete" style="display: none;"><span class="glyphicon glyphicon-trash"></span></a><span style="display:none" rel="welcome_txt" data-id="{{$qtnid}}" data-question-id="{{$qtnid}}" data-display-order="0" data-faction="edit">{{$welcome_txt[$qtnid]}}</span></div></li>
           
            @endif
             <li class="drop-space-intro">Add Welcome Text Here & Write Welcome Message</li>
            

            </ul>

            <div class="page-break">Page Break</div>

            <ul id="fields">
            @if(!empty($question_txt))
            @foreach($question_txt as $qtnid=>$qtnvalue) 

            <div class="response-label">
            <li rel="{{$qtnid}}" class="qtn_txt_db">{{$qtnvalue}}</li>
            </div>
            @endforeach
            @endif
            <li class="drop-space-questions">Add Questions Here</li>

            </ul>


            <div class="page-break" >Page Break</div>


            <ul id="outro">
            
             @if(!empty($thankyou_txt))
            <?php  list($qtnid) = array_keys($thankyou_txt); ?>
            <li rel="{{$qtnid}}" class="response-area  outro"><div class="response-label">{{$thankyou_txt[$qtnid]}}</div><div class="response-action"><a class="delete-question pull-right" href="#" title="Delete" style="display: none;"><span class="glyphicon glyphicon-trash"></span></a><span style="display:none" rel="thankyou_txt" data-id="{{$qtnid}}" data-question-id="{{$qtnid}}" data-display-order="-1" data-faction="edit">{{$thankyou_txt[$qtnid]}}</span></div></li>
            @endif
             <li class="drop-space-outro" >Add Thank you Text Here</li>
           
            </ul>

             <?php 

                $param[]=$survey_id;
                $redirect=Request::get('redirect');
                if($redirect=="home")
                {
                  $param[]='redirect='.$redirect;
                  $actionurl=URL::route('admin.dashboard');
                }
                else
                {
                   $actionurl=URL::route('theme.show',$survey_id);
                }

                ?>
  
                <a href="{{URL::route('survey.edit',$param)}}" class="btn btn-danger btn-md">Cancel</a>
                <button type="button" data-migrate="{{$actionurl}}" id="activate-step-2" class="btn btn-success btn-md">Save & Next</button>


	        </div>
	   
    </div>
               
            </div>
        </div>
    </div>
    </div>
    </div>
  

</form> 




<section class="welcome_text">
<!-- Modal -->


                          <div id="question_modal" class="modal fade" role="dialog">



                            <div class="modal-dialog modal-lg">



                              <!-- Modal content-->

                              <div class="modal-content">


                               

                                <div class="modal-body">
                                
                              
                                  <div class="form-group question-text-form-group">

                                  </div>
                                  <input type="hidden" name="display_order" value="0" id="question-display-order">
                                  <input type="hidden" name="faction" value="add" id="faction">
                                  <input type="hidden" name="question_id" value="" id="hidden-question-id">
                              </div>



                                <div class="modal-footer">

                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

                                <button type="button" id="question-submit-btn" class="btn btn-success" >Save</button>

                                {{HTML::Image("images/img_load.gif","ajax_load",["class"=>"load-image"])}}

                                </div>

                            

                              </div>

                          

                            </div>

</div>
  <div id="questionimport-modal" class="modal fade" role="dialog">



                            <div class="modal-dialog modal-lg">



                              <!-- Modal content-->

                              <div class="modal-content">


                               

                   <div class="modal-body">

                   <div id="show-error-import"></div>
               

                           <div class="form-group">
                            <p class="text-info">Upload .xls .xlsx file with following headers to update the questions for the survey. <b>(question_text,question_type,question_required,question_enabled,question_dimension,display_order,options,option_weight)</b><br>Options and Option weight should have the following format <b>value1|value2</b></p>
                                    <div class="col-sm-12">
                              <input type="file" id="question-file-import" class="filestyle"   data-buttonName="btn-primary" name="import_file"  accept=".xls, .xlsx"/>
                                  </div>
                            </div>
                                
                              
                                  
                    </div>



                                <div class="modal-footer">

                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

                                <button type="button" id="questionimport-submit-btn" class="btn btn-success" >Save</button>

                                {{HTML::Image("images/img_load.gif","ajax_load",["class"=>"load-ajximage"])}}

                                </div>

                            

                              </div>

                          

                            </div>
                           
</div>
<script type="text/javascript">
  $(document).ready(function(){

    $('.delete-question').tooltip();

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
              var error=JSON.stringify(data.error)
              /*var error='';
              for ( var i = 0, l = data.error.length; i < l; i++ ) {
                error +=data.error[i]+"<br>";      
              }*/
              
             
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
   
    
 $('#question-submit-btn').click(function(){

    var question_nature=$('#question_nature').val();
    var survey_id="{{$survey_id}}";
    var display_order=$('#question-display-order').val();
    var question_text_value='';
    var senddata='';
    var actionurl="";
    var question_id=$('#hidden-question-id').val();
    var faction=$('#faction').val();
    var question_required=0;
    var question_dimension='';

    if(question_nature=="welcome_txt" || question_nature=="thankyou_txt")
    {
     
      question_text_value= $('#question-type-textarea-richeditor').Editor("getText");
      $('#question-type-textarea-richeditor').val(question_text_value);
      //question_nature="textarea";

    }
    if(question_nature=="text") 
    {
      question_text_value=$('#question-type-text').val();
      question_required=$("input[name='question_required']:checked").val();
      question_dimension=$('#question_dimension').val();
    }

    //validation

    if(question_text_value.length>0){

    if(faction=="add" && question_id==""){
      senddata={
        survey_id:survey_id,
        "_token": "{{ csrf_token() }}",
        welcome_text:question_text_value,
        question_nature:question_nature,
        display_order:display_order,
        question_required:question_required,
        question_dimension:question_dimension
      };
      actionurl="{{URL::route('questions.store')}}";
    }

    if(faction=="edit" && question_id!=""){
      senddata={
        survey_id:survey_id,
        _method: 'PATCH',
        "_token": "{{ csrf_token() }}",
        welcome_text:question_text_value,
        question_id:question_id,
        question_nature:question_nature,
        display_order:display_order,
        question_required:question_required,
         question_dimension:question_dimension
      };
       actionurl="{{URL::route('questions.update',0)}}";
    }
     
    $.ajax({
          url:actionurl,
          type:'POST',
          dataType: 'json',
          data:senddata,
            beforeSend: function(){
          $('.load-image').show();
          },
          complete: function(){
              $('.load-image').hide();
          },
          success:function(data){
            console.log(data);
            var dropsection='';
            var dropclass='';
            //reset the conditions
             $('.alert.alert-danger.alert-dismissable').remove();
            // alert(data.question_nature);
            //reset the drag and drop conditions
             if(data.question_nature=="welcome_txt")
             {
                $('#question-type-textarea-richeditor').html('');
                $('.field-type.intro').draggable( "disable" );
                $('.field-type.intro').css("opacity",0.3);
                 if( $('.drop-space-intro').prev('.response-area.intro').length)
                  {
                    $('.drop-space-intro').prev('.response-area.intro').remove();
                  }
                  dropsection='.drop-space-intro';
                  dropclass='response-area  intro';

             }

             if(data.question_nature=="thankyou_txt")
             {
                $('#question-type-textarea-richeditor').html('');
                $('.field-type.outro').draggable( "disable" );
                $('.field-type.outro').css("opacity",0.3);
                 if( $('.drop-space-outro').prev('.response-area.outro').length)
                  {
                    $('.drop-space-outro').prev('.response-area.outro').remove();
                  }
                 dropsection='.drop-space-outro';
                 dropclass='response-area  outro';
             }

             if(data.question_nature=="thankyou_txt" || data.question_nature=="welcome_txt")
             {
                $(dropsection).before("<li rel="+data.question_id+" class='"+dropclass+"' ><div class=response-label>"+data.welcome_text+"</div><div class=response-action><a class='delete-question pull-right' href=#  title='Delete'><span class='glyphicon glyphicon-trash'></span></a><span style=display:none rel="+data.question_nature+" data-id="+data.question_id+" data-question-id="+data.question_id+" data-display-order="+data.display_order+" data-faction=edit>"+data.welcome_text_original+"</span></div></li>");
               
             
               $(dropsection).hide();
             }

             if(data.question_nature=="text")
             {
               var html="<li rel="+data.question_id+" class='response-area fields' ><div class=question-display-order>"+data.display_order+"</div><div class=response-label>"+data.welcome_text+"</div><div class=response-action><a class='delete-question pull-right' href=#  title='Delete'><span class='glyphicon glyphicon-trash'></span></a><span style=display:none rel="+data.question_nature+" data-id="+data.question_id+" data-question-id="+data.question_id+" data-display-order="+data.display_order+" data-faction=edit>"+data.welcome_text_original+"</span></div></li>"
                $('#fields li:last').before(html);
             }

               $('#question_modal').modal('hide');


              
           
            
             
             
             
          }
       });
  }
  else
  {
    $('#show-error-box').html('<div class="alert alert-danger alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a><strong>Error!</strong> Please enter the text.</div>');
  }

  });



  });
</script>



 
</section>







{!! HTML::script('script/bootstrap-filestyle.js')!!}
{{ HTML::script('script/editor.js') }}
{{ HTML::style('css/editor.css') }}
{{ HTML::script('script/sweetalert.min.js') }}
{{ HTML::style('css/sweetalert.css') }}
{{ HTML::style('css/admin-addquestion.css') }}

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

    
       

        $( ".drop-space-intro" ).droppable({
             hoverClass: "ui-state-active",
             drop: function( event, ui ) {

              var question_type=ui.draggable.attr('id');
              get_textarea(question_type,true);
              $('#question-display-order').val('0');

                $('#question_modal').modal({
                  show:true,
                  backdrop: 'static',
                  keyboard: false
                });
     
            }

        });
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

              var question_type=ui.draggable.attr('id');
             get_textarea(question_type,true);
              $('#question-display-order').val('-1');

                $('#question_modal').modal({
                  show:true,
                  backdrop: 'static',
                  keyboard: false
                });
     
            }

        });
/*Thankyou  section*/

/*Question Import*/

 $('.field-type.question-import').draggable({
        cursor      : "move",
        revert      : 'invalid',
        helper      : 'clone',
        appendTo    : 'body',
        containment : ".drop-space-questions",
        //classes     : {"ui-draggable": "intro",},
        start: function( event, ui ) {
            ui.helper.css({
              'width'     : '350px',
              'text-align': 'center'
            }); 
         }
        
        });
$(".drop-space-questions" ).droppable({
             hoverClass: "ui-state-active",
             drop: function( event, ui ) {
                $('#questionimport-modal').modal({
                  show:true,
                  backdrop: 'static',
                  keyboard: false
                });
     
            }

        });
/*Question Import





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


  $('body').on('mouseenter', '.response-area', function () {

     $(this).find('.delete-question').show();
     $(this).css('box-shadow','0 1px 1px #bababa');

  }).on('mouseleave', '.response-area', function () {
     $(this).find('.delete-question').hide();
     $(this).css('box-shadow','none');
});




$('body').on('click','.delete-question',function(){
var question_id=$(this).parent().parent().attr('rel');
var question_nature=$(this).next('span').attr('rel');

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
                if(question_nature=="thankyou_txt")
               {
                $('.response-area.outro').remove();
                $('.field-type.outro').draggable( "enable" );
                $('.field-type.outro').css('opacity','inherit');
                $('.drop-space-outro').show();
               
              }
              if(question_nature=="welcome_txt")
               {
                $('.response-area.intro').remove();
                $('.field-type.intro').draggable( "enable" );
                $('.field-type.intro').css('opacity','inherit');
                $('.drop-space-intro').show();
                
              }

              if(question_nature=="text")
              {
                
                $('.response-area.fields').each(function(){
                  if($(this).attr('rel')==question_id) $(this).remove();
              });
               
               
              }

               swal("Deleted!", "Your imaginary file has been deleted.", "success");
            }
          }
      });

 });


});

$('body').on('click','.response-label',function(){

  var question_text=$(this).next().children('span').html();

  var question_nature=$(this).next().children('span').attr('rel');

  var faction=$(this).next().children('span').attr('data-faction');

  var question_id=$(this).next().children('span').attr('data-question-id');

  var display_order=$(this).next().children('span').attr('data-display-order');

  $('#faction').val(faction);

  $('#hidden-question-id').val(question_id);

  $('#question-display-order').val(display_order++);

 $('#question_nature').val(question_nature);

  //var display_order= 0;

 if(question_nature=="thankyou_txt" || question_nature=="welcome_txt") {

  get_textarea(question_nature,true);

  $('#question-type-textarea-richeditor').Editor("setText",question_text);
}

if(question_nature=="text") {

  get_text();
  
  $('#question-type-text').val(question_text);
}
  
  $('#question_modal').modal({
                  show:true,
                  backdrop: 'static',
                  keyboard: false
                });
  

});

     
    });
</script>
<script type="text/javascript">
  function get_textarea(uiobject,richeditor)
  {
    var inputid="question-type-textarea";
    if(richeditor) inputid="question-type-textarea-richeditor";
    var html='<div class="remove-after-modal-close"><div id="show-error-box"></div><br><textarea class="form-control" id="'+inputid+'" style="display:none"></textarea><input id=question_nature type=hidden name=question_nature value='+uiobject+'></div>';
     $('.question-text-form-group').html(html);

     if(richeditor){
           $('#question-type-textarea-richeditor').Editor({'print':false});
           
   }


  }

  function get_text()
  {
    var inputid="question-type-text";
    var html='<div class="remove-after-modal-close"><div id="show-error-box"></div><label>Question Dimension</label><input type=text class="form-control" id=question_dimension value="" name="question_dimension"><br><label>Question</label><input type=text class="form-control" id="'+inputid+'" name="question_text"><input id=question_nature type=hidden name=question_nature value=text><label>Required</label><br><label class="radio-inline"><input type="radio" name="question_required" value=1>Yes</label><label class="radio-inline"><input type="radio" name="question_required" checked=checked value=0>No</label></div>';
     $('.question-text-form-group').html(html);
  }

  function get_radio()
  {
    var inputid="question-type-radio";
    var html='<div class="remove-after-modal-close"><div id="show-error-box"></div><label>Question Dimension</label><input type=text class="form-control" id=question_dimension value="" name="question_dimension"><br><label>Question</label><input type=text class="form-control" id="'+inputid+'" name="question_text"><input id=question_nature type=hidden name=question_nature value=text><label>Required</label><br><label class="radio-inline"><input type="radio" name="question_required" value=1>Yes</label><label class="radio-inline"><input type="radio" name="question_required" checked=checked value=0>No</label></div>';
     $('.question-text-form-group').html(html);
  }
</script>


<script type="text/javascript">
  $(document).ready(function(){


    $('#activate-step-2').on('click',function(){
      
      var welcome_txt_exists=parseInt($('#intro .response-area').length);
      var qtn_txt_exists=parseInt($('#fields .response-label').length);
      var thankyou_txt_exists=parseInt($('#outro .response-area').length);
     
      if(welcome_txt_exists >0 && thankyou_txt_exists >0 && qtn_txt_exists >0)
      {
        window.location=$(this).attr('data-migrate');
      }
      else
      {
        alert('Welcome Text , Thankyou Text and Questions should not be empty');
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

      /*$('#fields').droppable({
        //hoverClass: "ui-state-active",
        drop:function(event,ui){

          $('.drop-space-questions').css('background','#EFEFEF'); 

           var question_nature=ui.draggable.attr('rel');

           var child_length=$(this).children().length;

           var display_order=1;

           if(child_length >1)   display_order=2;
           
          $('#question-display-order').val(display_order);

                eval("get_" + question_nature + "("+display_order+")");
           

           alert($(this).children().length);
           // alert(question_nature);
             // get_textarea(question_type,true);
              //$('#question-display-order').val('0');

                $('#question_modal').modal({
                  show:true,
                  backdrop: 'static',
                  keyboard: false
                });



        }
      });*/

  });
</script>
<style type="text/css">
  .field-type.question-import {
    background: #dddddd none repeat scroll 0 0;
    border-radius: 3px;
    padding: 8px;
    cursor:pointer;
}
.load-ajximage{display: none;}
</style>
@endsection