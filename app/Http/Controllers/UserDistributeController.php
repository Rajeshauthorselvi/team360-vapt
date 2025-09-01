<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Input;
use Validator;
use Redirect;
use Mail;
use Session;
use Hash;
use Auth;

use App\UserSurvey;
use App\UserEmail;
use Str;



class UserDistributeController extends Controller
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
        'site.send_email_from'   =>  $survey_info->send_email_from,

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
 		$survey_id = config('site.survey_id');

 		$participant_id   = Auth::id();
	 
    	$respondents=$remind_respondents=array();


		$notify_respondents = UserEmail::getRespondentBasedOnSurveyStatus($participant_id,$survey_id,0);
		$remind_respondents = UserEmail::getRespondentBasedOnSurveyStatus($participant_id,$survey_id,1);

		$data['remind_respondents']     = $remind_respondents;
		$data['notify_respondents']     = $notify_respondents;
		$data['from_email']    		    = config('site.send_email_from');
		$data['title']    				= 'List of Respondents';
		$data['participant_id']   		= $participant_id;
		$data['survey_id']    			= config('site.survey_id');

	    $view=config('site.theme').'.users.distribute.email';

	    return view($view,$data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
		'from_email'	=>'required|email',
		'send_email'    => 'required',
		'bcc'    		=> 'required',
		//'cc'    		=> 'required',
		//'copy_email'    	=> 'required',
		'subject'    	=> 'required',
		'message_body'  => 'required'
		     );

        

        if($request->get('send_email')=="remainder")
        {
        	 $rules = array(
			'from_email_for_reminder'	=>'required|email',
			'send_email'    => 'required',
			'bcc_for_reminder'    		=> 'required',
			//'cc_for_reminder'    		=> 'required',
			//'copy_email_for_reminder'    	=> 'required',
			'subject_for_reminder'    	=> 'required',
			'message_body_for_reminder'  => 'required'
			     );
        	 
        }


        $input=$request->all();
       
        $error_info=array();
		$validator = Validator::make($input, $rules);

		if ($validator->fails())
		{
			return Redirect::back()->withInput()->withErrors($validator);

		} 

		else
		{

$survey_id= config('site.survey_id');
$survey_name=DB::table('surverys')->where('id',$survey_id)->value('title');
$participant_id=Auth::id();


$raters=DB::table('rater')
->select('rater.id as rater_id','rater.rater')
->leftjoin('survey_rater','rater.id','=','survey_rater.rater_id')
->where('survey_rater.survey_id',$survey_id)
->get();

			if($request->get('send_email')=="remainder")
        	{
				$input=array();
        	 	$input['from_email']=$request->get('from_email_for_reminder');
        	 	$input['bcc']=$request->get('bcc_for_reminder');
        	 	$input['cc']=$request->get('cc_for_reminder');
			    $input['copy_email']=$request->get('copy_email_for_reminder');
        	 	$input['subject']=$request->get('subject_for_reminder');
        	 	$input['message_body']=$request->get('message_body_for_reminder');
			
				
			}


//$copy=implode(',',$input['copy_email']);
$copy_email='';
if(!empty($input['copy_email'])) {
    $copy_email=explode(',',$input['copy_email']);
}

//dd($survey_id);

	
			foreach($input['bcc'] as $value)
			{

				$respondent_id=DB::table('users')->where('email',$value)->pluck('id');
				$userinfo=DB::table('users')->find($respondent_id);

				$users = DB::table('users')
				->join('user_survey_respondent','users.id','=','user_survey_respondent.respondent_id' )
				->join('rater','rater.id','=','user_survey_respondent.rater_id' )
				->where('users.id','>',1)
				->where('user_survey_respondent.respondent_id','!=',0)
				->whereIn('user_survey_respondent.survey_status',[1,2,4])
				->where('user_survey_respondent.respondent_id',$userinfo->id)
				->where('user_survey_respondent.survey_id',$survey_id)->get();

				$users_as_participant = DB::table('users')
				->join('user_survey_respondent','users.id','=','user_survey_respondent.participant_id' )
				->join('rater','rater.id','=','user_survey_respondent.rater_id' )
				->where('users.id','>',1)
				->where('user_survey_respondent.respondent_id','=',0)
				->whereIn('user_survey_respondent.survey_status',[1,2,4])
				->where('user_survey_respondent.participant_id',$userinfo->id)
				->where('user_survey_respondent.survey_id',$survey_id)->get();


$result = $users->merge($users_as_participant);


//dd($result);

$no=1;
if(count($result)>0){
        $rater_content=array();
		foreach($result as $rater)
		{
        $first_name=DB::table('users')->where('id',$rater->participant_id)->value('fname');
        $last_name=DB::table('users')->where('id',$rater->participant_id)->value('lname');
		$participant_name=$first_name.' '.$last_name;

        if ($rater->rater!="self") {
            $rater_content[]='<p>'.$no.'. '.ucfirst($participant_name).'</p>';
        }
		$no++;

		}

$rater_details=implode(' ',$rater_content);
unset($rater_content); 
// $content='<br><b>You have been invited to respond to a 360° Feedback Survey for:</b><br/>';
$content='';
}
else{

$rater_details='';
$content='Self Assessment';
}

               if(!empty($userinfo->password)){
                    try {
                        $decrypted = decrypt($userinfo->password);
                    } catch (DecryptException $e) {
                        $decrypted='';
                        // die($e->getMessage());
                    }
                    $random_pwd=$decrypted;
                }
                else
                {
                    $random_pwd=Str::random(8);
                    //$password=Hash::make($random_pwd);
                    $password=encrypt($random_pwd);
                }
                $survey_details=DB::table('surverys')->find($survey_id);
                $survey_url=url('/'.$survey_details->url).'/login';
               // if($survey_details->open_survey_flag==1)
             //   $survey_url=url('/os/'.$survey->url);
               

				$survey_info_replace='<p>'.$content.''.$rater_details.'</p>';

				$login_info_replace='<p><br><b>Here is your login details: </b><br><br/> Link: '.$survey_url.'<br><br> Email: '.$userinfo->email.'<br><br>Password: '.$random_pwd.'<br/></p>';


				$user_fname_replace='<span>'.ucfirst($userinfo->fname).' </span>';
				$user_lname_replace='<span>'.ucfirst($userinfo->lname).' </span>';
	
				 
				$search=array('[Surveys list]','[Login Details]','[fname]','[lname]'); 
				$replace=array($survey_info_replace,$login_info_replace,$user_fname_replace,$user_lname_replace);

				$message_body=str_replace($search,$replace, $input['message_body']);

				
			  	$data =array(
					'send_email'	=>$value,
				    'from_email'   	=>$input['from_email'],
					'subject'   	=>$input['subject'],
				    'message_body'  =>$message_body,
					'cc'   			=>$input['cc'],
					'copy_email'  =>$copy_email
					  );


					Mail::send(['html' => config('site.theme').'.users.distribute.sendemail'], $data, function($message) use($data) {
			             $message->from($data['from_email']);
                         $message->to($data['send_email']);
                         
			             if(!empty($data['cc'])) 
                            {
                                $message->cc($data['cc']);
                            }
					     if(!empty($data['copy_email'])) 
                            {
                                $message->bcc($data['copy_email']);
                            }
			             $message->subject($data['subject']);

			    	});
				

			    	//dd(Mail::failures());

			    	if(count(Mail::failures()) == 0 ) {

			    		$currenttime=date('Y-m-d H:i:s');

			    		if($request->get('send_email')=="remainder")
        				{
        					$update=['reminder_email_date'=>$currenttime];
        				}

        				if($request->get('send_email')=="notification")
        				{
        					$update=['notify_email_date'=>$currenttime];
        				}

	
						DB::table('user_survey_respondent')->where('respondent_id',$respondent_id)->where('participant_id',$participant_id)->where('survey_id',$survey_id)->update($update);
                        if(empty($userinfo->password)){
                            DB::table('users')->where('id',$respondent_id)->update(['password'=>$password]);
                        }

						$error_info['mailsent'][]=$value;
					}
					else
					{
						$error_info['mail_failed'][]=$value;
					}
	

			}

                return redirect()->route('user.dashboard',config('site.survey_slug'))->with('error_info',$error_info);



		}
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
      	$id=Auth::id();



	$survey=[Auth::user()->getsurvey_Details()->url];
	$url=$survey[0];

	$check_url=DB::table('surverys')
	->select('themes.file' )
	->leftjoin('themes','surverys.survey_theme_id','=','themes.id')
	->where('surverys.url',$url)
	->get();
	$survey_id=DB::table('surverys')->where('url',$url)->value('id');
	 
    	$from_email=DB::table('surverys')->where('id',$survey_id)->value('send_email_from');

    	$survey_exits=DB::table('surverys')->where('id',$survey_id)->whereRaw('end_date > now()')->exists();

    	$respondents=$remind_respondents=array();

    	if($survey_exits){
        
     
$respondents = DB::table('users')
		->select('users.email','users.id','users.fname','users.lname','user_survey_respondent.survey_status','surverys.participant_rater_manage')
		->join('user_survey_respondent','users.id','=','user_survey_respondent.respondent_id' )
		->join('surverys','surverys.id','=','user_survey_respondent.survey_id' )
		->where('users.id','>',1)
		->where('user_survey_respondent.survey_id',$survey_id)
		->where('user_survey_respondent.participant_id',$id)
		->where('user_survey_respondent.notify_email_date',null)
		->where('user_survey_respondent.survey_status','=',1)
		//->where('surverys.participant_rater_manage','=',1)
		->where('user_survey_respondent.respondent_id','>',0)
		->get()->toArray();


$remind_respondents= DB::table('users')
		->select('users.email','users.id','users.fname','users.lname','user_survey_respondent.survey_status')
		->join('user_survey_respondent','users.id','=','user_survey_respondent.respondent_id' )
		->join('surverys','surverys.id','=','user_survey_respondent.survey_id' )
		->where('users.id','>',1)
		->where('user_survey_respondent.survey_id',$survey_id)
		->where('user_survey_respondent.participant_id',$id)
		->where('user_survey_respondent.notify_email_date','<>',null)
		->whereIn('user_survey_respondent.survey_status',[1,2])
		->where('user_survey_respondent.respondent_id','>',0)
		->get();

       
    }

      //  dd($users);

        return view('users.distribute.index')
	->with('respondents',$respondents)
	->with('remind_users',$remind_respondents)
        ->with('survey_id',$id)
        ->with('survey_name',$url)
	->with('themes',$check_url)
        ->with('from_email',$from_email)
        ->with('title','Listing Participants to the survey');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
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
}
