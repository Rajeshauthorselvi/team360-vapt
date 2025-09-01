<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\UserSurvey;
use App\UserSurveyQuestion;
use Auth;
use DB;
use Hash;
use Illuminate\Http\Request;
use Session;
use Validator;

class UserController extends Controller
{
    public function __construct(Request $request)
    {

        $path_url = $request->path();
        $survey_info = UserSurvey::getInfo($path_url);

        $configuration = [
            'site.theme' => $survey_info->title,
            'site.survey_slug' => $survey_info->url,
            'site.survey_title' => $survey_info->survey_name,
            'site.survey_id' => $survey_info->survey_id,
            'site.survey_theme_id' => $survey_info->survey_theme_id,
            'site.left_logo' => $survey_info->logo,
            'site.right_logo' => $survey_info->right_logo,
            'site.header_text' => $survey_info->header_text,
            'site.footer_text' => $survey_info->footer_text,
            'site.theme_slug' => $survey_info->file,
            'site.shuffle_questions' => $survey_info->shuffle_questions,
            'site.question_per_page' => $survey_info->question_per_page,
            'site.dimension_hide' => $survey_info->dimension_hide,
            'site.url' => $survey_info->url,

        ];

        config($configuration);

    }
    /**

     * Display a listing of the resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function index(Request $request)
    {

        $input = $request->all();

        $survey_id = config('site.survey_id');

       

        $rater = (isset($input['rater'])) ? $input['rater'] : 0;
        $participant_id = (isset($input['participant'])) ? $input['participant'] : 0;

        $rater_name = DB::table('rater')->where('id', $rater)->value('rater');
        $participant_name = DB::table('users')->where('id', $participant_id)->value(DB::raw("CONCAT(`fname`, ' ', `lname`)"));

        $questions_without_textarea = UserSurveyQuestion::getQuestions($survey_id, $rater);
        $questions_with_textarea = UserSurveyQuestion::getQuestions($survey_id, $rater, 1);
        $questions = $questions_without_textarea->merge($questions_with_textarea);
        $questions = collect($questions)->sortBy('display_order')->toArray();
        if (config('site.theme') == "Dimension_Wise_Page") {
            $questions = UserSurveyQuestion::DimensionBasedQuestions($questions);
        }
        $welcome_text = UserSurveyQuestion::getWelcomeText($survey_id);
        $user_survey_id = UserSurveyQuestion::getUserSurveyId($survey_id, $participant_id, $rater, Auth::id());
        $user_responses = UserSurveyQuestion::getUserSurveyResponses($user_survey_id);
        $response_count = UserSurveyQuestion::getUserSurveyResponsesCount($user_survey_id, $survey_id);
        $user_survey_message = 'evaluating as ' . ucfirst($rater);

        $data['title'] = 'Take Survey';
        $data['survey_id'] = $survey_id;
        $data['welcome_text'] = $welcome_text;
        $data['user_survey_id'] = $user_survey_id;
        $data['responses'] = $user_responses;
        $data['questions'] = $questions;
        $data['response_count'] = $response_count;
        $data['dimension_hide'] = config('site.dimension_hide');
        $data['user_survey_message'] = $user_survey_message;
        $data['question_per_page'] = config('site.question_per_page');
        $data['url'] =config('site.url');

        $view = config('site.theme') . '.users.questions.index';
        if ($rater_name == "self") {
            $msg = "Self Assessment";
        } else {
            if ($survey_id==4) {
                $msg = 'Evaluating ' . $participant_name;
            }
            else{
                $msg = 'Evaluating ' . $participant_name . ' as ' . ucfirst($rater_name);
            }
        }
        Session::put('rater_msg', $msg);

        return view($view, $data);

    }

    /**

     * Show the form for creating a new resource.

     *

     * @return \Illuminate\Http\Response

     */

