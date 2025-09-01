@extends('layouts.default')

@section('content')

    <div class="container">
        <div class="row setup-content" id="step-5">

            <div class="col-xs-12">
                <div class="form-wrapper">
                    <div class="form-steps-wizard step5"> </div>


                    <div class="col-md-12 well">
                        <h3 class="need-margin-bottom-forstrip text-center">Add Participants to Survey</h3>


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

                        <form action="{{ route('delete.users') }}" method="POST" id="users_form">

                            <input type="hidden" name="_token" value="{{ csrf_token() }}">

                            <div class="pull-right" style="margin-bottom: 15px;">
                                <a href="{{ route('respondent.only_importrespondent', ['survey_id' => $survey_id]) }}"
                                    data-toggle="tooltip" title="Import Respondents" class="btn btn-success"><i
                                        class="fa fa-plus" aria-hidden="true"></i>&nbsp;Import Respondents</a>
                            </div>

                            <div class="pull-right" style="margin-bottom: 15px; margin-right: 15px;">
                                <a href="{{ route('addusers.create', ['survey_id' => $survey_id]) }}"
                                    data-toggle="tooltip" title="Add / Import Participants " class="btn btn-success"><i
                                        class="fa fa-plus" aria-hidden="true"></i>&nbsp;Add / Import Participants</a>
                            </div>
                            <div class="pull-right" style="float: left!important;">
                                <a href="javascript:void(0)" title="Delete Respondents" data-toggle="tooltip"
                                    class="btn btn-danger" id="delete_action" data-action="{{ route('delete.users') }}">
                                    <i class="fa fa-trash"></i>&nbsp;Delete Participants
                                </a>
                                &nbsp;
                            </div>
                            @if (Session::has('msg'))
                                @if (Session::get('mess_data') == 'success')
                                    <div class="alert alert-success alert-dismissable" style="clear: both;">
                                        <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
                                        <strong>Success!</strong> {!! Session::has('msg') ? Session::get('msg') : '' !!}
                                        <div class="clearfix"></div>
                                        @if (Session::has('updated_users'))
                                            <?php $updated_users_count = count(Session::get('updated_users')); ?>
                                            <?php $updated_users = Session::get('updated_users'); ?>
                                            @if ($updated_users_count > 0)
                                                Existing and updated partipants: ({{ $updated_users_count }})
                                                <ul>
                                                    @foreach ($updated_users as $user)
                                                        <li> {{ $user }}</li>
                                                    @endforeach
                                                </ul>
                                            @endif
                                        @endif
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

                            <table id="stable"
                                class="table table-striped table-bordered dt-responsive nowrap survey-table" width="100%"
                                cellspacing="0">
                                <thead>
                                    <tr>
                                        <th><input type="checkbox" id="selete_all"></th>
                                        <th>S.No</th>
                                        <th>First Name</th>
                                        <th>Last Name</th>
                                        <th>Email</th>
                                        <th>Status</th>
                                        <th>Respondents Count</th>
                                        <th>Respondents</th>
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
                                                            value="{{ $result->id }}">
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

                                                <td>
                                                    <?php
                                                    $respondents_count = DB::table('user_survey_respondent')
                                                        ->where('user_survey_respondent.respondent_id', '<>', 0)
                                                        ->where('user_survey_respondent.participant_id', $result->participant_id)
                                                        ->where('user_survey_respondent.survey_id', $result->survey_id)
                                                        ->count();
                                                    ?>
                                                    {{ $respondents_count }}
                                                </td>
                                                <td>
                                                    <a
                                                        href="{{ route('respondent.show', [$result->participant_id, 'survey_id' => $survey_id]) }}"><span
                                                            class="glyphicon glyphicon-user"
                                                            style="color:#337ab7"></span><span
                                                            class="glyphicon glyphicon-user"
                                                            style="color:#337ab7"></span><span style="word-spacing: 0px">
                                                            Manage</span></a>

                                                    <!--  {!! html_entity_decode(link_to_route('respondent.show', ' <span class="glyphicon glyphicon-user  " style="color:#2041BD"></span><span class="glyphicon glyphicon-user  " style="color:#2041BD"></span> Respondents/Rater', ['' . $result->id])) !!}  -->
                                                </td>
                                                <td width="20%">

                                                    <a href="{{ route('addusers.edit', [$result->id, 'survey_id' => $survey_id]) }}"
                                                        data-toggle="tooltip" title="Edit participant details"
                                                        class="btn btn-info"><span class="fa fa-edit"></span></a>

                                                    @if ($result->notify_email_date != null)
                                                        <a href="{{ route('resend.resendacess', ['participant_id' => $result->id, 'survey_id' => $survey_id, 'respondent_id' => $result->respondent_id]) }}"
                                                            data-toggle="tooltip" title="Resend Access"
                                                            class="btn btn-info"><span class="fa fa-refresh"></span></a>
                                                    @endif
                                                    <?php $count_responses = DB::table('responses')
                                                        ->where('user_survey_respondent_id', $result->user_survey_respondent_id)
                                                        ->count(); ?>
                                                    <form class="form-horizontal form-reopen-survey" role="form" method="GET"
                                                        action="{{ action('AddusersController@Reopen_survey') }}">
                                                        {{ csrf_field() }}

                                                        <input type="hidden" name="survey_id" value="<?= $survey_id ?>">
                                                        <input type="hidden" name="participant_id"
                                                            value="<?= $result->id ?>">
                                                        @if ($count_responses > 0)
                                                        <a  class="reopen-survey btn btn-info" data-toggle="tooltip" title="Reopen Survey" href="javascript::void(0)" att-href="{{ route('addusers.Reopen_survey',['survey_id'=>$survey_id,'participant_id'=>$result->id]) }}">
                                                            <span class="fa fa-repeat"></span>
                                                        </a>
                                                            {{-- <button class="reopen-survey btn btn-info" type="button"
                                                                data-toggle="tooltip" title="Reopen Survey"><span
                                                                    class="fa fa-repeat"></span></button> --}}
                                                        @endif
                                                    </form>

                                                    {{-- <form class="form-horizontal" role="form" method="GET"
                                                        action="{{ action('AddusersController@Clear_response') }}">
                                                        {{ csrf_field() }}

                                                        <input type="hidden" name="survey_id" value="<?= $survey_id ?>">
                                                        <input type="hidden" name="participant_id"
                                                            value="<?= $result->id ?>">
                                                        @if ($count_responses > 0)
                                                            <button class="clear-response btn btn-info" type="button"
                                                                data-toggle="tooltip" title="Clear Response"><span
                                                                    class="fa fa-remove"></span></button>
                                                        @endif
                                                    </form> --}}
                                                    @if ($count_responses > 0)
                                                        <a href="javascript:void(0)"  class="clear-response btn btn-info" attr-href="{{ route('addusers.Clear_response',['survey_id'=>$survey_id,'participant_id'=>$result->id]) }}"><span class="fa fa-remove"></span></a>
                                                    @endif

                                                    <?php

                                                    $count = DB::table('responses')
                                                        ->where('user_survey_respondent_id', $result->user_survey_respondent_id)
                                                        ->count();
                                                    $count_respondent = DB::table('user_survey_respondent')
                                                        ->where('participant_id', $result->participant_id)
                                                        ->where('survey_id', $result->survey_id)
                                                        ->where('respondent_id', '<>', '0')
                                                        ->count();

                                                    ?>
                                                    {!! Form::open(['method' => 'DELETE', 'route' => ['addusers.destroy', $result->id], 'class' => 'del_form']) !!}
                                                    {{ Form::hidden('survey_id', $survey_id) }}
                                                    @if ($count > 0 || $count_respondent > 0)
                                                        {{ Form::hidden('check_response_data', 'true', ['class' => 'check_response_data']) }}
                                                    @else
                                                        {{ Form::hidden('check_response_data', 'false', ['class' => 'check_response_data']) }}
                                                    @endif

                                                    <button class="delete-user-survey btn btn-danger" type="button"
                                                        data-toggle="tooltip" title="Delete"><span
                                                            class="fa fa-trash-o"></span></button>
                                                    {!! Form::close() !!}

                                                </td>

                                            </tr>
                                            <?php $s_no++; ?>
                                        @endforeach
                                    @else
                                        <tr>
                                            <td colspan="8" class="text-center">No Results Found</td>
                                        </tr>
                                    @endif
                                </tbody>
                            </table>
                        </form>
                        <div class="text-center col-sm-12" style="margin-top: 20px;">

                            {{ Form::hidden('survey_id', $survey_id) }}

                            <?php
                            $back_url = URL::route('theme.show', $survey_id);

                            ?>
                            <a href="{{ $back_url }}" class="btn btn-danger btn-md">Cancel</a>

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

        /*Select User Checkbox Actions */
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
                                url: "{{ route('delete.users') }}",
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
                $obj = $(this).attr('attr-href');
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
    <script>
        $('#activate-step-5').on('click', function() {

            var user_exists = "{{ count($data) }}";

            if (user_exists > 0) {
                window.location = $(this).attr('data-migrate');
            } else {
                swal("Please Look!", "Add Participants to the survey", "warning")

            }
        });
    </script>
    <script type="text/javascript">
        $(document).ready(function() {

            $('.reopen-survey').click(function() {
                $obj=$(this).attr('att-href');
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
                            window.location.href = $obj;
                        }
                    });

            });
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
                var text =
                    "Participant already respondend and hence you are not allowed to delete the participant.";
                var title = "";
            }
            $obj = $(this).closest('form');
            swal({
                    title: title,
                    text: text,
                    //  type: "warning",
                    showCancelButton: true,
                    confirmButtonClass: "btn-danger " + class1,
                    confirmButtonText: "Yes, delete it!",
                    closeOnConfirm: false
                },
                function(isConfirm) {

                    if (isConfirm == true) {
                        $obj.submit();
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
    </style>
@endsection
