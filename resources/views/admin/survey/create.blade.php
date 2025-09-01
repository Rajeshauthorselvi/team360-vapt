@extends('layouts.default')

@section('content')
    <!--
     <link rel="stylesheet" type="text/css" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

     <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/tagmanager/3.0.2/tagmanager.min.css">

     <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>

     <script type="text/javascript" src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

     <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/tagmanager/3.0.2/tagmanager.min.js"></script>

     <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-3-typeahead/4.0.1/bootstrap3-typeahead.min.js"></script>

    -->

    <!-- <form class="container"> -->

    {{ Form::open(['route' => 'survey.store','role' => 'form','class' => 'form-horizontal container','files' => 'true','id' => 'add-survey']) }}
    <div class="row setup-content" id="step-1">

        <div class="col-xs-12">
            <div class="form-wrapper">
                <div class="form-steps-wizard step1"> </div>


                <div class="col-md-12 well">
                    <h3 class="need-margin-bottom-forstrip text-center">Create New Survey</h3>


                    <!-- <form> -->

                    @if ($errors->any())
                        <div class="alert alert-danger fade in">

                            <a href="#" class="close" data-dismiss="alert">&times;</a>

                            <strong>Error!</strong> A problem has been occurred while submitting form.<br>
                            <ul>
                                {!! implode('', $errors->all('<li class="text-danger">:message</li>')) !!}

                            </ul>
                        </div>
                    @endif
                    <div class="container col-xs-12">
                        <div class="row clearfix">
                            <div class="col-md-8 col-md-offset-2 column">

                                <div class="form-group">

                                    {{ Form::label('title', 'Survey Name', ['class' => 'col-sm-2 ']) }}
                                    <div class="col-sm-10">
                                        {{ Form::text('title', null, ['class' => 'form-control']) }}
                                    </div>

                                </div>

                                <div class="form-group">

                                    {{ Form::label('cname', 'Client Name', ['class' => 'col-sm-2']) }}
                                    <div class="col-sm-10">
                                        {{ Form::text('client_name', null, ['class' => 'form-control','data-toggle' => 'tooltip','data-placement' => 'bottom','title' => 'Client Name']) }}
                                    </div>

                                </div>


                                <div class="form-group">

                                    {{ Form::label('sdate', 'Start Date', ['class' => 'col-sm-2 ']) }}
                                    <div class="col-sm-10">
                                        {{ Form::text('start_date', null, ['class' => 'form-control', 'id' => 'startdatetime', 'readonly' => true]) }}
                                    </div>

                                </div>

                                <div class="form-group">

                                    {{ Form::label('edate', 'End Date', ['class' => 'col-sm-2 ']) }}
                                    <div class="col-sm-10">
                                        {{ Form::text('end_date', null, ['class' => 'form-control', 'id' => 'enddatetime', 'readonly' => true]) }}
                                    </div>

                                </div>
                                <div class="form-group">

                                    {{ Form::label('femail', 'From Email', ['class' => 'col-sm-2']) }}
                                    <div class="col-sm-10">
                                        {{ Form::text('send_email_from', null, ['class' => 'form-control','data-toggle' => 'tooltip','data-placement' => 'bottom','title' => 'From Email']) }}
                                    </div>

                                </div>
                                <div class="form-group">

                                    {{ Form::label('femail', 'Sender Name', ['class' => 'col-sm-2']) }}
                                    <div class="col-sm-10">
                                        {{ Form::text('sender_name', null, ['class' => 'form-control','data-toggle' => 'tooltip','data-placement' => 'bottom','title' => 'Sender Name']) }}
                                    </div>

                                </div>



                                @include('admin.survey.images')

                                <div class="form-group">
                                    {{ Form::label('header_text', 'Header Text', ['class' => 'col-sm-2 ']) }}
                                    <div class="col-sm-10">
                                        {{ Form::textarea('header_text', null, ['class' => 'form-control', 'id' => 'header_text']) }}
                                    </div>
                                </div>

                                <div class="form-group">
                                    {{ Form::label('footer_text', 'Footer Text', ['class' => 'col-sm-2 ']) }}
                                    <div class="col-sm-10">
                                        {{ Form::textarea('footer_text', '© 2017 Ascendus. All Rights Reserved.', ['class' => 'form-control','id' => 'footer_text']) }}
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-10 col-sm-offset-2">
                                        <span id='printchatbox'></span>
                                    </div>
                                </div>

                                <div class="form-group">

                                    {{ Form::label('surl', 'Survey URL', ['class' => 'col-sm-2 ']) }}
                                    <div class="col-sm-10">
                                        {{ Form::text('url', null, ['id' => 'chatinput', 'class' => 'form-control url']) }}
                                    </div>

                                </div>

                                <div class="form-group">
                                    {{ Form::label('shuffle_questions', 'Shuffle Question', ['class' => 'col-sm-2 ']) }}

                                    <div class="col-sm-10">
                                        <input type="radio" id="sh_yes" name="shuffle_questions" value="1" checked>
                                        <label for="sh_yes">Yes</label>
                                        &nbsp;
                                        <input id="sh_no" type="radio" name="shuffle_questions" value="0">
                                        <label for="sh_no">No</label>
                                    </div>
                                </div>




                                <div class="form-group">

                                    {{ Form::label('rater', 'Rater / Respondent', ['class' => 'col-sm-2 ']) }}
                                    <div class="col-sm-10">
                                        <!--{{ Form::text('rater', null, ['class' => 'form-control']) }}-->
                                        {{ Form::text('rater', null, ['placeholder' => 'Rater / Respondent','class' => 'raters form-control','id' => 'rater']) }}
                                        <!-- {{ Form::hidden('rater_name', null, ['placeholder' => 'Rater / Respondent','class' => 'form-control','id' => 'rater']) }} -->
                                        <div id="chat-screen" class="well">
                                            <ul class="chat-screen list-unstyled">
                                                <li class="multipleInput-value">self<span><input type="hidden" value="self"
                                                            name="rater_value[]"></span><a href="javascript:void(0)"
                                                        class="multipleInput-close" title="Remove"><i
                                                            class="glyphicon glyphicon-remove-sign"></i></a></li>
                                            </ul>
                                        </div>
                                    </div>

                                </div>
                                <!--
      <div class="form-group">

       <label>Rater / Respondent:</label><br/>

       <input type="text" name="rater" placeholder="Tags" class="typeahead tm-input form-control tm-input-info" id=rater/>

      </div>
    -->

                                <div class="form-group">


                                    <div class="col-sm-10 col-sm-offset-2 text-left">
                                        {{ Form::checkbox('dimension_hide', 1, null, ['id' => 'dimension']) }}
                                        {{ Form::label('dimension', 'Dimension Hide ', ['id' => 'dimension']) }}
                                    </div>

                                </div>

                                <div class="form-group">


                                    <div class="col-sm-10 col-sm-offset-2 text-left">
                                        {{ Form::checkbox('participant_rater_manage', 1, null, ['id' => 'rater_chk']) }}
                                        {{ Form::label('rater_chk', 'Participants can Manage Rater/Respondent ? ', ['id' => 'rater_chk']) }}
                                    </div>

                                </div>
                                <br />
                                <div class="form-group">

                                    <div class="col-sm-12 text-center">
                                        <a href="{{ URL::route('admin.dashboard') }}"
                                            class="btn btn-danger btn-md">Cancel</a>
                                        <button id="activate-step-2" type="submit" class="btn btn-success btn-md">Save &
                                            Next</button>
                                    </div>

                                </div>




                            </div>
                        </div>

                    </div>


                </div>
            </div>
        </div>
    </div>

    <!-- </form> -->

    {{ Form::close() }}


    {!! HTML::script('script/moment.js') !!}
    {!! HTML::script('script/bootstrap-datetimepicker.js') !!}
    {!! HTML::style('css/bootstrap-datetimepicker.min.css') !!}

    {{ HTML::style('css/bootstrap3-wysihtml5.min.css') }}

    {{ HTML::script('script/bootstrap3-wysihtml5.js') }}

    <style>
        #printchatbox {
            color: blue
        }

        .chat-screen {

            margin: 10px;
            min-height: 80px;
            max-height: 80px;
            overflow: auto;
            width: auto;
        }

        li.multipleInput-value {
            float: left;
            margin-right: 2px;
            margin-bottom: 1px;
            border: 1px #eee solid;
            padding: 2px;
            background: #eee;
        }

        .multipleInput-close {
            width: 16px;
            height: 16px;
            display: block;
            float: right;
            margin: 0 3px;
        }

        #chat-screen {
            padding: 0;
            overflow: auto;
            border: 1px solid #ccc;
        }

        .required {
            border-color: #a94442 !important;
        }

    </style>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#footer_text,#header_text').wysihtml5({
                events: {
                    load: function() {
                        $('#footer_text,#header_text').addClass('textnothide');

                    },
                    change: function() {
                        $('#add-survey').bootstrapValidator('revalidateField', 'footer_text');
                        $('#add-survey').bootstrapValidator('revalidateField', 'header_text');
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
        });
    </script>
    <script>
        $(document).ready(function() {
            $("#chatinput").keyup(function() {
                var inputBox = $('.url').val();
                if ($('#fchk').val() == '1') {
                    $('#printchatbox').html({!! json_encode(url('/')) !!} + '/os/' + inputBox);
                } else {
                    $('#printchatbox').html({!! json_encode(url('/')) !!} + '/' + inputBox);
                }

            });

            $('#fchk').click(function() {
                var inputBox = $('.url').val();
                if ($('#fchk').val() == '1') {
                    $('#printchatbox').html({!! json_encode(url('/')) !!} + '/os/' + inputBox);
                } else {
                    $('#printchatbox').html({!! json_encode(url('/')) !!} + '/' + inputBox);
                }
            });
        });
    </script>
    </script>
    <script type="text/javascript">
        $(function() {
            $('#startdatetime').datetimepicker({
                format: 'DD/MM/YYYY LT',
                ignoreReadonly: true,
                minDate: moment()
            });
            $('#enddatetime').datetimepicker({
                format: 'DD/MM/YYYY LT',
                useCurrent: false,
                ignoreReadonly: true,
                minDate: moment(),

            });
            $("#startdatetime").on("dp.change", function(e) {

                $('#enddatetime').data("DateTimePicker").minDate(e.date);
                $('#add-survey').bootstrapValidator('revalidateField', 'start_date');
                // $(this).focus();
                //$(this).blur();
                $('.bootstrap-datetimepicker-widget').hide();


            });
            $("#enddatetime").on("dp.change", function(e) {
                $('#startdatetime').data("DateTimePicker").maxDate(e.date);
                $('#add-survey').bootstrapValidator('revalidateField', 'end_date');
                //$(this).focus();
                // $(this).blur();
                $('.bootstrap-datetimepicker-widget').hide();
            });
        });
        //

        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
            var url = $('[name=url]').val();
            $('#add-survey')
                .bootstrapValidator({
                    framework: 'bootstrap',
                    icon: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        logo: {
                            validators: {
                                notEmpty: {
                                    message: 'The field is required and cannot be empty.Maximum allowed image width size is 400px.'
                                },
                                file: {
                                    extension: 'png,jpg,jpeg',
                                    message: 'Please upload images only'
                                }
                            }
                        },
                        right_logo: {
                            validators: {
                                file: {
                                    extension: 'png,jpg,jpeg',
                                    message: 'Please upload images only'
                                }
                            }
                        },
                        title: {
                            validators: {
                                notEmpty: {
                                    message: 'The Field required and cannot be empty'
                                }
                            }
                        },
                        send_email_from: {
                            validators: {
                                notEmpty: {
                                    message: 'The Field required and cannot be empty'
                                },
                                emailAddress: {
                                    message: 'The value is not a valid email address'
                                }
                            }
                        },
                        start_date: {
                            validators: {
                                notEmpty: {
                                    message: 'The Field required and cannot be empty'
                                }
                            }
                        },
                        end_date: {
                            validators: {
                                notEmpty: {
                                    message: 'The Field required and cannot be empty'
                                },
                                /*date: {
                                     format: 'DD/MM/YYYY h:m:A',
                                     message: 'The value is not a valid date'
                                 }*/
                            }
                        },
                        url: {
                            validators: {
                                notEmpty: {
                                    message: 'The Field required and cannot be empty.'
                                },
                                regexp: {
                                    regexp: /^[\w]+$/,
                                    message: 'You can introduce just alphabetical characters, underscore, number but no spaces'
                                },
                                remote: {
                                    message: 'This Survey Name already exists',
                                    url: '{!! URL::route('check_survey') !!}',

                                    data: {
                                        url: url

                                    }
                                }
                            }
                        }

                    }
                });

        });
    </script>

    <script>
        $(document).ready(function() {


            /*Rater field validation*/
            $('form').submit(function(e) {

                $("#activate-step-2").attr("disabled", "disabled");
                var li_length = $('.chat-screen li').length;
                if (li_length == 0) {

                    $('#chat-screen').addClass('required');
                    $("#activate-step-2").attr("disabled", "disabled");
                    var err_count = $('.remove-message').size();
                    if (err_count == 0) {
                        $('#chat-screen').after(
                            '<small class="text-danger remove-message">The Field required and cannot be empty</small>'
                            );
                    }

                    return false;
                } else {
                    $('#chat-screen').removeClass('required');
                    $('.remove-message').remove();
                    $("#activate-step-2").removeAttr("disabled");
                    return true;
                }
            });

            $('.multipleInput-close').click(function(e) {
                $(this).parent().remove();
                e.preventDefault();
            });


            /*End Rater field validation*/

            /*Rater Field Keypress*/

            $('#rater').keydown(function(event) {

                if (event.keyCode == 13 || event.keyCode == 9) {
                    var append_val = true;
                    var keypress_val = $(this).val();
                    $(".chat-screen .multipleInput-value").each(function() {
                        if (keypress_val.trim() == $(this).text().trim()) {
                            append_val = false;
                        }
                    });

                    if ($(this).val() != "" && append_val == true) {
                        $('.chat-screen').append($('<li class="multipleInput-value" > ' + $("#rater")
                            .val() + '<span><input type="hidden" value="' + $("#rater").val() +
                                '" name="rater_value[]"></span></li>')
                            .append($(
                                    '<a href="javascript:void(0)" class="multipleInput-close" title="Remove"><i class="glyphicon glyphicon-remove-sign"></i></a>'
                                    )
                                .click(function(e) {
                                    $(this).parent().remove();
                                    e.preventDefault();
                                })
                            )
                        );
                    } else {

                        if ($(this).val() != "") {
                            alert('This Rater already exists');
                        }
                    }
                    $('.remove-message').remove();
                    $('#chat-screen').removeClass('required');
                    $("#activate-step-2").removeAttr("disabled");
                    $(this).val('');
                    event.preventDefault();
                    return false;
                }
            });
            /*End Rater Field Keypress*/



            /*autocomplete Raters**/

            src = "{{ route('searchajax') }}";
            $("#rater").autocomplete({
                source: function(request, response) {
                    $.ajax({
                        url: src,
                        dataType: "json",
                        data: {
                            term: request.term
                        },
                        success: function(data) {
                            response(data);
                            //Remove (No Result Found) drop down
                            if ($(".ui-menu-item > div").text() == "No Result Found") {
                                $(".ui-autocomplete").hide();
                            }
                        }
                    });
                },
                minLength: 1,
                select: function(event, ui) {

                    var appen_val = true;
                    $(".chat-screen .multipleInput-value").each(function() {
                        if (ui.item.value.trim() == $(this).text().trim()) {
                            appen_val = false;
                        }
                    });
                    if (ui.item.value != "No Result Found" && appen_val == true) {
                        $('.chat-screen').append($('<li class="multipleInput-value"> ' + ui.item.value +
                                '<span><input class="rater_value" type="hidden" value="' + ui.item
                                .value + '" name="rater_value[]"></span></li>')
                            .append($(
                                    '<a href="javascript:void(0)" class="multipleInput-close" title="Remove"><i class="glyphicon glyphicon-remove-sign"></i></a>'
                                    )
                                .click(function(e) {
                                    $(this).parent().remove();
                                    e.preventDefault();
                                })
                            )
                        );
                    } else {
                        alert('This Rater already exists');
                    }
                    $('.remove-message').remove();
                    $('#chat-screen').removeClass('required');
                    $("#activate-step-2").removeAttr("disabled");
                    $(this).val("");
                    return false;
                }


            });
        });
    </script>
    <style>
        .multipleInput-value {
            text-transform: capitalize;

        }

    </style>
@endsection
