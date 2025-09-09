<?php

namespace App\Http\Controllers;

use App\Models\Addusers;
use App\Imports\ParticipantImport;
use App\Models\Survey_rater;
use Arr;
use Auth;
use Excel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Redirect;
use Session;
use Str;

class AddusersController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $myFile = public_path('download/users.xls');

        $headers = ['Content-Type: application/vnd.ms-excel'];

        $newName = 'sample-users-file-' . time() . '.xls';

        return response()->download($myFile, $newName, $headers);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        $id = $request->get('survey_id');
        return view('admin.users.create')
            ->with('survey_id', $id)
            ->with('title', 'Add Participants to survey');
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
        Arr::forget($input, ['_token', 'survey_id', 'rater']);

        $survey_id = $request->get('survey_id');

        $userinfo = DB::table('users')->select('password')->where('email', $email)->first('password');

        try {
            $password = decrypt($userinfo->password);
            $encrypted=$userinfo->password;
        } catch (\Exception $e) {
            $encrypted = encrypt(rand(1,100000000));
        }

        $input['password'] = $encrypted;

        /*Insert values in to Database*/

        $date = date('Y-m-d H:i:s');

        $survey_exits = DB::table('surverys')->where('id', $survey_id)->where('start_date', '<', $date)->exists();

        $rater_self = DB::table('rater')->where('rater', '=', $request->get('rater'))->value('id');

        $rater_survey_id = Survey_rater::firstOrCreate(['rater_id' => $rater_self, 'survey_id' => $survey_id]);
        $input['last_modified'] = date("Y-m-d H:i:s");

        $email = $request->get('email');

        $user_exists = DB::table('users')->where('email', $email)->exists();

        if ($user_exists) {
            DB::table('users')->where('email', $email)->update($input);
            $participant_id = DB::table('users')->where('email', $email)->value('id');

            $user_survey_respondent = DB::table('user_survey_respondent')->where('participant_id', $participant_id)->where('respondent_id', 0)->where('survey_id', $survey_id)->exists();

            if (!$user_survey_respondent) {
                $survey_user_relation = [
                    'participant_id' => $participant_id,
                    'survey_id' => $survey_id,
                    'survey_status' => 1,
                    'rater_id' => $rater_self,
                ];
                DB::table('user_survey_respondent')->insert($survey_user_relation);
            }

        } else {
            $input['added_by'] = Auth::user()->id;
            $participant_id = DB::table('users')->insertGetId($input);

            $survey_user_relation = [
                'participant_id' => $participant_id,
                'survey_id' => $survey_id,
                'survey_status' => 1,
                'rater_id' => $rater_self,
            ];
            DB::table('user_survey_respondent')->insert($survey_user_relation);
        }

        return redirect()->route('addusers.show', $survey_id);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $redirect = $request->get('redirect');
        if ($redirect == 'home') {
            Session::put('redirect', 'home');
        }

        $users = DB::table('users')
            ->select('user_survey_respondent.*', 'users.*', 'user_survey_respondent.id as user_survey_respondent_id')
            ->join('user_survey_respondent', 'users.id', '=', 'user_survey_respondent.participant_id')
            ->where('users.id', '>', 1)
            ->where('user_survey_respondent.respondent_id', '=', 0)
            ->where('user_survey_respondent.survey_id', $id)->get();

        //->paginate(5);
        return view('admin.users.index')
            ->with('data', $users)
            ->with('survey_id', $id)
            ->with('title', 'Listing Participants to the survey');
        //->with('i', ($request->input('name', 1) - 1) * 5);

        /* return view('admin.users.create',compact('datas'))
    ->with('survey_id',$id)
    ->with('title','Add Participants to survey');*/
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
        $user = Addusers::find($id);
        return view('admin.users.edit')
            ->with('user', $user)
            ->with('survey_id', $survey_id)
            ->with('title', 'Updating Participants to survey');
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

        $request->validate( [
            'email' => 'email',
        ]);
        $survey_id = $request->get('survey_id');
        $survey_status = 4;

        $date = date('Y-m-d H:i:s');

        $survey_exits = DB::table('surverys')->where('id', $survey_id)->where('start_date', '<', $date)->exists();

        //if($survey_exits) $survey_status=1;

        $update = $request->all();

        Arr::forget($update, ['_token', '_method', 'survey_id']);
        $update['last_modified'] = date("Y-m-d H:i:s");
        $update['email']=strtolower($request->email);
        $check_same_email_others=DB::table('users')->where('id','<>',$id)->where('email',$update['email'])->first();
        if ($check_same_email_others) {
            DB::table('users')->where('id', $id)->delete();
            DB::table('user_survey_respondent')
            ->where('survey_id', $survey_id)
            ->where('participant_id', $id)
            ->update(['participant_id'=>$check_same_email_others->id]);
        }
        else{
            DB::table('users')->where('id', $id)->update($update);
        }

