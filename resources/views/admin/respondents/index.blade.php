@extends('layouts.default')

@section('content')

    <div class="container">
        <div class="row setup-content" id="step-4">

            <div class="col-xs-12">
                <div class="form-wrapper">
                    <div class="form-steps-wizard step4"> </div>

                    <?php $participant_fname = DB::table('users')
                        ->where('id', $participant_id)
                        ->value('fname'); ?>
                    <?php $participant_lname = DB::table('users')
                        ->where('id', $participant_id)
                        ->value('lname'); ?>
                    <div class="col-md-12 well">
                        <h3 class="need-margin-bottom-forstrip text-center">Add Respondents to Survey</h3>


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
                        <form action="{{ route('delete.respondent') }}" method="POST" id="users_form">

                            <input type="hidden" name="_token" value="{{ csrf_token() }}">
                            <div class="pull-right" style="float: left!important;">
                                <a href="javascript:void(0)" title="Delete Respondents" data-toggle="tooltip"
                                    class="btn btn-danger" id="delete_action" data-action="{{ route('delete.users') }}">
                                    <i class="fa fa-trash"></i>&nbsp;Delete Respondents
                                </a>
                                &nbsp;
                            </div>
                        <div class="pull-right" style="margin-bottom: 15px;">
                            <a href="{{ route('respondent.create', ['participant_id' => $participant_id, 'survey_id' => $survey_id]) }}"
                                data-toggle="tooltip" title="Add New" class="btn btn-success"><i class="fa fa-plus"
                                    aria-hidden="true"></i>&nbsp;Add New</a>
                        </div>
                        @if (Session::has('msg'))
                            @if (Session::get('mess_data') == 'success')
                                <div class="alert alert-success alert-dismissable" style="clear: both;">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <strong>Success!</strong> {!! Session::has('msg') ? Session::get('msg') : '' !!}
                                </div>
                            @elseif(Session::get('mess_data') == 'error')
                                <div class="alert alert-danger alert-dismissable" style="clear: both;">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <strong>Error!</strong>
                                    <ul class="list-unstyled">
                                        @foreach (Session::get('msg') as $error)
                                            <li>{{ $error }} </li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif

                        @endif
                                <div class="clearfix"></div>
                        @if (Session::has('mailstatus'))
                            <?php
                            $mailstatus = Session::get('mailstatus'); ?>
                            @if (count($mailstatus) == 0)
                                <div class="alert alert-success alert-dismissable" style="clear: both;">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <strong>Success!</strong> Mail Sent successfully.
                                </div>
                            @else
                                <div class="alert alert-danger alert-dismissable" style="clear: both;">
                                    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                    <strong>Oops!</strong> Mail Not Sent.
                                </div>
                            @endif
                        @endif


                        @if (Session::has('reopen_survey_message'))

                            <div class="alert alert-success alert-dismissable" style="clear: both;">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <strong>Survey Reopened successfully!</strong>
                            </div>
                        @endif
                        @if (Session::has('clear_response_message'))

                            <div class="alert alert-success alert-dismissable" style="clear: both;">
                                <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                <strong>Response cleared successfully!</strong>
                            </div>

                        @endif

                        <span class="participant_name">Participant Name:<b> {{ $participant_fname }}
                                {{ $participant_lname }}</b></span>
                        <table id="stable" class="table table-striped table-bordered dt-responsive nowrap survey-table"
                            width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selete_all"></th>
                                    <th>S.No</th>
                                    <th>First Name</th>
                                    <th>Last Name</th>
                                    <th>Email</th>
                                    <th>Status</th>
                                    <th>Respondent Type</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>

                                @if (count($data) != 0)
                                    <?php $s_no = 1; ?>
                                    @foreach ($data as $key => $result)
                                        <tr>
                                            <td>
                                                @if ($result->survey_status == '0' || $result->survey_status == '1')
                                                    <input type="checkbox" name="userids[]" class="user_ids"
                                                        value="{{ $result->id.'~'.$participant_id }}">
                                                    <input type="hidden" name="survey_id" value="{{ $survey_id }}">
                                                @else
                                                    -
                                                @endif

                                            </td>
                                            <td>{{ $key + 1 }}</td>
                                            <td>{{ $result->fname }}</td>
                                            <td>{{ $result->lname }}</td>
                                            <td>{{ $result->email }}</td>
                                            <td>
                                                <?php
                                                if ($result->survey_status == '0') {
                                                    echo '<span class="alert-danger">Closed</span>';
                                                } elseif ($result->survey_status == '1') {
                                                    echo '<span class="alert-info">Active</span>';
                                                } elseif ($result->survey_status == '2') {
                                                    echo '<span class="alert-warning">Partly Completed</span>';
                                                } elseif ($result->survey_status == '3') {
                                                    echo '<span class="alert-success">Completed</span>';
                                                } elseif ($result->survey_status == '4') {
                                                    echo '<span class="alert-danger">In-Active</span>';
                                                }
                                                ?>
                                            </td>

                                            <td>{{ $result->rater }}</td>
                                            <td width="20%" class="icon">

                                                <a href="{{ route('respondent.edit', [$result->id, 'survey_id' => $survey_id, 'participant_id' => $participant_id]) }}"
                                                    data-toggle="tooltip" title="Edit respondent details"
                                                    class="btn btn-info"><span class="fa fa-edit"></span></a>

                                                @if ($result->notify_email_date != null)
                                                    <a href="{{ route('resend.resendacess', ['respondent_id' => $result->id, 'survey_id' => $survey_id, 'participant_id' => $participant_id]) }}"
                                                        data-toggle="tooltip" title="Resend Access"
                                                        class="btn btn-info"><span class="fa fa-refresh"></span></a>
                                                @endif

                                                <?php $count_responses = DB::table('responses')
                                                    ->where('user_survey_respondent_id', $result->user_survey_respondent_id)
                                                    ->count(); ?>

                                                @if ($result->survey_status == '3')
                                                        <a href="javascript:void(0)"  class="reopen-survey btn btn-info" data-href="{{ route('respondent.reopen_survey',['survey_id'=>$survey_id,'respondent_id'=>$result->id,'participant_id'=>$participant_id]) }}" data-toggle="tooltip" title="Reopen Survey">
                                                            <span  class="fa fa-repeat"></span>
                                                        </a>
                                                @endif

                                                @if ($count_responses > 0)
                                                    <a href="javascript:void(0)"  class="clear-response btn btn-info" data-href="{{ route('respondent.clear_response',['survey_id'=>$survey_id,'respondent_id'=>$result->id,'participant_id'=>$participant_id]) }}" data-toggle="tooltip" title="Clear Response"><span class="fa fa-remove"></span></a>
                                                @endif
                                                <?php

                                                $count = DB::table('responses')
                                                    ->where('user_survey_respondent_id', $result->user_survey_respondent_id)
                                                    ->count();

                                                ?>


                                                @if ($count > 0)
                                                        <?php $check_response_data="true"; ?>
                                                @else
                                                        <?php $check_response_data="false"; ?>
                                                @endif
                                                @if ($count > 0)
                                                    {{ Form::hidden('check_response_data', 'true', ['class' => 'check_response_data']) }}
                                                @else
                                                    {{ Form::hidden('check_response_data', 'false', ['class' => 'check_response_data']) }}
                                                @endif
                                                <a class="delete-user-survey btn btn-danger" href="javascript:void(0)" data-toggle="tooltip" title="Delete" data-href="{{ route('single.delete.respondent',['survey_id'=>$survey_id,'respondent_id'=>$result->id,'participant_id'=>$participant_id,'check_response_data'=>$check_response_data]) }}">
                                                    <span class="fa fa-trash-o"></span>
                                                </a>


                                            </td>

                                        </tr>
                                        <?php $s_no++; ?>
                                    @endforeach
                                @else
                                    <tr>
                                        <td colspan="7" class="text-center">No Results Found</td>
                                    </tr>
                                @endif
                            </tbody>
                        </table>
                    </form>
                        <div class="text-center col-sm-12" style="margin-top: 20px;">

                            {{ Form::hidden('survey_id', $survey_id) }}
                            {{ Form::hidden('participant_id', $participant_id) }}

                            <a href="{{ route('addusers.show',[$survey_id]) }}" class="btn btn-danger btn-md">Cancel</a>

                            <button id="activate-step-5" data-migrate="{{ URL::route('distribute.show', $survey_id) }}"
                                type="button" class="btn btn-success btn-md">Save & Next</button>

                        </div>

                    </div>
                </div>
            </div>
        </div>


    </div>

    {!! HTML::script('script/dataTable/jquery.dataTables.min.js') !!}
    {!! HTML::style('css/dataTable/jquery.dataTables.min.css') !!}
    {{ HTML::script('script/sweetalert.min.js') }}
    {{ HTML::style('css/sweetalert.css') }}


    {!! HTML::script('js/dataTables.bootstrap4.min.js') !!}
    {!! HTML::style('css/dataTable/dataTables.bootstrap4.min.css') !!}

    <script type="text/javascript">
            /*Select User Checkbox Actions */
            $('#selete_all').change(function() {
            if ($(this).prop("checked") == true) {
                $('input:checkbox').prop('checked', true);
            } else {
                $('input:checkbox').prop('checked', false);
            }
        });

        $(document).on('change', '.user_ids', function(event) {
            var checkedNum = $('input[name="userids[]"]:checked').length;
            var total_users = $('select[name="stable_length"]').val();
            if (checkedNum == total_users) {
                $('#selete_all').prop('checked', true);
            } else {
                $('#selete_all').prop('checked', false);
            }
        });
        $(document).on('click', '#delete_action', function(event) {
            var checkedNum = $('input[name="userids[]"]:checked').length;
            if (checkedNum != 0) {
                swal({
                        title: "Are you sure want to delete users?",
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
                            var arr = [];
                            $('input[name="userids[]"]:checked').each(function() {
                                arr.push($(this).val());
                            });
                            $.ajax({
                                type: "POST",
                                url: "{{ route('delete.respondent') }}",
                                data: {
                                    survey_id: "{{ $survey_id }}",
                                    userids: arr,
                                    _token: "{{ csrf_token() }}"
                                },
                                success: function(response) {
                                    location.reload();
                                }
                            });
                        } else {
                            swal("Cancelled", "You Cancelled", "error");
                        }
                    });
            } else {
                swal("Oops...", "Please Select Users", "warning");
            }
        });
        $(document).ready(function() {

            $('.clear-response').click(function() {
                $obj = $(this).data('href');
                swal({
                        title: "Are you sure?",
                        text: "Do u want to clear the response!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-danger",
                        confirmButtonText: "Yes, clear it!",
                        closeOnConfirm: false
                    },
                    function(isConfirm) {
                        if (isConfirm) {
                            window.location=$obj;
                        }
                    });

            });
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.reopen-survey').click(function() {
                $obj = $(this).data('href');
                swal({
                        title: "Are you sure?",
                        text: "Do u want to reopen the survey!",
                        type: "warning",
                        showCancelButton: true,
                        confirmButtonClass: "btn-success",
                        confirmButtonText: "Yes, reopen it!",
                        closeOnConfirm: false
                    },
                    function(isConfirm) {
                        if (isConfirm) {
                            window.location=$obj;
                        }
                    });
            });
        });
    </script>
    <script>
        $('#activate-step-5').on('click', function() {

            var user_exists = "{{ count($data) }}";

            if (user_exists > 0) {
                window.location = $(this).attr('data-migrate');
            } else {
                swal("Please Look!", "Add Respondents to the survey", "warning")

            }
        });
    </script>

    <script type="text/javascript">
        $('.delete-user-survey').click(function() {
            var res = $(this).prevAll('.check_response_data').val();
            if (res != "true") {
                var text = "Your will not be able to recover this imaginary file!";
                var title = "Are you sure?";
            } else {
                var class1 = "hidden";
                var text = "Respondent already respondend and hence you are not allowed to delete the respondent.";
                var title = "";
            }
            $obj = $(this).data('href');
            swal({
                    title: title,
                    text: text,
                    // type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger " + class1,
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false
                },
                function(isConfirm) {
                    if (isConfirm == true) {
                        window.location= $obj;
                    }
                });

        });

        $(document).ready(function() {
            $('#stable').DataTable({
                "bSort": false
            });



        });
    </script>
    <script type="text/javascript">
        $('[data-toggle="tooltip"]').tooltip();
    </script>
    <style type="text/css">
        table.survey-table {
            float: inherit;
        }

        table.dataTable.no-footer {
            border-bottom: none;
        }

    </style>
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
            padding: 10px 10px;
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

        .participant_name {
            font-size: 18px;
            line-height: 3.5;
            text-transform: capitalize;
        }

    </style>
@endsection
