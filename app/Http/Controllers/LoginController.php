<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Auth;
use DB;
use Hash;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Http\Request;
use Mail;
use Redirect;
use Session;
use Validator;
use Arr;
use Str;
use Http;
class LoginController extends Controller
{
    public function index(Request $request)
    {
        $path_url = $request->path();
        $url = explode('/', $path_url);

        $theme = DB::table('surverys')
            ->leftjoin('themes', 'surverys.survey_theme_id', 'themes.id')
            ->where('url', $url[0])
            ->value('file');

        $survey_id = DB::table('surverys')
            ->where('url', $url[0])
            ->value('id');

        $check_url = DB::table('surverys')
            ->select('surverys.title as survey_name', 'surverys.survey_theme_id',
                'surverys.logo', 'surverys.url', 'themes.file')
            ->leftjoin('themes', 'surverys.survey_theme_id', '=', 'themes.id')
            ->where('surverys.url', $url[0])
            ->get();
        return view('auth.login')->with('themes', $check_url)->with('url', $url[0])->with('survey_id', $survey_id)->with('themes', $theme);

    }

    public function post_login(Request $request)
    {

        $rules = [
            'email' => 'required|email',
            'password' => 'required',

        ];
        $messages=[];
        if (env('APP_ENV')=="production"){
            $rules['g-recaptcha-response'] = 'required';
            $messages = [
                'g-recaptcha-response.required' => 'Please verify the captcha.',
            ];
        }


        $survey_url = $request->get('survey_url');
        if (!empty($survey_url)) {
            $survey_id = DB::table('surverys')->where('url', $survey_url)->value('id');
            if (!empty($survey_id)) {
                Session::put('survey_id', $survey_id);
                Session::put('survey_url', $survey_url);
            }
        }
        $input = $request->all();
        $input['email'] = strip_tags($input['email']);
        $input['password'] = strip_tags($input['password']);

        $validation = Validator::make($input, $rules,$messages);

        if ($validation->passes()) {

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




            $input = $request->all();
            Arr::forget($input, ['_token', 'survey_url']);
            $user = User::where('email', $input['email'])->first();
            if ($user) {
                try {
                    $decrypted = decrypt($user->password);
                } catch (DecryptException $e) {
                    $decrypted = '';
                }
                if ($input['password'] == $decrypted) {
                    Auth::login($user);
                    $user_role = Auth::id();
                    if ($user_role == 1) {
                        return Redirect::route('admin.dashboard');
                    }

                    return Redirect::route('user.dashboard', $survey_url);
                }

            }

            return Redirect::back()->withInput()->withErrors(['email' => 'Invalid Login Details']);
        } else {
            return Redirect::back()->withInput()->withErrors($validation);
        }
    }

    public function ResetPasswordindex(Request $request)
    {

        if (env('APP_ENV')=="production"){
            $rules['g-recaptcha-response'] = 'required';
            $messages = [
                'g-recaptcha-response.required' => 'Please verify the captcha.',
            ];

            $validation = Validator::make($request->all(), $rules,$messages);
            if ($validation->passes()) {
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
            } else {
                return Redirect::back()->withInput()->withErrors($validation)->with('from', 'reset');

            }
        }


        $input = $request->all();
        $data = $input['resetemail'];
        $userinfo = DB::table('users')
            ->where('email', $data)
            ->first();
        if (!$userinfo) {

            session()->flash('error', 'This Email does Not exists');
            return redirect()->back();
        } else {

            $get_survey_email = DB::table('user_survey_respondent')
                ->select('surverys.send_email_from','surverys.sender_name', 'surverys.id')
                ->leftjoin('users', 'user_survey_respondent.respondent_id', 'users.id')
                ->leftjoin('surverys', 'user_survey_respondent.survey_id', 'surverys.id')
                ->where('users.email', $userinfo->email)
                ->orderBy('surverys.id', 'asc')
                ->first();

            // dd($get_survey_email);
            // foreach ($get_survey_email as $key => $value) {
            $survey_email = (isset($get_survey_email->send_email_from)) ? $get_survey_email->send_email_from : 'info@ascendus.com';
            // }
                try {
                    $encrypted = $userinfo->password;
                    $decrypted = decrypt($userinfo->password);
                } catch (DecryptException $e) {
                    $decrypted = Str::random(8);
                    $encrypted = encrypt($decrypted);
                }
                $password = $decrypted;
                DB::table('users')
                    ->where('email', $userinfo->email)
                    ->update(['password' => $encrypted]);

            $data = [
                'email_form' => $survey_email,
                'to_email' => $userinfo->email,
                'password'=>$password,
                'subject' => 'Reset your Password',
                'message' => "Your email : " . $userinfo->email . " <br/> " .
                "Your password : " . $password,
            ];
            $sender_name='Survey Support';
            // $sender_name=isset($get_survey_email->sender_name)?$get_survey_email->sender_name:'Survey Support';
            Mail::send('email.forgot_email', $data, function ($message) use ($data,$sender_name) {
                $message->from($data['email_form'],$sender_name)
                    ->to($data['to_email'])
                    ->subject($data['subject']);
                    // ->setBody($data['message'], 'text/html');
            });
            session()->flash('success', 'Your password has changed. Please check your Mail.');
            return redirect()->back();
        }
    }

