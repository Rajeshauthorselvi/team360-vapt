@extends('layouts.default')

@section('content')
	

<!-- <form class="container"> -->
 <form class="form-horizontal container"> 
           
    <div class="row setup-content" id="step-2">
        <div class="col-xs-12">
            <div class="col-md-12 well text-center">
                <h2> STEP 2 - Build Questions</h2>

           
   
                           @if ($errors->any())
                                  <div class="alert alert-danger fade in">

                                <a href="#" class="close" data-dismiss="alert">&times;</a>

                                <strong>Error!</strong> A problem has been occurred while submitting form.<br>
                                <ul>
                                {!! implode('', $errors->all('<li class="text-danger">:message</li>')) !!}
                                
                                 </ul> </div>
                                @endif             
    <div class="container col-xs-12">
        <div class="row clearfix">
		    <div class="col-md-3 column">

            <ul class="column">
                <li class="field-type intro" rel="textarea">Welcome Screen</li>
               
            </ul>
            <ul class="column">
                <li class="field-type fields" rel="text">Heading Texts</li>
                <li class="field-type fields" rel="radio">Single Choice</li>
                <li class="field-type fields" rel="checkbox">Multiple Choice</li>
                <li class="field-type fields" rel="dropdown">Dropdown </li>
                <li class="field-type fields" rel="text">Single Line Text </li>
                <li class="field-type fields" rel="textarea">Multiple Line Text </li>
                <li class="field-type fields">Question Import</li>
               
            </ul>
            <ul class="column">
               <li class="field-type outro" rel="textarea">Thank you Screen</li>
            </ul>
			
		    </div>

            <div class="col-md-9 question-drop-drag" id="question-drop-drag">

            <ul id="intro">
            <li class="drop-space-intro">Add Welcome Text Here & Write Welcome Message</li>
            </ul>

            <div class="page-break">Page Break</div>

            <ul id="fields">
             <li class="drop-space-questions">Add Questions Here</li>
            </ul>


            <div class="page-break" >Page Break</div>


            <ul id="outro">
            <li class="drop-space-outro" >Add Thank you Text Here</li>
            </ul>


	        </div>
	   
    </div>
                
  
                <a href="{{URL::route('admin.dashboard')}}" class="btn btn-danger btn-md">Cancel</a>
                <button id="activate-step-2" class="btn btn-success btn-md">Save & Next</button>
            </div>
        </div>
    </div>

</form> 







<section class="deletemodal">
<div id="delete-modal" class="modal fade" role="dialog">

  <div class="modal-dialog">
    <div class="modal-content">
          <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-hidden="true"><span class="glyphicon glyphicon-remove" aria-hidden="true"></span></button>
        <h4 class="modal-title custom_align" id="Heading">Delete this entry</h4>
      </div>
          <div class="modal-body">
       
       <div class="alert alert-danger"><span class="glyphicon glyphicon-warning-sign"></span> Are you sure you want to delete this Record?</div>
       
      </div>
        <div class="modal-footer ">
        <input type="hidden" id="delete-record" value="">
        <button type="button" class="btn btn-success" id="delete-action-btn"><span class="glyphicon glyphicon-ok-sign"></span>&nbsp;Yes</button>
        <button type="button" class="btn btn-default" data-dismiss="modal"><span class="glyphicon glyphicon-remove"></span>&nbsp;No</button>
      </div>
        </div>
    <!-- /.modal-content --> 
  </div>
  </div>
</section>

<section class="welcome_text">
<!-- Modal -->


                          <div id="welcome_txt_modal" class="modal fade" role="dialog">



                            <div class="modal-dialog modal-lg">



                              <!-- Modal content-->

                              <div class="modal-content">


                                <div id="show-error-box"></div>

                                <div class="modal-body">
                                
                              
                                  <div class="form-group welcome-text-form-group">

                                  {{Form::textarea('question_text',null,['class'=>'form-control','required','id'=>'welcome_txt_area'])}}

                                  </div>
                               
                                  <input type="hidden" name="faction" value="add" id="faction">
                                  <input type="hidden" name="question_id" value="" id="hidden-question-id">
                              </div>



                                <div class="modal-footer">

                                <button type="button" class="btn btn-danger" data-dismiss="modal">Cancel</button>

                                <button type="button" id="welcome-txt-submit-btn" class="btn btn-success" >Save</button>

                                {{HTML::Image("images/img_load.gif","ajax_load",["class"=>"load-image"])}}

                                </div>

                            

                              </div>

                          

                            </div>
                           

