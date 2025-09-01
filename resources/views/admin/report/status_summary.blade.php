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
                @if (count($summary_report) > 0)
                <div class="pull-right">
                    <a href="{{ route('export.status_summary',['survey_id'=>$survey_id]) }}" class="btn btn-primary">
                        <span class="glyphicon glyphicon-download-alt"></span>
                        Download
                        <span class="fa fa-file-excel-o"></span>
                    </a>
                </div>

                <br>
                <br>
                <br>
                @endif

                <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="survey_status">
                    <thead>
                        <tr>
                            <th>S.No</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Self</th>
                            @foreach ($all_raters as $rater)
                                <th>{{ $rater }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <?php $s_no=1; ?>
                        @foreach ($summary_report as $report)
                            <tr>
                                <td>{{ $s_no }}</td>
                                <td>{{ $report['name'] }}</td>
                                <td>{{ $report['email'] }}</td>
                                <td>{{ ($report['self_status']==3)?'Yes':'No' }}</td>
                                @foreach ($all_raters as $rater_id=>$rater)
                                <td>
                                    {{ $report['raters_completed'][$rater_id]['completed'] }} of {{ $report['raters_completed'][$rater_id]['total_respondent'] }}
                                </td>
                                @endforeach
                            </tr>
                            <?php $s_no++; ?>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
