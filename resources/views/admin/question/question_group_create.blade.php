@extends('layouts.default')

@section('content')
    <div class="container">
        <div class="row setup-content" id="step-3">

            <div class="col-xs-12">

                <div class="form-wrapper">

                    <div class="form-steps-wizard step3"> </div>


                    <div class="col-md-12 well">

                        <h3 class="need-margin-bottom-forstrip text-center">Questions Grouping</h3>
                        <div>
                            <div class="table-responsive">
                                @if (Session::get('message') != '')
                                    <div class="col-sm-12 alert alert-success">
                                        <strong>Success!</strong> {{ Session::get('message') }}
                                        {{ Session::forget('message') }}
                                        <button type="button" class="close" data-dismiss="alert">&times;</button>


                                    </div>
                                @endif
                                <?php

                                $question_dimension = [];

                                foreach ($questions as $key => $value) {
                                    $question_dimension[$value->question_dimension][] = $value->question_text . '|' . $value->question_id;
                                }

                                // dd($question_dimension);

                                ?>
                                <?php

                                $cell_count = sizeof($raters);
                                $question_count = sizeof($questions);
                                $s_no = 1;
                                ?>
                                <?php $action = 'questions_post_group'; ?>
                                <form action="{{ route($action, ['survey_id' => $survey_id]) }}" method="POST" id="add-participants" class="form-horizontal">
                                @csrf
                                <table class="table table-bordered survey-table">
                                    <thead class="quesiton_group_heading">
                                        <th width="1%">Q.No</th>
                                        <th class="first-head text-center">Statements</th>
                                        @foreach ($raters as $rater)
                                            <th class="text-center"><input type="checkbox"
                                                    id="rater{{ $rater->rater_id }}" value="{{ $rater->rater_id }}"
                                                    class="select_{{ $rater->rater_id }} "><br><label
                                                    for="rater{{ $rater->rater_id }}">{{ $rater->rater }}</label><input
                                                    type="hidden" value="{{ $rater->rater_id }}"></th>
                                        @endforeach
                                    </thead>

                                    <tbody class="t-body">
                                        @if ($question_count > 0)
                                            @foreach ($question_dimension as $key => $values)
                                                <tr class="td_question">
                                                <tr>
                                                    <td colspan="12" class="text-center comptenency">
                                                        <strong>{!! $key !!}</strong>
                                                    </td>
                                                    <?php $dimension_slug = $key; ?>
                                                </tr>
                                                <tr>
                                                    <td></td>
                                                    <td></td>
                                                    @foreach ($raters as $rater)
                                                        <td class="text-center comptenency">
                                                            <input type="checkbox"
                                                                dimension="{{ $dimension_slug . '_' . $rater->rater_id }}"
                                                                class="dimension_check">
                                                        </td>
                                                    @endforeach
                                                </tr>
                                                @foreach ($values as $key => $value)
                                                    <?php $data_explode = explode('|', $value); ?>
                                                    <tr class="questions">
                                                        <td>{{ $s_no }}</td>
                                                        <td>
                                                            <?php
                                                            $letters = range('A', 'Z');
                                                            $kkey = '';
                                                            $question_text = explode('~', $data_explode[0]);

                                                            if (sizeof($question_text) > 1) {
                                                                foreach ($question_text as $k => $v) {
                                                                    $qtext = $letters[$k] . ') ' . trim($v);
                                                                    if ($k == 0) {
                                                                        $kkey = $key + 1;
                                                                    }
                                                                    echo $question_arr = trim($qtext) . '<br>';
                                                                    $is_grid = 'yes_';
                                                                }
                                                            } else {
                                                                echo $question_arr = trim($data_explode[0]);
                                                                $is_grid = 'no_';
                                                            }
                                                            ?>
                                                        </td>
                                                        <?php $question_id_split = explode('~', $data_explode[1]); ?>



                                                        @foreach ($raters as $rater)
                                                            <td class="text-center" style="vertical-align: middle;">
                                                                @foreach ($question_id_split as $key => $question_id)
                                                                    <input type="checkbox" name="q_r_id[]"
                                                                        class="select_{{ $rater->rater_id }} rater-ques{{ $rater->rater_id }} use-grid"
                                                                        value="{{ $question_id }}|{{ $rater->rater_id }}"
                                                                        rater-id="{{ $rater->rater_id }}"
                                                                        grid="{{ $is_grid . $rater->rater_id }}"
                                                                        style="vertical-align:middle"
                                                                        attr-dimension="{{ $dimension_slug.'_'.$rater->rater_id }}"
                                                                        >
                                                                @endforeach
                                                            </td>
                                                        @endforeach
                                                    </tr>
                                                    <?php $s_no++; ?>
                                                @endforeach
                                                </tr>
                                            @endforeach
                                        @else
                                            <tr>
                                                <td colspan="{{ $cell_count + 2 }}" class="text-center">No Results
                                                    Found
                                                </td>
                                            </tr>
                                        @endif
                                    </tbody>

                                </table>
                                <div class="text-center">
                                    <a href="{{ URL::route('questions.show', $survey_id) }}"
                                        class="btn btn-danger">Cancel</a>
                                    <input type="submit" class="btn btn-success" value="Save" id="submit">
                                </div>
                                </form>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
        </div>
        @if ($count_completed_status > 0)
            <script type="text/javascript">
              /*   $(':checkbox[type=checkbox]').click(function() {
                    return false;
                }); */
            </script>
        @endif
        <script type="text/javascript">
            $(".quesiton_group_heading input[type=checkbox]").change(function() {
                if ($(this).prop("checked") == true) {
                    $('input:checkbox[rater-id="' + $(this).val() + '"]').prop('checked', true);
                    var checked_status = true;
                } else {
                    $('input:checkbox[rater-id="' + $(this).val() + '"]').prop('checked', false);
                    var checked_status = false;
                }
                var parent_rater = $(this).val();
                $(".dimension_check").each(function() {
                    var current_dimension = $(this).attr('dimension');
                    var split_val = current_dimension.split('_');
                    var rater_id = split_val[1];
                    if (parent_rater == rater_id) {
                        $(this).prop('checked', checked_status);
                    }
                });
            });


            /*Valdation on submit*/
            $("form").submit(function() {

                var length = $(".t-body").find("input[type=checkbox]:checked").length;
                if (length == 0) {
                    swal(
                        'Oops...',
                        'Please Select atleast one option!',
                        'error'
                    )
                    return false;
                }

            });
            /*Valdation on submit*/

            $(document).ready(function() {
                <?php if(!empty($options)){ ?>
                /*Selected value default check*/

                var q_all_values = <?php echo json_encode($options); ?>;
                $.each(q_all_values, function(i, rater_id) {
                    var rater_id = q_all_values[i].rater_id;
                    var question_id = q_all_values[i].question_id;
                    var stored_val = question_id + '|' + rater_id;

                    $("[name='q_r_id[]']").each(function() {
                        var checkbox_val = $(this).val();
                        if (stored_val == checkbox_val) {
                            $(this).attr('checked', 'checked');
                        }
                    });
                });
                <?php } else{ ?>


                $(':checkbox[type=checkbox]').prop('checked', true);

                <?php }  ?>
                /*Selected value default check*/

                /*Select Question group*/

                $(".use-grid").hide().filter(":first-child").show();

                $('.use-grid[grid]').change(function() {

                    if ($(this).is(":checked")) {
                        $(this).parent().find('input:checkbox').attr('checked', 'checked');
                    } else {
                        $(this).parent().find('input:checkbox').removeAttr('checked');
                    }
                });


                /*Select Question group*/


                /*Page load to default check or unceck heading*/
                $(".quesiton_group_heading input[type=checkbox]").each(function() {
                    var head_rater_id = $(this).val();
                    var size_question = $('[rater-id=' + head_rater_id + ']:checked').length;
                    var question_count = $('[rater-id=' + head_rater_id + ']').length;
                    // alert(question_count);
                    if (size_question == question_count) {
                        $('.select_' + head_rater_id).prop('checked', true);
                    }

                });
                /*Page load to default check or unceck heading*/

                /*To change Check or uncheck. based on Sub-child*/
                $(".quesiton_group_heading input[type=checkbox]").each(function() {
                    var rater_id = $(this).val();
                    $('.rater-ques' + rater_id).change(function() {

                        if ($('.rater-ques' + rater_id + ':checked').length == $('.rater-ques' +
                                rater_id).length) {
                            $('#rater' + rater_id).prop('checked', true);
                        } else {
                            $('#rater' + rater_id).prop('checked', false);
                        }
                    });
                });
                /*To change Check or uncheck. based on Sub-child*/

                /*Page load to default dimension check or unceck heading*/
                $(".dimension_check").each(function() {
                    var current_dimension = $(this).attr('dimension');
                    var length = $('[attr-dimension=' + current_dimension + ']').length;
                    var checked_length = $('[attr-dimension=' + current_dimension + ']:checked').length;
                    if (checked_length == length) {
                        $('[dimension=' + current_dimension + ']').prop('checked', true);
                    }


                });
                /*Page load to default dimension check or unceck heading*/
                /*To change Check or uncheck. based on Sub-child*/
                $(".quesiton_group_heading input[type=checkbox]").each(function() {
                    var rater_id = $(this).val();
                    $('.rater-ques' + rater_id).change(function() {

                        var currenct_dimension = $(this).attr('attr-dimension');
                        var length = $('[attr-dimension=' + currenct_dimension + ']').length;
                        var checked_length = $('[attr-dimension=' + currenct_dimension + ']:checked')
                            .length;

                        if ($('.rater-ques' + rater_id + ':checked').length == $('.rater-ques' +
                                rater_id).length) {
                            $('#rater' + rater_id).prop('checked', true);
                            $('[dimension=' + currenct_dimension + ']').prop('checked', true);
                        } else {
                            $('#rater' + rater_id).prop('checked', false);
                            $('[dimension=' + currenct_dimension + ']').prop('checked', false);
                        }

                        if (checked_length == length) {
                            $('[dimension=' + currenct_dimension + ']').prop('checked', true);
                        } else {
                            $('[dimension=' + currenct_dimension + ']').prop('checked', false);
                        }

                    });
                });
                /*To change Check or uncheck. based on Sub-child*/
            });
            $(document).on('change', '.dimension_check', function() {
                var current_attr = $(this).attr('dimension');
                var var_split_val = current_attr.split('_');
                var current_checked = $(this).is(":checked");
                var rater_id = var_split_val[1];
                if (current_checked) {
                    $('[attr-dimension=' + current_attr + ']').prop('checked', true);
                } else {
                    $('#rater' + rater_id).prop('checked', false);
                    $('[attr-dimension=' + current_attr + ']').prop('checked', false);
                }
            });
        </script>

        <style type="text/css">
            .survey-table th {
                background: #2041bd none repeat scroll 0 0;
                color: #ffffff;
            }

            .first-head {
                width: 40%;
            }

            .comptenency {
                background-color: #EC971F;
                color: #fff
            }

        </style>
    @endsection
