@extends('layouts.default')

@section('content')
	

<!-- <form class="container"> -->

            {{ Form::open(array('route' => 'questions.store','role'=>'form','class'=>'form-horizontal container','files'=>'true')) }}
    <div class="row setup-content" id="step-2">
        <div class="col-xs-12">
            <div class="col-md-12 well text-center">
                <h2> STEP 2 - Build Questions</h2>

<!-- <form> -->               
   
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

            <ul class="question-objects" id="question-objects">
                <li id="drag-welcome" rel="1" class="ui-state-default">Welcome Screen</li>
                <li>Heading Texts</li>
                <li>Single Choice</li>
                <li>Multiple Choice</li>
                <li>Dropdown </li>
                <li>Single Line Text </li>
                <li>Multiple Line Text </li>
                <li>Question Import</li>
                <li>Thank you Screen</li>
            </ul>
			
		    </div>

            <div class="col-md-9 question-drop-drag" id="question-drop-drag">

            <div id="welcome-text" class="welcome-text">
            
            Add Welcome Text Here & Write Welcome Message
           
            </div>

            <div class="page-break">Page Break</div>

            <div id="add-questions" class="add-questions">Add Questions Here</div>


            <div class="page-break" >Page Break</div>

            <div id="thankyou-text" class="thankyou-text">Add Thank you Text Here</div>


	        </div>
	   
    </div>
                
   {{Form::close()}}
                <a href="{{URL::route('admin.dashboard')}}" class="btn btn-danger btn-md">Cancel</a>
                <button id="activate-step-2" class="btn btn-success btn-md">Save & Next</button>
            </div>
        </div>
    </div>

<!-- </form> -->

{{Form::close()}}

{{ HTML::style('script/font-awesome/css/font-awesome.css') }}
{{ HTML::script('script/summernote/summernote.js') }}
{{ HTML::style('script/summernote/summernote.css') }}


<script type="text/javascript">

$(document).ready(function(){

  $('body').on('mouseenter', '.welcome_text_afterDB', function () {

     $(this).find('.delete-question').show();

  });
  $('body').on('mouseleave', '.welcome_text_afterDB', function () {
     $(this).find('.delete-question').hide();
});

$('[data-toggle="tooltip"]').tooltip(); 


$('body').on('click','.welcome_text_afterDB',function(){

  alert('i am came from welcome');

  $('#welcome_txt_modal').modal({
      show:true,
      backdrop: 'static',
      keyboard: false
    });

});



$('body').on('click','.delete-question',function(){

  alert('i am came from delete');

});

});

    $(function () {

       $('#drag-welcome').draggable({
        cursor: "move",
        revert:'invalid',
        helper: 'clone',
        appendTo:'body',
        containment: "#welcome-text",
          classes: {
    "ui-draggable": "highlight",
  }
    });

           $( "#welcome-text" ).droppable({
            accept: "#drag-welcome",
             hoverClass: "ui-state-active",

            drop: function( event, ui ) {
                //alert('came');
                 //var elem = ui.draggable;
                 //var question_type_id=elem.attr('rel');
                 //var drop_elem=ui.droppable;
                 //cache items
             
                 //localStorage.setItem("welcome_text", $(this).html());
                 //
                // $(this).html('{{HTML::Image("images/img_load.gif","ajax_load",["id"=>"load-image"])}}');
                //$('#load-image').hide();
      /*$.ajax({
        url:"{{URL::route('questions.store')}}",
        type:'POST',
        data:{survey_id:survey_id,question_type_id:question_type_id,"_token": "{{ csrf_token() }}"},
          beforeSend: function(){
        $('#load-image').show();
        },
        complete: function(){
            $('#load-image').hide();
        },
        success:function(e){
          $('body').append(e);
        }
     });*/
    
       $('#welcome_txt_modal').modal({
      show:true,
      backdrop: 'static',
      keyboard: false
    });
     
            }


           
    });

/*var $modal = $('#welcome_txt_modal');

//when hidden
$modal.on('hidden.bs.modal', function(e) { 
  $('#welcome-text').html(localStorage.getItem("welcome_text"));
  return this.render(); //DOM destroyer
});

$modal.modal('hide'); 
*/

     
    });
</script>



<style type="text/css">
    ul{list-style: none;}
    .highlight{
        list-style: none; 
        background: #E6F3E2; 
        width:300px; 
        padding: 8px; 
        border:1px solid #E6F3E2; 
        border-radius: 5px;
        color: #B6ADA2;
        text-align: center;

    }
</style>

<section class="welcome_text">
<!-- Modal -->


                          <div id="welcome_txt_modal" class="modal fade" role="dialog">



                            <div class="modal-dialog modal-lg">



                              <!-- Modal content-->

                              <div class="modal-content">



                                <div class="modal-header">

                                  <button type="button" class="close" data-dismiss="modal">&times;</button>

                                  <h4 class="modal-title">Add Welcome Text</h4>

                                </div>

<div id="show-error-box"></div>

                                <div class="modal-body">
                                
                              
                                  <div class="form-group welcome-text-form-group">

                                  {{Form::textarea('question_text',null,['class'=>'form-control','required','id'=>'welcome_txt_area'])}}

                                  </div>
                               

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
 
    $('#welcome_txt_area').summernote({
      onKeyup: function(e) {

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
//$('.note-statusbar').hide(); 






   $('#welcome-txt-submit-btn').click(function(){

    var welcome_text_val=$($('#welcome_txt_area').code()).text();

    if(welcome_text_val!=""){
    var survey_id="{{$survey_id}}";
    var welcome_text_value=$('#welcome_txt_area').code();
   $.ajax({
          url:"{{URL::route('questions.store')}}",
          type:'POST',
          dataType: 'json',
          data:{survey_id:survey_id,welcome_text:welcome_text_value,"_token": "{{ csrf_token() }}"},
            beforeSend: function(){
          $('.load-image').show();
          },
          complete: function(){
              $('.load-image').hide();
          },
          success:function(data){
             $('#drag-welcome').draggable( "disable" );
             $('#drag-welcome').css("opacity",0.3);
             /*$('.welcome-text.ui-droppable ').css ({
    'background': '#e5efe5 none repeat scroll 0 0 !important',
    'border': '1px solid #dddddd !important',
    'color': '#39434f !important',
    'padding': '8px !important',
    'text-align': 'left'
});*/
            $('#welcome_txt_modal').modal('hide');
            $('#welcome-text').replaceWith("<div rel="+data.question_id+" class='welcome_text_afterDB'>"+data.welcome_text+"<a class='delete-question pull-right' href='#' data-toggle='tooltip' title='Delete'><span class='glyphicon glyphicon-trash'></span></a></div>");

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
.welcome_text_afterDB
{
  background: #e5efe5 none repeat scroll 0 0 !important;
    border: 1px solid #dddddd !important;
    color: #39434f !important;
    padding: 8px !important;
    text-align: left;
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
.delete-question{display: none;}
</style>
 </div>
  
</section>
@endsection