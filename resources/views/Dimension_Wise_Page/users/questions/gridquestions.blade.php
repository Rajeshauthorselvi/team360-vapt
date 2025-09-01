<li class="group active @if ($count == 1) {{ 'focus' }} @endif" data-id="{{ $count }}">
    <div class="panel panel-primary question" rel="{{ ++$key }}" data-question-type="{{ $ques->question_type }}">
        <input type="hidden" name="question_id[]" value="{{ $ques->question_id }}">

        <div class="panel-heading">
            <div class="question-label"> {!! $ques->question_preamble !!}
                @if ($ques->question_required == 1)
                    <span class="qtn-required" style="visibility: hidden;">*</span>
                @endif
            </div>
        </div>

        <?php

        $qtext = explode('|', $ques->question_text);
        $qqid = explode('|', $ques->question_id);
        $c = array_combine($qqid, $qtext);

        $optext = $ques->options;

        $optionth = isset($ques->optionth) ? $ques->optionth : [];

        $user_response = [];
        $datanottouch = 0;
        if (!empty($responses)) {
            $user_response = array_keys($responses, $ques->question_id);
        }
        if (!empty($user_response)) {
            $datanottouch = 1;
        }

        $questionArrayTmp = explode('~', $qtext[0]); //added by Raj for spliting and adding <th>

        //Code to find whether the sub dimension contains words like "3 items", "(a)","(b)" and this means that the group about question a, b, c should added upto 6. This logic is just for TVRLS written by Raj

        $checkSum6Flag = 0;

        if (strstr($ques->question_sub_dimension, '3 items')) {
            $checkSum6Flag++;
        }
        if (strstr($ques->question_sub_dimension, '(a)')) {
            $checkSum6Flag++;
        }
        if (strstr($ques->question_sub_dimension, '(b)')) {
            $checkSum6Flag++;
        }
        if (strstr($ques->question_sub_dimension, '(c)')) {
            $checkSum6Flag++;
        }

        if ($checkSum6Flag >= 2) {
            $count = 1;
        }
        $arrayAlpCount = ['(a)', '(b)', '(c)'];
        $cntAlp = 0;
        ?>
        <div class="panel-body question-options" data-nottouch="{{ $datanottouch }}">
            <table class="table table-bordered">
                <tr>

                    @if (count($optionth) > 0)
                        <th class="question-dimension" style="width:35%"></th>
                        @foreach ($optionth as $optionthi)
                            <th class="text-center question-dimension" style="width:4%">{!! $optionthi !!}</th>
                        @endforeach
                    @endif
                    @if (count($questionArrayTmp) == 2)
                        <th class="question-dimension" style="width:35%"></th>
                    @endif
                    {{-- <th class="text-center question-dimension" style="width:15%">Additional Comments</th> --}}

                </tr>
                @if (count($c) > 0)
                    <?php //$count=1;
                    ?>
                    @foreach ($c as $ques_id => $ques_text)
                        <?php

                        $questionArray = explode('~', $ques_text);
                        if (count($questionArray) == 2) {
                            $questionTextPart1 = $questionArray[0];
                            $questionTextPart2 = $questionArray[1];
                        } else {
                            $questionTextPart1 = $ques_text;
                            $questionTextPart2 = '';
                        }
                        // echo $arrayAlpCount[$cnt];
                        // $cnt++;
                        ?>
                        <tr class="data-question-type-grid">
                            @if ($checkSum6Flag >= 2)
                                <td class=" question-dimension">

                                    {{ $arrayAlpCount[$count - 1] }}. {!! $questionTextPart1 !!}

                                </td>
                            @else
                                <td class=" question-dimension">
                                    {{ $count }}. {!! $questionTextPart1 !!}
                                </td>
                            @endif

                            <?php
                            $i_options = isset($optext[$ques_id]) ? $optext[$ques_id] : [];
                            $option_count = 1;
                            //dd($i_options);
                            ?>

                            @foreach ($i_options as $key => $i_option)

                                <td class="grid-td"><?php
if ($checkSum6Flag >= 2) {
    yieldoptionforgrid($ques_id, $i_option, $option_count, $user_survey_id, $key, 'checkSum6', $ques->question_id);
} else {
    yieldoptionforgrid($ques_id, $i_option, $option_count, $user_survey_id, $key, '', $ques->question_id);
}

?>
                                </td>

                                <?php $option_count++; ?>
                            @endforeach
{{--
                            <td>
                                <?php

                                if (!empty($responses)) {
                                    $user_response_id = array_keys($responses, $ques_id);
                                    // var_dump($user_response_id[0]);

                                    //yield_input_grid('textarea', $ques_id, $i_options, $user_response_id);
                                } else {
                                   // yield_input_grid('textarea', $ques_id, $i_options, []);
                                }
                                ?>
                            </td> --}}

                            @if ($questionTextPart2 != '')
                                <td class=" question-dimension">{!! $questionTextPart2 !!}</td>
                            @endif
                        </tr>
                        @php $count++ @endphp
                    @endforeach
                @endif
                <?php unset($c); ?>
            </table>
        </div>
        <div class="message">
            <span><strong>Ooops!</strong> You must make a selection</span>
            <div>
            </div>

        </div>
</li>
<style type="text/css">
    .data-question-type-grid .question-dimension {
        text-align: left;
    }

</style>
