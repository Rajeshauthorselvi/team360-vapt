<?php

function yield_input($type, $qid, $qoptions, $user_response)
{
    $tresponse = null;
    $oresponse = null;
    $tstyle = 'display:none';
    if (!empty($user_response) and count($user_response) == 1) {
        $responses = DB::table('responses')->find($user_response);
        $tresponse = $responses->text_response;
        $oresponse = $responses->option_id;
    } elseif (count($user_response) > 1) {
        foreach ($user_response as $key => $value) {
            $responses = DB::table('responses')->find($value);
            $oresponse[] = $responses->option_id;
        }
    }

    if (!empty($tresponse)) {
        $tstyle = 'display:inherit';
    }

    $result = '';
    switch ($type) {
        case 'text':
            $result = Form::text('_' . $qid, $tresponse, ['class' => 'form-control required', 'data-type' => 'text']);
            break;
        case 'textarea':
            $result = Form::textarea('_' . $qid, $tresponse, ['class' => 'form-control required', 'data-type' => 'textarea']);
            break;
        case 'dropdown':
            $options = ['' => 'Please Select'];
            foreach ($qoptions as $option_text => $qoption) {
                $options[$qoption] = $option_text;
            }
            $result = Form::select('_' . $qid, $options, $oresponse, ['class' => 'form-control required', 'data-type' => 'dropdown']);
            break;

        case 'radio':
            $option_count = 1;
            foreach ($qoptions as $option_text => $qoption) {
                $checked = $qoption == $oresponse ? 'checked' : null;
                if (strtolower($option_text) == 'others') {
                    $result .= '<div class="option-subsection" >' . Form::radio('_' . $qid, $qoption, $checked, ['class' => 'required op_radio', 'id' => 'radiolabel_' . $qid . '_' . $option_count, 'data-type' => 'radio']);
                    $result .= Form::label('radiolabel_' . $qid . '_' . $option_count, $option_text);
                    $result .= Form::textarea('others_' . $qid, $tresponse, ['class' => 'others-textarea required form-control', 'rows' => '5', 'style' => $tstyle, 'data-type' => 'others-textarea', 'id' => '_' . $qid]) . '</div>';
                } else {
                    $result .= '<div class="option-subsection" >' . Form::radio('_' . $qid, $qoption, $checked, ['class' => 'required op_radio', 'id' => 'radiolabel_' . $qid . '_' . $option_count, 'data-type' => 'radio']);
                    $result .= Form::label('radiolabel_' . $qid . '_' . $option_count, $option_text) . '</div>';
                }
                $option_count++;
            }
            break;
        case 'checkbox':
            $option_count = 1;
            foreach ($qoptions as $option_text => $qoption) {
                $checked = is_array($oresponse) ? (in_array($qoption, $oresponse) ? 'checked' : null) : ($qoption == $oresponse ? 'checked' : null);
                $result .= '<div class="option-subsection" >' . Form::checkbox('_' . $qid . '[]', $qoption, $checked, ['class' => 'required', 'id' => 'checkboxlabel_' . $qid . '_' . $option_count, 'data-type' => 'checkbox']);
                $result .= Form::label('checkboxlabel_' . $qid . '_' . $option_count, $option_text) . '</div>';
                $option_count++;
            }
            break;

        default:
            break;
    }
    echo $result;
}