<script type="text/javascript">
  $(document).ready(function(){

    $('.delete-question').tooltip();
 
    $('#welcome_txt_area').summernote({onKeyup: function(e) {

        $("#welcome_txt_area").val($(this).code());
      },
     toolbar: [
    ['style', ['style']],
    ['font', ['bold', 'italic', 'underline', 'clear']],
    ['fontname', ['fontname']],
    ['color', ['color']],
    ['para', ['ul', 'ol', 'paragraph']],
    ['height', ['height']],
    ['table', ['table']],
    ['insert', ['link', 'picture', 'hr']],
    ['view', [ 'codeview']],
    ['help', ['help']]
  ],
      height:200,
    focus: true,
   
});

   $('#welcome-txt-submit-btn').click(function(e){

    var welcome_text_val=$($('#welcome_txt_area').code()).text();

    if(welcome_text_val!=""){
    var survey_id="{{$survey_id}}";
    var welcome_text_value=$('#welcome_txt_area').code();
    var question_id=$('#hidden-question-id').val();
    var faction=$('#faction').val();
    var senddata='';
    var actionurl="";
    if(faction=="add" && question_id==""){
      senddata={survey_id:survey_id,"_token": "{{ csrf_token() }}",welcome_text:welcome_text_value};
      actionurl="{{URL::route('questions.store')}}";
    }

    if(faction=="edit" && question_id!=""){
      senddata={survey_id:survey_id,_method: 'PATCH',"_token": "{{ csrf_token() }}",welcome_text:welcome_text_value,question_id:question_id};
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

            $('.alert.alert-danger.alert-dismissable').remove();
           
            $('#welcome_txt_area').code('');
               $('.field-type.intro').draggable( "disable" );
               $('.field-type.intro').css("opacity",0.3);
           
              $('#welcome_txt_modal').modal('hide');
             
              if( $('.drop-space-intro').prev('.response-area.intro').length)
              {
                $('.drop-space-intro').prev('.response-area.intro').remove();
              }
             $('.drop-space-intro').before("<li rel="+data.question_id+" class='response-area  intro' ><div class=response-label>"+data.welcome_text+"</div><div class=response-action><a class='delete-question pull-right' href=#  title='Delete'><span class='glyphicon glyphicon-trash'></span></a></div></li>");
             $('#welcome_txt_area').code(data.welcome_text_original);
             $('#faction').val('edit');
             $('#hidden-question-id').val(data.question_id);
             $('.drop-space-intro').hide();
          }
       });
  }
  else
  {
    $('#show-error-box').html('<div class="alert alert-danger alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">×</a><strong>Error!</strong> Welcome Text Cannot be empty.</div>');
  }

  });



  });
</script>


<style type="text/css">
.glyphicon.glyphicon-trash{color: #000;}
.response-area.intro {
    background-color: #e5efe5;
    border: 1px solid #d8d8d8;
    float: left;
    width: 100%;
    margin-bottom: 5px;

}
.response-label
{
    color:#39434f;
    float: left;
    font-size: 14px;
    padding: 10px;
    text-align: left;
    width: 95%;
    cursor: pointer;
}

.form-group.welcome-text-form-group {
    margin: auto;
}
#show-error-box {
    margin: auto;
    max-width: 80%;
    padding: 15px 0 0;
}
.alert.alert-danger.alert-dismissable {
    margin-bottom: auto;
}
.load-image{display: none;}
.delete-question{display: none;padding: 10px;}
</style>
 </div>
</section>


<style type="text/css">
  ul.column {
    float: left;
    width: 100%;
    padding: 0px;
    list-style: none;
}
.question-drop-drag ul{list-style: none; float: left;width: 100%;}
.field-type.intro {
    background: #cce6c5 none repeat scroll 0 0;
    border-radius: 2px;
    color: #557058;
    padding: 8px;
    cursor: pointer;
}

