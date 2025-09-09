<div id="questionadd-modal" class="modal fade" role="dialog">

    <div class="modal-dialog modal-lg">

        <!-- Modal content-->

        <div class="modal-content">



            <form role="form" id="qedit-form">

                <div class="modal-body">

                    <div class="form-group">
                        <label class="col-sm-3">Question Dimension</label>
                        <div class="col-sm-9">
                            <input type="text" name="question_dimension" value="{{ $question_dimension }}" class="form-control">
                        </div>
                    </div>


                    <div class="form-group">
                        <label class="col-sm-3">Question <span class="qtn-required">*</span></label>
                        <div class="col-sm-9">

                            <input type="hidden" name="question_type" value="{{$question_type}}">
                            <input type="hidden" name="display_order" value="{{$display_order}}">
                            <input type="hidden" name="survey_id" value="{{$survey_id}}">
                            <input type="hidden" name="question_id" value="">
                            <input type="hidden" name="_method" value="POST">
                            <input type="hidden" name="_token" value="{{csrf_token()}}">

                            <textarea name="question" class="form-control" rows="5"></textarea>
                        </div>
                    </div>




                    @if($question_type=="radio" || $question_type=="checkbox" || $question_type=="dropdown")
                    <?php $i=1; ?>
                    @if(count($question_options)>0)

                    @foreach($question_options as $option_weight=>$option_text)
                    @if($i==1)
                    <div class="form-group no-margin">
                        <label class="control-label col-sm-6" for="field1">Choices</label>
                        <div class="controls col-sm-6">

                            <div class="field_wrapper">

                                <div class="no-left-pad">
                                    <label>Option Text</label>
                                    <input class="form-control shrink" name="fields[]" value="{{$option_text}}"
                                        type="text" />

                                </div>



                            </div>

                        </div>
                    </div>


                    <div class="form-group no-margin">
                        <div class="controls col-sm-12">

                            <div class="field_wrapper">
                                <div class="col-sm-6 no-right-pad">
                                    <label>Option Weight</label>
                                    <input class="form-control shrink" name="option_weight[]" value="{{$option_weight}}"
                                        type="text" />

                                </div>
                            </div>
                        </div>
                    </div>

                    @else
                    <div class="form-group no-margin">
                        <div class="controls col-sm-6 col-sm-offset-6">
                            <div class="field_wrapper">
                                <div class="no-left-pad"><input class="form-control shrink" name="fields[]" type="text"
                                        value="{{$option_text}}" /></div>
                            </div>
                        </div>
                    </div>
                    <div class="form-group no-margin">
                        <div class="controls col-sm-12">
                            <div class="field_wrapper">
                                <div class="col-sm-6 no-right-pad"><input class="form-control shrink"
                                        name="option_weight[]" value="{{$option_weight}}" type="text" /> </div>
                                <div class="col-sm-2 col-sm-offset-4 no-pad"><a href="javascript:void(0);"
                                        class="btn btn-danger btn-add removeButton" type="button"> <span
                                            class="glyphicon glyphicon-minus"></span> </a></div>
                            </div>
                        </div>
                    </div>
                    @endif

                    <div class="clear-fix"></div>

                    <?php $i++;?>
                    @endforeach
                    <input type="hidden" name="opt_weight" value="{{ $option_weight }}" id="opt_weight">

                    @else
                    @for($i=1;$i<=5;$i++) @if($i==1) <div class="form-group no-margin">
                        <label class="control-label col-sm-6" for="field1">Choices</label>
                        <div class="controls col-sm-6">

                            <div class="field_wrapper">

                                <div class="no-left-pad">
                                    <label>Option Text</label>
                                    <input class="form-control shrink" name="fields[]" value="" type="text" />

                                </div>



                            </div>

                        </div>
                </div>


                <div class="form-group no-margin">
                    <div class="controls col-sm-12">

                        <div class="field_wrapper">
                            <div class="col-sm-6 no-right-pad">
                                <label>Option Weight</label>
                                <input class="form-control shrink" name="option_weight[]" value="{{$i}}" type="text" />

                            </div>
                        </div>
                    </div>
                </div>

                @else
                <div class="form-group no-margin">
                    <div class="controls col-sm-6 col-sm-offset-6">
                        <div class="field_wrapper">
                            <div class="no-left-pad"><input class="form-control shrink" name="fields[]" type="text" />
                            </div>
                        </div>
                    </div>
                </div>
                <div class="form-group no-margin">
                    <div class="controls col-sm-12">
                        <div class="field_wrapper">
                            <div class="col-sm-6 no-right-pad"><input class="form-control shrink" name="option_weight[]"
                                    value="{{$i}}" type="text" /> </div>
                            <div class="col-sm-2 col-sm-offset-4 no-pad"><a href="javascript:void(0);"
                                    class="btn btn-danger btn-add removeButton" type="button"> <span
                                        class="glyphicon glyphicon-minus"></span> </a></div>
                        </div>
                    </div>
                </div>
                @endif

                <div class="clear-fix"></div>
                <input type="hidden" name="opt_weight" value="5" id="opt_weight">

                @endfor


                @endif
                <div class="form-group no-margin" id="optionTemplate">
                    <div class="col-sm-12">
                        <a href="#" class="addButton btn btn-primary pull-right" title="Add field"><span
                                class="glyphicon glyphicon-plus">Add</span></a>
                    </div>
                </div>
                @endif

                <div class="form-group">
                    <label class="col-sm-3">Question Required<span class="qtn-required">*</span></label>
                    <div class="col-sm-9">


                        <input type="radio" name="question_required" value="1" id="qreqy" checked>
                        <label for="qreqy">Yes</label>

                        <input type="radio" name="question_required" value="0" id="qreqn">
                        <label for="qreqn">No</label>


                    </div>
                </div>



                @if($question_type=="radio")


                <div class="form-group">
                    <label class="col-sm-3">Add "other" option</label>
                    <div class="col-sm-9">


                        <input type="radio" name="addother" value="1" id="addothery">
                        <label for="addothery">Yes</label>

                        <input type="radio" name="addother" value="0" id="addothern" checked>
                        <label for="addothern">No</label>

                    </div>
                </div>

                @endif





                <div class="modal-footer">

                    <button type="button" class="btn btn-danger cancel" id="revert" data-dismiss="modal">Cancel</button>

                    <button type="submit" id="questionedit-submit-btnn" class="btn btn-success">Save</button>

                    <img src="{{ asset('images/img_load.gif') }}" alt="ajax_load" class="load-ajximage">


                </div>

            </form>

        </div>



    </div>

