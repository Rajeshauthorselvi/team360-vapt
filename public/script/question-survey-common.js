/*During window scroll */

$(window).bind('scroll', function (e) {

    var h = window.innerHeight;
    var c = h / 2;

    var currentposition = $(this).scrollTop() + $('.inner-header').outerHeight() + c / 2;
    var total = $('.question-container li').length;

    $('.question-container li').each(function () {

        var li = $(this);
        var position = li.offset().top - currentposition;

        if (position <= 0) {

            li.addClass('focus');

            $('#next-btn').val(li.next().attr('data-id'));
            $('#prev-btn').val(li.prev().attr('data-id'));

            if (li.attr('data-id') > 1) {
                $('#prev-btn').css({ 'opacity': 1, 'pointer-events': 'inherit', 'transition': '0.2s' });

            }
            else {
                $('#prev-btn').css({ 'opacity': 0.3, 'pointer-events': 'none', 'transition': '0.2s' });

            }

            if (total == li.attr('data-id')) {
                $('#next-btn').css({ 'opacity': 0.3, 'pointer-events': 'none', 'transition': '0.2s' });
                $('#form-submit').show();

            }
            else {
                $('#next-btn').css({ 'opacity': 1, 'pointer-events': 'inherit', 'transition': '0.2s' });

                if ($(this).hasClass('.qtn-required')) {
                    $('#form-save').hide();
                }


            }

        } else {
            li.removeClass('focus');
            $('.question-container li:first').addClass('focus');
            $('.question-container li:last').addClass('focus');
        }


    });

});
/*function to check input error*/
function checkerror(obj) {
    var error = false;
    var input_type = obj.attr('data-type');

    var required_exists = obj.closest('.panel-primary').find('.qtn-required').length;

    if (input_type == 'radio' && required_exists > 0 && obj.closest('.question-options').find('input[type=radio]:checked').val() === undefined) {
        error = true;
    }
    else if (input_type == 'radio' && required_exists > 0 && obj.closest('.question-options').find('.others-textarea:visible').val() == "") {
        error = true;
    }
    else if (input_type == 'checkbox' && required_exists > 0 && obj.closest('.question-options').find('input[type=checkbox]:checked').val() === undefined) {
        error = true;
    }
    else if (input_type == 'text' && required_exists > 0 && obj.closest('.question-options').find('input[type=text]').val() == "") {
        error = true;
    }
    else if (input_type == 'textarea' && required_exists > 0 && obj.closest('.question-options').find('textarea').val() == "") {
        error = true;
    }
    else if (input_type == 'dropdown' && required_exists > 0 && obj.closest('.question-options').find('select').val() == "") {

        error = true;
    }
    else if (input_type == 'grid' && required_exists > 0) {

        var closestinput = obj.closest('li').find('tr.data-question-type-grid');
        var closestinput_length = closestinput.length
        if (closestinput_length > 0) {
            var checkcount = 0;
            $(closestinput).each(function () {
                checkcount += parseInt($(this).find('input[type=radio]:checked').length);
            });

            if (closestinput_length != checkcount) error = true;

        }

    }
    else {
        error = false;
    }

    return error;

}