/*
$user_survey_respondent_exists=DB::table('user_survey_respondent')
->where('participant_id',$id)
->where('rater_id',1)
->where('respondent_id',0)
->where('survey_id',$survey_id)->update(['survey_status'=>$survey_status]);*/

        return redirect()->route('addusers.show', $survey_id);
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
        // Addusers::find($id)->delete();
        $survey_id = $request->get('survey_id');
        $user_survey_respondent = DB::table('user_survey_respondent')->select('id')->where('participant_id', $id)->where('survey_id', $survey_id)->get()->toArray();

        DB::table('user_survey_respondent')->where('participant_id', $id)->where('survey_id', $survey_id)->delete();
        foreach ($user_survey_respondent as $user_survey_respondent_id) {
//var_dump( $user_survey_respondent_id->id);

            $responses = DB::table('responses')->where('user_survey_respondent_id', $user_survey_respondent_id->id)->delete();
        }

        /*Redirect to indexcontroller*/
        return redirect()->route('addusers.show', $survey_id);
    }

    public function checkduplicate($email, $key, $users)
    {
        $users = $users[0];
        $result = false;

        if ($email != null && $key != "" && count($users) > 0) {

            foreach ($users as $k => $v) {

                if ($k == $key) {
                    continue;
                }

                if ($v['email'] == $email) {
                    $result = true;
                    break;
                }
            }
        }

        return $result;

    }

    public function importusers(Request $request)
    {

        /*Get input File*/
        if ($request->hasFile('import_file')) {

            $import = new ParticipantImport;
            Excel::import($import, $request->file('import_file'));

            $total_datas = (array) $import->DataContainer();
            $update = array();
            $insert = array();
            if (count($total_datas) > 0) {
                $i = 2;
                $error = array();
                foreach ($total_datas[0] as $key => $value) {
                    if ($value['fname'] == "") {
                        $error[] = "Column First name found empty at line number " . $i;
                    }
                    if ($value['email'] == "") {
                        $error[] = "Column Respondent Email found empty at line number " . $i;
                    } elseif (empty(filter_var($value['email'], FILTER_VALIDATE_EMAIL))) {
                        $error[] = "(".$value['email'].") is not a valid email address at line number $i";
                    }
                    $checkduplicate = $this->checkduplicate($value['email'], $key, $total_datas);
                    if ($checkduplicate) {
                        $error[] = $value['email'] . ' users are repeated at line number' . $i;
                    }
                    if ($value['lname'] != '') {
                        $datas = $value['lname'];
                    } else {
                        $datas = '';
                    }
                    $users[] = ['fname' => $value['fname'], 'lname' => $datas, 'email' => strtolower($value['email'])];
                    $i++;
                }
            } else {
                $error[] = "No datas Found";
                $nodata_found = "nodata";
            }
        }

        if (isset($nodata_found) || isset($header_mismatch) || count($error) > 0) {
            session()->flash('total_error_count', count($error));
            session()->flash('msg', $error);
            return redirect()->back();
        } else {
            $survey_id = $request->get('survey_id');

            if (!empty($users)) {
                $total_success = 0;
                $updated_user = array();
                foreach ($users as $key => $insert_value) {
                    $insert_val = array_map('trim', $insert_value);
                    $insert_val['email'] = strtolower($insert_val['email']);
                    $user_exists = DB::table('users')->where('email', $insert_val['email'])->exists();
                    if ($insert_val['email'] != null && $insert_val['fname'] != null && filter_var($insert_val['email'], FILTER_VALIDATE_EMAIL)) {
                        $insert_val['last_modified'] = date("Y-m-d H:i:s");

                        if ($user_exists) {
                            $userid = DB::table('users')->where('email', $insert_val['email'])->value('id');
                            $user_survey_respondent_exists = DB::table('user_survey_respondent')
                                ->where('participant_id', $userid)
                                ->where('respondent_id', 0)
                                ->where('survey_id', $survey_id)
                                ->first();

                            $user_survey_respondent_id = DB::table('user_survey_respondent')
                                ->where('participant_id', $userid)
                                ->where('respondent_id', 0)
                                ->where('survey_id', $survey_id)
                                ->value('id');
                            if (isset($user_survey_respondent_exists)) {
                                $survey_status = $user_survey_respondent_exists->survey_status;
                                $date = date('Y-m-d H:i:s');
                                /*$survey_exits=DB::table('surverys')->where('id',$survey_id)->where('start_date','<',$date)->exists();
                                if($survey_exits) $survey_status=1;*/
                                $survey_user_relation = DB::table('user_survey_respondent')
                                    ->where('id', $user_survey_respondent_id)
                                    ->update([
                                        'participant_id' => $userid,
                                        'survey_id' => $survey_id,
                                        'survey_status' => $survey_status,
                                        'rater_id' => '1',
                                        'respondent_id' => '0',
                                    ]);
                                array_push($updated_user, $insert_val['email']);
                            } else {
                                $survey_status = 4;
                                $date = date('Y-m-d H:i:s');
                                $survey_exits = DB::table('surverys')->where('id', $survey_id)->where('start_date', '<', $date)->exists();
                                if ($survey_exits) {
                                    $survey_status = 1;
                                }
                                $survey_user_relation = [
                                    'participant_id' => $userid,
                                    'survey_id' => $survey_id,
                                    'survey_status' => 1,
                                    'rater_id' => '1',
                                    'respondent_id' => '0',
                                ];
                                DB::table('user_survey_respondent')->insert($survey_user_relation);
                            }
                        } else {
                            $insert_val['added_by'] = Auth::user()->id;
                            $insert_val['password'] = encrypt(rand(1,100000000));
                            $userid = DB::table('users')->insertGetId($insert_val);
                            $survey_status = 4;
                            $date = date('Y-m-d H:i:s');
                            $survey_exits = DB::table('surverys')->where('id', $survey_id)->where('start_date', '<', $date)->exists();
                            if ($survey_exits) {
                                $survey_status = 1;
                            }
                            $survey_user_relation = [
                                'participant_id' => $userid,
                                'survey_id' => $survey_id,
                                'survey_status' => 1,
                                'rater_id' => '1',
                                'respondent_id' => '0',
                            ];
                            DB::table('user_survey_respondent')->insert($survey_user_relation);
                        }
                    }
                    $total_success += 1;
                }
                if (isset($error) and !empty($error)) {
                    $message = $error;
                    $mess_data = "error";
                } else {
                    $message = $total_success . " participants are imported successfully!";
                    $mess_data = "success";
                }
            }
            session()->flash('updated_users', $updated_user);
            session()->flash('mess_data', $mess_data);
            return redirect()->route('addusers.show', $survey_id)->with('msg', $message);
        }

    }

    public function Reopen_survey(Request $request)
    {

        $survey_id = $request->get('survey_id');
        $participant_id = $request->get('participant_id');
        $datas = array('survey_status' => '2');
        $result = DB::table('user_survey_respondent')->where('survey_id', $survey_id)->where('participant_id', $participant_id)->where('respondent_id', 0)->update($datas);
        $reopen_survey_message = "Survey Reopened successfully!";

        return redirect()->route('addusers.show', $survey_id)->with('reopen_survey_message', $reopen_survey_message);

    }

    public function Clear_response(Request $request)
    {

        $survey_id = $request->get('survey_id');
        $participant_id = $request->get('participant_id');
        $user_survey_respondent_id = DB::table('user_survey_respondent')->where('survey_id', $survey_id)->where('participant_id', $participant_id)->where('respondent_id',0)->value('id');
        $result = DB::table('responses')->where('user_survey_respondent_id', $user_survey_respondent_id)->delete();

        $datas = array(
            'survey_status' => '1',
            'notify_email_date'=>null,
            'reminder_email_date'=>null,
            'last_submitted_date'=>null
        );
        $result = DB::table('user_survey_respondent')
                  ->where('survey_id', $survey_id)
                  ->where('participant_id', $participant_id)
                  ->where('respondent_id', 0)
                  ->update($datas);
        $clear_response_message = "Response clear successfully!";

        return redirect()->route('addusers.show', $survey_id)->with('clear_response_message', $clear_response_message);
    }

