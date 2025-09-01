<table class="table table-striped table-bordered" cellspacing="0" width="100%" id="survey_status">
    <thead>
        <tr>
            <th>S.No</th>
            <th style="width: 200px">Name</th>
            <th style="width: 200px">Email</th>
            <th style="width: 100px">Self</th>
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
