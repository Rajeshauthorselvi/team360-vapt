@extends('layouts.default')

@section('content')


<div class="container">
<div class="row">

  <div class="col-sm-8 col-sm-offset-2">
    <h3 class="text-center">Add Participants</h3>

  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#add-manually">Add New</a></li>
    <li><a data-toggle="tab" href="#add-import">Bulk Import</a></li>
  </ul>



  <div class="tab-content">

    <div id="add-manually" class="tab-pane fade in active">


                        @if ($errors->any())
                                  <div class="alert alert-danger fade in">

                                <a href="#" class="close" data-dismiss="alert">&times;</a>

                                <strong>Error!</strong> A problem has been occurred while submitting form.<br>
                                <ul>
                                {!! implode('', $errors->all('<li class="text-danger">:message</li>')) !!}
                                
                                 </ul> </div>
                                @endif   
      {!! Form::open(array('route' => 'addusers.store','method'=>'POST','id'=>'add-participants','class'=>'form-horizontal')) !!}

       <div class="form-group">
        {{Form::label('title','First Name',['class'=>'col-sm-2 '])}} 
        <div class="col-sm-10">
        {{Form::text('fname',null,['class'=>'form-control','placeholder'=>'First Name'])}}
        </div>
        
        </div>
        <div class="form-group">

        {{Form::label('title','Last Name',['class'=>'col-sm-2 '])}} 
        <div class="col-sm-10">
        {{Form::text('lname',null,['class'=>'form-control','placeholder'=>'Last Name'])}}
        </div>
        
        </div>
        <div class="form-group">

        {{Form::label('title','Email',['class'=>'col-sm-2 '])}} 
        <div class="col-sm-10">
        {{Form::email('email',null,['class'=>'form-control','placeholder'=>'Email'])}}
        </div>
        
        </div>
	<!--<div class="form-group">

        {{Form::label('rater','Respondent / Rater Type',['class'=>'col-sm-2 '])}} 
        <div class="col-sm-10">
        {{Form::text('rater','self',['class'=>'form-control','placeholder'=>'Respondent / Rater Type'])}}
        </div>
        
        </div>-->
{{Form::hidden('survey_id',$survey_id)}}
{{Form::hidden('rater','self')}}

 
   <div class="form-group text-center " align="center" >
      <a href="{{URL::route('addusers.show',$survey_id)}}" class="btn btn-danger">Cancel</a>
    <button type="submit" class="btn btn-success">Save</button>

       
   </div>
      {!! Form::close() !!}
  </div>
    
    

   
    <div id="add-import" class="tab-pane fade">
     @if(Session::get('msg'))

      <div class="alert alert-danger import-process-error">
        <a class="close" onclick="$('.alert').hide()">x</a>
        @if(Session::get('msg')!="Heading Mismatch line @ 1 . (first_name,last_name,email,demographic_data) Plz enter this format.")
           <strong>Whoops! Some error occurred.</strong><br><br>
        <ul>
          @foreach(Session::get('msg') as $value)
            <li>{{$value}}</li>
          @endforeach
        </ul>
        @else
          {{Session::get('msg')}}
        @endif
      </div>
    @endif
    <form action="{{ URL::route('importusers') }}" class="form-horizontal" method="POST" id="import_process" enctype="multipart/form-data">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <div class="form-group">
      <p class="text-info">Upload .xls .xlsx file with following headers to update the participant list. <b>(fname,lname,email)</b></p>
      <p class="text-info" >Download sample users <a href="{{URL::route('addusers.index')}}" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> Download</a></p>
      {{Form::hidden('survey_id',$survey_id)}}
            <input type="file" class="form-control filestyle" data-buttonName="btn-primary" placeholder="File type:xls,xlsx" name="import_file" id="upload" accept=".xls, .xlsx"/>
      </div>
    
<div class="form-group" align="center">
<a href="{{URL::route('addusers.show',$survey_id)}}" class="btn btn-danger">Cancel</a>
       <button type="submit" class="btn btn-success">Save</button>
   
</div>
       
    </form>
    </div>


  </div>
    

    

  
 </div>
