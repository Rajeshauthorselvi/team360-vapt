@extends('layouts.default')

@section('content')

    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row text-center">
                    <h4><span><b>Survey Name :</b> </span><strong>{{ $survey_name }}</strong></h4>
                </div>
            </div>
            <div class="panel-body">

                @if (count($survey_details) > 0)
                    <form method="POST" action="{{ route('export.status_report', ['survey_id' => $survey_id, 'survey_name' => $survey_name]) }}" class="form-horizontal">
                        @csrf

                        <div class="pull-right">
                            <button type="submit" name="button" class="btn btn-primary">
                                <span class="glyphicon glyphicon-download-alt"></span> Download
                                <span class="fa fa-file-excel-o"></span>
                            </button>
                        </div>
                    </form>
                    <br>
                    <br>
                    <br>
                @endif

                <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="survey_status">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Participant Name</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Respondent Type</th>
                            <th>Status</th>
                            <th>Email sent date</th>
                            <th>Respondent submitted date</th>

                        </tr>

                    </thead>
                    <tbody>
                        <?php $s_no = 1; ?>
                        @if (count($survey_details) > 0)
                            @foreach ($survey_details as $user_details)
                                <?php if ($user_details->rater == 'self') {
                                    $class = 'self';
                                } else {
                                    $class = 'no_self';
                                }

                                ?>

                                <tr class="{{ $class }}">
                                    <td>{{ $s_no }}</td>
                                    <td>
                                        {{ $user_details->par_fname.' '.$user_details->par_lname }}
                                    </td>
                                    <td>{{ $user_details->fname . ' ' . $user_details->lname }}</td>
                                    <td>{{ $user_details->email }}</td>
                                    <td>
                                        @if ($user_details->rater)
                                            {{ Str::ucfirst($user_details->rater) }}
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        <?php
                                        if ($user_details->survey_status == '0') {
                                            echo '<span class="alert-danger">Closed</span>';
                                        } elseif ($user_details->survey_status == '1') {
                                            echo '<span class="alert-info">Active</span>';
                                        } elseif ($user_details->survey_status == '2') {
                                            echo '<span class="alert-warning">Partly Completed</span>';
                                        } elseif ($user_details->survey_status == '3') {
                                            echo '<span class="alert-success">Completed</span>';
                                        } elseif ($user_details->survey_status == '4') {
                                            echo '<span class="alert-danger">In-Active</span>';
                                        }
                                        ?>
                                    </td>
                                    @if ($user_details->notify_email_date)
                                        <td>
                                            <img src="{{ asset('images/calendar-icon.png') }}" alt="Calendar Icon">
                                            {{ date('d/m/Y', strtotime($user_details->notify_email_date)) }}
                                            <img src="{{ asset('images/time-icon.png') }}" alt="Time Icon">
                                            {{ date('g:i:A', strtotime($user_details->notify_email_date)) }}

                                        </td>
                                    @else
                                        <td class="text-center">-</td>
                                    @endif
                                    @if ($user_details->last_submitted_date)
                                        <td class="last_submitted_date">
                                            <img src="{{ asset('images/calendar-icon.png') }}" alt="Calendar Icon">
                                            {{ date('d/m/Y', strtotime($user_details->last_submitted_date)) }}
                                            <img src="{{ asset('images/time-icon.png') }}" alt="Time Icon">
                                            {{ date('H:i:A', strtotime($user_details->last_submitted_date)) }}

                                        </td>
                                    @else
                                        <td class="last_submitted_date text-center">-</td>
                                    @endif
                                    </td>


                                </tr>
                                <?php $s_no++; ?>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="7" class="text-center">No Results Found</td>
                            </tr>
                    </tbody>
                    @endif
                </table>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $('.show_demo_datas').change(function() {
                if (this.checked) {
                    $('.demo_heading').show('slow');
                    $('.demographic_data').show('slow');
                } else {
                    $('.demo_heading').hide('slow');
                    $('.demographic_data').hide('slow');
                }

            });
        });
    </script>
<!-- DataTables CSS -->
<link rel="stylesheet" href="{{ asset('css/dataTable/jquery.dataTables.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/dataTable/dataTables.bootstrap4.min.css') }}">

<!-- DataTables JS -->
<script src="{{ asset('script/dataTable/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            $('#survey_status').DataTable({
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

        .demographic_data {

            border-bottom: 0.5px solid #dddddd !important;
            border-top: 0.5px solid #dddddd !important;
            border-left: 0.5px solid #dddddd !important;
            border-right: 0.5px solid #dddddd !important;

        }

        .last_submitted_date {
            border-right: 0.5px solid #dddddd !important;
        }

        .self td {
            border-color: #fff !important;

        }

        .self {
            background-color: #eee !important;

        }

        .no_self td {
            border-color: #fff !important;
        }

        .no_self {

            background-color: #DDDDDD !important;
        }
    </style>
@endsection
