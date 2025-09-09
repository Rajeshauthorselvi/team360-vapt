@extends('layouts.default')

@section('content')


    <div class="" style="padding: 10px;">
        <div class="row">
            <div class="col-md-12">
                <div class="col-md-12 text-right">
                    <a href="javascript:void(0)" attr-href="{{ route('delete.survey') }}"
                        class="btn btn-danger delete-survey">
                        Delete Survey
                    </a>
                    <a href="{{ route('survey.index') }}" class="btn btn-warning" id="copy-survey" data-toggle="tooltip" title="Copy & Create Survey">Copy & Create Survey</a>
                    <a href="{{ route('survey.create') }}" class="btn btn-success" data-toggle="tooltip" title="Create New Survey">Create New Survey</a>
                </div>
                @if (Session::has('error_info'))
                    <?php
                    $semails = '';
                    $mailstatus = Session::get('error_info'); ?>

                    @if (isset($mailstatus['mailsent']))
                        @foreach ($mailstatus['mailsent'] as $email)

                            <?php $semails .= $email . ','; ?>

                        @endforeach
                    @endif
                    @if (!empty($semails))
                        <?php $email = rtrim($semails, ','); ?>
                        <div class="alert alert-success alert-dismissable" style="clear: both;">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <strong>Success!</strong> Mail Sent Successfully.<br />
                        </div>
                    @endif
                    <?php
                    $nemails = ''; ?>
                    @if (isset($mailstatus['mail_failed']))
                        @foreach ($mailstatus['mail_failed'] as $email)

                            <?php $nemails .= $email . ','; ?>

                        @endforeach
                    @endif
                    @if (!empty($nemails))
                        <div class="alert alert-danger alert-dismissable" style="clear: both;">
                            <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                            <strong>Oops!</strong> Mail Not Sent to <strong>{{ rtrim($nemails, ',') }}</strong>
                        </div>
                    @endif

                @endif

                @if (Session::has('success_msg'))
                    <div class="alert alert-success alert-dismissable" style="clear: both;">
                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                        <strong>Success! </strong>{!! Session::get('success_msg') !!}.
                    </div>
                @endif

                <ul class="nav nav-tabs">
                    <li class="active"><a data-toggle="tab" href="#active-survey">Active Survey</a></li>
                    <li><a data-toggle="tab" href="#closed-survey">Closed Survey</a></li>
                </ul>


                <div class="tab-content">

                    <div id="active-survey" class="tab-pane fade in active">
                        <table id="active_survey"
                            class="table table-striped table-bordered dt-responsive nowrap survey-table" width="100%"
                            cellspacing="0">
                            <thead>
                                <th class="text-center"> </th>
                                <th class="text-center">Survey Name</th>
                                <th class="text-center">Client Name</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Questions</th>
                                <th class="text-center">Design</th>
                                <th class="text-center">Manage Participant <br />& Respondent</th>
                                <th class="text-center">Share</th>
                                <th class="text-center">Reports</th>
                            </thead>
                            <tbody>
                                <?php $count = 1; ?>
                                @if (count($active_survey_details) > 0)
                                    @foreach ($active_survey_details as $survey)
                                        <tr>
                                            <td class="text-center">
                                                <input type="radio" name="survey" id="" value="{{ $survey->id }}">
                                            </td>
                                            <td class="text-left">
                                                <div>{{ $survey->title }}</div>
                                                <div class="text-center">
                                                    <a href="{{ route('survey.edit', [$survey->id]) }}"
                                                        data-toggle="tooltip" title="Edit Survey details"
                                                        class="btn-link"><i class="fa fa-edit"></i></a>
                                                </div>

                                            </td>
                                            <td>{{ $survey->client_name }}</td>
                                            <td class="text-center">
                                                <img src="{{ asset('images/calendar-icon.png') }}" alt="Calendar Icon"> {{ date('d/m/Y', strtotime($survey->start_date)) }}
                                                <img src="{{ asset('images/time-icon.png') }}" alt="Time Icon"> {{ date('H:i:A', strtotime($survey->start_date)) }}
                                                <br>
                                                <img src="{{ asset('images/calendar-icon.png') }}" alt="Calendar Icon"> {{ date('d/m/Y', strtotime($survey->end_date)) }}
                                                <img src="{{ asset('images/time-icon.png') }}" alt="Time Icon"> {{ date('H:i:A', strtotime($survey->end_date)) }}
                                            </td>

                                            <td class="text-center">
                                                <a href="{{ route('questions.show', [$survey->id]) }}" class="btn btn-link">
                                                    <img src="{{ asset('images/manage-icon.png') }}" alt="Manage Icon"> Manage
                                                </a>
                                                <br>
                                                <a href="{{ route('ques.ques_export', ['survey_id=' . $survey->id, 'survey_name=' . $survey->title]) }}" class="btn btn-link">
                                                    <span class="fa fa-question-circle" style="color:#2041BD"></span> Download Question
                                                </a>
                                                <br>
                                                <a href="{{ route('questions_group', ['survey_id=' . $survey->id]) }}" class="btn btn-link">
                                                    <img src="{{ asset('images/manage-icon.png') }}" alt="Manage Icon"> Question Group
                                                </a>
                                            </td>

                                            <td class="text-center">
                                                <a href="{{ route('theme.show', [$survey->id]) }}" class="btn btn-link">
                                                    <img src="{{ asset('images/manage-icon.png') }}" alt="Manage Icon"> Manage
                                                </a>
                                            </td>

                                            <td class="text-center">
                                                <a href="{{ route('addusers.show', [$survey->id]) }}" class="btn btn-link">
                                                    <img src="{{ asset('images/manage-icon.png') }}" alt="Manage Icon"> Manage
                                                </a>
                                            </td>

                                            <td class="text-left">

                                                <a href="{{ route('distribute.show', [$survey->id]) }}" class="btn btn-link">
                                                    <img src="{{ asset('images/mail-icon.png') }}" alt="Mail Icon"> Email
                                                </a>
                                                <br>
                                                <div id="text{{ $count }}" style="display:none"><?php echo url('/' . $survey->url) . '/login'; ?>
                                                </div>

                                                <a class="btn btn-link" href="javascript: void(0)"
                                                    onclick="copyToClipboard('#text{{ $count }}')"
                                                    data-toggle="tooltip" title="Copy Link"><span
                                                        class="glyphicon glyphicon-copy"></span> Copy link</a>
                                                <br>

                                                <div id="survey_link{{ $count }}" style="display:none">
                                                    <?php echo route('status.report', 'key=' . encrypt($survey->id)); ?></div>
                                                <a href="javascript:void(0)" class="btn btn-link" id="copy_survey_status"
                                                    onclick="copySurveyLink('#survey_link{{ $count }}')">
                                                    <i class="glyphicon glyphicon-copy"></i>
                                                    Copy survey status link
                                                </a>
                                                <br>
                                                <div class="survey_summary_link{{$survey->id }}" style="display:none">
                                                    <?php echo route('summary.report', 'key=' . base64_encode($survey->id)); ?></div>
                                                <a href="javascript:void(0)" class="btn btn-link" id="copy_survey_status"
                                                    onclick="copySurveySummaryLink('.survey_summary_link{{ $survey->id }}')">
                                                    <i class="glyphicon glyphicon-copy"></i>
                                                    Copy summary status link
                                                </a>
                                            </td>
                                            <td class="text-left">
                                                <a href="{{ route('status.rawscore', ['survey_id=' . $survey->id]) }}" class="btn btn-link">
                                                    <span class="glyphicon glyphicon-align-justify" style="color:#2041BD"></span> Raw score
                                                </a>
                                                <br>
                                                <a href="{{ route('text.text_response', ['survey_id=' . $survey->id]) }}" class="btn btn-link">
                                                    <span class="glyphicon glyphicon-text-width" style="color:#2041BD"></span> Text Response
                                                </a>
                                                <br>
                                                <a href="{{ route('status.status_report', ['survey_id=' . $survey->id]) }}" class="btn btn-link">
                                                    <span class="glyphicon glyphicon-export" style="color:#2041BD"></span> Status Report
                                                </a>
                                                <br>
                                                <a href="{{ route('participantreport.show', [$survey->id]) }}" class="btn btn-link">
                                                    <span class="glyphicon glyphicon-user" style="color:#2041BD"></span> Participant Reports
                                                </a>

                                                {{-- <br>
<a href="{{ url('status_summary/'.$survey->id) }}" class="btn btn-link">
	<span class="glyphicon glyphicon-alert" style="color:#2041BD"></span> &nbsp;Status Summary
</a> --}}
                                                <br>
                                                <a href="{{ route('users-password.index', ['survey_id' => $survey->id]) }}"
                                                    class="btn btn-link">
                                                    <span class="fa fa-key" style="color:#2041BD"></span>
                                                    Users Password
                                                </a>


                                                <br>
                                                <a href="{{ route('status.status_summary', ['survey_id' => $survey->id]) }}"
                                                    class="btn btn-link">
                                                    <span class="fa fa-list-alt" style="color:#2041BD"></span>
                                                    Status Summary
                                                </a>


                                            </td>
                                        </tr>
                                        <?php $count++; ?>
                                    @endforeach

                                @else

                                    <tr>
                                        <td colspan="8" class="text-center">No Survey Found!</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>


                    <div id="closed-survey" class="tab-pane fade">
                        <table id="closed_survey"
                            class="table table-striped table-bordered dt-responsive nowrap survey-table" width="100%"
                            cellspacing="0">
                            <thead>
                                <th class="text-center"> </th>
                                <th class="text-center">Survey Name</th>
                                <th class="text-center">Client Name</th>
                                <th class="text-center">Date</th>
                                <th class="text-center">Questions</th>
                                <th class="text-center">Design</th>
                                <th class="text-center">Manage Participant <br />& Respondent</th>
                                <th class="text-center">Share</th>
                                <th class="text-center">Reports</th>
                            </thead>
                            <tbody>
                                <?php $count = 1; ?>
                                @if (count($closed_survey_details) > 0)
                                    @foreach ($closed_survey_details as $survey)
                                        <tr>
                                            <td class="text-center">
                                                <input type="radio" name="survey" value="$survey->id">
                                            </td>
                                            <td class="text-left">
                                                <div>{{ $survey->title }}</div>
                                                <div class="text-center"><a
                                                        href="{{ route('survey.edit', [$survey->id]) }}"
                                                        data-toggle="tooltip" title="Edit Survey details"
                                                        class="btn-link"><span class="fa fa-edit"></span></a>
                                                </div>

                                            </td>
                                            <td>{{ $survey->client_name }}</td>
                                            <td class="text-center">
                                                <img src="{{ asset('images/calendar-icon.png') }}" alt="Calendar Icon"> {{ date('d/m/Y', strtotime($survey->start_date)) }}
                                                <img src="{{ asset('images/time-icon.png') }}" alt="Time Icon"> {{ date('H:i:A', strtotime($survey->start_date)) }}
                                                <br>
                                                <img src="{{ asset('images/calendar-icon.png') }}" alt="Calendar Icon"> {{ date('d/m/Y', strtotime($survey->end_date)) }}
                                                <img src="{{ asset('images/time-icon.png') }}" alt="Time Icon"> {{ date('H:i:A', strtotime($survey->end_date)) }}
                                            </td>

                                            <td class="text-center">
                                                <a href="{{ route('questions.show', [$survey->id]) }}" class="btn btn-link">
                                                    <img src="{{ asset('images/manage-icon.png') }}" alt="Manage Icon"> Manage
                                                </a>
                                                <br>
                                                <a href="{{ route('ques.ques_export', ['survey_id=' . $survey->id, 'survey_name=' . $survey->title]) }}">
                                                    <span class="fa fa-question-circle" style="color:#2041BD"></span> Download Question
                                                </a>
                                            </td>

                                            <td class="text-center">
                                                <a href="{{ route('theme.show', [$survey->id]) }}" class="btn btn-link">
                                                    <img src="{{ asset('images/manage-icon.png') }}" alt="Manage Icon"> Manage
                                                </a>
                                            </td>

                                            <td class="text-center">
                                                <a href="{{ route('addusers.show', [$survey->id]) }}" class="btn btn-link">
                                                    <img src="{{ asset('images/manage-icon.png') }}" alt="Manage Icon"> Manage
                                                </a>
                                            </td>

                                            <td class="text-left">

                                                <a href="{{ route('distribute.show', [$survey->id]) }}" class="btn btn-link">
                                                    <img src="{{ asset('images/mail-icon.png') }}" alt="Mail Icon"> Email
                                                </a>
                                                <br>

                                                <div id="texta{{ $count }}" style="display:none">
                                                    <?php echo url('/' . $survey->url) . '/login'; ?></div>
                                                <a class="btn btn-link" href="javascript: void(0)"
                                                    onclick="copyToClipboard('#texta{{ $count }}')"><span
                                                        class="glyphicon glyphicon-copy"></span> Copy link</a>
                                                <br>
                                                <div class="survey_summary_link{{ $survey->id }}" style="display:none">
                                                    <?php echo route('summary.report', 'key=' . base64_encode($survey->id)); ?></div>
                                                <a href="javascript:void(0)" class="btn btn-link" id="copy_survey_status"
                                                    onclick="copySurveySummaryLink('.survey_summary_link{{ $count }}')">
                                                    <i class="glyphicon glyphicon-copy"></i>
                                                    Copy survey status link
                                                </a>


                                            </td>
                                            <td class="text-left">
                                                <a href="{{ route('status.rawscore', ['survey_id=' . $survey->id]) }}" class="btn btn-link">
                                                    <span class="glyphicon glyphicon-align-justify" style="color:#2041BD"></span> Raw score
                                                </a>
                                                <br>
                                                <a href="{{ route('text.text_response', ['survey_id=' . $survey->id]) }}" class="btn btn-link">
                                                    <span class="glyphicon glyphicon-text-width" style="color:#2041BD"></span> Text Response
                                                </a>
                                                <br>
                                                <a href="{{ route('status.status_report', ['survey_id=' . $survey->id]) }}" class="btn btn-link">
                                                    <span class="glyphicon glyphicon-export" style="color:#2041BD"></span> Status Report
                                                </a>
                                                <br>
                                                <a href="{{ route('participantreport.show', [$survey->id]) }}" class="btn btn-link">
                                                    <span class="glyphicon glyphicon-user" style="color:#2041BD"></span> Participant Reports
                                                </a>

                                                {{-- <br>
<a href="{{ url('status_summary/'.$survey->id) }}" class="btn btn-link">
	<span class="glyphicon glyphicon-alert" style="color:#2041BD"></span> &nbsp;Status Summary
</a> --}}
                                                <br>
                                                <a href="{{ route('users-password.index', ['survey_id' => $survey->id]) }}"
                                                    class="btn btn-link">
                                                    <span class="fa fa-key" style="color:#2041BD"></span>
                                                    Users Password
                                                </a>

                                                <br>
                                                <a href="{{ route('status.status_summary', ['survey_id' => $survey->id]) }}"
                                                    class="btn btn-link">
                                                    <span class="fa fa-list-alt" style="color:#2041BD"></span>
                                                    Status Summary
                                                </a>

                                            </td>
                                        </tr>
                                        <?php $count++; ?>
                                    @endforeach

                                @else

                                    <tr>
                                        <td colspan="8" class="text-center">No Survey Found!</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>


                </div>


                <link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">
                <script src="{{ asset('script/sweetalert.min.js') }}"></script>

                <script>
                    function copyToClipboard(element) {
                        var $temp = $("<input>");
                        $("body").append($temp);
                        $temp.val($(element).text()).select();
                        document.execCommand("copy");
                        $temp.remove();
                        swal("Link Copied!");
                    }

                    function copySurveyLink(data) {
                        var $temp = $("<input>");
                        $("body").append($temp);
                        $temp.val($(data).text()).select();
                        document.execCommand("copy");
                        $temp.remove();
                        swal("Link Copied!");
                    }
                    function copySurveySummaryLink(data) {
                        var $temp = $("<input>");
                        $("body").append($temp);
                        $temp.val($(data).text()).select();
                        document.execCommand("copy");
                        $temp.remove();
                        swal("Link Copied!");
                    }
                </script>
                <style media="screen">
                    .glyphicon-copy {
                        color: #2041bd;
                    }

                    .nav>li.active a,
                    .nav>li.active a:hover,
                    .nav>li.active a:focus {
                        background-color: #286090;
                        border-color: #286090;
                        color: #ffffff;
                    }

                </style>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        $(document).ready(function() {
            $('[data-toggle="tooltip"]').tooltip();
        });
    </script>

