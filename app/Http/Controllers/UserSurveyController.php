<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\UserSurvey;
use Validator;
use Auth;
use Hash;
use DB;
use Session;
use Carbon\Carbon;
use App\User;
use Arr;
use Illuminate\Routing\Redirector;
use Http;
class UserSurveyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
 public function __construct(Request $request, Redirector $redirect){

        $path_url=$request->path();
        $survey_info=UserSurvey::getInfo($path_url);
        if (!isset($survey_info)) {
            $redirect->to('/login')->send();
        }
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
            'site.participant_rater_manage'   =>  $survey_info->participant_rater_manage,
            'site.show_relationship'   =>  $survey_info->show_relationship
        ];


        config($configuration);

    }
    public function index(Request $request)
    {

        // dd(date('d-m-Y g:i:s'));

	$user_survey_info_participant=UserSurvey::getSurveyInfoByRater(Auth::id(),0);
	$user_survey_info_respondent=UserSurvey::getSurveyInfoByRater(0,Auth::id());
    $participant_manager_rater=(isset($user_survey_info_participant[0]->respondent_id)) ? $user_survey_info_participant[0]->respondent_id: 1;

    $user_survey_info = array_merge($user_survey_info_participant,$user_survey_info_respondent);

    $survey_exists=DB::table('surverys')->where('id',config('site.survey_id'))->where('end_date', '>' ,Carbon::now())->exists();

	$view=config('site.theme').'.users.dashboard';
    $data['user_survey_info'] = $user_survey_info;
    $data['participant_manager_rater']= $participant_manager_rater;
    $data['survey_exists'] = $survey_exists;

    $data['title'] = 'Home';
    $data['participant_rater_manage']=config( 'site.participant_rater_manage');
    $data['show_relationship']=config( 'site.show_relationship');
    Session::put('name','Welcome <strong>'.Auth::user()->fname.' '.Auth::user()->lname.'</strong>');

	return view($view,$data);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $data['title']          =   'Login';

        $view=config('site.theme').'.users.auth.login';

        return view($view,$data);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $rules = [
         'email'=>'required|email',
         'password'=>'required'
        ];
        if (env('APP_ENV')=="production"){
            $rules['g-recaptcha-response'] = 'required';
            $messages = [
                'g-recaptcha-response.required' => 'Please verify the captcha.',
            ];
        }
        $validation=Validator::make($request->all(),$rules,$messages);

        if($validation->passes())
        {
            if (env('APP_ENV')=="production"){

                // Verify reCAPTCHA
                $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
                    'secret' => config('services.recaptcha.secret_key'),
                    'response' => $request->input('g-recaptcha-response'),
                    'remoteip' => $request->ip(),
                ]);
                $recaptcha = $response->json();
                if (!($recaptcha['success'] ?? false)) {
                    return back()->withErrors(['captcha' => 'Captcha verification failed. Please try again.']);
                }
            }


            $input=$request->all();
            Arr::forget($input,['_token','survey_url']);

            $user=User::where('email',$input['email'])->first();
            if($user)
            {
              try {
                  $decrypted = decrypt($user->password);
              } catch (DecryptException $e) {
                 $decrypted='';
                // die($e->getMessage());
              }
              if($input['password']==$decrypted){
                Auth::login($user);
                $user_role=Auth::id();
                if($user_role==1) return redirect()->route('admin.dashboard');
                Session::put('survey_url',config('site.survey_slug'));
                return redirect()->route('user.dashboard',config('site.survey_slug'));
              }

            }
            return redirect()->back()->withInput()->withErrors(['email'=>'Invalid Login Details']);
        }
        else
        {
            return redirect()->back()->withInput()->withErrors($validation);
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
       $data['title']          =   'Change Password';

       $view=config('site.theme').'.users.change-password';

       return view($view,$data);
    }

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
    public function update(Request $request)
    {
        $rules=array(
           'password' => 'required',
           'confirm_password' => 'required|same:password'
            );
        $input=$request->all();
        $validator=Validator::make($input,$rules);

        if($validator->passes())
        {
            $user_id=Auth::id();
            $new_password=$request['password'];
            $hashed_password=encrypt($new_password);
            //$hashed_password=Hash::make($new_password);

            DB::table('users')->where('id',$user_id)->update(['password'=>$hashed_password]);

            return redirect()->route('user.dashboard',config('site.survey_slug'))->withSuccess('Password Updated Successfully!');
        }
        else
        {
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
    public function signout()
    {
      Auth::logout();
      Session::flush();
      Session::put('survey_url', config('site.survey_slug'));
      return redirect()->to(config('site.survey_slug').'/login');
    //   return redirect()->route('login',[config('site.survey_slug')]);
    }
}