$(document).ready(function () {

    /*Disable Enter button in form*/
    $("input").keypress(function (evt) {
        var charCode = evt.charCode || evt.keyCode;
        if (charCode == 13) {
            return false;
        }

    });


    /*During Take survey Action*/
    $('#take-survey').on('click', function () {
        $('#welcome-section').fadeOut('slow').css('-webkit-transition', 'background 5s');
        $('#question-container').fadeIn('slow').css('-webkit-transition', 'background 1s');
        $('#fixed-footer').fadeIn('slow').css('-webkit-transition', 'background 1s');
        $('html, body').animate({ scrollTop: 0 }, 'slow');
    });

    /* Check Question Required Exists*/
    function CheckQuestion(showmsg) {
        var liposition = new Array();
        $('.question-container li').each(function () {
            var check_required = $(this).find('.qtn-required').length;
            var closesinput = $(this).find('.grid-required , .required');

            $.each($(this).find('.table > tbody tr'), function (index, val) {
                var current_field_val = $(this).find('input:checked').val();
                if (current_field_val == undefined) {
                    $(this).css('background', '#e3889a');
                }
                else {
                    $(this).css('background', '#fff');
                }
            });

            if (check_required > 0 && checkerror(closesinput)) {
                liposition.push($(this).attr('data-id'));
                if (showmsg == 0) $(this).find('.message').show();
            }
            else {
                $(this).find('.message').hide();
            }
        });

        return liposition;
    }



    function CheckQuestionWithInDimension(showmsg) {
        var liposition = new Array();
        $('.active-parent li').each(function (index, val) {

            var check_required = $(this).find('.qtn-required').length;
            var closesinput = $(this).find('.grid-required , .required');

            var total_question_length = $(this).find('.table > tbody tr').length
            var total_answered_question_length = $(this).find('.table > tbody tr input:checked').length

            $.each($(this).find('.table > tbody tr'), function (index, val) {
                var current_field_val = $(this).find('input:checked').val();
                if (current_field_val == undefined) {
                    $(this).css('background', '#e3889a');
                }
                else {
                    $(this).css('background', '#fff');
                }
            });

            if (check_required > 0 && checkerror(closesinput)) {
                liposition.push($(this).attr('data-id'));
                if (showmsg == 0) $(this).find('.message').show();
            }
            else {
                $(this).find('.message').hide();
                $(this).find('.question-dimension').css('color', '#31708f');
            }


        });
        return liposition;
    }



    /*During Form Save Action*/

    $('#form-save').click(function () {

        $('#formaction').val('save');
        var liposition = CheckQuestion(0);
        total_req = $('.qtn-required').length;
        if (liposition.length != total_req && total_req > 0) {
            $('#form-save').fadeTo(1000, 0.4);
            $('#survey-user-form').submit();
        }
        else {
            if (liposition.length > 0) {
                var height = $('li[data-id="' + liposition[0] + '"]').offset().top - $('.inner-header').outerHeight() - 10;
                $('html, body').animate({ scrollTop: height }, 'slow');
            }
        }

    });

    var total_dimension_question = $('.dimension_count').length;

    $('.total-page').text(total_dimension_question)

    function CheckDimensionQuestions() {
        var total_dimension_question = $('.active-parent').find('.qtn-required').length;
        var current_dimension = $('.active-parent').attr('dimension-count');
        var next_dimension = parseInt(current_dimension) + 1;

        var liposition = CheckQuestionWithInDimension(0);
        if (liposition.length > 0) {
            var height = $('li[data-id="' + liposition[0] + '"]').offset().top - $('.inner-header').outerHeight() - 10;
            $('html, body').animate({ scrollTop: height }, 'slow');
        }
        else {

            $('[dimension-count="' + current_dimension + '"]').removeClass('active-parent');
            $('[dimension-count="' + current_dimension + '"]').addClass('hidden');

            $('[dimension-count="' + next_dimension + '"]').removeClass('hidden');
            $('[dimension-count="' + next_dimension + '"]').addClass('active-parent');
            $('html, body').animate({ scrollTop: 0 }, 'slow');

            if (next_dimension != 1) {
                $('#previous').removeClass('hidden');
                $('#previous').css('display', 'block');
            }
            else {
                $('#previous').addClass('hidden');
            }

            var total_dimension = $('.dimension_count').length;
            var total_dimension = parseInt(total_dimension) - 1;

            if (total_dimension == current_dimension) {
                $('#form-submit').removeClass('hidden');
                $('#next').hide();
                $('#previous').removeClass('hidden');
            }
            else {
                $('#form-submit').addClass('hidden');
                $('#next').show();
            }

        }


        var current_page = $('.active-parent').attr('dimension-count');
        $('.current-page').text(current_page);


    }

    $(document).on('click', '#next', function () {
        CheckDimensionQuestions();
    });


    $(document).on('click', '#previous', function () {
        var current_dimension = $('.active-parent').attr('dimension-count');
        var prev_dimension = parseInt(current_dimension) - 1;
        $('#form-submit').addClass('hidden');



        $('[dimension-count="' + current_dimension + '"]').removeClass('active-parent');
        $('[dimension-count="' + current_dimension + '"]').addClass('hidden');

        $('[dimension-count="' + prev_dimension + '"]').addClass('active-parent');
        $('[dimension-count="' + prev_dimension + '"]').removeClass('hidden');

        if (prev_dimension == 1) {
            $('#previous').hide();
        }
        $('#next').show();

        var current_page = $('.active-parent').attr('dimension-count');
        $('.current-page').text(current_page);

    });

    $(document).ready(function () {
        checkCheckboxValid();
    });
    function checkCheckboxValid() {
        var check_data_type=$('[data-question-type="checkbox"]');
        var liposition = new Array();
        var minAllowed = 2;
        var maxAllowed = 2;

        $.each(check_data_type, function (i, v) {
            var check_postion=$(this).parents('li').attr('data-id');
            var question_id=$(this).find('[name="question_id[]"]').val();
            var check_checked_length=$('[name="_'+question_id+'[]"]:checked').length;

            if (check_checked_length >= maxAllowed) {
                $('[name="_'+question_id+'[]"]:not(:checked)').prop('disabled', true);
                $(this).parents('.question-options').find('.error-message-check').addClass('hidden')
            } else {
                $('[name="_'+question_id+'[]"]').prop('disabled', false);
                $(this).parents('.question-options').find('.error-message-check').addClass('hidden')
            }
            if (check_checked_length < minAllowed) {
                $(this).parents('.question-options').find('.error-message-check').removeClass('hidden').text('Please select ' + minAllowed + ' checkboxes.');
                liposition.push(check_postion);
            }
        });
        return liposition;
    }

    /*During Form Save Action*/
    $('#form-submit').click(function () {

        $('#formaction').val('submit');
        var liposition = CheckQuestion(0);
        var survey_id = $('.survey_id').val();
        var valid_count = $('.invalid').length;
        if (survey_id == 11) {
            if (valid_count > 0) {
                if (liposition.length > 0) {
                    var height = $('li[data-id="' + liposition[0] + '"]').offset().top - $('.inner-header').outerHeight() - 10;
                    $('html, body').animate({ scrollTop: height }, 'slow');
                }
                else {
                    if (liposition.length > 0) {
                        if (confirm('Are you sure. You cannot edit your responses once submitted')) {
                            $('#form-submit').fadeTo(1000, 0.4);
                            $('#survey-user-form').submit();
                        }
                    }
                }
            }
            else{
                    if (confirm('Are you sure. You cannot edit your responses once submitted')) {
                        $('#form-submit').fadeTo(1000, 0.4);
                        $('#survey-user-form').submit();
                    }
            }
        }
        else if (survey_id == 37) {
            var checkbox_validation=checkCheckboxValid();
            if (liposition.length > 0 || checkbox_validation.length>0) {
                if (liposition.length>0) {
                    var height = $('li[data-id="' + liposition[0] + '"]').offset().top - $('.inner-header').outerHeight() - 10;
                    $('html, body').animate({ scrollTop: height }, 'slow');
                }
                else if (checkbox_validation.length>0) {
                    var height = $('li[data-id="' + checkbox_validation[0] + '"]').offset().top - $('.inner-header').outerHeight() - 10;
                    $('html, body').animate({ scrollTop: height }, 'slow');
                }
            }
            else {
                if (confirm('Are you sure. You cannot edit your responses once submitted')) {
                    $('#form-submit').fadeTo(1000, 0.4);
                    $('#survey-user-form').submit();
                }
            }
        }
        else {
            if (liposition.length > 0) {
                var height = $('li[data-id="' + liposition[0] + '"]').offset().top - $('.inner-header').outerHeight() - 10;
                $('html, body').animate({ scrollTop: height }, 'slow');
            }
            else {
                if (confirm('Are you sure. You cannot edit your responses once submitted')) {
                    $('#form-submit').fadeTo(1000, 0.4);
                    $('#survey-user-form').submit();
                }
            }
        }



    });

    /*Navigation of section by Previous and Next Action*/
    $('#next-btn,#prev-btn').on('click', function () {

        var h = window.innerHeight;
        var c = h / 2;
        var id = $(this).val();
        var height = $('li[data-id="' + id + '"]').offset().top - $('.inner-header').outerHeight() - 10;
        $('html, body').animate({ scrollTop: height }, 'slow');

    });

    /*During Option change(giving answer to questions)*/
    $(".question-options").change(function () {
        var showmsg = 1;
        var liposition = CheckQuestion(showmsg);
        /*if(liposition.length === 0) $('#form-save').hide();
        else $('#form-save').show();*/

    });

    /**/

    $('body').on('change', '.required', function () {

        if ($(this).next('label').text() == "others" || $(this).hasClass('others-textarea')) {
            $(this).parent().find('.others-textarea').show().focus();
        }
        else {
            $(this).closest('.question-options').find('.others-textarea').val('').hide();
        }



        if (!checkerror($(this))) {
            $(this).parents('li').find('.message').hide();
            var id = $(this).parents('li').next().attr('data-id');

            if (id != "" && id != undefined) {

                var check_field = $(this).attr("data-type");
                if (check_field == "radio" || check_field == "dropdown") {
                    var height = $('li[data-id="' + id + '"]').offset().top - $('.inner-header').outerHeight() - 10;
                    $('html, body').animate({ scrollTop: height }, 'slow');
                }
            }
            var datatouched = $(this).closest('.question-options').attr('data-nottouch');
            if (datatouched == 0) {

                var totalanswer = parseInt($('#answered').attr('data-count')) + 1;
                $('#answered').attr('data-count', totalanswer);
                $('#answered').text(totalanswer);

                var totalquestions = parseInt($('#total-questions').text());
                var percentage = Math.floor((totalanswer / totalquestions) * 100);

                $('.progress-bar').css('width', percentage + '%').attr('aria-valuenow', percentage);
                $(this).closest('.question-options').attr('data-nottouch', 1);
            }

        }
        else {


            $(this).parents('li').find('.message').show();


            if ($(this).closest('.question-options').find('label:last').text() != "others") {

                $(this).closest('.question-options').attr('data-nottouch', 0);
                var totalanswer = parseInt($('#answered').attr('data-count')) - 1;
                $('#answered').attr('data-count', totalanswer);
                $('#answered').text(totalanswer);
                var totalquestions = parseInt($('#total-questions').text());
                var percentage = Math.floor((totalanswer / totalquestions) * 100);
                $('.progress-bar').css('width', percentage + '%').attr('aria-valuenow', percentage);
            }
            else {
                var datatouched = $(this).closest('.question-options').attr('data-nottouch');
                if (datatouched == 1) {
                    var totalanswer = parseInt($('#answered').attr('data-count')) - 1;
                    $('#answered').attr('data-count', totalanswer);
                    $('#answered').text(totalanswer);
                    var totalquestions = parseInt($('#total-questions').text());
                    var percentage = Math.floor((totalanswer / totalquestions) * 100);
                    $('.progress-bar').css('width', percentage + '%').attr('aria-valuenow', percentage);
                    $(this).closest('.question-options').attr('data-nottouch', 0);
                }
            }
        }

    });

    $('body').on('keyup', '.required', function (el) {

        var check_field = $(this).attr("data-type");
        var text_id = $(this).attr('name');
        var length_data = $('[name="' + text_id + '"]').val().length;

        if (length_data == 0) $(this).parent(".question-options").find('.on_sub').remove();


        if (!checkerror($(this))) {
            var id = $(this).parents('li').next().attr('data-id');

            if (id != undefined) {
                if (check_field == "textarea" || check_field == "text") {



                    if (length_data > 0) {
                        /*
                                  $(this).before("<div class='on_sub'><input type='button' data-count='"+text_id+"' id='next_move'  value='Ok' class='btn btn-success input'></div>");
                        */

                        $(this).before("<div class='on_sub'><button type='button' data-count='" + text_id + "' id='next_move' class='btn btn-success input'> <span class='glyphicon glyphicon-ok'></span></button></div>");

                        $('.input').on('click', function () {
                            var id = $(this).parents('li').next().attr('data-id');
                            var height = $('li[data-id="' + id + '"]').offset().top - $('.inner-header').outerHeight() - 10;
                            $('html, body').animate({ scrollTop: height }, 'slow');
                        });
                    }
                    // else{
                    //   // $$(this).parent('.on_sub').remove();
                    //   $(this).parent(".question-options").find('.on_sub').remove();
                    // }
                }
            }

        }
    });

    $('body').on('click', '.required', function () {

        var check_field = $(this).attr("data-type");

        var $check_name = $(this).attr('name');
        // alert($check_name);
        var data_length = $('input[name="' + $check_name + '"]').filter(':checked').length;

        if (data_length == 0) $(this).parents(".question-options").find('.on_submit').remove();

        if (!checkerror($(this))) {
            var id = $(this).parents('li').next().attr('data-id');
            var crnt_data_id = $(this).prev().attr('data-count');
            var check_id = $(this).attr('name');

            if (id != "" && id != undefined) {
                if (check_field == "checkbox") {
                    var on_submit_count = $(this).parents(".question-options").find('.on_submit').length;

                    if (data_length > 0 && on_submit_count == 0) {
                        /*
                                  $(this).after("<div class='on_submit'><input type='button' data-count='"+check_id+"' id='next_move_check'  value='Ok' class='btn btn-success input'></div>");

                        */
                        $(this).before("<div class='on_submit'><button type='button' data-count='" + check_id + "' id='next_move_check'  class='btn btn-success input'><span class='glyphicon glyphicon-ok'></span></button></div>");

                        $('.input').on('click', function () {
                            var id = $(this).parents('li').next().attr('data-id');
                            var height = $('li[data-id="' + id + '"]').offset().top - $('.inner-header').outerHeight() - 10;
                            $('html, body').animate({ scrollTop: height }, 'slow');
                        });
                    }
                }
            }

        }
    });


    $('.required').click(function () {

        var check_field = $(this).attr("data-type");

        if (check_field == "radio") {

            var radio_name = $(this).attr('name');
            var radio_id = $(this).attr('id');
            var label_name = $("label[for='" + radio_id + "']").text();

            if (label_name === 'others') {
                $('#' + radio_name + '').keyup(function () {

                    var length_data = $('[name="others' + radio_name + '"]').val().length;

                    var on_submit_count = $(this).parents(".question-options").find('.on_submit_others').length;


                    if (length_data > 0 && on_submit_count == 0) {
                        $(this).before("<div class='on_sub" + radio_name + "  on_submit_others'><button type='button' data-count='" + radio_id + "' id='next_move_others'   class='btn btn-success input'><span class='glyphicon glyphicon-ok'></span></button></div>");
                        $('.input').on('click', function () {
                            var height = $('li[data-id="' + id + '"]').offset().top - $('.inner-header').outerHeight() - 10;
                            $('html, body').animate({ scrollTop: height }, 'slow');
                        });


                    }
                    else if (length_data == 0) {
                        $(".on_sub" + radio_name + "").remove();
                    }

                });

            }
            else if (label_name != 'others') {
                $(".on_sub" + radio_name + "").remove();
            }


            if (!checkerror($(this))) {
                var id = $(this).parents('li').next().attr('data-id');
                var crnt_data_id = $(this).prev().attr('data-count');

                if (id != "" && id != undefined) {
                    $('.input').on('click', function () {
                        var height = $('li[data-id="' + id + '"]').offset().top - $('.inner-header').outerHeight() - 10;
                        // $('html, body').animate({scrollTop:height}, 'slow');
                    });
                }
            }
        }

    });



    $('.grid-required').click(function () {

        var total_question = parseInt($(this).closest('table').find('tr.data-question-type-grid').length);

        var obj = $(this).closest('table').find('tr.data-question-type-grid');
        var checkcount = 0;
        $(obj).each(function () {

            checkcount += parseInt($(this).find('input[type=radio]:checked').length);

        });
        var question_required = $(this).parents('.question').find('.qtn-required').length;

        var id = $(this).parents('li').next().attr('data-id');

        if (id != "" && id != undefined) {

            if (total_question == checkcount) {

                $(this).parents('li').find('.message').hide();
                var height = $('li[data-id="' + id + '"]').offset().top - $('.inner-header').outerHeight() - 10;
                // $('html, body').animate({scrollTop:height}, 'slow');
                var datatouched = $(this).closest('.question-options').attr('data-nottouch');
                if (datatouched == 0) {

                    var totalanswer = parseInt($('#answered').attr('data-count')) + 1;
                    $('#answered').attr('data-count', totalanswer);
                    $('#answered').text(totalanswer);

                    var totalquestions = parseInt($('#total-questions').text());
                    var percentage = Math.floor((totalanswer / totalquestions) * 100);

                    $('.progress-bar').css('width', percentage + '%').attr('aria-valuenow', percentage);
                    $(this).closest('.question-options').attr('data-nottouch', 1);
                }
            }
            else {
                if (question_required == 1) {
                    $(this).parents('li').find('.message').hide();
                }
                else {
                    $(this).parents('li').find('.message').hide();

                }
            }


        }
        else {
            if (total_question == checkcount) {
                var datatouched = $(this).closest('.question-options').attr('data-nottouch');
                if (datatouched == 0) {

                    var totalanswer = parseInt($('#answered').attr('data-count')) + 1;
                    $('#answered').attr('data-count', totalanswer);
                    $('#answered').text(totalanswer);

                    var totalquestions = parseInt($('#total-questions').text());
                    var percentage = Math.floor((totalanswer / totalquestions) * 100);

                    $('.progress-bar').css('width', percentage + '%').attr('aria-valuenow', percentage);
                    $(this).closest('.question-options').attr('data-nottouch', 1);
                }
            }
        }
    });

    /*end of onload document ready function*/
});