.field-type.fields {
    background: #f1f1f1 none repeat scroll 0 0;
    border: 1px solid #d8d8d8;
    box-shadow: none;
    color: #555555;
    font-size: 12px;
    font-weight: 500;
    padding: 8px;
    text-shadow: none;
    margin-bottom:10px;
    cursor: pointer; 
    float: left;
    margin-right: 2%;
    width: 48%;
    border-radius: 2px;
}
.field-type.outro
{
  background: #dbeced none repeat scroll 0 0;
    border: medium none !important;
    box-shadow: none;
    color: #5e7374;
    font-size: 13px;
    font-weight: 500;
    padding: 8px;
    text-shadow: none;
    cursor: pointer;
     border-radius: 2px;
}
.field-type.intro:hover{background: #D9ECD3 none repeat scroll 0 0;  }
.field-type.fields:hover{background: #FBFBFB none repeat scroll 0 0; }
.field-type.outro:hover{background: #E9F3F4 none repeat scroll 0 0;  }
.question-drop-drag ul{padding: 0;}

.page-break {
    background: #D8DAA5 none repeat scroll 0 0;
    padding:3px;
    border-radius: 5px;
    border:1px dashed #999;
    margin-bottom:10px; 
    color: #65655d;
    opacity: 0.8;
    clear: both;
}

.drop-space-intro {
    background: #e4f2df none repeat scroll 0 0;
    border: 1px dashed #999999;
    border-radius: 3px;
    clear: both;
    margin-bottom: 6px;
    opacity: 0.9;
    position: relative;
    color: #899186;
    padding: 30px;
  }

.drop-space-questions{
   background: #eeeeee none repeat scroll 0 0;
    border: 1px dashed #999999;
    border-radius: 3px;
    clear: both;
    margin-bottom: 6px;
    opacity: 0.9;
    position: relative;
    color: color: #aeaeae;
    padding: 15px;
}

.drop-space-outro {
    background: #dff1f2 none repeat scroll 0 0;
    border: 1px dashed #999999;
    border-radius: 3px;
    clear: both;
    margin-bottom: 6px;
    opacity: 0.9;
    position: relative;
    color: #869191;
    padding: 30px;
  }

.ui-draggable{list-style: none;}
.response-action {
    float: right;
    width: 5%;
}


</style>

{{ HTML::style('script/font-awesome/css/font-awesome.css') }}
{{ HTML::script('script/summernote/summernote.js') }}
{{ HTML::style('script/summernote/summernote.css') }}
{{ HTML::script('script/sweetalert.min.js') }}
{{ HTML::style('css/sweetalert.css') }}

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

              var question_type=ui.draggable.attr('rel');


                $('#welcome_txt_modal').modal({
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

              var question_type=ui.draggable.attr('rel');


                $('#welcome_txt_modal').modal({
                  show:true,
                  backdrop: 'static',
                  keyboard: false
                });
     
            }

        });
/*Thankyou  section*/





/*var $modal = $('#welcome_txt_modal');

//when hidden
$modal.on('hidden.bs.modal', function(e) { 
  $('#welcome-text').html(localStorage.getItem("welcome_text"));
  return this.render(); //DOM destroyer
});

$modal.modal('hide'); 
*/

  $('body').on('mouseenter', '.response-area.intro', function () {

     $(this).find('.delete-question').show();
     $(this).css('box-shadow','0 1px 1px #bababa');

  });
  $('body').on('mouseleave', '.response-area.intro', function () {
     $(this).find('.delete-question').hide();
     $(this).css('box-shadow','none');
});




$('body').on('click','.delete-question',function(){
var question_id=$(this).parent().parent().attr('rel');
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
              
              $('.response-area.intro').remove();
              $('.field-type.intro').draggable( "enable" );
              $('.field-type.intro').css('opacity','inherit');
              $('.drop-space-intro').show();
              $('#welcome_txt_area').code('');
               swal("Deleted!", "Your imaginary file has been deleted.", "success");
            }
          }
      });

 });


});

$('body').on('click','.response-label',function(){

 $('#welcome_txt_modal').modal({
                  show:true,
                  backdrop: 'static',
                  keyboard: false
                });
  

});

     
    });
</script>
<script type="text/javascript">
  function get_textarea(val)
  {
    alert('kdfkdjfkd');


  }
</script>
@endsection