    public function open_survey(Request $request, $survey_url)
    {
        $check_url = DB::table('surverys')
            ->select('surverys.title as survey_name', 'surverys.survey_theme_id',
                'surverys.logo', 'surverys.url', 'themes.file')
            ->leftjoin('themes', 'surverys.survey_theme_id', '=', 'themes.id')
            ->where('surverys.url', $survey_url)
            ->get();

        $survey_id = DB::table('surverys')->where('url', $survey_url)->value('id');
        $survey_details = DB::table('surverys')->find($survey_id);
        $survey_closed = DB::table('surverys')->where('id', $survey_id)->whereRaw('start_date < now()')->whereRaw('end_date < now()')->exists();

        $ip = $_SERVER['REMOTE_ADDR'];

        $check_exists_ip = DB::table('users')
            ->leftjoin('user_survey_respondent', 'users.id', '=', 'user_survey_respondent.participant_id')
            ->leftjoin('surverys', 'user_survey_respondent.survey_id', 'surverys.id')
            ->where('surverys.open_survey_flag', '=', '1')
            ->where('last_access_ip', $ip)->get();

        foreach ($check_exists_ip as $key => $value) {
            if ($value->last_access_ip == $ip && $survey_url == $value->url) {
                return view('users.questions.nosurvey')
                    ->with('Details', 'Already survey taken!')
                    ->with('title', 'Take Survey');
            }
        }

        if ($survey_closed) {
            return view('users.questions.nosurvey')
                ->with('Details', 'Survey Closed!')
                ->with('title', 'Take Survey');
        }

        $questions = DB::table('questions')

            ->select(DB::raw('surverys.id,GROUP_CONCAT(question_text,"") as question_text,GROUP_CONCAT(questions.id,"") as question_id,questions.question_type,questions.question_required,questions.question_dimension'))

            ->join('surverys', 'questions.survey_id', '=', 'surverys.id')

            ->where('questions.survey_id', $survey_id)

        //->where('questions.question_enabled',1)

            ->where('display_order', '>', 0)
            ->orderBy('display_order', 'ASC')
            ->groupBy('display_order')
            ->get();

        $welcome_text = DB::table('questions')->where('survey_id', $survey_details->id)->where('display_order', 0)->value('question_text');

        foreach ($questions as $key => $question) {

            $exploded = explode(',', $question->question_id);

            // var_dump($exploded);

            if (count($exploded) > 1) {

                foreach ($exploded as $key => $iquestion_id) {

                    $option_values[$iquestion_id] = DB::table('options')->where('question_id', $iquestion_id)->pluck('id', 'option_text');

                }
                $question->optionth = DB::table('options')->where('question_id', $iquestion_id)->pluck('option_text');
            } else {
                //$option_values="";
                $option_values = DB::table('options')->whereIn('question_id', $exploded)->get();
            }
            $question->options = $option_values;

            unset($option_values);

        }

        $user_response = array();

        return view('users.questions.opensurvey')
            ->with('title', 'Take Survey')
            ->with('survey_details', $survey_details)
            ->with('welcome_text', $welcome_text)
            ->with('responses', $user_response)
            ->with('themes', $check_url)
            ->with('questions', $questions);

    }

