
<?php

function yield_input($type,$qid,$qoptions,$user_response)
{
  $tresponse=null;
  $oresponse=null;
  $tstyle='display:none';
  if(!empty($user_response) AND count($user_response)==1) {
    $responses=DB::table('responses')->find($user_response);
    $tresponse=$responses->text_response;
    $oresponse=$responses->option_id;

  }
  else if($user_response!="")
  {
    foreach ($user_response as $key => $value) {
       $responses=DB::table('responses')->find($value);
       $oresponse[]=$responses->option_id;
    }
  }




  if(!empty($tresponse)) $tstyle='display:inherit';




  $result='';
  switch ($type) {
    case 'text':
        $result = '<input type="text" name="_' . $qid . '" value="' . $tresponse . '" class="form-control required" data-type="text">';
        break;

    case 'textarea':
        $result = '<textarea name="_' . $qid . '" class="form-control required" data-type="textarea">' . $tresponse . '</textarea>';
        break;

    case 'dropdown':
        $result = '<select name="_' . $qid . '" class="form-control required" data-type="dropdown">';
        $result .= '<option value="">Please Select</option>';
        foreach ($qoptions as $option_text => $qoption) {
            $selected = ($qoption == $oresponse) ? ' selected' : '';
            $result .= '<option value="' . $qoption . '"' . $selected . '>' . $option_text . '</option>';
        }
        $result .= '</select>';
        break;

    case 'radio':
        $option_count = 1;
        $result = '';
        foreach ($qoptions as $option_text => $qoption) {
            $checked = ($qoption == $oresponse) ? ' checked' : '';
            if (strtolower($option_text) == "others") {
                $result .= '<div class="option-subsection">';
                $result .= '<input type="radio" name="_' . $qid . '" id="radiolabel_' . $qid . '_' . $option_count . '" class="required op_radio" data-type="radio" value="' . $qoption . '"' . $checked . '>';
                $result .= '<label for="radiolabel_' . $qid . '_' . $option_count . '">' . $option_text . '</label>';
                $result .= '<textarea name="others_' . $qid . '" id="_' . $qid . '" class="others-textarea required form-control" rows="5" style="' . $tstyle . '" data-type="others-textarea">' . $tresponse . '</textarea>';
                $result .= '</div>';
            } else {
                $result .= '<div class="option-subsection">';
                $result .= '<input type="radio" name="_' . $qid . '" id="radiolabel_' . $qid . '_' . $option_count . '" class="required op_radio" data-type="radio" value="' . $qoption . '"' . $checked . '>';
                $result .= '<label for="radiolabel_' . $qid . '_' . $option_count . '">' . $option_text . '</label>';
                $result .= '</div>';
            }
            $option_count++;
        }
        break;

    case 'checkbox':
        $option_count = 1;
        $result = '';
        foreach ($qoptions as $option_text => $qoption) {
            if (is_array($oresponse)) {
                $checked = in_array($qoption, $oresponse) ? ' checked' : '';
            } else {
                $checked = ($qoption == $oresponse) ? ' checked' : '';
            }
            $result .= '<div class="option-subsection">';
            $result .= '<input type="checkbox" name="_' . $qid . '[]" id="checkboxlabel_' . $qid . '_' . $option_count . '" class="required" data-type="checkbox" value="' . $qoption . '"' . $checked . '>';
            $result .= '<label for="checkboxlabel_' . $qid . '_' . $option_count . '">' . $option_text . '</label>';
            $result .= '</div>';
            $option_count++;
        }
        break;

    default:
        $result = '';
        break;
}
  echo $result;
}


function yieldoptionforgrid($question_id, $i_option, $key, $user_survey_id, $option_label = '')
{
    $checked = false;

    // Get the response for this option
    $response = DB::table('responses')
        ->where('user_survey_respondent_id', $user_survey_id)
        ->where('option_id', $i_option)
        ->value('question_id');

    if ($response == $question_id) {
        $checked = true;
    }

    $result = '<div class="option-subsection option-subsection-grid">';
    $result .= '<input type="radio" name="_' . $question_id . '" value="' . $i_option . '" class="grid-required op_radio" id="radiolabel_' . $question_id . '_' . $key . '" data-type="grid"' . ($checked ? ' checked' : '') . '>';
    $result .= '<span class="grid_label"><label for="radiolabel_' . $question_id . '_' . $key . '">' . $option_label . '</label></span>';
    $result .= '</div>';

    print $result;
}



?>
