<table>
    <thead>
        <tr>
            <th class="competency-header text-center"></th>
            <th class="competency-header text-center"></th>
            <th class="competency-header text-center"></th>
            <th class="competency-header text-center"></th>

            @if (count($text_responses) > 0)

            @if (count($question_id) > 0)
            @foreach ($question_id as $dimension => $order)
            <?php $r_count = count($order); ?>
            <th colspan={{ $r_count }} style="text-align: center;">{{$dimension}}</th>
            @endforeach
            @endif

            @endif

        </tr>
        <tr>
            <th class="header text-center">S.No</th>
            <th class="header text-center" style="width: 200px">Name</th>
            <th class="header text-center" style="width: 200px">Email</th>
            <th class="header text-center">Rater-type</th>

            @if (count($text_responses) > 0)
            @if (count($question_id) > 0)
            <?php $s_no = 1; ?>
            @foreach ($question_id as $dimension => $order)
            @foreach ($order as $order_value)
            <th class="text-center">Q{{ $s_no }}</th>
            <?php $s_no++; ?>
            @endforeach
            @endforeach
            @endif
            @endif

        </tr>
    </thead>
    <tbody>
        <?php $i = 1; ?>
        @foreach ($text_responses as $display_no => $response)
        <tr>
            <td> {{ $i }} </td>
            <td>
                <?php
                if ($response->respondent_id != 0) {
                    $f_l_name = DB::table('users')->selectRaw('GROUP_CONCAT(COALESCE(CONCAT(fname, " ", lname), fname)) AS user_name')->where('id', $response->participant_id)->value('user_name');
                    
                } else {
                    $f_l_name = $response->fname.' '.$response->lname;
                }
                ?>
                {{ $f_l_name }}
            </td>
            <td>{{ $response->email }}</td>
            <td>{{ Str::ucfirst($response->rater) }}</td>
            @if (isset($response->responses))
            @foreach ($response->responses as $key => $question_text)
            @if (count($question_text) > 0)
            <td>
                @foreach ($question_text as $key => $each_question_text)
                {{$each_question_text}}
                @endforeach
            </td>
            @else
            <td class="text-center"> - </td>
            @endif
            @endforeach
            @endif
        </tr>
        <?php $i++; ?>
        @endforeach
    </tbody>
</table>
<?php //exit;
?>
