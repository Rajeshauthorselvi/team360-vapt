@extends(config('site.theme').'.users.layouts.default')

@section('content')



    <div class="container parent">
        <div class="row">
            @include(config('site.theme') . '.users.questions.functions')

            @if ($welcome_text != '')
                @include(config('site.theme') . '.users.questions.welcome')
            @endif
            <div class="col-sm-12  col-md-12 col-xs-12 welcome-txt" id="question-container">

                <form method="POST" action="{{ route('user.store', config('site.survey_slug')) }}" id="survey-user-form">
                    @csrf

                <div class="bs-example" data-example-id="contextual-panels">

                    <ul class="question-container">
                        @if (count($questions) > 0)
                        <?php $count = 1; ?>
                            @foreach ($questions as $dimension => $question)
                                <?php $iteration_count = $loop->iteration; ?>
                                @if ($iteration_count != 1)
                                    <?php $class_dimension = 'hidden'; ?>
                                @else
                                    <?php $class_dimension = 'active-parent'; ?>
                                @endif

                                <div class="dimension_count {{ $class_dimension }}"
                                    dimension-count="{{ $iteration_count }}">
                                    @if ($dimension_hide == 0)
                                        <div class="question-page head">
                                            {!! $dimension !!}
                                        </div>
                                    @endif
                                    <?php $dimensionCount = 1; ?>
                                    @foreach ($question as $key => $ques)
                                        @if ($dimensionCount == 1 && $ques->question_sub_dimension != '')
                                            <div class="question-dimension question-cont">
                                                {!! $ques->question_sub_dimension !!}
                                            </div>
                                        @endif
                                        @if ($ques->question_type == 'grid')
                                            @include(config('site.theme') . '.users.questions.gridquestions')
                                            @php
                                                $qtext = explode('|', $ques->question_text);
                                                $count += count($qtext);
                                            @endphp
                                        @else
                                            @include(config('site.theme') . '.users.questions.alltypequestions')
                                            <?php $count++; ?>
                                        @endif
                                        <?php $dimensionCount++; ?>
                                    @endforeach
                                </div>
                            @endforeach
                        @else
                            <div class="title m-b-md">
                                <div class="text-center">NO QUESTIONS FOUND</div>
                            </div>
                        @endif
                    </ul>
                </div>

                <input type="hidden" name="currentli" id="currentli" value="1">
                <input type="hidden" name="formaction" id="formaction" value="submit">
                <input type="hidden" name="user_survey_respondent_id" value="{{ $user_survey_id }}">
                </form>


            </div>

        </div>
    </div>
    <?php $rcount = $response_count; ?>
    <style>
        .question-page.head{
            width: 100%;
            text-align: center;
            background: #d9edf7;
            padding: 11px;
            margin-bottom: 6px;
        }
    </style>
    @if ($rcount == 0 && $welcome_text == '')
        <style type="text/css">
            #question-container,
            #fixed-footer {
                display: inherit;
            }

        </style>
    @endif

    @if ($rcount > 0)
        <style type="text/css">
            #question-container,
            #fixed-footer {
                display: inherit;
            }

        </style>

        <script type="text/javascript">
            $(document).ready(function() {

                var error = 0;
                var liposition = new Array();

                $('.question-container li').each(function() {
                    var check_required = $(this).find('.qtn-required:visible').length;
                    var closesinput = $(this).find('.grid-required , .required');
                    var closes = $(this).find('tr.data-question-type-grid').length;
                    // alert(closes);
                    if (closes > 0) {
                        var obj = $(this).find('tr.data-question-type-grid');
                        var checkcount = 0;
                        $(obj).each(function() {
                            checkcount += parseInt($(this).find('input[type=radio]:checked').length);
                            //alert(checkcount);
                        });
                        check_req = $(this).find('.qtn-required').length;
                        if (check_req == 1) { //alert(closes+'_'+checkcount);
                            if (closes == checkcount) {
                                $(this).find('.message').hide();
                            } else {
                                $(this).find('.message').show();
                                liposition.push($(this).attr('data-id'));
                                return null;
                            }

                        }
                    } else {

                        if (check_required > 0 && checkerror(closesinput)) {
                            liposition.push($(this).attr('data-id'));
                            $(this).find('.message').show();
                        } else {
                            $(this).find('.message').hide();
                        }
                    }
                });


                if (liposition.length > 0) {
                    var height = $('li[data-id="' + liposition[0] + '"]').offset().top - $('.inner-header')
                    .outerHeight() - 10;
                    $('html, body').animate({
                        scrollTop: height
                    }, 'slow');

                }

            });
        </script>
    @endif

    <script type="text/javascript">
        $("input[type=radio]").change(
        function() { //code to check whether the sum of selected option is 6 for TVRLS application, code by Raj

            //alert( $("input[type=radio][name="+ this.name + "]").id );

            if ($(this).attr('class') == "grid-required op_radio checkSum6") {
                var totalSumSelectOpt = 0;
                var totalRdbSel = 1;
                var selectedVal = $(this).attr('id');
                var selectedValArray = selectedVal.split('_');
                var userSelectedVal = (selectedValArray[2] -
                1); //Logic to identify scale text from the radio button id
                // alert(userSelectedVal);
                totalSumSelectOpt = userSelectedVal;
                var hv = $('#hidden_' + selectedValArray[1]).attr(
                "value"); //get the question id's stored in hidden field
                var questionArray = hv.split('|'); //split the question ids which was taken from above line
                var qtnCount = 0;
                for (qtnCount = 0; qtnCount < questionArray.length; qtnCount++) {
                    //alert('_'+questionArray[qtnCount]);
                    var srdBtnId = $('input[type=radio][name=_' + questionArray[qtnCount] + ']:checked').attr(
                    'id'); //Get the Id of selected radio button for this preamble
                    if (typeof srdBtnId !==
                        "undefined") //When I choose only 1st question the loop runs through all questions in the preamble and returns undefined and hence we are checking
                    {
                        //alert(srdbtnid);
                        if (srdBtnId != selectedVal) {
                            var questionsSelectedArray = srdBtnId.split('_');
                            totalRdbSel++;
                            totalSumSelectOpt = totalSumSelectOpt + (questionsSelectedArray[2] - 1);
                            if (totalSumSelectOpt > 6) {
                                alert(
                                    "Please fill the survey correctly. The sum of all three sub-questions (a), (b) and (c) should not exceed 6 in question ");
                                //$(this).attr('id').checked = false;
                                $(this).attr('checked', false);

                            }
                            if ((totalSumSelectOpt < 6) && (totalRdbSel == 3)) {
                                alert(
                                    "Please fill the survey correctly. The sum of all three sub-questions (a), (b) and (c) should not be less than 6 in question ");
                                $(this).attr('checked', false);
                            }



                            //alert(totalSumSelectOpt+'_'+(questionsSelectedArray[2] -1))

                        }

                    }
                }

                //alert(selectedValArray[2]);
            }
            //alert( $(this).attr('id')+'_'+ $(this).attr('class'));

        });
    </script>

    @include(config('site.theme') . '.users.questions.fixedfooter')

@endsection
