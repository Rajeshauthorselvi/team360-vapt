@extends('default.users.layouts.default')

@section('content')



    <div class="container parent">
        <div class="row">
            <script src="{{ asset('script/question-survey-common.js') }}"></script>

            @include('default.users.questions.functions')

            @include('default.users.questions.welcome')

            <div class="col-sm-12  col-md-12 col-xs-12 welcome-txt" id="question-container">

                <form method="POST" action="{{ route('user.store', config('site.survey_slug')) }}" id="survey-user-form">
                    @csrf


                <?php $no = 1; ?>

                <div class="bs-example" data-example-id="contextual-panels">

                    <ul class="question-container">



                        @if (count($questions) > 0)
                            @foreach ($questions as $key => $question)
                                @if ($question->question_type == 'grid')
                                    @include(
                                        'default.users.questions.gridquestions'
                                    )
                                @else
                                    @include(
                                        'default.users.questions.alltypequestions'
                                    )
                                @endif
                                <?php $no++; ?>
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
                <input type="hidden" name="survey_id" value="{{ $survey_id }}" class="survey_id">
                </form>

            </div>

        </div>
    </div>

    <?php $rcount = $response_count; ?>
    <style type="text/css">
        .inner-header .container {
            margin-top: 30px;
        }
        .pl-0{
            padding-left: 0;
        }
        .pr-0{
            padding-right: 0;
        }
        .error-message-check{
            border-radius: 3px;
            margin-top: 10px;
            padding: 5px;
            width: 35%;
            background: #990000 none repeat scroll 0 0;
            color: #fff;
            font-weight: normal;
            font-size: 13px;
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
    @if ($survey_id == 72)
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
                    if (closes > 0) {
                        var obj = $(this).find('tr.data-question-type-grid');
                        var checkcount = 0;
                        $(obj).each(function() {
                            checkcount += parseInt($(this).find('input[type=radio]:checked').length);
                            //alert(checkcount);
                        });
                        check_req = $(this).find('.qtn-required').length;
                        if (check_req == 1) {
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
    @if ($survey_id==11)
        <script>
                $(document).on('keyup','[data-type="textarea"]',function () {
                var myStr = $(this).val();
                    const input = myStr
                    const result = input
                    .split( ) // split the sentences
                    .filter(sentence => sentence !== '') // remove empty sentences
                    .map(sentence => sentence.split(' ') // split the words
                    .filter(word => word !== '') // remove empty words
                    .length); // get number of words in sentence

                    console.log(`There are ${result.length} sentences.`);
                    var sentence="";
                    result.forEach((item, index) => {
                        sentence=item;
                    });
                    var parent=$(this).parent('.question-options');
                    parent.find('.word_count').text(sentence);
                    if (sentence < 20) {
                        parent.find('.count_message').show();
                        parent.find('.count_message').addClass('invalid');
                        parent.find('.count_message').removeClass('valid');
                    } else {
                        parent.find('.count_message').hide();
                        parent.find('.count_message').addClass('valid');
                        parent.find('.count_message').removeClass('invalid');
                    }
            });
        </script>
    @endif
    @if ($survey_id==37)
    <script>
        $(document).on('change','[data-type="checkbox"]',function () {

            var question_name=$(this).attr('name');
            var checked_length=$('[name="'+question_name+'"]:checked').length;
            var minAllowed = 2;
            var maxAllowed = 2;
            if(checked_length > 0){

                if (checked_length >= maxAllowed) {
                    $('[name="'+question_name+'"]:not(:checked)').prop('disabled', true);
                    $(this).parents('.question-options').find('.error-message-check').addClass('hidden')
                } else {
                    $('[name="'+question_name+'"]').prop('disabled', false);
                    $(this).parents('.question-options').find('.error-message-check').addClass('hidden')
                }

                /* if(checked_length < 2){
                    $('#form-submit').attr('disabled',true);
                    $(this).parents('.question-options').find('.error-message-check').removeClass('hidden');
                }
                else{
                    $('#form-submit').removeAttr('disabled');
                    $(this).parents('.question-options').find('.error-message-check').addClass('hidden');
                } */

                if (checked_length < minAllowed) {
                    // $('#form-submit').attr('disabled',true);
                    $(this).parents('.question-options').find('.error-message-check').removeClass('hidden').text('Please select ' + minAllowed + ' checkboxes.');
                }
                else{
                    // $('#form-submit').removeAttr('disabled');
                }
            }
            else{

                $('#form-submit').removeAttr('disabled');
                $(this).parents('.question-options').find('.error-message-check').addClass('hidden');
            }
        });
    </script>
    @endif
    @include('default.users.questions.fixedfooter')

@endsection
