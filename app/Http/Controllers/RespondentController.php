<?php

namespace App\Http\Controllers;

use App\Addusers;
use App\Imports\AllRespondentImport;
use App\Imports\RespondentImport;
use App\Survey_rater;
use Arr;
use Auth;
use DB;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect as FacadesRedirect;
use Str;
use Redirect;
use Session;
class RespondentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $myFile = public_path('download/respondent.xls');

        $headers = ['Content-Type: application/vnd.ms-excel'];

        $newName = 'respondent-file-' . time() . '.xls';

        return response()->download($myFile, $newName, $headers);

    }

    public function respondent_download()
    {
        $myFile = public_path('download/all-respondent.xls');

        $headers = ['Content-Type: application/vnd.ms-excel'];

        $newName = 'respondents-file-' . time() . '.xls';

        return response()->download($myFile, $newName, $headers);

    }
    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $input = $request->all();

        $survey_id = $request->get('survey_id');
        $participant_id = $request->get('participant_id');

        $participant_email = DB::table('users')->where('id', $participant_id)->value('email');
        $rater_id = DB::table('survey_rater')->where('survey_id', $survey_id)
            ->join('rater', 'rater.id', '=', 'survey_rater.rater_id')->where('rater', '<>', 'self')->get();

        $raters = DB::table('rater')
            ->select('rater.id as rater_id', 'rater.rater')
            ->leftjoin('survey_rater', 'rater.id', '=', 'survey_rater.rater_id')
            ->where('survey_rater.survey_id', $survey_id)
            ->where('rater.id', '<>', 1)
            ->get();

        return view('admin.respondents.create')
            ->with('survey_id', $survey_id)
            ->with('rater_id', $rater_id)
            ->with('raters', $raters)
            ->with('participant_id', $participant_id)
            ->with('participant_email', $participant_email)
            ->with('title', 'Add Respondents to survey');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $input = $request->all();
        $email = $request->get('email');
        $input['email']=$email=strtolower($email);
//dd($input);
        Arr::forget($input, ['_token', 'survey_id', 'participant_id', 'rater']);

        $survey_id = $request->get('survey_id');
        $participant_id = $request->get('participant_id');

        $rater = $request->get('rater');

        //$rater_self=DB::table('rater')->where('rater','=',$rater)->value('id');

        $rater_survey_id = Survey_rater::firstOrCreate(['rater_id' => $rater, 'survey_id' => $survey_id]);

        $input['last_modified'] = date("Y-m-d H:i:s");

        $userinfo = DB::table('users')->select('password')->where('email', $email)->first('password');

        try {
            $password = decrypt($userinfo->password);
            $encrypted=$userinfo->password;
        } catch (\Exception $e) {
            $encrypted = encrypt(rand(1,100000000));
        }

        $input['password'] = $encrypted;

        $survey_status = 4;

        $date = date('Y-m-d H:i:s');

        $survey_exits = DB::table('surverys')->where('id', $survey_id)->where('start_date', '<', $date)->exists();

        if ($survey_exits) {
            $survey_status = 1;
        }

        $email = $request->get('email');
        $users_email = DB::table('users')->pluck('email')->toArray();

        $user_exists = DB::table('users')->where('email', $email)->exists();

        if ($user_exists) {
            DB::table('users')->where('email', $email)->update($input);
            $respondent_id = DB::table('users')->where('email', $email)->value('id');

            $user_survey_respondent = DB::table('user_survey_respondent')->where('respondent_id', $respondent_id)->where('participant_id', $participant_id)->where('survey_id', $survey_id)->exists();

            if (!$user_survey_respondent) {
                $survey_user_relation = [
                    'participant_id' => $participant_id,
                    'respondent_id' => $respondent_id,
                    'survey_id' => $survey_id,
                    'survey_status' => 1,
                    'rater_id' => $rater,
                ];
                DB::table('user_survey_respondent')->insert($survey_user_relation);
            }

        } else {
            $input['added_by'] = Auth::user()->id;

            $respondent_id = DB::table('users')->insertGetId($input);

            $survey_user_relation = [
                'participant_id' => $participant_id,
                'respondent_id' => $respondent_id,
                'survey_id' => $survey_id,
                'survey_status' => 1,
                'rater_id' => $rater,
            ];

            DB::table('user_survey_respondent')->insert($survey_user_relation);

        }

        return redirect()->route('respondent.show', [$participant_id, 'survey_id' => $survey_id]);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $input = $request->all();
        $survey_id = $request->get('survey_id');
