<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use App\UserSurvey;
use App\Respondent;

use App\Rater;
use App\Survey_rater;
use App\Addusers;
use App\User;


use Excel;
use Session;
use Auth;
use Carbon\Carbon;
use Arr;
use Str;
class UserRespondentController extends Controller
{

   public function __construct(Request $request){

        $path_url=$request->path();
        $survey_info=UserSurvey::getInfo($path_url);

        $configuration=[
        'site.theme'            =>  $survey_info->title,
        'site.survey_slug'      =>  $survey_info->url,
        'site.survey_title'     =>  $survey_info->survey_name,
        'site.survey_id'        =>  $survey_info->survey_id,
        'site.survey_theme_id'  =>  $survey_info->survey_theme_id,
        'site.left_logo'        =>  $survey_info->logo,
        'site.right_logo'       =>  $survey_info->right_logo,
        'site.header_text'      =>  $survey_info->header_text,
        'site.footer_text'      =>  $survey_info->footer_text,
        'site.theme_slug'       =>  $survey_info->file,
        'site.shuffle_questions'=>  $survey_info->shuffle_questions,
        'site.dimension_hide'   =>  $survey_info->dimension_hide,


        ];


        config($configuration);

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

    $respondent_list  = Respondent::getRespondentBasedOnParticipant(Auth::id());

    $data['users']    = $respondent_list;
    $data['title']    = 'List of Respondents';
    $data['participant_id']    = Auth::id();
    $data['survey_id']    = config('site.survey_id');

    $view=config('site.theme').'.users.respondent.list';

    return view($view,$data);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {

      $rater_list                     = Respondent::getRaterList(config('site.survey_id'));
      $survey_rater_list              = Respondent::getSurveyRaterList(config('site.survey_id'));
      $data['rater_list']             = $rater_list;
      $data['survey_rater_list']      = $survey_rater_list;
      $data['title']                  = 'Add Respondents';
      $data['participant_details']    = Auth::user();
      $data['survey_id']              = config('site.survey_id');

      $view=config('site.theme').'.users.respondent.create';

      return view($view,$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function storeRespondent($input,$rater,$survey_id,$participant_id){

       $rater_survey_id= Survey_rater::firstOrCreate(['rater_id'=>$rater,'survey_id'=>$survey_id]);

       $respondent_exist = DB::table('users')->where('email',$input['email'])->exists();
       $survey_exist=DB::table('surverys')->where('id',$survey_id)->where('start_date','<',Carbon::now())->exists();

       $input=array_map('trim',$input);

       if($respondent_exist){
         $input['last_modified']=date("Y-m-d H:i:s");
         $user = User::where('email',$input['email'])->first();
         $user->update($input);
         $respondent_id=$user->id;
       }
       else
       {
        $input['added_by']=$participant_id;
        $respondent_id=User::insertGetId($input);
       }

       $survey_status = 4;
       if($survey_exist) $survey_status =1;
       $user_survey_respondent=DB::table('user_survey_respondent')->where('respondent_id',$respondent_id)->where('participant_id',$participant_id)->where('survey_id',$survey_id)->exists();
       if($user_survey_respondent){

        $user_survey_respondent_id=DB::table('user_survey_respondent')->where('respondent_id',$respondent_id)->where('participant_id',$participant_id)->where('survey_id',$survey_id)->value('id');
        $survey_user_relation=[
                                'participant_id'=>$participant_id,
                                'respondent_id'=>$respondent_id,
                                'survey_id'=>$survey_id,
                                'survey_status'=>$survey_status,
                                'rater_id'=>$rater
                              ];
        DB::table('user_survey_respondent')->where('id',$user_survey_respondent_id)->update($survey_user_relation);
       }
       else
       {
           $survey_user_relation=[
                                'participant_id'=>$participant_id,
                                'respondent_id'=>$respondent_id,
                                'survey_id'=>$survey_id,
                                'survey_status'=>$survey_status,
                                'rater_id'=>$rater
                              ];
          DB::table('user_survey_respondent')->insert($survey_user_relation);
       }
    }
    public function store(Request $request)
    {
       $input = $request->all();
       $participant_id=$input['participant_id'];
       $survey_id=$input['survey_id'];
       $rater=$input['rater'];

       Arr::forget($input,['_token','survey_id','participant_id','rater']);

       $this->storeRespondent($input,$rater,$survey_id,$participant_id);

       return redirect()->route('manage-respondent.index',config('site.survey_slug'));

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request,$id)
    {


      $action =$request->action;

      if($action=="validate-user-respondent"){

          $survey_id=$request->get('survey_id');
          $participant_id=$request->get('participant_id');
          $email=$request->get('email');

          $respondent_email = Respondent::getRespondentEmailBySurveyId($participant_id,$survey_id);
          $isAvailable=true;
          if(in_array($email,$respondent_email))
          {
            $isAvailable=false;
          }
          return json_encode(array('valid' => $isAvailable));
      }
      if($action=="download-sample-respondent-import"){

        $myFile = public_path('download/respondent.xls');

        $headers = ['Content-Type: application/vnd.ms-excel'];

        $newName = 'respondent-file-'.time().'.xls';


        return response()->download($myFile, $newName, $headers);
      }
      if($action=="reopensurvey"){

      $survey_id=$request->get('survey_id');
      $respondent_id=$request->get('respondent_id');

      $participant_id=Auth::id();

      $result=DB::table('user_survey_respondent')->where('survey_id',$survey_id)->where('respondent_id',$respondent_id)->where('participant_id',$participant_id)->update(array('survey_status' => '2'));

      $reopen_survey_message= "Survey Reopened successfully!";
      return redirect()->route('manage-respondent.index',config('site.survey_slug'))
      ->with('reopen_survey_message',$reopen_survey_message);

      }
      if($action=="emptyresponse"){

      $input=$request->all();

      $survey_id=$request->get('survey_id');
      $respondent_id=$request->get('respondent_id');
      $participant_id=Auth::id();
      $user_survey_respondent_id=DB::table('user_survey_respondent')->where('survey_id',$survey_id)->where('respondent_id',$respondent_id)->where('participant_id',$participant_id)->value('id');

      $result=DB::table('responses')->where('user_survey_respondent_id',$user_survey_respondent_id)->delete();

      $result=DB::table('user_survey_respondent')->where('survey_id',$survey_id)->where('respondent_id',$respondent_id)->where('participant_id',$participant_id)->update(['survey_status' => '2']);

      $clear_response_message= "Survey Response deleted successfully!";

      return redirect()->route('manage-respondent.index',config('site.survey_slug'))
      ->with('reopen_survey_message',$clear_response_message);
      }


    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request,$id)
    {

      $input=$request->all();

      $rater_id=DB::table('user_survey_respondent')->where('survey_id',config('site.survey_id'))->where('respondent_id',$input['user_id'])->value('rater_id');

      $rater_list                     = Respondent::getRaterList(config('site.survey_id'));
      $survey_rater_list              = Respondent::getSurveyRaterList(config('site.survey_id'));
      $data['rater_list']             = $rater_list;
      $data['survey_rater_list']      = $survey_rater_list;
      $data['title']                  = 'Edit Respondents';
      $data['participant_details']    = Auth::user();
      $data['user_details']           = User::find($input['user_id']);
      $data['survey_id']              = config('site.survey_id');
      $data['rater_id']               = $rater_id;
      $view=config('site.theme').'.users.respondent.edit';

     // dd($data);

      return view($view,$data);
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
      $update=$request->all();
	    Arr::forget($update,['_token','_method','survey_id','participant_id','rater','respondent_id']);
    	$update['last_modified']=date("Y-m-d H:i:s");
    	$survey_id=$request->get('survey_id');
      $rater=$request->get('rater');
      $respondent_id = $request->get('respondent_id');
      $participant_id=$request->get('participant_id');
    	DB::table('users')->where('id', $respondent_id)->update($update);
    	$survey_rater=Survey_rater::firstOrCreate(['rater_id'=>$request->get('rater'),'survey_id'=>$survey_id]);
    	$survey_status=4;
    	$date = date('Y-m-d H:i:s');
      $survey_exits=DB::table('surverys')->where('id',$survey_id)->where('start_date','<',$date)->exists();
      if($survey_exits) $survey_status=1;
      $user_survey_respondent=DB::table('user_survey_respondent')->where('survey_id',$survey_id)->where('respondent_id',$respondent_id)->where('participant_id',$participant_id)->update(['rater_id'=>$rater,'survey_status'=>$survey_status]);
     return redirect()->route('manage-respondent.index',config('site.survey_slug'));

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request,$id)
    {
       $survey_id=$request->get('survey_id');
	     $participant_id=Auth::id();

       $user_survey_respondent=DB::table('user_survey_respondent')->where('respondent_id',$request->get('respondent_id'))->where('participant_id',$participant_id)->where('survey_id',$survey_id)->value('id');

      DB::table('user_survey_respondent')->where('participant_id',$participant_id)->where('respondent_id',$request->get('respondent_id'))->where('survey_id',$survey_id)->where('rater_id','<>',1)->delete();

      DB::table('responses')->where('user_survey_respondent_id',$user_survey_respondent)->delete();

      return redirect()->route('manage-respondent.index',config('site.survey_slug'));
    }

 public function checkDuplicateEntry($email,$input,$key){

  $match_found=false;
  foreach ($input as $k=> $i) {
    if($key<=$k) break;
    if($k==$key-2) continue;
    if(isset($i['r_email']) && trim($i['r_email']) == trim($email)) {
      $match_found=true;
      break;
    }
  }
  return $match_found;

 }
 public function CheckExcelImportErrors($input,$survey_id,$participant_id,$i,$rows){
  $error=array();
  if(!isset($input->r_fname) AND !isset($input->r_lname) AND !isset($input->r_email) AND !isset($input->r_type))
      {
        $error['header_mismatch']="Header Mismatch at line number 1. Please follow the format(r_fname,r_lname,r_email,r_type).";
      }
      else
      {
         $check_rater_exists=false;
         if(isset($input->r_type) AND  !empty(trim($input->r_type)))
        $check_rater_exists=DB::table('survey_rater')->where('survey_id',$survey_id)->where('rater_id',$input->r_type)->exists();



        $participant_email= DB::table('users')->where('id',$participant_id)->value('email');


        if(!isset($input->r_fname) OR empty(trim($input->r_fname)))  $error[]="Column First name found empty at line number ".$i;
        if(!isset($input->r_email) OR empty(trim($input->r_email)))  $error[]="Column Respondent Email found empty at line number ".$i;
        elseif(empty(filter_var($input->r_email,FILTER_VALIDATE_EMAIL))) $error[]="Column Respondent Email ($input->r_email)  is not a valid email address at line number $i";
        elseif($input->r_email==$participant_email) $error[]="Column Respondent Email same as Participant Email  at line number ".$i;
        elseif ($this->checkDuplicateEntry($input->r_email,$rows,$i)) {
          $error[]="Column Respondent Email is duplicated  at line number ".$i;
        }

        if(!isset($input->r_type) OR empty(trim($input->r_type)))  $error[]="Column Rater type found empty at line number ".$i;
        elseif($input->r_type==1)   $error[]="Column Rater_type self  not valid for respondent at line number ".$i;
        elseif(!$check_rater_exists) $error[]="Column Rater_type is not valid at line number ".$i;

      }

  return $error;
 }
 public function importRespondent(Request $request)
 {
      $survey_id=$request->get('survey_id');
      $participant_id=$request->get('participant_id');


      $errors=$respondent=array();

      if($request->hasFile('import_file')){

      $path = $request->file('import_file')->getRealPath();

      $rows = Excel::load($path, 'UTF-8')->all()->toArray();

      //dd($rows);

      for ($i = 0; $i < count($rows); $i++) {

        $row=array_filter($rows[$i]);
        if(count($row)==0) break;


        $header=array_keys($row);
          if($i==0){
          $diff=array_intersect(['r_fname','r_email','r_type'], $header);
          if( (count($diff) < 3) || ( (count($diff)==3 && count($header)==4) && !in_array('r_lname', $header))  ){
            $emsg=['header_mismatch'=>"Header Mismatch at line number 1 . Please follow the format(r_fname,r_lname,r_email,r_type)."];
             session()->flash('msg', $emsg);
             return redirect()->back();
          }}
          else
          {
            if(count($header)==4 && !in_array('r_lname', $header)){
            $emsg=['header_mismatch'=>"Header Mismatch at line number 1 . Please follow the format(r_fname,r_lname,r_email,r_type)."];
             session()->flash('msg', $emsg);
             return redirect()->back();
          }}



        $individual_row = (object) $row;
        $cint=$i+2;
        $error=$this->CheckExcelImportErrors($individual_row,$survey_id,$participant_id,$cint,$rows);

        $errors=array_merge($errors, $error);

        if(isset($error['header_mismatch'])){
           session()->flash('msg', $errors);
           return redirect()->back();
        }
        if(count($error)==0){
          $respondent[]=$row;
        }


      }



        foreach ($respondent as $key => $rinput) {
          $rater=$rinput['r_type'];
          if(!array_key_exists('r_lname', $rinput)) $rinput['lname']='';
          if(isset($rinput['r_fname'])) $rinput['fname']=$rinput['r_fname'];
          if(isset($rinput['r_lname'])) $rinput['lname']=$rinput['r_lname'];
          if(isset($rinput['r_email'])) $rinput['email']=$rinput['r_email'];
          Arr::forget($rinput,['r_type','r_fname','r_lname','r_email']);

          $this->storeRespondent($rinput,$rater,$survey_id,$participant_id);
        }

     if(count($errors)>0){
           session()->flash('msg', $errors);
           return redirect()->back();
      }
      else
      {
        session()->flash('sucessmsg', 'All the respondents are imported successfully!...');
        return redirect()->route('manage-respondent.index',config('site.survey_slug'));
      }
    }
    else
    {
      session()->flash('upload_file_error','Uploaded file is either empty or invalid ');
      return redirect()->back();
    }
}






}