<link rel="stylesheet" href="{{ asset('css/dataTable/jquery.dataTables.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/dataTable/dataTables.bootstrap4.min.css') }}">
<script src="{{ asset('script/dataTable/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            $('.delete-survey').click(function(event) {

                var survey_id = $('input[name=survey]:checked').val();
                var href_attr = $(this).attr('attr-href');

                if (survey_id == undefined) {
                    swal("Oops...", "Please Select Survey", "warning");
                } else {
                    swal({

                            title: "Are you sure want to delete this survey?",

                            text: "You will not be able to recover this item",

                            type: "warning",

                            showCancelButton: true,

                            confirmButtonClass: "btn-danger",

                            confirmButtonText: "Confirm",

                            cancelButtonText: "Cancel",

                            closeOnConfirm: false,

                            closeOnCancel: false

                        },

                        function(isConfirm) {

                            if (isConfirm) {
                                window.location = href_attr + '?survey_id=' + survey_id;

                            } else {

                                swal("Cancelled", "You Cancelled", "error");

                            }

                        });

                }

            });



            $('#active_survey').DataTable({
                "bSort": false
            });

            $('#closed_survey').DataTable({
                "bSort": false
            });

        });
    </script>

    <style>
        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: white;
            border: 1px solid white;
            color: white !important;
        }

        table.dataTable.no-footer {
            border-bottom: 2px solid #dddddd;
        }

        table.dataTable thead th,
        table.dataTable thead td {
            border-bottom: 2px solid #dddddd;
            padding: 10px 18px;
        }

        th {
            color: white;
            background: #2041BD;
        }

        .pagination>li>a:focus,
        .pagination>li>a:hover,
        .pagination>li>span:focus,
        .pagination>li>span:hover {

            border-color: white !important;

        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:active {
            box-shadow: none;
            outline: none;
        }

        .alert-dismissable {
            margin: 50px 0 20px;
        }

        .alert-success {
            line-height: 1.6;
        }

    </style>
@endsection
