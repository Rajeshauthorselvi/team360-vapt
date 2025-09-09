<div id="questionedit-modal" class="modal fade" role="dialog">



    <div class="modal-dialog modal-lg">



        <!-- Modal content-->

        <div class="modal-content">



            <form role="form" id="qedit-form">

                <div class="modal-body">


                    <input type="hidden" name="question_type" value="grid">
                    <input type="hidden" name="display_order" value="{{$display_order}}">
                    <input type="hidden" name="survey_id" value="{{$survey_id}}">

                    <input type="hidden" name="_method" value="POST">
                    <input type="hidden" name="_token" value="{{csrf_token()}}">

                    <div class="form-group">
                        <label class="col-sm-3">Question Dimension</label>
                        <div class="col-sm-8">
                            <input type="text" name="question_dimension" value="{{ $question_dimension }}"
                                class="form-control">
                        </div>
                    </div>
                    <?php $count=1; $q_ids=[];?>
                    @foreach ($questions as $key => $question)

                    <div class="form-group">
                        <label class="col-sm-3 lab-question">Statement {{$count}}<span
                                class="qtn-required">*</span></label>
                        <div class="col-sm-8">
                            <textarea name="question[]" class="form-control"
                                rows="1">{{ $question->question_text }}</textarea>
                        </div>

                        @if($key>0)

                        <div class="col-sm-1 ">
                            <a href="javascript:void(0);" class="btn btn-danger btn-addq" type="button"> <span
                                    class="glyphicon glyphicon-minus"></span> </a>
                        </div>
                        @endif
                    </div>


                    <?php $q_ids[]=$question->id ;$count++;?>
                    @endforeach
                    <input type="hidden" name="qqcount" value="{{ --$count }}" id="qqcount">
                    <div class="form-group" id="questionTemplate">
                        <div class="col-sm-12">
                            <a href="#" class="addQButton btn btn-primary pull-right" title="Add field"><span
                                    class="glyphicon glyphicon-plus">Add</span></a>
                        </div>
                    </div>
                    <?php $q_ids=(count($q_ids)>0) ? implode(",", $q_ids) : ""; ?>
                    <input type="hidden" name="question_id" value="{{$q_ids}}">
                    @foreach ($options as $key => $qoption)
                    @if($key==0)
                    <div class="form-group no-margin">
                        <label class="control-label col-sm-6" for="field1">Choices</label>
                        <div class="controls col-sm-6">

                            <div class="field_wrapper">

                                <div class="no-left-pad">
                                    <label>Option Text</label>
                                    <input class="form-control shrink" name="fields[]" value="{{$qoption->option_text}}"
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
                                    <input class="form-control shrink" name="option_weight[]"
                                        value="{{$qoption->option_weight}}" type="text" />
                                </div>
                            </div>
                        </div>
                    </div>
                    @else

                    <div class="form-group no-margin">

                        <div class="controls col-sm-6 col-sm-offset-6">

                            <div class="field_wrapper">

                                <div class="no-left-pad">
                                    <input class="form-control shrink" name="fields[]" value="{{$qoption->option_text}}"
                                        type="text" />
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="form-group no-margin">
                        <div class="controls col-sm-12">

                            <div class="field_wrapper">
                                <div class="col-sm-6 no-right-pad">

                                    <input class="form-control shrink" name="option_weight[]"
                                        value="{{$qoption->option_weight}}" type="text" />
                                    <input type="hidden" value="{{$qoption->option_weight}}" name="option_weight_count"
                                        class="option_weight_count">
                                </div>

                                <div class="col-sm-2 col-sm-offset-4 no-pad">
                                    <a href="javascript:void(0);" class="btn btn-danger btn-add removeButton"
                                        type="button"> <span class="glyphicon glyphicon-minus"></span> </a>
                                </div>
                            </div>
                        </div>
                    </div>


                    @endif
                    @endforeach


                    <div class="form-group no-margin" id="optionTemplate">
                        <div class="col-sm-12">
                            <a href="#" class="addButton btn btn-primary pull-right" title="Add field"><span
                                    class="glyphicon glyphicon-plus">Add</span></a>
                        </div>
                    </div>



                    <div class="form-group">
                        <label class="col-sm-3">Question Required<span class="qtn-required">*</span></label>
                        <div class="col-sm-9">
                            <?php
                                $qtny=$qtnn=0;
                                if($question_required) $qtny=1;
                                else $qtnn=1;
                            ?>
                            <input type="radio" name="question_required" value="1" id="qreqy" {{ $qtny ? 'checked' : ''
                                }}>
                            <label for="qreqy">Yes</label>

                            <input type="radio" name="question_required" value="0" id="qreqn" {{ $qtnn ? 'checked' : ''
                                }}>
                            <label for="qreqn">No</label>


                        </div>
                    </div>
                </div>



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

    .col-sm-1.col-sm-offset-11 {
        margin-top: 5px;
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


		var fields= {   'question[]': {
	                    	validators: {
	                        	notEmpty: {
	                            message: 'The Field required and cannot be empty'
	                        	}
	                   		}
	                	},
                    'question_dimension': {
                        validators: {
                            notEmpty: {
                              message: 'The Field required and cannot be empty'
                            }
                        }
                    },
            };

	fields['fields[]']= {
                    validators: {
                        notEmpty: {
                            message: 'The Field required',
                        }

                    }
                };

    fields['option_weight[]']= {
                    validators: {
                        notEmpty: {
                            message: 'The Field required',
                        }

                    }
                };



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
                var count=parseInt($('.option_weight_count:last').val())+1;
                $('.option_weight_count:last').val(count);
		             $clone2    = $template
                                .clone()
                                .removeClass('hidden')
                                .removeAttr('id')
                                .insertBefore($template),


                  $option2=$clone2.html('<div class="controls col-sm-12"> <div class="field_wrapper"><div class="col-sm-6 no-right-pad"><input class="form-control shrink" name="option_weight[]" value="'+count+'" type="text" /> </div><div class="col-sm-2 col-sm-offset-4 no-pad"><a href="javascript:void(0);" class="btn btn-danger btn-add removeButton" type="button"> <span class="glyphicon glyphicon-minus"></span> </a></div></div></div>');
                   $option=$clone2.find('[name="option_weight[]"]');
            // Add new field
            $('#qedit-form').bootstrapValidator('addField', $option);
        })

            // Add button click handler
        .on('click', '.addQButton', function() {

          var count=parseInt($('#qqcount').val())+1;
          $('#qqcount').val(count);

            var $template = $('#questionTemplate'),
                $clone1    = $template
                                .clone()
                                .removeClass('hidden')
                                .removeAttr('id')
                                .insertBefore($template),
                  $option1= $clone1.html('<label class="col-sm-3 lab-question">Statement'+count+'<span class="qtn-required">*</span></label> <div class="col-sm-8"><textarea class="form-control" rows="1" name="question[]" cols="50"></textarea></div><div class="col-sm-1 "><a href="javascript:void(0);" class="btn btn-danger btn-addq" type="button"> <span class="glyphicon glyphicon-minus"></span> </a></div>');
                   $option=$clone1.find('[name="question[]"]');
                // Add new field
                $('#qedit-form').bootstrapValidator('addField', $option);


        })
          // Remove button click handler
        .on('click', '.btn-addq', function() {
          var count=parseInt($('#qqcount').val())-1;
          $('#qqcount').val(count);

            var $row    = $(this).parents('.form-group'),
                $option = $row.find('[name="question[]"]');

            // Remove element containing the option
            $row.remove();

            // Remove field
            $('#qedit-form').bootstrapValidator('removeField', $option);

             var cint=1;
            $('.lab-question').each(function(){
              $(this).html('Statement'+cint +'<span class="qtn-required">*</span>');
              cint++;
            });

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
					          url:"{{URL::route('questionEditGrid')}}",
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


                        if(data.question_id)
                      {
                         $('#fields li').each(function(){

                                var id=parseInt($(this).find('.response-order-list').html());

                                if(id==data.display_order)
                                {
                                  $(this).attr('rel',data.question_id);
                                  $(this).attr('id','item_'+data.question_id);
                                  $(this).find('.response-label-db').html(data.question_text);

                                  $(this).find('.question-dimension-section').html(data.question_dimension);

                          $(this).tooltip('hide').attr('data-original-title', data.question_dimension).tooltip('fixTitle').tooltip('show');
                          $('.response-area-db').tooltip();
                                   $('#questionedit-modal').modal('hide');
                                   $('body').removeClass('modal-open');
                      $('.modal-backdrop').remove();

                      $(this).find('.response-order-list').css({"background":"#fff"});

                          $(this).css({"opacity":1});

                                }

                              });
                      }





					          }

					      });
			      }


        });
        });

</script>
