@extends('default.users.layouts.default')
@section('content')
<div class="container">
    <div class="row">
        <div class="col-sm-8 col-sm-offset-2">
            <div id="add-manually" class="">
                <!-- <div class="panel panel-info"> -->
                <!-- <div class="panel-heading">
                    <h3 class="panel-title">

                    </h3>
  </div>
            -->
                <h3 class="text-center">{{$title}}</h3>
                @if ($errors->any())
                <div class="alert alert-danger fade in">
                    <a href="#" class="close" data-dismiss="alert">&times;</a>
                    <strong>Error!</strong> A problem has been occurred while submitting form.<br>
                    <ul> {!! implode('', $errors->all('<li class="text-danger">:message</li>')) !!}</ul>
                </div>
                @endif

                <form action="{{ route('manage-respondent.update', [config('site.survey_slug'), $user_details->id]) }}"
                    method="POST" id="add-participants" class="form-horizontal">
                    @csrf
                    @method('PATCH')
                    <div class="form-group col-sm-12">
                        <label for="fname" class="col-sm-5 col-md-5">First Name</label>
                        <div class="col-sm-12">
                            <input type="text" name="fname" id="fname" class="form-control" placeholder="First Name">
                        </div>
                    </div>

                    <div class="form-group col-sm-12">
                        <label for="lname" class="col-sm-5 col-md-5">Last Name</label>
                        <div class="col-sm-12">
                            <input type="text" name="lname" id="lname" class="form-control" placeholder="Last Name">
                        </div>
                    </div>

                    <div class="form-group col-sm-12">
                        <label for="email" class="col-sm-5 col-md-5">Email</label>
                        <div class="col-sm-12">
                            <input type="email" name="email" id="email" class="form-control" placeholder="Email"
                                disabled>
                        </div>
                    </div>

                    <div class="form-group col-sm-12">
                        <label for="rater" class="col-sm-7 col-md-5">Rater / Respondent</label>
                        <div class="col-sm-12">
                            <select name="rater" id="rater" class="form-control">
                                @foreach($survey_rater_list as $key => $value)
                                <option value="{{ $key }}" {{ $rater_id==$key ? 'selected' : '' }}>
                                    {{ $value }}
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="form-group" id="optionTemplate">
                        <div class="col-sm-12">
                            <input type="hidden" name="survey_id" value="{{ $survey_id }}">
                            <input type="hidden" name="participant_id" value="{{ $participant_details->id }}">
                            <input type="hidden" name="respondent_id" value="{{ $user_details->id }}">
                        </div>
                    </div>


                    <div class="form-group col-sm-12" align="center">
                        <a href="{{ route('manage-respondent.index',config('site.survey_slug')) }}"
                            class="btn btn-danger">Cancel</a>

                        <button type="submit" class="btn btn-submit">update</button>


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
                $option2   = $clone.html(' <div class="col-sm-12 col-sm-offset-2"><input class="form-control demographic-data" type="text" name="demographic_data[]" placeholder="Key:Value"/> <a href="javascript:void(0);" class="removeButton btn btn-danger" title="Remove field"><span class="glyphicon glyphicon-minus"></span></a></div>');
                $option    =$clone.find('[name="demographic_data[]"]');
            // Add new field
            $('#add-participants').bootstrapValidator('addField', $option);
        })

        // Remove button click handler
        .on('click', '.removeButton', function() {
            var $row    = $(this).parents('.form-group col-sm-12'),
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
<style type="text/css">
    footer {
        position: fixed;
    }

    #add-manually {
        margin-bottom: 117px;
    }

    #add-participants {
        padding: 10px;
    }

    .form-group.col-sm-12 {
        padding-left: 46px;
        margin-top: 10px;
    }
</style>
@endsection