//check whether the  participant already exists to this survey
    public function checkparticipant_email(Request $request)
    {

        $survey_id = $request->get('survey_id');
        $email = $request->get('email');
        $participant_email = DB::table('user_survey_respondent')
            ->join('surverys', 'surverys.id', '=', 'user_survey_respondent.survey_id')
            ->join('users', 'user_survey_respondent.participant_id', '=', 'users.id')
            ->where('users.email', $email)
            ->where('user_survey_respondent.survey_id', $survey_id);
        if ($request->from == "edit_participant") {
            $participant_email->where('participant_id', '<>', $request->user_id);
        }
        $participant_email = $participant_email->value('users.email');

        if ($participant_email == $email) {
            $isAvailable = false;
        } else {
            $isAvailable = true;
        }
        echo json_encode(array(
            'valid' => $isAvailable,
        ));

    }

    public function DeleteUsers(Request $request)
    {
        $survey_id = $request->get('survey_id');
        $userids = $request->get('userids');

        foreach ($userids as $key => $id) {
            $user_survey_respondent = DB::table('user_survey_respondent')
                ->where('participant_id', $id)
                ->where('survey_id', $survey_id)
                ->pluck('id')
                ->toArray();
            DB::table('user_survey_respondent')->where('participant_id', $id)->where('survey_id', $survey_id)->delete();
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

    public function DummyExecution()
    {

        $all_users = DB::table('user_survey_respondent')->where('id', 12811)->value('id');

    }
}
