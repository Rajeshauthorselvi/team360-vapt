@extends('layouts.default')

@section('content')
    <!-- <form class="container"> -->

        <form method="POST" action="{{ route('survey.update', $data->id) }}" role="form" class="form-horizontal container" enctype="multipart/form-data" id="add-survey">
            @csrf
            @method('PATCH')
    <div class="row setup-content" id="step-1">
        <div class="col-xs-12">
            <div class="form-wrapper">
                <div class="form-steps-wizard step1"> </div>


                <div class="col-md-12 well">
                    <h3 class="need-margin-bottom-forstrip text-center">Edit Survey</h3>

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
                                    <label for="title" class="col-sm-2">Survey Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="title" id="title" class="form-control" required oninput="sanitizeInput(this)" value="{{ $data->title }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="client_name" class="col-sm-2">Client Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="client_name" id="client_name" class="form-control" data-toggle="tooltip" data-placement="bottom" title="Client Name"
                                               oninput="sanitizeInput(this)"  value="{{ $data->client_name }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="start_date" class="col-sm-2">Start Date</label>
                                    <div class="col-sm-10">
                                        <?php
                                            $start_date = date('d/m/Y h:i A', strtotime($data->start_date));
                                            $end_date = date('d/m/Y h:i A', strtotime($data->end_date));
                                        ?>

                                        <input type="text" name="start_date" id="startdatetime" class="form-control"
                                               value="{{ date('d/m/Y h:i A', strtotime($data->start_date)) }}"
                                               required readonly>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="end_date" class="col-sm-2">End Date</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="end_date" id="enddatetime" class="form-control"
                                               value="{{ date('d/m/Y h:i A', strtotime($data->end_date)) }}"
                                               required readonly>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="send_email_from" class="col-sm-2" data-toggle="tooltip" data-placement="bottom" title="From Email">From Email</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="send_email_from" id="send_email_from" class="form-control" value="{{ $data->send_email_from }}">
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="sender_name" class="col-sm-2">Sender Name</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="sender_name" id="sender_name" class="form-control" data-toggle="tooltip" data-placement="bottom" title="Sender Name"
                                               oninput="sanitizeInput(this)" value="{{ $data->sender_name }}">
                                    </div>
                                </div>

                                @include('admin.survey.images')

                                <div class="form-group">
                                    <label for="header_text" class="col-sm-2">Header Text</label>
                                    <div class="col-sm-10">
                                        <textarea name="header_text" id="header_text" class="form-control">{{ $data->header_text }}</textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="footer_text" class="col-sm-2">Footer Text</label>
                                    <div class="col-sm-10">
                                        <textarea name="footer_text" id="footer_text" class="form-control">{{ $data->footer_text }}</textarea>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <div class="col-sm-10 col-sm-offset-2">
                                        <span id="printchatbox"></span>
                                    </div>
                                </div>

                                <div class="form-group">
                                    <label for="chatinput" class="col-sm-2">Survey URL</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="url" id="chatinput" class="form-control url" required value="{{ $data->url }}">
                                    </div>
                                </div>


                                <!-- Shuffle QUestion -->
                                <div class="form-group">
                                    <label for="shuffle_questions" class="col-sm-2">Shuffle Question</label>


                                    <div class="col-sm-10">
                                        @if ($data->shuffle_questions == 1)
                                            <input type="radio" id="sh_yes" name="shuffle_questions" value="1" checked>
                                            <label for="sh_yes">Yes</label>
                                            &nbsp;
                                            <input id="sh_no" type="radio" name="shuffle_questions" value="0">
                                            <label for="sh_no">No</label>
                                        @else
                                            <input type="radio" id="sh_yes" name="shuffle_questions" value="1">
                                            <label for="sh_yes">Yes</label>
                                            &nbsp;
                                            <input id="sh_no" type="radio" name="shuffle_questions" value="0" checked>
                                            <label for="sh_no">No</label>
                                        @endif
                                    </div>
                                </div>
                                <!-- Shuffle QUestion -->

                                <div class="form-group">

                                    <label for="rater" class="col-sm-2">Rater / Respondent</label>
                                    <div class="col-sm-10">
                                        <input type="text" name="rater" id="rater" class="raters form-control" placeholder="Rater / Respondent" oninput="sanitizeInput(this)">
                                        <div id="chat-screen" class="well">
                                            <ul class="chat-screen list-unstyled">
                                                @foreach ($raters as $rater)
                                                    <li class="multipleInput-value">
                                                        {{ $rater->rater }}
                                                        <span>
                                                            <input class="rater_value" value="{{ $rater->rater }}"
                                                                name="rater_value[]" type="hidden">
                                                        </span>
                                                        <a href="javascript:void(0)" class="multipleInput-close"
                                                            title="Remove"><i class="glyphicon glyphicon-remove-sign"></i>
                                                        </a>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                        <div class="error-mess"></div>
                                    </div>

                                </div>



                                <div class="form-group">
                                    <?php $dimension_hide = $data->dimension_hide; ?>

                                    <div class="col-sm-10 col-sm-offset-2 text-left">
                                        <input type="checkbox" name="dimension_hide" value="1" id="dimension_hide" {{ $dimension_hide=='1' ? 'checked':'' }}>
                                        <label for="dimension_hide">Dimension Hide</label>
                                    </div>

                                </div>


                                <div class="form-group">
                                    <?php $checkbox_value = $data->participant_rater_manage; ?>
                                    <div class="col-sm-10 col-sm-offset-2 text-left">
                                        <input type="checkbox" name="participant_rater_manage" value="1" id="rater_chk" {{ $checkbox_value=='1' ? 'checked':'' }}>
                                        <label for="rater_chk">Participants can Manage Rater/Respondent ?</label>
                                    </div>
                                </div>


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

                    <!-- </form> -->
                </div>
            </div>
        </div>
    </div>


    <!-- </form> -->

