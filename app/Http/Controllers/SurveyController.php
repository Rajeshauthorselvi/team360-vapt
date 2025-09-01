<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Rater;
use App\Survey_rater;
use Arr;
use DB;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;
use Validator;

class SurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function checkSurvey(Request $request)
    {

        $url = $request->get('url');
        $if_check = DB::table('surverys')->where('url', $url)->exists();

        if ($if_check == true) {
            $isAvailable = false;
        } else {
            $isAvailable = true;
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }
    public function index()
    {
        $active_survey_details = DB::table('surverys')->whereRaw('end_date > now()')->orderby('end_date', 'DESC')->get();
        $closed_survey_details = DB::table('surverys')->whereRaw('end_date < now()')->orderby('end_date', 'DESC')->get();
        return view('admin.dashboard')
            ->with('active_survey_details', $active_survey_details)
            ->with('closed_survey_details', $closed_survey_details)
            ->with('title', 'Home');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.survey.create')
            ->with('title', 'New Survey');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = array(
            'title' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            // 'logo'=>'required|mimes:jpeg,jpg,png|dimensions:max_width=400',
            //'right_logo'=>'mimes:jpeg,jpg,png|dimensions:max_width=400',
            'url' => 'required|unique:surverys,url',
            'send_email_from' => 'required|email',
        );
        $input = $request->all();
//
        $multi_rater_value = $request['rater_value'];
        $validator = Validator::make($input, $rules);
        if ($validator->passes()) {

// $rater_id=DB::table('rater')->firstOrCreate(['rater'=>$request->rater]);

            Arr::forget($input, ['_token', 'logo', 'right_logo', '_wysihtml5_mode', 'rater', 'rater_value']);
            // array_forget($input,['_token','logo','right_logo','_wysihtml5_mode','rater','rater_value']);
            $input['logo'] = '';
            $input['right_logo'] = '';
            $input['survey_theme_id'] = 1;

            $input['sender_name']=$request->sender_name;
            $dtparts = explode(" ", $input['start_date']);
            $time = date("H:i:s", strtotime($dtparts[1] . $dtparts[2]));
            $date = str_replace('/', '-', $dtparts[0]);
            $start_date = date('Y-m-d', strtotime($date)) . " " . $time;

            $dtparts = explode(" ", $input['end_date']);
            $time = date("H:i:s", strtotime($dtparts[1] . $dtparts[2]));
            $date = str_replace('/', '-', $dtparts[0]);
            $end_date = date('Y-m-d', strtotime($date)) . " " . $time;

            $input['start_date'] = $start_date;
            $input['end_date'] = $end_date;

            $survey_id = DB::table('surverys')->insertGetId($input);

            $self_exits = DB::table('rater')->where('rater', 'self')->exists();

            if (isset($self_exits)) {

                $insert_self = Rater::firstOrCreate(['rater' => 'self']);

                $rater_survey = DB::table('survey_rater')->insert(['rater_id' => $insert_self->id, 'survey_id' => $survey_id]);

            }

//$rater=DB::table('rater')->insert($survey_user_relation);

            foreach ($multi_rater_value as $key => $value) {

                $rater_id = Rater::firstOrCreate(['rater' => $value]);
                $rater_survey_id = Survey_rater::firstOrCreate(['rater_id' => $rater_id->id, 'survey_id' => $survey_id]);
            }

            // $rater_survey_id=DB::table('survey_rater')->insertGetId(['rater_id'=>$rater_id->id,'survey_id'=>$survey_id]);

            if ($request->hasFile('logo')) {
                $file_extension = $request->file('logo')->getClientOriginalExtension();
                $image_name = $survey_id . "." . $file_extension;
                $request->logo->move(public_path('storage/surveys/'), $image_name);
                //$path=public_path('storage/surveys')."/".$image_name;
                DB::table('surverys')->where('id', $survey_id)->update(['logo' => $image_name]);
            }

            if ($request->hasFile('right_logo')) {
                $file_extension_right = $request->file('right_logo')->getClientOriginalExtension();
                $image_name_right = $survey_id . "-right." . $file_extension_right;
                $request->right_logo->move(public_path('storage/surveys/'), $image_name_right);
                //$path=public_path('storage/surveys')."/".$image_name;
                DB::table('surverys')->where('id', $survey_id)->update(['right_logo' => $image_name_right]);
            }

            return redirect()->route('questions.show', $survey_id);

        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $survey = DB::table('surverys')->find($id);

        $raters = DB::table('survey_rater')
            ->leftjoin('rater', 'survey_rater.rater_id', '=', 'rater.id')
            ->where('survey_rater.survey_id', $id)
            ->get();
        // dd($survey);
        return view('admin.survey.copycreate')
            ->with('data', $survey)
            ->with('raters', $raters)
            ->with('title', 'Copy & Create Survey');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        $redirect = $request->get('redirect');
        if ($redirect == "home") {
            return redirect()->route('admin.dashboard');
        }

        $survey = DB::table('surverys')->find($id);

        $raters = DB::table('survey_rater')
            ->leftjoin('rater', 'survey_rater.rater_id', '=', 'rater.id')
            ->where('survey_rater.survey_id', $id)
            ->get();

        return view('admin.survey.edit')
            ->with('data', $survey)
            ->with('raters', $raters)
            ->with('title', 'Update Survey');
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $rules = array(
            'title' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            //'logo'=>'required|mimes:jpeg,jpg,png',
            'url' => 'required|unique:surverys,url,' . $id,
            'send_email_from' => 'required|email',
        );
        $input = $request->all();
        $multi_rater_value = $request['rater_value'];

        $participant_rater_manage = $request->get('participant_rater_manage');
        if (is_null($participant_rater_manage)) {$participant_rater_manage = '0';} else { $participant_rater_manage = '1';}
        $input['participant_rater_manage'] = $participant_rater_manage;

        $dimension_hide = $request->get('dimension_hide');
        if (is_null($dimension_hide)) {$dimension_hide = '0';} else { $dimension_hide = '1';}
        $input['dimension_hide'] = $dimension_hide;

        $validator = Validator::make($input, $rules);
        if ($validator->passes()) {
            Arr::forget($input, ['_token', 'logo', '_method', 'rater', 'right_logo', 'llogo_path', 'rlogo_path', '_wysihtml5_mode', 'rater_value']);

            $dtparts = explode(" ", $input['start_date']);
            $time = date("H:i:s", strtotime($dtparts[1] . $dtparts[2]));
            $date = str_replace('/', '-', $dtparts[0]);
            $start_date = date('Y-m-d', strtotime($date)) . " " . $time;

            $dtparts = explode(" ", $input['end_date']);
            $time = date("H:i:s", strtotime($dtparts[1] . $dtparts[2]));
            $date = str_replace('/', '-', $dtparts[0]);
            $end_date = date('Y-m-d', strtotime($date)) . " " . $time;
            $input['start_date'] = $start_date;
            $input['end_date'] = $end_date;
            DB::table('survey_rater')->where('survey_id', $id)->delete();
            foreach ($multi_rater_value as $key => $value) {
                $rater_id = Rater::firstOrCreate(['rater' => $value]);
                $rater_survey_id = Survey_rater::firstOrCreate(['rater_id' => $rater_id->id, 'survey_id' => $id]);
            }

            DB::table('surverys')->where('id', $id)->update($input);
            $change_status = DB::table('surverys')
                ->select('surverys.end_date', 'surverys.start_date', 'user_survey_respondent.survey_status', 'user_survey_respondent.survey_id', 'user_survey_respondent.id as usr_id')
                ->leftjoin('user_survey_respondent', 'surverys.id', 'user_survey_respondent.survey_id')
                ->where('surverys.id', $id)
                ->get();
/*
foreach ($change_status as $key => $values_status) {
$date = date('Y-m-d H:i:s');
if ($date > $values_status->end_date) {
if ($values_status->survey_status != "3" || $values_status->survey_status != 2) {
DB::table('user_survey_respondent')
->where('survey_id', $values_status->survey_id)
->where('survey_status', '!=', '3')
->where('survey_status', '!=', '2')
->update(['survey_status' => 0]);
}
} elseif ($date < $values_status->start_date) {
if ($values_status->survey_status != "3" || $values_status->survey_status != 2) {
DB::table('user_survey_respondent')
->where('survey_id', $values_status->survey_id)
->where('survey_status', '!=', '3')
->where('survey_status', '!=', '2')
->update(['survey_status' => 4]);
}
} else {
if ($values_status->survey_status != "3" || $values_status->survey_status != 2) {
DB::table('user_survey_respondent')
->where('survey_id', $values_status->survey_id)
->where('survey_status', '!=', '3')
->where('survey_status', '!=', '2')
->update(['survey_status' => 1]);
}
}
}
 */
            if ($request->hasFile('logo')) {

                $survey_details = DB::table('surverys')->find($id);
                $destinationPath = storage_path('app') . '/' . $survey_details->logo;
                if (File::exists($destinationPath)) {
                    File::delete($destinationPath);
                }
                $file_extension = $request->file('logo')->getClientOriginalExtension();
                $image_name = $id . "." . $file_extension;
                $request->logo->move(public_path('storage/surveys/'), $image_name);
                DB::table('surverys')->where('id', $id)->update(['logo' => $image_name]);
            }
            if ($request->hasFile('right_logo')) {
                $file_extension_right = $request->file('right_logo')->getClientOriginalExtension();
                $image_name_right = $id . "-right." . $file_extension_right;
                $request->right_logo->move(public_path('storage/surveys/'), $image_name_right);
                //$path=public_path('storage/surveys')."/".$image_name;
                DB::table('surverys')->where('id', $id)->update(['right_logo' => $image_name_right]);
            }

            return redirect()->route('questions.show', $id);
            //~ return redirect()->route('admin.dashboard');

        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function dashboard()
    {
/*
        $change_status = DB::table('surverys')
            ->select('surverys.end_date', 'surverys.start_date', 'user_survey_respondent.survey_status', 'user_survey_respondent.survey_id')
            ->leftjoin('user_survey_respondent', 'surverys.id', 'user_survey_respondent.survey_id')
            ->get();

        foreach ($change_status as $key => $values_status) {
            $date = date('Y-m-d H:i:s');
            //  $current_date=date('Y-m-d');
            //  $start_date=  date('Y-m-d', strtotime($values_status->start_date));
            //  $end_date  =  date('Y-m-d', strtotime($values_status->end_date));

            if ($date > $values_status->end_date) {
                if ($values_status->survey_status != "3" || $values_status->survey_status != 2) {
                    DB::table('user_survey_respondent')
                        ->where('survey_id', $values_status->survey_id)
                        ->where('survey_status', '!=', '3')
                        ->where('survey_status', '!=', '2')
                        ->update(['survey_status' => 0]);
                }
            } elseif ($date < $values_status->start_date) {
                if ($values_status->survey_status != "3" || $values_status->survey_status != 2) {
                    DB::table('user_survey_respondent')
                        ->where('survey_id', $values_status->survey_id)
                        ->where('survey_status', '!=', '3')
                        ->where('survey_status', '!=', '2')
                        ->update(['survey_status' => 4]);
                }
            } else {
                if ($values_status->survey_status != "3" || $values_status->survey_status != 2) {
                    DB::table('user_survey_respondent')
                        ->where('survey_id', $values_status->survey_id)
                        ->where('survey_status', '!=', '3')
                        ->where('survey_status', '!=', '2')
                        ->update(['survey_status' => 1]);
                }
            }

        }
*/
        $active_survey_details = DB::table('surverys')->whereRaw('end_date > now()')->orderby('id', 'DESC')->get();
        $closed_survey_details = DB::table('surverys')->whereRaw('end_date < now()')->orderby('id', 'DESC')->get();
        return view('admin.dashboard')
            ->with('active_survey_details', $active_survey_details)
            ->with('closed_survey_details', $closed_survey_details)
            ->with('title', 'Home');
    }

    public function copysurvey(Request $request)
    {
        $rules = array(
            'title' => 'required',
            'start_date' => 'required',
            'end_date' => 'required',
            //'logo'=>'required|mimes:jpeg,jpg,png',
            'url' => 'required|unique:surverys,url',
            'send_email_from' => 'required|email',
        );
        $input = $request->all();
        $multi_rater_value = $request['rater_value'];

        $participant_rater_manage = $request->get('participant_rater_manage');
        if (is_null($participant_rater_manage)) {$participant_rater_manage = '0';} else { $participant_rater_manage = '1';}
        $input['participant_rater_manage'] = $participant_rater_manage;

        $dimension_hide = $request->get('dimension_hide');
        if (is_null($dimension_hide)) {$dimension_hide = '0';} else { $dimension_hide = '1';}
        $input['dimension_hide'] = $dimension_hide;

//dd($participant_rater_manage);

        $validator = Validator::make($input, $rules);
        if ($validator->passes()) {

            Arr::forget($input, ['_token', 'logo', '_method', 'right_logo', '_wysihtml5_mode', 'rater', 'rater_value']);

            $dtparts = explode(" ", $input['start_date']);
            $time = date("H:i:s", strtotime($dtparts[1] . $dtparts[2]));
            $date = str_replace('/', '-', $dtparts[0]);
            $start_date = date('Y-m-d', strtotime($date)) . " " . $time;

            $dtparts = explode(" ", $input['end_date']);
            $time = date("H:i:s", strtotime($dtparts[1] . $dtparts[2]));
            $date = str_replace('/', '-', $dtparts[0]);
            $end_date = date('Y-m-d', strtotime($date)) . " " . $time;

            $input['start_date'] = $start_date;
            $input['end_date'] = $end_date;

            //~ $survey_id=DB::table('surverys')->insertGetId($input);
            $survey_id = DB::table('surverys')->insertGetId([
                'title' => $input['title'],
                'start_date' => $input['start_date'],
                'end_date' => $input['end_date'],
                'send_email_from' => $input['send_email_from'],
                'url' => $input['url'],
                'participant_rater_manage' => $input['participant_rater_manage'],
                'dimension_hide' => $input['dimension_hide'],
                'logo' => $input['llogo_path'],
                'right_logo' => $input['rlogo_path'],
            ]);

            foreach ($multi_rater_value as $key => $value) {

                $rater_id = Rater::firstOrCreate(['rater' => $value]);
                $rater_survey_id = Survey_rater::firstOrCreate(['rater_id' => $rater_id->id, 'survey_id' => $survey_id]);
            }

            if ($request->hasFile('logo')) {
                $file_extension = $request->file('logo')->getClientOriginalExtension();
                $image_name = $survey_id . "." . $file_extension;
                $request->logo->move(public_path('storage/surveys/'), $image_name);
                //$path=public_path('storage/surveys')."/".$image_name;
                DB::table('surverys')->where('id', $survey_id)->update(['logo' => $image_name]);

            }

            if ($request->hasFile('right_logo')) {
                $file_extension_right = $request->file('right_logo')->getClientOriginalExtension();
                $image_name_right = $survey_id . "-right." . $file_extension_right;
                $request->right_logo->move(public_path('storage/surveys/'), $image_name_right);
                //$path=public_path('storage/surveys')."/".$image_name;
                DB::table('surverys')->where('id', $survey_id)->update(['right_logo' => $image_name_right]);
            }

            $questions = DB::table('questions')->where('survey_id', $input['survey_id'])->get();
            foreach ($questions as $question) {
                $question_insert = DB::table('questions')
                    ->insertGetId(['survey_id' => $survey_id,
                        'question_text' => $question->question_text,
                        'question_type' => $question->question_type,
                        'question_required' => $question->question_required,
                        //'question_enabled'=>$question->question_enabled,
                        'question_dimension' => $question->question_dimension,
                        'display_order' => $question->display_order,
                    ]);

                $options = DB::table('options')->where('question_id', $question->id)->get();
                foreach ($options as $option_values) {
                    $option_insert = DB::table('options')
                        ->insert([
                            'option_text' => $option_values->option_text,
                            'option_weight' => $option_values->option_weight,
                            'question_id' => $question_insert,
                        ]);
                }
            }

            $survey_theme_id = DB::table('surverys')->where('id', $input['survey_id'])->value('survey_theme_id');
            $theme_update = DB::table('surverys')->where('id', $survey_id)->update(['survey_theme_id' => $survey_theme_id]);

            return redirect()->route('questions.show', $survey_id);

        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }

    }
    public function autoComplete(Request $request)
    {
        $query = $request->get('term', '');

        $Rater = Rater::where('rater', 'LIKE', '%' . $query . '%')->get();

        $data = array();
        foreach ($Rater as $rater) {
            $data[] = array('value' => $rater->rater, 'id' => $rater->id);
        }
        if (count($data)) {
            return $data;
        } else {
            return ['value' => 'No Result Found', 'id' => ''];
        }

    }
    public function DeleteSurvey(Request $request)
    {
        $survey_id = $request->survey_id;
        $survey_details=DB::table('surverys')->where('id', $survey_id)->first();
        $left_logo=public_path('storage/surveys/').$survey_details->logo;
        $right_logo=public_path('storage/surveys/').$survey_details->right_logo;
        if(File::exists($left_logo)){
            File::delete($left_logo);
        }
        if(File::exists($right_logo)){
            File::delete($right_logo);
        }
        DB::table('surverys')->where('id', $survey_id)->delete();
        Survey_rater::where('id', $survey_id)->delete();
        $questions = DB::table('questions')->where('survey_id', $survey_id)->get();
        foreach ($questions as $question) {
            $options = DB::table('options')->where('question_id', $question->id)->delete();
        }
        DB::table('questions')->where('survey_id', $survey_id)->delete();
        return Redirect::back();
    }
}