    public function store_open_survey(Request $request)
    {
        $responses = $request->all();
        $question_id = $responses['question_id'];
        $survey_id = $responses['survey_id'];

        //fetch user_name using survey_id
        $user_names = DB::table('user_survey_respondent')
            ->join('surverys', 'surverys.id', '=', 'user_survey_respondent.survey_id')
            ->join('users', 'users.id', '=', 'user_survey_respondent.participant_id')
        // ->where('surverys.id','=',$survey_id)
            ->pluck('users.fname')->toArray();

        for ($random = 1; in_array('guest' . $random . '', $user_names); $random++) {

        }

        $ip = $_SERVER['REMOTE_ADDR'];
        $users = array(
            'fname' => 'guest' . $random,
            'lname' => 'guest' . $random,
            'email' => 'guest' . $random . '@gmail.com',
            'last_access_ip' => $ip,
        );

        $participant_id = DB::table('users')->insertGetId($users);
        //input for user_survey_respondent table
        $user_survey_respondent = array(
            'participant_id' => $participant_id,
            'survey_id' => $survey_id,
            'survey_status' => '1',
        );

        //save input to user_survey_respondent table and get user_survey_respondent_id
        $user_survey_respondent_id = DB::table('user_survey_respondent')->insertGetId($user_survey_respondent);
        DB::table('responses')->where('user_survey_respondent_id', $user_survey_respondent_id)->delete();

        foreach ($question_id as $question_value) {

            $array1 = explode(',', $question_value);

            foreach ($array1 as $question) {

                $value = '_' . $question;
                if (!empty($responses[$value])) {
                    $question_type = DB::table('questions')->where('id', $question)->value('question_type');
                    switch ($question_type) {
                        case 'text':
                            $option_arr = [
                                'option_text' => null,
                                'option_weight' => 0,
                                'question_id' => $question,
                            ];
                            $option = DB::table('options')->insertGetId($option_arr);
                            $text_response = $responses[$value];
                            break;

                        case 'textarea':
                            $option_arr = [
                                'option_text' => null,
                                'option_weight' => 0,
                                'question_id' => $question,
                            ];
                            $option = DB::table('options')->insertGetId($option_arr);
                            $text_response = $responses[$value];
                            break;
                        case 'dropdown':
                            $option = $responses[$value];
                            $text_response = '';
                            break;
                        case 'radio':
                            $option = $responses[$value];
                            $oval = 'others' . $value;
                            $text_response = (isset($responses[$oval])) ? $responses[$oval] : "";
                            break;
                        case 'checkbox':
                            $option = $responses[$value];
                            $text_response = '';
                            break;
                        case 'grid':

                            $option = $responses[$value];

                            $text_response = '';

                            break;

                        default:
                            $text_response = '';
                            break;
                    }

                    if (is_array($option)) {
                        foreach ($option as $key => $value) {
                            $output[] =
                                [
                                'user_survey_respondent_id' => $user_survey_respondent_id,
                                'option_id' => $option[$key],
                                'question_id' => $question,
                                'text_response' => $text_response,
                            ];
                        }
                    } else {
                        $output =
                            [
                            'user_survey_respondent_id' => $user_survey_respondent_id,
                            'option_id' => $option,
                            'question_id' => $question,
                            'text_response' => $text_response,
                        ];
                    }

                    /*echo "<pre>";
                    print_r($output);
                    echo "</pre>";*/

                    DB::table('responses')->insert($output);
                    unset($output);

                }
            }

        }
// return null;

        $status = ['survey_status' => 3, 'last_submitted_date' => date('Y-m-d H:i:s')];

        DB::table('user_survey_respondent')->where('participant_id', $participant_id)->where('survey_id', $survey_id)->update($status);
        $survey_details = DB::table('surverys')->find($survey_id);
        return redirect()->route('othankyou', $survey_details->url);

    }
    //

    public function thankyou_screen($survey_url)
    {
        $survey_id = DB::table('surverys')->where('url', $survey_url)->value('id');
        $survey_details = DB::table('surverys')->find($survey_id);

        $thankyou_text = DB::table('questions')->where('survey_id', $survey_id)->where('display_order', '-1')->value('question_text');

        return view('users.questions.othankyou')
            ->with('title', 'Take Survey')
            ->with('survey_id', $survey_id)
            ->with('survey_details', $survey_details)
            ->with('thankyou_text', $thankyou_text);
    }

}