</form>


<script src="{{ asset('script/moment.js') }}"></script>
<script src="{{ asset('script/bootstrap-datetimepicker.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/bootstrap-datetimepicker.min.css') }}">

<link rel="stylesheet" href="{{ asset('css/bootstrap3-wysihtml5.min.css') }}">
<script src="{{ asset('script/bootstrap3-wysihtml5.js') }}"></script>

    <style type="text/css">
        .img-cancel {
            display: none;
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
            var inputBox = $('.url').val();
            var url = {!! json_encode(url('/')) !!};
            $(window).load(function() {
                if ($('#fchk').val() == '1') {
                    $('#printchatbox').html(url + '/os/' + inputBox);
                } else {
                    $('#printchatbox').html(url + '/' + inputBox);
                }
            });

            $("#chatinput").keyup(function() {
                var inputBox = $('.url').val();
                if ($('#fchk').val() == '1') {
                    $('#printchatbox').html(url + '/os/' + inputBox);
                } else {
                    $('#printchatbox').html(url + '/' + inputBox);
                }

            });

            $('#fchk').click(function() {
                var inputBox = $('.url').val();
                if ($('#fchk').val() == '1') {
                    $('#printchatbox').html(url + '/os/' + inputBox);
                } else {
                    $('#printchatbox').html(url + '/' + inputBox);
                }
            });
        });
    </script>
    <style>
        #printchatbox {
            color: blue
        }

    </style>
    <script type="text/javascript">
        $(function() {


            $('#startdatetime').datetimepicker({
                format: 'DD/MM/YYYY LT',
                ignoreReadonly: true,
            });
            var next = moment("{{ $start_date }}", 'DD-MM-YYYY LT');
            $('#enddatetime').datetimepicker({
                format: 'DD/MM/YYYY LT',
                ignoreReadonly: true,
                minDate: moment(next)
            });

            $("#startdatetime").on("dp.change", function(e) {

                $('#enddatetime').data("DateTimePicker").minDate(e.date);
                $('#add-survey').bootstrapValidator('revalidateField', 'start_date');
                $(this).focus();
                $(this).blur();


            });
            $("#enddatetime").on("dp.change", function(e) {
                var nextSecond = e.date.subtract(1, 'seconds');
                $('#startdatetime').data("DateTimePicker").maxDate(nextSecond);
                $('#add-survey').bootstrapValidator('revalidateField', 'end_date');
                $(this).focus();
                $(this).blur();
            });
        });

        $(document).ready(function() {

            $('[data-toggle="tooltip"]').tooltip();

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
                        title: {
                            validators: {
                                notEmpty: {
                                    message: 'The Field required and cannot be empty'
                                }
                            }
                        },
                        start_date: {
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
                                }

                            }
                        }
                    }
                });
            //
        });
    </script>

    <script>
        $(document).ready(function() {

            $(".multipleInput-close").click(function() {
                $(this).parent().remove();
                return false;
            });

            /*Rater field validation*/
            $('#add-survey').submit(function(e) {

                var li_length = $('.chat-screen li').length;
                if (li_length == 0) {

                    $('.error-mess').html(
                        '<small class="text-danger remove-message">The Field required and cannot be empty</small>'
                        );
                    $('#chat-screen').addClass('required');
                    $("#activate-step-2").attr("disabled", "disabled");
                    return false;
                } else {
                    $('#chat-screen').removeClass('required');
                    $('.remove-message').remove();
                    $("#activate-step-2").removeAttr("disabled");
                    return true;
                }
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
                        $('.chat-screen').append($('<li class="multipleInput-value"> ' + $("#rater").val() +
                                '<span><input type="hidden" value="' + $("#rater").val() +
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

                    var append_val = true;
                    $(".chat-screen .multipleInput-value").each(function() {
                        if (ui.item.value.trim() == $(this).text().trim()) {
                            append_val = false;
                        }
                    });
                    if (ui.item.value != "No Result Found" && append_val == true) {
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
