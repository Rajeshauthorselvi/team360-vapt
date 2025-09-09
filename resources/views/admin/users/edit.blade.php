@extends('layouts.default')

@section('content')


<div class="container">
<div class="row">
<div class="col-sm-offset-2 col-sm-8">
<div id="add-manually" class="tab-pane fade in active">

<h3 class="text-center">Edit - Participant Details</h3>
                        @if ($errors->any())
                                  <div class="alert alert-danger fade in">

                                <a href="#" class="close" data-dismiss="alert">&times;</a>

                                <strong>Error!</strong> A problem has been occurred while submitting form.<br>
                                <ul>
                                {!! implode('', $errors->all('<li class="text-danger">:message</li>')) !!}

                                 </ul> </div>
                                @endif

        <form method="POST" action="{{ route('addusers.update', $user->id) }}" id="add-participants" class="form-horizontal">
            @csrf
            @method('PATCH')


            <div class="form-group">
                <label for="fname" class="col-sm-2">First Name</label>
                <div class="col-sm-10">
                    <input type="text" name="fname" class="form-control" placeholder="First Name" oninput="sanitizeInput(this)" value="{{ $user->fname }}">
                </div>
            </div>

            <div class="form-group">
                <label for="lname" class="col-sm-2">Last Name</label>
                <div class="col-sm-10">
                    <input type="text" name="lname" class="form-control" placeholder="Last Name" oninput="sanitizeInput(this)" value="{{ $user->lname }}">
                </div>
            </div>

            <div class="form-group">
                <label for="email" class="col-sm-2">Email</label>
                <div class="col-sm-10">
                    <input type="email" name="email" class="form-control" placeholder="Email"  value="{{ $user->email }}">
                </div>
            </div>



 <div class="form-group" id="optionTemplate">
 <div class="col-sm-12">
 <input type="hidden" name="survey_id" value="{{ $survey_id }}">
<!-- <a href="javascript:void(0);" class="addButton btn btn-primary pull-right" title="Add field"><span class="glyphicon glyphicon-plus">Add</span></a>-->
 </div>
 </div>
   <div class="form-group text-center" align="center" >
   <a href="{{URL::route('addusers.show',$survey_id)}}" class="btn btn-danger">Cancel</a>
    <button type="submit" class="btn btn-success">update</button>


   </div>
        </form>
  </div>
</div>
</div>
</div>
<script type="text/javascript">
	  $(document).ready(function(){
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
                            url:"{!!URL::route('checkparticipant_email',['from'=>'edit'])!!}",
                            data:{
                                survey_id:"{{$survey_id}}",
                                email:$("input[name='email']").val(),
                                user_id:"{{ $user->id }}",
                                from:'edit_participant'
                            }
                        }
                    }
                },
                fname: {
                    validators: {
                        notEmpty: {
                            message: 'The Field required and cannot be empty'
                        }
                    }
                },

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
