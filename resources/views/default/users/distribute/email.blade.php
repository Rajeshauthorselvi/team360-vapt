@extends('default.users.layouts.default')
@section('content')



<div class="container">
 <div class="row setup-content" id="step-5">

        <div class="col-xs-12">
        <div class="form-wrapper">
        <div class="form-steps-wizard step5"> </div>
                  
              
            <div class="col-md-12">
                <h3 class="need-margin-bottom-forstrip text-center">Send Email</h3>
               
        
   
                           @if ($errors->any())
                                  <div class="alert alert-danger fade in">

                                <a href="#" class="close" data-dismiss="alert">&times;</a>

                                <strong>Error!</strong> A problem has been occurred while submitting form.<br>
                                <ul>
                                {!! implode('', $errors->all('<li class="text-danger">:message</li>')) !!}
                                
                                 </ul> </div>
                                @endif 

  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#add-notification">Notification</a></li>
    <li><a data-toggle="tab" href="#add-reminder">Reminder</a></li>
  </ul>
  <div class="tab-content">

    <div id="add-notification" class="tab-pane fade in active">

    {!! Form::open(array('route'=>['manage-email.store',config('site.survey_slug')],'method'=>'POST','id'=>'distribute-participants','class'=>'form-horizontal')) !!}


  
    {!! Form::hidden('send_email', 'notification') !!}
  
  
  
  


  <?php $send_email_from=(isset($from_email)) ? $from_email : null; ?>
  <div class="form-group">
        <div class="col-sm-12">
        {!! Form::email('from_email', $send_email_from,['class' => 'form-control','placeholder' => 'From Email']) !!}
    </div></div>

  
  
    <div class="form-group">
        <div class="col-sm-12">
  {!! Form::email('cc', null,['class' => 'form-control','placeholder' => 'CC']) !!}
  </div></div>

 <div class="form-group"> 
        <div class="col-sm-12"> 
  {!! Form::text('copy_email', null,['class' => 'form-control','placeholder' => 'BCC']) !!} 
  </div></div> 

  <div class="form-group">
        <div class="col-sm-12 col-md-12 col-xs-12">
        <div class="participant-wraper">
        <?php $count=1;$i=1;?>
        @foreach($notify_respondents as $respondent)
        @if($count==1) 
        <div class="col-md-12 col-xs-12">
        <label><input type="checkbox" checked="checked" id="select_all"/> Select all</label>
        </div>
        @endif
        <div class="col-md-6 col-xs-12">
        {{Form::checkbox('bcc[]',$respondent->email,1,['id'=>'chk'.$count,'class'=>'case'])}}
        <label for="{{'chk'.$count}}">{{$respondent->fname}}({{$respondent->email}})</label>
        </div>
	@if($i==250)
	<div class="col-md-9 ">
	<hr>
	</div>
	<?php $i=0; ?>
	@endif
	<?php $count++;$i++?>
	@endforeach
   
     </div>
   </div>
 </div>

    <div class="form-group">
        <div class="col-sm-12">
        {!! Form::text('subject', null,['class' => 'form-control','placeholder' => 'Subject','id'=>'subject']) !!}
    </div></div>

   <div class="form-group">
        <div class="col-sm-12">
        <div class="lnbrd">
       {!! Form::textarea('message_body','[fname][lname][Surveys list][Login Details]', array('id'  => 'message_body','class' => 'textarea form-control')) !!}
       </div>
  
    </div></div>
    
     <small><strong>Note :[fname]</strong> use this shortcode to yield first name in the section. <br/>