</div>


<style type="text/css">
    .form-group {
        display: inline-block;
        position: relative;
        width: 100%;
    }

    .form-group.no-margin {
        float: left;
        width: 50%;
    }

    .no-left-pad {
        padding-left: 0;
    }

    .no-right-pad {
        padding-right: 0;
    }

    .no-pad {
        padding: 0;
    }

    #optionTemplate {
        clear: both;
        width: 100%;
    }
</style>
<script type="text/javascript">
    $(document).keyup(function(e) {
     if (e.keyCode == 27) {
 $('.cancel').click();
    }
});
$(document).ready(function(){

	$('#revert').click(function(){
  $('#fields li').each(function(){
    $(this).find('.response-order-list').css({"background":"#fff"});
    $(this).css({"opacity":1});
  });
});


		var fields= {   question: {
	                    	validators: {
	                        	notEmpty: {
	                            message: 'The Field required and cannot be empty'
	                        	}
	                   		}
	                	}
            };

	@if($question_type=="radio" || $question_type=="checkbox" ||$question_type=="dropdown")

	fields['fields[]']= {
                                       validators: {
                        notEmpty: {
                            message: 'The Field required',
                        },



 		callback: {
                        callback: function(value, validator, $field) {
                            var $emails          = validator.getFieldElements('fields[]'),
                                numEmails        = $emails.length,
                                notEmptyCount    = 0,
                                obj              = {},
                                duplicateRemoved = [];

                            for (var i = 0; i < numEmails; i++) {
                                var v = $emails.eq(i).val();
                                if (v !== '') {
                                    obj[v] = 0;
                                    notEmptyCount++;
                                } //if
                            } //for

                            for (i in obj) {
                                duplicateRemoved.push(obj[i]);
                            } //for

                            if (duplicateRemoved.length === 0) {
                               /* return {
                                    valid: false,
                                    message: 'You must fill at least one email address'
                                };*/
                            } else if (duplicateRemoved.length !== notEmptyCount) {
                                return {
                                    valid: false,
                                    message: 'The option value must be unique'
                                };
                            } //if else

                            validator.updateStatus('fields[]', validator.STATUS_VALID, 'callback');
                            return true;
                        } //callback
                    } //callback



                    }//validators





                };//fields

    fields['option_weight[]']= {
                    validators: {
                        notEmpty: {
                            message: 'The Field required',
                        }

                    }
                };


	@endif
	$('#qedit-form').bootstrapValidator({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
           	fields:fields

           })

             // Add button click handler
        .on('click', '.addButton', function() {
                var count=parseInt($('#opt_weight').val())+1;
                $('#opt_weight').val(count);

            var $template = $('#optionTemplate'),
                $clone1    = $template
                                .clone()
                                .removeClass('hidden')
                                .removeAttr('id')
                                .insertBefore($template),
                  $option1= $clone1.html('<div class="controls col-sm-6 col-sm-offset-6"><div class="field_wrapper"><div class="no-left-pad"><input class="form-control shrink" name="fields[]" type="text" /></div></div></div>');
                   $option=$clone1.find('[name="fields[]"]');
		            // Add new field
		            $('#qedit-form').bootstrapValidator('addField', $option);

		             $clone2    = $template
                                .clone()
                                .removeClass('hidden')
                                .removeAttr('id')
                                .insertBefore($template),


                  $option2=$clone2.html('<div class="controls col-sm-12"> <div class="field_wrapper"><div class="col-sm-6 no-right-pad"><input class="form-control shrink" name="option_weight[]" value="'+count+'" type="text" /> </div><div class="col-sm-2 col-sm-offset-4 no-pad"><a href="javascript:void(0);" class="btn btn-danger btn-add removeButton" type="button"> <span class="glyphicon glyphicon-minus"></span> </a></div></div></div>');
                   $option=$clone2.find('[name="option_weight[]"]');
            // Add new field
            $('#qedit-form').bootstrapValidator('addField', $option);

            $template.before('<div class="clear-fix"></div>');
        })

        // Remove button click handler
        .on('click', '.removeButton', function() {
            var $row    = $(this).parents('.form-group'),
                $option = $row.find('[name="option_weight[]"]');
                  var $row2    = $(this).parents('.form-group').prev('.form-group'),
                    $option2 = $row2.find('[name="fields[]"]');

            // Remove element containing the option
            $row.remove();

            // Remove field
            $('#qedit-form').bootstrapValidator('removeField', $option);




            // Remove element containing the option
            $row2.remove();

            // Remove field
            $('#qedit-form').bootstrapValidator('removeField', $option2);
        })

        // Called after adding new field
        .on('added.field.fv', function(e, data) {
            // data.field   --> The field name
            // data.element --> The new field element
            // data.options --> The new field options

            if (data.field === 'fields[]') {
                if ($('#qedit-form').find(':visible[name="fields[]"]').length >= MAX_OPTIONS) {
                    $('#qedit-form').find('.addButton').attr('disabled', 'disabled');
                }
            }
        })

        // Called after removing the field
        .on('removed.field.fv', function(e, data) {
           if (data.field === 'fields[]') {
                if ($('#qedit-form').find(':visible[name="fields[]"]').length < MAX_OPTIONS) {
                    $('#qedit-form').find('.addButton').removeAttr('disabled');
                }
            }
        })
        .on('submit', function (e) {
			      if (!e.isDefaultPrevented()){
			      	e.preventDefault();
			      	$.ajax({
					          url:"{{URL::route('questionEdit')}}",
					          type:'POST',
					          dataType: 'json',
					          data:$('#qedit-form').serialize(),
					            beforeSend: function(){
					          $('.load-image').show();
					          },
					          complete: function(){
					              $('.load-image').hide();
					          },
					          success:function(data){

                      if(data.error && data.error!="")
                      {
                         $('<div class="alert alert-danger alert-dismissable"><a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a><strong>Oops! </strong> '+data.error+'</div>').insertBefore('.form-group:nth-child(1)');

                      } else{

                      var html='<li id="item_'+data.question_id+'" rel="'+data.question_id+'" class="response-area-db  questiontxt-db"  title="'+data.question_dimension+'"  data-question-type="'+data.question_nature+'" data-preamble="'+data.question_dimension+'"><div class="response-order-list">'+data.display_order+'</div><div class="response-label-db">'+data.question_text+'</div><div class="question-dimension-section">'+data.question_dimension+'</div><div class="response-action"><a class="delete-question pull-right" href="#" title="Delete" style="display: none;"><span class="glyphicon glyphicon-trash"></span></a></div></li>';

                      $('#fields li:last').after(html);


				              $('#questionadd-modal').modal('hide');
				              $('body').removeClass('modal-open');
											$('.modal-backdrop').remove();

                       $('.response-area-db').tooltip();
                       $('.delete-question').tooltip();
                     }





					          }

					      });
			      }


        });
        });

</script>