//dd($id);
        //  $redirect=$request->get('redirect');
        // if($redirect=='home') Session::put('redirect','home');
        $users = DB::table('users')
            ->select('users.*', 'rater.rater', 'user_survey_respondent.survey_status', 'user_survey_respondent.id as user_survey_respondent_id', 'user_survey_respondent.notify_email_date')
            ->join('user_survey_respondent', 'users.id', '=', 'user_survey_respondent.respondent_id')
            ->join('rater', 'rater.id', '=', 'user_survey_respondent.rater_id')
            ->where('users.id', '>', 1)
            ->where('user_survey_respondent.respondent_id', '!=', 0)
            ->where('user_survey_respondent.participant_id', $id)
            ->where('user_survey_respondent.survey_id', $survey_id)->get();
//dd($users);
        //->paginate(5);

//dd($users);
        return view('admin.respondents.index')
            ->with('data', $users)
            ->with('participant_id', $id)
            ->with('survey_id', $survey_id)
            ->with('title', 'Listing Respondents to the survey');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {

        $survey_id = $request->get('survey_id');
        $participant_id = $request->get('participant_id');
        $user = Addusers::find($id);

        $rater_id = DB::table('user_survey_respondent')->where('survey_id', $survey_id)->where('respondent_id', $id)->value('rater_id');
        $rater = DB::table('rater')->where('id', $rater_id)->value('rater');

        $raters = DB::table('survey_rater')->where('survey_id', $survey_id)
            ->join('rater', 'rater.id', '=', 'survey_rater.rater_id')->where('rater', '<>', 'self')->where('rater.id', '<>', $rater_id)->get();

        return view('admin.respondents.edit', compact('user', 'rater', 'survey_id'))
            ->with('participant_id', $participant_id)
            ->with('respondent_id', $id)
            ->with('raters', $raters)
            ->with('rater_id', $rater_id)
            ->with('title', 'Updating Respondents to survey');
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

        $update = $request->all();

        $participant_id = $request->get('participant_id');
        Arr::forget($update, ['_token', '_method', 'survey_id', 'participant_id', 'rater']);
        $survey_id = $request->get('survey_id');
        $update['last_modified'] = date("Y-m-d H:i:s");
        $update['email']=strtolower($request->email);
        $check_same_email_others=DB::table('users')->where('id','<>',$id)->where('email',$update['email'])->first();
        $rater = $request->get('rater');
        if ($check_same_email_others) {
            DB::table('users')->where('id', $id)->delete();
            // DB::table('users')->where('id', $check_same_email_others->id)->update($update);
            DB::table('user_survey_respondent')
            ->where('survey_id', $survey_id)
            ->where('respondent_id',  $id)
            ->where('participant_id', $participant_id)
            ->update(['rater_id' => $rater,'respondent_id'=>$check_same_email_others->id]);
        }
        else{
            DB::table('users')->where('id', $id)->update($update);
            DB::table('user_survey_respondent')
            ->where('survey_id', $survey_id)
            ->where('respondent_id', $id)
            ->where('participant_id', $participant_id)
            ->update(['rater_id' => $rater]);
        }

        return redirect()->route('respondent.show', [$participant_id, 'survey_id' => $survey_id]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        /*Find and delete particular id from Database*/

        //Addusers::find($id)->delete();
        //DB::table('users')->where('id',$id)->delete();
        $survey_id = $request->get('survey_id');
        $respondent_id = $request->get('respondent_id');
        $participant_id = $request->get('participant_id');

        $user_survey_respondent = DB::table('user_survey_respondent')->where('participant_id', $participant_id)->where('respondent_id', $respondent_id)->where('survey_id', $survey_id)->value('id');

        DB::table('user_survey_respondent')->where('participant_id', $participant_id)->where('respondent_id', $respondent_id)->where('survey_id', $survey_id)->delete();

        $responses = DB::table('responses')->where('user_survey_respondent_id', $user_survey_respondent)->delete();

        /*Redirect to indexcontroller*/
        //  return redirect()->route('respondent.show',$survey_id);

        return redirect()->route('respondent.show', [$participant_id, 'survey_id' => $survey_id]);
    }
    public function reopen_survey(Request $request)
    {

        $survey_id = $request->get('survey_id');
        $respondent_id = $request->get('respondent_id');
        $participant_id = $request->get('participant_id');
        $datas = array('survey_status' => '2');
//dd( $survey_id);
        $result = DB::table('user_survey_respondent')->where('survey_id', $survey_id)->where('respondent_id', $respondent_id)->where('participant_id', $participant_id)->update($datas);
        $reopen_survey_message = "Survey Reopened successfully!";

        return redirect()->route('respondent.show', [$participant_id, 'survey_id' => $survey_id])->with('reopen_survey_message', $reopen_survey_message);
        //return back();
    }

    public function clear_response(Request $request)
    {

        $input = $request->all();
        $survey_id = $request->get('survey_id');
        $respondent_id = $request->get('respondent_id');
        $participant_id = $request->get('participant_id');
        $user_survey_respondent_id = DB::table('user_survey_respondent')->where('survey_id', $survey_id)->where('respondent_id', $respondent_id)->where('participant_id', $participant_id)->value('id');
        $result = DB::table('responses')->where('user_survey_respondent_id', $user_survey_respondent_id)->delete();
        $datas = array(
            'survey_status' => '1',
            'notify_email_date' => null,
            'reminder_email_date' => null,
            'last_submitted_date' => null,
        );
        $result = DB::table('user_survey_respondent')
            ->where('survey_id', $survey_id)
            ->where('respondent_id', $respondent_id)
            ->where('participant_id', $participant_id)
            ->update($datas);
        $clear_response_message = "Response cleared successfully!";

        return redirect()->route('respondent.show', [$participant_id, 'survey_id' => $survey_id])->with('clear_response_message', $clear_response_message);

    }

    public function checkduplicate($email, $key, $users)
    {
        $result = false;

        if ($email != null && $key != "" && count($users) > 0) {

            foreach ($users as $k => $v) {

                if ($k == $key) {
                    continue;
                }
                if ($v['r_email'] == $email) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;

    }

    public function importRespondent(Request $request)
    {

        $survey_id = $request->get('survey_id');
        $participant_id = $request->get('participant_id');
        $participant_email = DB::table('users')->where('id', $participant_id)->value('email');

        if ($request->hasFile('import_file')) {

            $import = new RespondentImport;
            Excel::import($import, $request->file('import_file'));

            $total_datas = (array) $import->DataContainer();

            if (count($total_datas) > 0) {
                $i = 2;
                $error = array();
                $r_fname=$this->CheckHeading($total_datas[0][0],'r_fname');
                $r_lname=$this->CheckHeading($total_datas[0][0],'r_lname');
                $r_email=$this->CheckHeading($total_datas[0][0],'r_email');
                $r_type=$this->CheckHeading($total_datas[0][0],'r_type');
                if ($r_fname && $r_lname && $r_email && $r_type) {
                    foreach ($total_datas[0] as $key => $value) {

                            $check_rater_exists = DB::table('survey_rater')->where('survey_id', $survey_id)->where('rater_id', $value['r_type'])->exists();

                            if (empty(trim($value['r_fname']))) {
                                $error[] = "Column First name found empty at line number " . $i;
                            }

                            if (empty(trim($value['r_email']))) {
                                $error[] = "Column Respondent Email found empty at line number " . $i;
                            } elseif (empty(filter_var($value['r_email'], FILTER_VALIDATE_EMAIL))) {
                                $error[] = "Column Respondent Email (".$value['r_email'].")  is not a valid email address at line number $i";
                            } elseif ($value['r_email'] == $participant_email) {
                                $error[] = "Column Respondent Email same as Participant Email  at line number " . $i;
                            }

                            if (empty(trim($value['r_type']))) {
                                $error[] = "Column Rater type found empty at line number " . $i;
                            } elseif ($value['r_type'] == 1) {
                                $error[] = "Column Rater_type self  not valid for respondent at line number " . $i;
                            } elseif (!$check_rater_exists) {
                                $error[] = "Column Rater_type is not valid at line number " . $i;
                            }

                            $checkduplicate = $this->checkduplicate($value['r_email'], $key, $total_datas[0]);

                            if ($checkduplicate) {
                                $error[] = $value['r_email'] . ' user are repeated at line number ' . $i;
                            }
                            $users[] = ['fname' => $value['r_fname'], 'lname' => $value['r_lname'], 'email' => $value['r_email'], 'rater_id' => $value['r_type']];
                        $i++;
                    }
                }
                else {
                    $header_mismatch = "header_mismatch";
                    $error[] = "Header Mismatch at line number 1 . Please follow the format(r_fname,r_lname,r_email,r_type).";
                }

            } else {
                $error[] = "No datas Found";
                $nodata_found = "nodata";
            }
        }
        if (isset($nodata_found) || isset($header_mismatch) || count($error)>0) {

            session()->flash('msg', $error);
            return redirect()->back();
        } else {

            $survey_id = $request->get('survey_id');
            $participant_id = $request->get('participant_id');
            $participant_email = DB::table('users')->where('id', $participant_id)->value('email');

            //       if(!empty($users)){

            foreach ($users as $insert_val) {
                $insert_val['email'] = strtolower($insert_val['email']);

                $check_rater_exists_in_survey = DB::table('survey_rater')->where('survey_id', $survey_id)->where('rater_id', $insert_val['rater_id'])->exists();

                if ($insert_val['rater_id'] && ($insert_val['rater_id'] != 1) && ($insert_val['email'] != $participant_email) && ($check_rater_exists_in_survey) && filter_var($insert_val['email'], FILTER_VALIDATE_EMAIL)) {

                    $insert_val['last_modified'] = date("Y-m-d H:i:s");
                    $user_exists = DB::table('users')->where('email', $insert_val['email'])->exists();
                    if ($insert_val['email'] != null && $insert_val['fname'] != null) {

                        $user_id = DB::table('users')->where('email', $insert_val['email'])->value('id');

                        if ($user_exists) {

                            // array_forget($insert_val,'rater_id');
                            // array_forget($insert_val,['rater_id']);

                            /*  DB::table('users')
                            ->where('id',$user_id)
                            ->update([
                            'fname'=>$insert_val['fname'],
                            'lname'=>$insert_val['lname'],
                            'email'=>$insert_val['email'],
                            'last_modified'=>$insert_val['last_modified']
                            ]);*/

                            $user_survey_respondent_exists = DB::table('user_survey_respondent')
                                ->where('participant_id', $participant_id)
                            //->where('rater_id',$insert_val['rater_id'])
                                ->where('respondent_id', $user_id)
                                ->where('survey_id', $survey_id)->exists();
                            $user_survey_respondent_id = DB::table('user_survey_respondent')
                                ->where('participant_id', $participant_id)
                                ->where('respondent_id', $user_id)
                            //->where('rater_id',$insert_val['rater_id'])
                                ->where('survey_id', $survey_id)->value('id');

                            if (!$user_survey_respondent_exists) {

                                $survey_status = 4;

                                $date = date('Y-m-d H:i:s');

                                $survey_exits = DB::table('surverys')->where('id', $survey_id)->where('start_date', '<', $date)->exists();

                                if ($survey_exits) {
                                    $survey_status = 1;
                                }

                                $survey_user_relation = [
                                    'participant_id' => $participant_id,
                                    'survey_id' => $survey_id,
                                    'survey_status' => 1,
                                    'rater_id' => $insert_val['rater_id'],
                                    'respondent_id' => $user_id,
                                ];

                                // var_dump($survey_user_relation);
                                DB::table('user_survey_respondent')->insert($survey_user_relation);
                            } else {
                                $survey_status = 4;

                                $date = date('Y-m-d H:i:s');

                                $survey_exits = DB::table('surverys')->where('id', $survey_id)->where('start_date', '<', $date)->exists();

                                if ($survey_exits) {
                                    $survey_status = 1;
                                }

                                $survey_user_relation = DB::table('user_survey_respondent')
                                    ->where('id', $user_survey_respondent_id)
                                    ->update([
                                        'participant_id' => $participant_id,
                                        'survey_id' => $survey_id,
                                        'survey_status' => 1,
                                        'rater_id' => $insert_val['rater_id'],
                                        'respondent_id' => $user_id,
                                    ]);
                            }

                        } else {
                            $insert_val['added_by'] = Auth::user()->id;
                            $userid = DB::table('users')->insertGetId([
                                'fname' => $insert_val['fname'],
                                'lname' => $insert_val['lname'],
                                'email' => $insert_val['email'],
                                'password' => encrypt(rand(1,100000000)),
                                'last_modified' => $insert_val['last_modified'],
                                'added_by' => $insert_val['added_by'],
                            ]);

                            $survey_status = 4;

                            $date = date('Y-m-d H:i:s');

                            $survey_exits = DB::table('surverys')->where('id', $survey_id)->where('start_date', '<', $date)->exists();

                            if ($survey_exits) {
                                $survey_status = 1;
                            }

                            $survey_user_relation = [
                                'participant_id' => $participant_id,
                                'survey_id' => $survey_id,
                                'survey_status' => 1,
                                'rater_id' => $insert_val['rater_id'],
                                'respondent_id' => $userid,
                            ];

                            DB::table('user_survey_respondent')->insert($survey_user_relation);

                        }

                    }

                }

            }

            if (isset($error) and !empty($error)) {
                $message = $error;
                $mess_data = "error";
            } else {
                $message = "Datas are imported successfully!";
                $mess_data = "success";
            }

            session()->flash('mess_data', $mess_data);
            return redirect()->route('respondent.show', [$participant_id, 'survey_id' => $survey_id])->with('msg', $message);

        }

    }

    public function only_importrespondent(Request $request)
    {

        $input = $request->all();

        $survey_id = $request->get('survey_id');
        $raters = DB::table('rater')
            ->select('rater.id as rater_id', 'rater.rater')
            ->leftjoin('survey_rater', 'rater.id', '=', 'survey_rater.rater_id')
            ->where('survey_rater.survey_id', $survey_id)
            ->where('rater.id', '<>', 1)
            ->get();

        // $respondent_id=$request->get('respondent_id');
        //$participant_id=$request->get('participant_id');
        //$user_survey_respondent_id=DB::table('user_survey_respondent')->where('survey_id',$survey_id)->where('respondent_id',$respondent_id)->where('participant_id',$participant_id)->value('id');

        return view('admin.respondents.import', ['survey_id' => $survey_id])->with('raters', $raters)
            ->with('title', 'Import Respondents to the survey');
    }
    public function CheckHeading($headings,$key)
    {
        return  Arr::has($headings, $key);

    }
    public function array_has_dupes($arr) {
        $dups = array();
        foreach ($arr as $par_email => $value) {
            $repeat_array=array_diff_assoc($value, array_unique($value));
            foreach ($repeat_array as $key => $email_array) {
                $dups[] =$par_email . ' user are repeated in participant '.$email_array.' at line number ' . ($key+1);
            }
        }
        return $dups;
     }
    public function checkDuplicateEmails($datas)
    {
        $error_email=array();
        foreach ($datas as $key => $email_data) {
            $error_email[$email_data['r_email']][$key]=$email_data['p_email'];
        }
        $check_dup=$this->array_has_dupes($error_email,$key);
        return $check_dup;
    }
    public function only_importrespondent_store(Request $request)
    {
        $survey_id = $request->get('survey_id');

        /*Get input File*/
        if ($request->hasFile('import_file')) {

            $import = new AllRespondentImport;
            Excel::import($import, $request->file('import_file'));
            $total_datas = $import->DataContainer();
            $update = array();
            $insert = array();
            $users = array();
            $valid_datas = array();

            if (count($total_datas) > 0) {
                $i = 2;
                $error = array();
                $p_email=$this->CheckHeading($total_datas[0],'p_email');
                $r_fname=$this->CheckHeading($total_datas[0],'r_fname');
                $r_lname=$this->CheckHeading($total_datas[0],'r_lname');
                $r_email=$this->CheckHeading($total_datas[0],'r_email');
                $r_type=$this->CheckHeading($total_datas[0],'r_type');

                if ($p_email && $r_fname && $r_lname && $r_email && $r_type) {
                    foreach ($total_datas as $key=>$value) {
                            $check_rater_exists = DB::table('survey_rater')->where('survey_id', $survey_id)->where('rater_id', $value['r_type'])->exists();
                            $check_participant_exists = DB::table('user_survey_respondent')->join('users', 'users.id', '=', 'user_survey_respondent.participant_id')->where('survey_id', $survey_id)->where('users.email', $value['p_email'])->exists();
                            if (empty(trim($value['p_email']))) {
                                $error[] = "Column Participant Email found empty at line number $i";
                            } elseif (empty(filter_var($value['p_email'], FILTER_VALIDATE_EMAIL))) {
                                $error[] = "Column Participant Email (" . $value['p_email'] . ") is not a valid email address at line number $i";
                            } elseif (!$check_participant_exists) {
                                $error[] = "Column Participant email is not valid for this survey at line number " . $i;
                            }
                            if (empty(trim($value['r_fname']))) {
                                $error[] = "Column First name found empty at line number " . $i;
                            }
                            if (empty(trim($value['r_email']))) {
                                $error[] = "Column Respondent Email found empty at line number " . $i;
                            } elseif (empty(filter_var($value['r_email'], FILTER_VALIDATE_EMAIL))) {
                                $error[] = "Column Respondent Email (" . $value['r_email'] . ")  is not a valid email address at line number $i";
                            } elseif ($value['r_email'] == $value['p_email']) {
                                $error[] = "Column Respondent Email same as Participant Email  at line number " . $i;
                            }

                            if (empty(trim($value['r_type']))) {
                                $error[] = "Column Rater type found empty at line number " . $i;
                            } elseif ($value['r_type'] == 1) {
                                $error[] = "Column Rater type self  not valid for respondent at line number " . $i;
                            } elseif (!$check_rater_exists) {
                                $error[] = "Column Rater type is not valid at line number " . $i;
                            }
                           /*  $checkduplicate = $this->checkduplicate($value['r_email'], $key, $total_datas);

                            if ($checkduplicate) {
                                $error[] = $value['r_email'] . ' user are repeated at line number ' . $i;
                            } */
                            if ($check_participant_exists) {
                                if ($value['r_lname'] != '') {
                                    $last_name = $value['r_lname'];
                                } else {
                                    $last_name = '';
                                }
                                $users[] = ['fname' => $value['r_fname'], 'lname' => $last_name, 'email' => $value['r_email'], 'rater_id' => $value['r_type'], 'p_email' => $value['p_email']];
                                array_push($valid_datas, 1);
                            }
                        $i++;
                    }
                }
                else {
                    $header_mismatch = "header_mismatch";
                    $error[] = "Header Mismatch at line number 1 . Please follow the format (p_email,r_fname,r_lname,r_email,r_type).";

                }
            } else {
                $error[] = "No datas Found";
                $nodata_found = "nodata";
            }
        }


        $toal_valid_datas = array_sum($valid_datas);
        $email_error=$this->checkDuplicateEmails($total_datas);
        $error=array_merge($error,$email_error);
        if (isset($nodata_found) || isset($header_mismatch) || count($error)>0) {
            session()->flash('msg', $error);
            return redirect()->back();
        } else {
            $update_count=0;
            $updated_emails=array();
            foreach ($users as $insert_val) {
                $check_rater_exists_in_survey = DB::table('survey_rater')->where('survey_id', $survey_id)->where('rater_id', $insert_val['rater_id'])->exists();

                $participant_exists = DB::table('users')->where('email', $insert_val['p_email'])->exists();

                if ($insert_val['p_email'] && $insert_val['rater_id'] && ($insert_val['rater_id'] != 1) && ($check_rater_exists_in_survey) && ($insert_val['p_email'] != $insert_val['email']) && ($participant_exists) && filter_var($insert_val['p_email'], FILTER_VALIDATE_EMAIL) && filter_var($insert_val['email'], FILTER_VALIDATE_EMAIL)) {
                    $insert_val['last_modified'] = date("Y-m-d H:i:s");

                    $user_exists = DB::table('users')->where('email', $insert_val['email'])->exists();
                    if ($insert_val['email'] != null && $insert_val['fname'] != null) {
                        $insert_val['email'] = strtolower($insert_val['email']);
                        $user_id = DB::table('users')->where('email', $insert_val['email'])->value('id');

                        if ($user_exists) {

                            // array_forget($insert_val,'rater_id');
                            // array_forget($insert_val,['rater_id']);

                            $update_user = DB::table('users')
                                ->where('id', $user_id)
                                ->update([
                                    'fname' => $insert_val['fname'],
                                    'lname' => $insert_val['lname'],
                                    'email' => $insert_val['email'],
                                    'last_modified' => $insert_val['last_modified'],
                                ]);

                            $participant_id = DB::table('users')->where('email', $insert_val['p_email'])->value('id');

                            $user_survey_respondent_exists = DB::table('user_survey_respondent')
                                ->where('participant_id', $participant_id)
                                ->where('respondent_id', $user_id)
                            //->where('rater_id',$insert_val['rater_id'])
                                ->where('survey_id', $survey_id)->exists();

                            $user_survey_respondent_id = DB::table('user_survey_respondent')
                                ->where('participant_id', $participant_id)
                                ->where('respondent_id', $user_id)
                            //->where('rater_id',$insert_val['rater_id'])
                                ->where('survey_id', $survey_id)->value('id');

                            if (!$user_survey_respondent_exists) {
                                $survey_status = 4;

                                $date = date('Y-m-d H:i:s');

                                $survey_exits = DB::table('surverys')->where('id', $survey_id)->where('start_date', '<', $date)->exists();

                                if ($survey_exits) {
                                    $survey_status = 1;
                                }

                                $survey_user_relation = [
                                    'participant_id' => $participant_id,
                                    'survey_id' => $survey_id,
                                    'survey_status' => 1,
                                    'rater_id' => $insert_val['rater_id'],
                                    'respondent_id' => $user_id,
                                ];

                                DB::table('user_survey_respondent')->insert($survey_user_relation);
                            } else {
                                $survey_status = 4;

                                $date = date('Y-m-d H:i:s');

                                $survey_exits = DB::table('surverys')->where('id', $survey_id)->where('start_date', '<', $date)->exists();

                                if ($survey_exits) {
                                    $survey_status = 1;
                                }

                                $survey_user_relation = DB::table('user_survey_respondent')
                                    ->where('id', $user_survey_respondent_id)
                                    ->update([
                                        'participant_id' => $participant_id,
                                        'survey_id' => $survey_id,
                                        'survey_status' => 1,
                                        'rater_id' => $insert_val['rater_id'],
                                        'respondent_id' => $user_id,
                                    ]);
                            }
                            $update_count++;
                            array_push($updated_emails,$insert_val['email']);
                        } else {
                            $insert_val['added_by'] = Auth::user()->id;
                            $userid = DB::table('users')
                                      ->insertGetId([
                                        'fname' => $insert_val['fname'],
                                        'lname' => $insert_val['lname'],
                                        'email' => $insert_val['email'],
                                        'last_modified' => $insert_val['last_modified'],
                                        'added_by' => $insert_val['added_by'],
                                        'password'=>encrypt(rand(1,100000000))
                                    ]);

                            $survey_status = 4;

                            $date = date('Y-m-d H:i:s');

                            $survey_exits = DB::table('surverys')->where('id', $survey_id)->where('start_date', '<', $date)->exists();

                            if ($survey_exits) {
                                $survey_status = 1;
                            }

                            $participant_id = DB::table('users')->where('email', $insert_val['p_email'])->value('id');

                            $survey_user_relation = [
                                'participant_id' => $participant_id,
                                'survey_id' => $survey_id,
                                'survey_status' => 1,
                                'rater_id' => $insert_val['rater_id'],
                                'respondent_id' => $userid,
                            ];

                            DB::table('user_survey_respondent')->insert($survey_user_relation);
                        }

                    }

                }
            }

            if (isset($error) and !empty($error)) {
                $message = $error;
                $mess_data = "error";
            } else {
                $message = "Totally $toal_valid_datas imported!";
                $mess_data = "success";
                $updated_users=0;
                $updated_emails;
                Session::flash('updated_users',$updated_emails);
            }

            session()->flash('mess_data', $mess_data);
            return redirect()->route('addusers.show', $survey_id)->with('msg', $message);

        }

    }

//check whether the email id same for respondent and  participant
    public function checkparticipant(Request $request)
    {

        $survey_id = $request->get('survey_id');
        $participant_id = $request->get('participant_id');
        $email = $request->get('email');
        $respondent_id = $request->get('respondent_id');
        $participant_email = DB::table('users')->where('id', $participant_id)->value('email');

        $currenct_relation_id = DB::table('user_survey_respondent')
            ->join('users', 'user_survey_respondent.respondent_id', 'users.id')
            ->where('participant_id', $participant_id)
            ->where('respondent_id', $respondent_id)
            ->where('survey_id', $survey_id)
            ->value('user_survey_respondent.id');
        $respondent_email = DB::table('user_survey_respondent')
            ->join('users', 'user_survey_respondent.respondent_id', 'users.id')
            ->where('participant_id', $participant_id)
            ->where('respondent_id', '>', 0)
            ->where('survey_id', $survey_id);
        if ($request->from == "edit_respondent") {
            $respondent_email->where('user_survey_respondent.id', '<>', $currenct_relation_id);
            $respondent_email->where('users.email', $email);
        }
        $respondent_email = $respondent_email->pluck('email')->toArray();

        if (in_array($email, $respondent_email)) {
            $isAvailable = false;
        } else {
            $isAvailable = true;
        }
        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }
    public function DeleteRespondents(Request $request)
    {
        $survey_id = $request->get('survey_id');
        $userids = $request->get('userids');

        foreach ($userids as $key => $id) {
            $id_explode=explode('~',$id);
            $participant_id=$id_explode[1];
            $respondent_id=$id_explode[0];
            $user_survey_respondent = DB::table('user_survey_respondent')
                ->where('participant_id', $participant_id)
                ->where('respondent_id', $respondent_id)
                ->where('survey_id', $survey_id)
                ->pluck('id')
                ->toArray();
            DB::table('user_survey_respondent')->where('participant_id', $participant_id)->where('respondent_id', $respondent_id)->where('survey_id', $survey_id)->delete();
            foreach ($user_survey_respondent as $user_survey_respondent_id) {
                if (isset($user_survey_respondent_id['id'])) {
                    $responses = DB::table('responses')
                        ->where('user_survey_respondent_id', $user_survey_respondent_id['id'])
                        ->delete();
                }
            }
        }

        Session::flash('success_msg', 'Participants deleted successfully');
        return ['status'=>true];
    }
    public function SingleDeleteRespondent(Request $request)
    {
        $survey_id = $request->get('survey_id');
        $respondent_id = $request->get('respondent_id');
        $participant_id = $request->get('participant_id');
        DB::table('user_survey_respondent')->where('participant_id', $participant_id)->where('respondent_id', $respondent_id)->where('survey_id', $survey_id)->delete();
        $user_survey_respondent = DB::table('user_survey_respondent')
            ->where('participant_id', $participant_id)
            ->where('respondent_id', $respondent_id)
            ->where('survey_id', $survey_id)
            ->first();

            if ($user_survey_respondent) {
                DB::table('responses')
                    ->where('user_survey_respondent_id', $user_survey_respondent->id)
                    ->delete();
            }

            Session::flash('success_msg', 'Participant deleted successfully');
            return Redirect::back();
    }

}