<span style="padding-left:3.4em"><strong>[lname]</strong> use this shortcode to yield last name in the section </span><br/>
<span style="padding-left:3.4em"><strong>[Surveys list]</strong> use this shortcode to yield survey information in the section </span><br/>
<span style="padding-left:3.4em"><strong>[Login Details]</strong> use this shortcode to yield login information in the section</span></small>
  <br>
    <?php   $actionurl=URL::route('user.dashboard',config('site.survey_slug')); ?>
     <div class="form-group" style="margin-top: 10px;">
        <div class="col-sm-12 need-margin">
    {!! Form::hidden('survey_id', $survey_id) !!}

  
                <a href="{{$actionurl}}" class="btn btn-danger btn-md">Back</a>
  {!! Form::submit('Send Email', array('class' => 'btn btn-submit')) !!}
  </div>
  </div>

  {!! Form::close() !!}

   </div>
    
    

    <div id="add-reminder" class="tab-pane fade">
    {!! Form::open(array('route'=> ['manage-email.store',config('site.survey_slug')],'method'=>'POST','id'=>'distribute-participants-reminder','class'=>'form-horizontal')) !!}


        {!! Form::hidden('send_email', 'remainder') !!}
  

  <?php $send_email_from=(isset($from_email)) ? $from_email : null; ?>
  <div class="form-group">
        <div class="col-sm-12">
        {!! Form::email('from_email_for_reminder', $send_email_from,['class' => 'form-control','placeholder' => 'From Email']) !!}
    </div></div>

  
  
    <div class="form-group">
        <div class="col-sm-12">
  {!! Form::email('cc_for_reminder', null,['class' => 'form-control','placeholder' => 'CC']) !!}
  </div></div>

 <div class="form-group"> 
        <div class="col-sm-12"> 
  {!! Form::text('copy_email_for_reminder', null,['class' => 'form-control','placeholder' => 'BCC']) !!} 
  </div></div>
 
  <div class="form-group">
        <div class="col-sm-12 col-xs-12 col-md-12">
        <div class="participant-wraper hidden">
        <?php $count=1;$i=1;?>
       @foreach($remind_respondents as $participant)
        @if($count==1) 
        <div class="col-md-12 col-xs-12">
        <label><input type="checkbox" checked="checked" id="select_all_another"/> Select all</label>
        </div>
        @endif
        <div class="col-md-6 col-xs-12">
        {{Form::checkbox('bcc_for_reminder[]',$participant->email,1,['id'=>'chkl'.$count,'class'=>'case_another'])}}
        <label for="{{'chkl'.$count}}">{{$participant->fname}}({{$participant->email}})</label>
        </div>
	@if($i==250)
	<div class="col-sm-9 ">
	<hr>
	</div>
	<?php $i=0; ?>
	@endif
	<?php $count++;$i++?>
	@endforeach
   
     </div></div></div>
   

    <div class="form-group">
        <div class="col-sm-12">
        {!! Form::text('subject_for_reminder', null,['class' => 'form-control','placeholder' => 'Subject','id'=>'subject']) !!}
    </div></div>

   <div class="form-group">
        <div class="col-sm-12">
        <div class="lnbrd">
       {!! Form::textarea('message_body_for_reminder','[fname][lname][Surveys list][Login Details]', array('id'  => 'message_body_for_reminder','class' => 'textarea form-control')) !!}
       </div>

    </div></div>
   
     <small><strong>Note :[fname]</strong> use this shortcode to yield first name in the section. <br/>
<span style="padding-left:3.4em"><strong>[lname]</strong> use this shortcode to yield last name in the section </span><br/>
<span style="padding-left:3.4em"><strong>[Surveys list]</strong> use this shortcode to yield survey information in the section </span><br/>
<span style="padding-left:3.4em"><strong>[Login Details]</strong> use this shortcode to yield login information in the section</span></small>
   <br>
    <div class="form-group" style="margin-top: 10px;">
        <div class="col-sm-12 need-margin">
    {!! Form::hidden('survey_id', $survey_id) !!}
    
  
                <a href="{{$actionurl}}" class="btn btn-danger btn-md ">Back</a>
    
  {!! Form::submit('Send Email', array('class' => 'btn btn-submit')) !!}
  </div>
  </div>

  {!! Form::close() !!}

    </div>

  </div> 



</div>
</div>
</div>
</div>
</div>




{{ HTML::style('css/bootstrap3-wysihtml5.min.css') }}
 
{{ HTML::script('script/bootstrap3-wysihtml5.js') }}

  <style media="screen">
.nav-tabs li.bv-tab-error>a {
  color: #555;
}

.nav > li.active a, .nav > li.active a:hover, .nav > li.active a:focus {
    background-color: #286090;
    border-color: #286090;
    color: #ffffff;
}
.tab-content
{
margin-bottom: 42px;
}
@media(min-width: 800px){
  .tab-content{
    padding: 45px;
  }
}
.nav a {
  border:1px solid #ddd !important;
}
</style>

