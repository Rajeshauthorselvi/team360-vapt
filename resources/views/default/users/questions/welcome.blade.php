<?php $rcount = $response_count; ?>
@if (isset($welcome_text) and $rcount == 0)
    <div class="col-sm-12  col-md-8 col-md-offset-2 col-xs-12" id="welcome-section">
        <div class="site-content">
            <div class="welcome-box">
                <div class="welcome-body">
                    @if (Request::has('rater') && Request::get('rater') == 33)
                        <b>Introduction</b>
                        <br><br>
                        Thank you for being a part of our quest for excellence. Your feedback on this leader will be
                        most useful for their professional development.
                        <br><br>
                        This questionnaire has 2 sections.
                        <br><br>
                        <b>SECTION 1</b>
                        <ul style="line-height:22px;">
                            <li>This section has rating-based questions. Use the rating scale provided to assess this
                                leader. All rating-based questions are mandatory</li>
                        </ul><br>
                        <b>Rating Scale</b>
                        <br><br>
                        Rate each leader on the extent and consistency to which they demonstrate these behaviours in
                        their interactions with you.
                        <br><br>
                        6 - Strongly Agree<br>
                        5 - Agree<br>
                        4 - Slightly Agree<br>
                        3 - Slightly Disagree<br>
                        2 - Disagree<br>
                        1 - Strongly Disagree<br>
                        NA - Don’t have insight in this area / Not Applicable to their role<br><br>
                        <b>SECTION 2</b>
                        <ul style="line-height:22px;">
                            <li>This is a qualitative section with 3 questions</li>
                            <li>All questions are mandatory</li>
                        </ul>
                    @else
                        {!! $welcome_text !!}
                    @endif

                </div>
            </div>
            <div class="welcome-footer text-center">
                @if ($survey_id == 55)
                    <a href="{{ url($url . '/user-dashboard') }}" class="btn btn-danger">Disagree</a>
                    {{ Form::button('Agree', ['class' => 'btn btn-submit ', 'id' => 'take-survey']) }}
                @else
                    {{ Form::button('Take Survey', ['class' => 'btn btn-submit ', 'id' => 'take-survey']) }}
                @endif
            </div>
        </div>
    </div>
    <style type="text/css">
        footer {
            position: relative;
        }

        .welcome-body table td,
        .welcome-body table th {
            padding: 5px;
        }

        .welcome-box {
            max-height: 450px;
            overflow: auto;
        }
    </style>
@endif
