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
            			message:'This Respondent already exist',
            			url:routeurl,
            			data:{
            			survey_id:survey_id,
            			participant_id:participant_id,
            			email:$("input[name='email']").val(),
                  action:"validate-user-respondent"
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
 		             rater: {
                    validators: {
                        notEmpty: {
                            message: 'The Field required and cannot be empty'
                        }
                    }
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
});


$("#email").change(function (e) {
  
  var email=$.trim($("input[name='email']").val());
  if(participant_email==email)
  {
    swal('You cannot be a respondent to yourself');
    $('#email').val('');
    $('#add-participants').bootstrapValidator('revalidateField','email');
  }
});

});