<script type="text/javascript">
	$(document).ready(function(){

		
        $('#distribute-participants').bootstrapValidator({
          framework: 'bootstrap',
         
          icon: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
          fields: {
            from_email: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  },
                  emailAddress: {
                        message: 'The value is not a valid email address'
                    }
                  
                }
            },
              /*cc: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }
                  
                  
                }
            },
	copy_email: { 
                validators: { 
                  notEmpty: { 
                      message: 'The Field required and cannot be empty' 
                  } 
                  
                  
                } 
            },*/ 
              subject: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }
                  
                }
            },
           message_body: {
           	group: '.lnbrd',
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }
                  
                }
            },
            'bcc[]': {
                validators: {
                 choice: {
                        min: 1,
                        message: 'Please select atleast one'
                    }
                  
                }
            },
          }
});


 $('#distribute-participants-reminder').bootstrapValidator({
          framework: 'bootstrap',
         
          icon: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
          fields: {
            from_email_for_reminder: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  },
                  emailAddress: {
                        message: 'The value is not a valid email address'
                    }
                  
                }
            },
             /* cc_for_reminder: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }
                  
                  
                }
            },


 	copy_email_for_reminder: { 
                validators: { 
                  notEmpty: { 
                      message: 'The Field required and cannot be empty' 
                  } 
                  
                  
                } 
            },
*/
              subject_for_reminder: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }
                  
                }
            },
           message_body_for_reminder: {
           	group: '.lnbrd',
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }
                  
                }
            },
            'bcc_for_reminder[]': {
                validators: {
                  choice: {
                        min: 1,
                        message: 'Please select atleast one'
                    }
                  
                }
            },
          }
});


         $('#message_body').wysihtml5({
		 	events: {
			        load: function () {
			            $('#message_body').addClass('textnothide');
			      
			    },
				change: function () {
				   $('#distribute-participants').bootstrapValidator('revalidateField', 'message_body');
				}
			  },
                "font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
                "emphasis": true, //Italics, bold, etc. Default true
                "lists": false, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
                "html": false, //Button which allows you to edit the generated HTML. Default false
                "link": false, //Button to insert a link. Default true
                "image": false, //Button to insert an image. Default true,
                "color": false //Button to change color of font  
            });


          $('#message_body_for_reminder').wysihtml5({
		 	events: {
			        load: function () {
			            $('#message_body_for_reminder').addClass('textnothide');
			      
			    },
				change: function () {
				   $('#distribute-participants-reminder').bootstrapValidator('revalidateField', 'message_body_for_reminder');
				}
			  },
                "font-styles": true, //Font styling, e.g. h1, h2, etc. Default true
                "emphasis": true, //Italics, bold, etc. Default true
                "lists": false, //(Un)ordered lists, e.g. Bullets, Numbers. Default true
                "html": false, //Button which allows you to edit the generated HTML. Default false
                "link": false, //Button to insert a link. Default true
                "image": false, //Button to insert an image. Default true,
                "color": false //Button to change color of font  
            });

	})
</script>
<style type="text/css">
iframe.wysihtml5-sandbox{height: 214px !important;}
.textnothide {
    /*display: block !important;*/
  	height: 215px !important;
    position: absolute;
   width: 97.6% ;
    z-index: -1;
}
.state-icon {
    left: -5px;
}
.list-group-item-primary {
    color: rgb(255, 255, 255);
    background-color: rgb(66, 139, 202);
}

.well .list-group {
    margin-bottom: 0px;
}

li.list-group-item {
    float: left;
    margin-right: 1%;
    width: 32%;
}
.list-group-item:last-child,.list-group-item:first-child { border-radius:inherit;}
.participant-wraper {
    float: left;
    width: 100%;
}
.split-4 {
    float: left;
    width: 50%;
}
</style>
<script type="text/javascript">
$(document).ready(function(){
$("#select_all").change(function(){  //"select all" change
    var status = this.checked; // "select all" checked status
    $('.case').each(function(){ //iterate all listed checkbox items
        this.checked = status; //change ".checkbox" checked status
    });

     $('#distribute-participants').bootstrapValidator('revalidateField', 'bcc[]');
});

$('.case').change(function(){ //".checkbox" change
    //uncheck "select all", if one of the listed checkbox item is unchecked
    if(this.checked == false){ //if this item is unchecked
        $("#select_all")[0].checked = false; //change "select all" checked status to false
    }
   
    //check "select all" if all checkbox items are checked
    if ($('.case:checked').length == $('.case').length ){
        $("#select_all")[0].checked = true; //change "select all" checked status to true
    }
});

$("#select_all_another").change(function(){ 
 //"select all" change
    var status = this.checked; // "select all" checked status
    $('.case_another').each(function(){ //iterate all listed checkbox items
        this.checked = status; //change ".checkbox" checked status
    });

     $('#distribute-participants-reminder').bootstrapValidator('revalidateField', 'bcc_for_reminder[]');
});

$('.case_another').change(function(){ //".checkbox" change
    //uncheck "select all", if one of the listed checkbox item is unchecked
    if(this.checked == false){ //if this item is unchecked
        $("#select_all_another")[0].checked = false; //change "select all" checked status to false
    }
   
    //check "select all" if all checkbox items are checked
    if ($('.case_another:checked').length == $('.case_another').length ){
        $("#select_all_another")[0].checked = true; //change "select all" checked status to true
    }
});

});
</script>
@endsection