</div>
</div>
{!! HTML::script('script/bootstrap-filestyle.js')!!}
<style media="screen">
.nav-tabs li.bv-tab-error>a {
  color: #555;
}
.nav.nav-tabs a
{
  border:1px solid #eee;
}
.nav > li.active a ,.nav > li.active a:hover,.nav > li.active a:focus {
    background-color: #e6e7e8;
}
</style>
<style media="screen">
	.glyphicon-copy{
		color: #2041bd;
	}
	.nav > li.active a, .nav > li.active a:hover, .nav > li.active a:focus {
    background-color: #286090;
    border-color: #286090;
    color: #ffffff;
}
</style>
<script type="text/javascript">
	$(document).ready(function(){
		
	if($(".import-process-error").css('visibility'))
	{
	  $('.nav-tabs a[href="#add-import"]').tab('show')
	}

    $('#add-participants')
        .bootstrapValidator({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
              email: {
                  validators: {
                    notEmpty: {
                        message: 'The Field required and cannot be empty'
                    },
                    emailAddress: {
                        message: 'The value is not a valid email address'
                     },
 		remote: {
			message:'The Participant already exists to this survey',
			url:"{!!URL::route('checkparticipant_email')!!}",
			data:{
			survey_id:"{{$survey_id}}",
			email:$("input[name='email']").val()

                             }//data
                    }//remote
                  }
              },
                 
                fname: {
                    validators: {
                        notEmpty: {
                            message: 'The Field required and cannot be empty'
                        }
                    }
                },
		rater: {
                    validators: {
                        notEmpty: {
                            message: 'The Field required and cannot be empty'
                        }
                    }
                },
                'demographic_data[]': {
                    validators: {
                        callback: {
                        callback: function(value, validator, $field) {
                            if (value === '') {
                                return true;
                            }
                            if (value.search(":") === -1) {
                                return {
                                    valid: false,
                                    message: 'The Field should follow the format (key:value)'
                                }
                            }
                            return true;
                        }
                    }

                    }
                }
            }
        })

        // Add button click handler
        .on('click', '.addButton', function() {
            var $template = $('#optionTemplate'),
                $clone    = $template
                                .clone()
                                .removeClass('hidden')
                                .removeAttr('id')
                                .insertBefore($template),
                $option2   = $clone.html(' <div class="col-sm-10 col-sm-offset-2"><input class="form-control demographic-data" type="text" name="demographic_data[]" placeholder="Key:Value"/> <a href="javascript:void(0);" class="removeButton btn btn-danger" title="Remove field"><span class="glyphicon glyphicon-minus"></span></a></div>');
                $option    =$clone.find('[name="demographic_data[]"]');
            // Add new field
            $('#add-participants').bootstrapValidator('addField', $option);
        })

        // Remove button click handler
        .on('click', '.removeButton', function() {
            var $row    = $(this).parents('.form-group'),
                $option = $row.find('[name="demographic_data[]"]');

            // Remove element containing the option
            $row.remove();

            // Remove field
            $('#add-participants').bootstrapValidator('removeField', $option);
        })

        // Called after adding new field
        .on('added.field.fv', function(e, data) {
            // data.field   --> The field name
            // data.element --> The new field element
            // data.options --> The new field options

            if (data.field === 'demographic_data[]') {
                if ($('#add-participants').find(':visible[name="demographic_data[]"]').length >= MAX_OPTIONS) {
                    $('#add-participants').find('.addButton').attr('disabled', 'disabled');
                }
            }
        })

        // Called after removing the field
        .on('removed.field.fv', function(e, data) {
           if (data.field === 'demographic_data[]') {
                if ($('#add-participants').find(':visible[name="demographic_data[]"]').length < MAX_OPTIONS) {
                    $('#add-participants').find('.addButton').removeAttr('disabled');
                }
            }
        });


        $('#import_process')
      .bootstrapValidator({
          framework: 'bootstrap',
          icon: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
          fields: {
            import_file: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  },
                  file: {
                    extension: 'xls,xlsx',
                    message: 'Please select xls or xlsx file formats only'
                }
                }
            },
          }
})



  });
</script>

@endsection