    public function create()
    {

        $survey_id = config('site.survey_id');
        $thankyou_text = DB::table('questions')->where('survey_id', $survey_id)->where('display_order', '-1')->value('question_text');

        $data['title'] = 'Take Survey';
        $data['survey_id'] = $survey_id;
        $data['thankyou_text'] = $thankyou_text;
        $view = config('site.theme') . '.users.questions.thankyou';
        return view($view, $data);

    }

    /**

     * Store a newly created resource in storage.

     *

     * @param  \Illuminate\Http\Request  $request

     * @return \Illuminate\Http\Response

     */

    public function store(Request $request)
    {

        $responses = $request->all();

        $user_survey_respondent_id = $responses['user_survey_respondent_id'];

        $question_id = $responses['question_id'];

        DB::table('responses')->where('user_survey_respondent_id', $user_survey_respondent_id)->delete();

        foreach ($question_id as $question_value) {

            $array1 = explode('|', $question_value);

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

        $action_type = $request->get('formaction');

        $status_id = '';

        if ($action_type == "save") {
            $status_id = 2;
        }

        if ($action_type == "submit") {
            $status_id = 3;
        }

        $status = ['survey_status' => $status_id, 'last_submitted_date' => date('Y-m-d H:i:s')];

        if ($status_id == 3) {

            DB::table('user_survey_respondent')->where('id', $user_survey_respondent_id)->update($status);

            return redirect()->route('thankyou_screen', config('site.survey_slug'));

        } else {

            if ($status_id == 2) {

                DB::table('user_survey_respondent')->where('id', $user_survey_respondent_id)->update($status);

            }

            return redirect()->route('user.dashboard', config('site.survey_slug'));

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

        $id = Auth::user()->getsurvey_Details()->id;

        $questions = DB::table('questions')

            ->select('surverys.id', 'questions.id as question_id', 'questions.question_text', 'questions.question_type', 'questions.question_required', 'questions.question_dimension')

            ->join('surverys', 'questions.survey_id', '=', 'surverys.id')

            ->where('questions.survey_id', $id)

        //->where('questions.question_enabled',1)

            ->where('display_order', '>', 0)

            ->get();

        foreach ($questions as $key => $question) {

            $option_values = DB::table('options')->where('question_id', $question->question_id)->get();

            $question->options = $option_values;

        }

        return view('users.questions.index')

            ->with('title', 'Take Survey')

            ->with('questions', $questions);

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

    public function thankyou_screen()
    {

        $id = Auth::user()->getsurvey_Details()->id;

        $thankyou_text = DB::table('questions')->where('survey_id', $id)->where('display_order', '-1')->value('question_text');

        return view('users.questions.thankyou')

            ->with('title', 'Take Survey')

            ->with('survey_id', $id)

            ->with('thankyou_text', $thankyou_text);

    }

    public function signout()
    {
        $survey_url = Auth::user()->getsurvey_Details()->url;

        Auth::logout();

        Session::flush();

        return redirect()->to($survey_url . '/login');
    }

    public function change_password(Request $request)
    {

        $url = Auth::user()->getsurvey_Details()->url;
        $user_id = Auth::id();

        $check_url = DB::table('surverys')
            ->select('themes.file')
            ->leftjoin('themes', 'surverys.survey_theme_id', '=', 'themes.id')
            ->where('surverys.url', $url)
            ->get();

        return view('users.change_password.index')

            ->with('title', 'Change Password')
            ->with('themes', $check_url);

    }
    public function save_password(Request $request)
    {
        $rules = array(
            'password' => 'required',
            'confirm_password' => 'required|same:password',
        );
        $input = $request->all();
        $validator = Validator::make($input, $rules);

        if ($validator->passes()) {
            $user_id = Auth::id();
            $new_password = $request['password'];
            $hashed_password = Hash::make($new_password);
            $confirm_password = $request['confirm_password'];

            DB::table('users')->where('id', $user_id)->update(['password' => $hashed_password]);

            return redirect()->route('user.dashboard', Auth::user()->getsurvey_Details()->url);
        } else {
            return redirect()->back()->withErrors($validator)->withInput();
        }
    }

}