function yield_input_grid($type, $qid, $qoptions, $user_response)
{
    $tresponse = null;
    $oresponse = null;
    $tstyle = 'display:none';
    if (!empty($user_response) and count($user_response) == 1) {
        $responses = DB::table('responses')->find($user_response);
        $tresponse = $responses->text_response;
        $oresponse = $responses->option_id;
    } elseif (count($user_response) > 1) {
        foreach ($user_response as $key => $value) {
            $responses = DB::table('responses')->find($value);
            $oresponse[] = $responses->option_id;
        }
    }

    if (!empty($tresponse)) {
        $tstyle = 'display:inherit';
    }

    $result = '';
    switch ($type) {
        case 'text':
            $result = Form::text('txt_' . $qid, $tresponse, ['class' => 'form-control required', 'data-type' => 'text']);
            break;
        case 'textarea':
            $result = Form::textarea('txt_' . $qid, $tresponse, ['class' => 'form-control required', 'data-type' => 'textarea']);
            break;
        case 'dropdown':
            $options = ['' => 'Please Select'];
            foreach ($qoptions as $option_text => $qoption) {
                $options[$qoption] = $option_text;
            }
            $result = Form::select('_' . $qid, $options, $oresponse, ['class' => 'form-control required', 'data-type' => 'dropdown']);
            break;

        case 'radio':
            $option_count = 1;
            foreach ($qoptions as $option_text => $qoption) {
                $checked = $qoption == $oresponse ? 'checked' : null;
                if (strtolower($option_text) == 'others') {
                    $result .= '<div class="option-subsection" >' . Form::radio('_' . $qid, $qoption, $checked, ['class' => 'required op_radio', 'id' => 'radiolabel_' . $qid . '_' . $option_count, 'data-type' => 'radio']);
                    $result .= Form::label('radiolabel_' . $qid . '_' . $option_count, $option_text);
                    $result .= Form::textarea('others_' . $qid, $tresponse, ['class' => 'others-textarea required form-control', 'rows' => '5', 'style' => $tstyle, 'data-type' => 'others-textarea', 'id' => '_' . $qid]) . '</div>';
                } else {
                    $result .= '<div class="option-subsection" >' . Form::radio('_' . $qid, $qoption, $checked, ['class' => 'required op_radio', 'id' => 'radiolabel_' . $qid . '_' . $option_count, 'data-type' => 'radio']);
                    $result .= Form::label('radiolabel_' . $qid . '_' . $option_count, $option_text) . '</div>';
                }
                $option_count++;
            }
            break;
        case 'checkbox':
            $option_count = 1;
            foreach ($qoptions as $option_text => $qoption) {
                $checked = is_array($oresponse) ? (in_array($qoption, $oresponse) ? 'checked' : null) : ($qoption == $oresponse ? 'checked' : null);
                $result .= '<div class="option-subsection" >' . Form::checkbox('_' . $qid . '[]', $qoption, $checked, ['class' => 'required', 'id' => 'checkboxlabel_' . $qid . '_' . $option_count, 'data-type' => 'checkbox']);
                $result .= Form::label('checkboxlabel_' . $qid . '_' . $option_count, $option_text) . '</div>';
                $option_count++;
            }
            break;

        default:
            break;
    }
    echo $result;
}

function yieldoptionforgrid($question_id, $i_option, $key, $user_survey_id, $option_name, $checkSumClass, $questionGroupIds)
{
    //dd($question_id,$i_option,$key,$user_survey_id,$option_name,$checkSumClass, $questionGroupIds);
    $checked = false;
    $result = '';
    if ($option_name == '0') {
        $option_name = '0&nbsp;';
    }
    //if(in_array($question_id,$responses))
    //{
    $responses = DB::table('responses')
        ->where('user_survey_respondent_id', $user_survey_id)
        ->where('option_id', $i_option)
        ->value('question_id');
    if ($responses == $question_id) {
        $checked = true;
        $result = '<div class="option-subsection option-subsection-grid" >' . Form::radio('_' . $question_id, $i_option, $checked, ['class' => 'grid-required op_radio ' . $checkSumClass, 'id' => 'radiolabel_' . $question_id . '_' . $key, 'data-type' => 'grid']) . '<span class="grid_label" >' . Form::label('radiolabel_' . $question_id . '_' . $key, $option_name) . '</span></div><input type="hidden" id="hidden_' . $question_id . '" value=' . $questionGroupIds . '>';
        print $result;
        //var_dump($option_name);
    } else {
        $checked = false;
        $result = '<div class="option-subsection option-subsection-grid" >' . Form::radio('_' . $question_id, $i_option, $checked, ['class' => 'grid-required op_radio ' . $checkSumClass, 'id' => 'radiolabel_' . $question_id . '_' . $key, 'data-type' => 'grid']) . '<span class="grid_label" >' . Form::label('radiolabel_' . $question_id . '_' . $key, $option_name) . '</span></div><input type="hidden" id="hidden_' . $question_id . '" value=' . $questionGroupIds . '>';
        print $result;
        // var_dump($option_name);
    }
    //}
}

?>
