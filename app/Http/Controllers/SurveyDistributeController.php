<?php

namespace App\Http\Controllers;

use App\EmailTemplate;
use DB;
use Illuminate\Http\Request;
use Mail;
use Redirect;
use Str;

class SurveyDistributeController extends Controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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

        $input = array();

        $input = $request->all();

        $file_name = '';
        if ($request->hasFile('attachment_doc')) {
            $file = $request->file('attachment_doc');

            // echo $OriginalName    = $file->getClientOriginalName();
            $OriginalName = basename($request->file('attachment_doc')->getClientOriginalName(), '.' . $request->file('attachment_doc')->getClientOriginalExtension());

            $file_extension = $request->file('attachment_doc')->getClientOriginalExtension();
            //    //Move Uploaded File
            $file_name = $OriginalName . '-' . strtotime("now") . "." . $file_extension;
            $destinationPath = $file->move(public_path('documents/'), $file_name);

        }

        // dd($input);
        $survey_id = $request->get('survey_id');

        $input = array();

        /*Participant part datas*/
        $input['send_email'] = $request->get('send_email');
        if ($request->get('send_email') == "notification-participant") {
            $input['from_email'] = $request->get('from_email_participant');
            $input['bcc'] = $request->get('bcc_participant');
            $input['cc'] = $request->get('cc_participant');
            $input['copy_email'] = $request->get('copy_email_participant');
            $input['subject'] = $request->get('subject_participant');
            $input['message_body'] = $request->get('message_body_participant');
        } elseif ($request->get('send_email') == "notification-respondent") {

            $input['from_email'] = $request->get('from_email_respondent');
            $input['bcc'] = $request->get('bcc_respondent');
            $input['cc'] = $request->get('cc_respondent');
            $input['copy_email'] = $request->get('copy_email_respondent');
            $input['subject'] = $request->get('subject_respondent');
            $input['message_body'] = $request->get('message_body_respondent');
        }
        /*Participant part datas*/

        /*Remainder part datas*/

        if ($request->get('send_email') == 'remainder-participant') {

            $input['from_email'] = $request->get('from_email_for_reminder_participant');
            $input['bcc'] = $request->get('bcc_for_reminder_participant');
            $input['cc'] = $request->get('cc_for_reminder_participant');
            $input['copy_email'] = $request->get('copy_email_for_reminder_participant');
            $input['subject'] = $request->get('subject_for_reminder_participant');
            $input['message_body'] = $request->get('message_body_for_reminder_participant');
        } elseif ($request->get('send_email') == "remainder-respondent") {

            $input['from_email'] = $request->get('from_email_for_reminder_respondent');
            $input['bcc'] = $request->get('bcc_for_reminder_respondent');
            $input['cc'] = $request->get('cc_for_reminder_respondent');
            $input['copy_email'] = $request->get('copy_email_for_reminder_respondent');
            $input['subject'] = $request->get('subject_for_reminder_respondent');
            $input['message_body'] = $request->get('message_body_for_reminder_respondent');
        }

        /*Remainder part datas*/

        if ($request->get('send_email') == "notification-participant") {
            $subject = $request->get('subject_participant');
            $content = $request->get('message_body_participant');

        } elseif ($request->get('send_email') == "notification-respondent") {
            $subject = $request->get('subject_respondent');
            $content = $request->get('message_body_respondent');
        } elseif ($request->get('send_email') == "remainder-participant") {
            $subject = $request->get('subject_for_reminder_participant');
            $content = $request->get('message_body_for_reminder_participant');
        } elseif ($request->get('send_email') == "remainder-respondent") {
            $subject = $request->get('subject_for_reminder_respondent');
            $content = $request->get('message_body_for_reminder_respondent');
        }

        EmailTemplate::updateOrCreate(
            ['survey_id' => $survey_id, 'type' => $request->get('send_email')],

            [
                'subject' => $subject,
                'content' => $content,
                'type' => $request->get('send_email'),
            ]);

        //dd($input);
        $copy_email = '';
        if (!empty($input['copy_email'])) {
            $copy_email = explode(',', $input['copy_email']);
        }

        foreach ($input['bcc'] as $value) {

            $userid = DB::table('users')->where('email', $value)->pluck('id');
            $userinfo = DB::table('users')->find($userid);

            $respondent_details = DB::table('users')
                ->join('user_survey_respondent', 'users.id', '=', 'user_survey_respondent.respondent_id')
                ->join('rater', 'rater.id', '=', 'user_survey_respondent.rater_id')
                ->where('users.id', '>', 1)
                ->where('user_survey_respondent.respondent_id', '!=', 0)
                ->whereIn('user_survey_respondent.survey_status', [1, 2, 4])
                ->where('user_survey_respondent.respondent_id', $userinfo->id)
                ->where('user_survey_respondent.survey_id', $survey_id)->get();

            $participant_details = DB::table('users')
                ->join('user_survey_respondent', 'users.id', '=', 'user_survey_respondent.participant_id')
                ->join('rater', 'rater.id', '=', 'user_survey_respondent.rater_id')
                ->where('users.id', '>', 1)
                ->where('user_survey_respondent.respondent_id', '=', 0)
                ->whereIn('user_survey_respondent.survey_status', [1, 2, 4])
                ->where('user_survey_respondent.participant_id', $userinfo->id)
                ->where('user_survey_respondent.survey_id', $survey_id)->get();

            $result = $respondent_details->merge($participant_details);

            $no = 1;

            if (count($result) > 0) {
                $rater_content = array();
                foreach ($result as $rater) {

                    $user_details = DB::table('users')->where('id', $rater->participant_id)->first();
                    $first_name = $user_details->fname;
                    $last_name = $user_details->lname;
                    $participant_name = $first_name . ' ' . $last_name;
                    if ($rater->rater != "self") {
                        $rater_content[] = '<p><b>' . $no . '. ' . ucfirst($participant_name) . '</b></p>';
                        //$rater_content[]='<b>'.ucfirst($participant_name).'</b><br>';
                    }

                    $no++;

                }

                $rater_details = implode(' ', $rater_content);
                unset($rater_content);
                $content = '';

/*  if ($request->get('send_email')=="remainder-respondent" || $request->get('send_email')=="notification-respondent") {
$content='<b>You have been invited to respond to a 360° Feedback Survey for:</b>';
}
else{
$content='<b>Yourself</b><br>';
}
 */
            } else {

                $rater_details = '';
                $content = '';
            }

            try {
                $password = decrypt($userinfo->password);
            } catch (\Exception $e) {
                $decrypted =  encrypt(rand(1,100000000));;
                $password = decrypt($decrypted);
            }
            $survey_details = DB::table('surverys')->find($survey_id);
            $survey_url = url('/' . $survey_details->url) . '/login';

            $survey_info_replace = '<p>' . $content . '' . $rater_details . '</p>';

            $login_info_replace = '<p>Link: <b>' . $survey_url . '</b><br> Email: <b>' . $userinfo->email . '</b><br>Password: <b>' . $password . '</b><br/></p>';

            $user_fname_replace = '<span>' . ucfirst($userinfo->fname) . ' </span>';
            $user_lname_replace = '<span>' . ucfirst($userinfo->lname) . ' </span>';

            $search = array('[Surveys list]', '[Login Details]', '[fname]', '[lname]');
            $replace = array($survey_info_replace, $login_info_replace, $user_fname_replace, $user_lname_replace);

            $message_body = str_replace($search, $replace, $input['message_body']);

            $data = array(
                'send_email' => $value,
                'from_email' => $input['from_email'],
                'subject' => $input['subject'],
                'message_body' => $message_body,
                'cc' => $input['cc'],
                'copy_email' => $copy_email,
            );
            $subject_search = array('[fname]', '[lname]');
            $subject_replace = array(ucfirst($userinfo->fname), ucfirst($userinfo->lname));
            $subject_body = str_replace($subject_search, $subject_replace, $input['subject']);
            $data['subject'] =$subject_body;
            $replay_to=$request->replay_to;
            $sender_name = isset($survey_details->sender_name)?$survey_details->sender_name:'Survey Support';
            Mail::send(['html' => 'admin.distribute.sendemail'], $data, function ($message) use ($data, $file_name, $sender_name,$replay_to) {
                $message->from($data['from_email'], $sender_name);
                $message->to($data['send_email']);

                if (!empty($data['cc'])) {
                    $message->cc($data['cc']);
                }
                if (!empty($data['copy_email'])) {
                    $message->bcc($data['copy_email']);
                }
                $message->subject($data['subject']);

                if (isset($replay_to)) {
                    $message->replyTo($replay_to);
                }
                if (isset($file_name)) {
                    if ($file_name != '') {
                        $message->attach(public_path('documents/' . $file_name));
                    }
                }

            });

            if (count(Mail::failures()) == 0) {

                date_default_timezone_set('Asia/Kolkata');
                $currenttime = date('Y-m-d H:i:s');

                if ($input['send_email'] == "remainder-participant") {
                    $if_respondent = 'no';
                    $update = ['reminder_email_date' => $currenttime];
                } else if ($input['send_email'] == "remainder-respondent") {
                    $if_respondent = 'yes';
                    $update = ['reminder_email_date' => $currenttime];
                } else if ($input['send_email'] == "notification-respondent") {
                    $if_respondent = 'yes';
                    $update = ['notify_email_date' => $currenttime];
                } else if ($input['send_email'] == "notification-participant") {
                    $if_respondent = 'no';
                    $update = ['notify_email_date' => $currenttime];
                }

                $update_datas = DB::table('user_survey_respondent');
                if ($if_respondent == "yes") {
                    $update_datas->where('respondent_id', $userid);
                }

                if ($if_respondent == "no") {
                    $update_datas->where('participant_id', $userid)->where('respondent_id', '=', '0');
                }

                $update_datas->where('survey_id', $survey_id)->update($update);

                /*Update Password */
                DB::table('users')->where('id', $userid)->update(['password' => encrypt($password)]);

                $error_info['mailsent'][] = $value;
                // $userinfo=DB::table('users')->find($userid);
            } else {
                $error_info['mail_failed'][] = $value;
            }

        }

        return Redirect::route('admin.dashboard')->with('error_info', $error_info);

        // }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $survey_id = $id;
         $survey_details= DB::table('surverys')->where('id', $id)->first();
         $from_email=$survey_details->send_email_from;
         $from_name=$survey_details->sender_name;
        $users = $remind_users = array();

        $email_templates = EmailTemplate::where('survey_id', $survey_id)->get();

        $templates = array();
        foreach ($email_templates as $key => $template) {
            $templates[$template->type] = [
                'subject' => $template->subject,
                'content' => $template->content,
            ];
        }

        $participants = DB::table('users')
            ->select('users.email', 'users.id', 'users.fname', 'users.lname', 'user_survey_respondent.survey_status')
            ->join('user_survey_respondent', 'users.id', '=', 'user_survey_respondent.participant_id')
            ->where('users.id', '>', 1)
            ->where('user_survey_respondent.survey_id', $id)
            ->where('user_survey_respondent.notify_email_date', null)
            ->whereIn('user_survey_respondent.survey_status',[1,2])
            ->where('user_survey_respondent.respondent_id', '=', 0)
            ->get();

        $remind_participants = DB::table('users')
            ->select('users.email', 'users.id', 'users.fname', 'users.lname', 'user_survey_respondent.survey_status')
            ->join('user_survey_respondent', 'users.id', '=', 'user_survey_respondent.participant_id')
            ->where('users.id', '>', 1)
            ->where('user_survey_respondent.survey_id', $id)
            ->where('user_survey_respondent.notify_email_date', '<>', null)
        //->where('user_survey_respondent.reminder_email_date',null)
            ->whereIn('user_survey_respondent.survey_status', [1, 2, 4])
            ->where('user_survey_respondent.respondent_id', '=', 0)
            ->get();

        $respondents = DB::table('users')
            ->select('users.email', 'users.id', 'users.fname', 'users.lname', 'user_survey_respondent.survey_status')
            ->join('user_survey_respondent', 'users.id', '=', 'user_survey_respondent.respondent_id')
            ->where('users.id', '>', 1)
            ->where('user_survey_respondent.survey_id', $id)
            ->where('user_survey_respondent.notify_email_date', null)
            ->whereIn('user_survey_respondent.survey_status',[1,2])
            ->where('user_survey_respondent.respondent_id', '>', 0)
            ->groupBy('users.email')
            ->get();

        $remind_respondents = DB::table('users')
            ->select('users.email', 'users.id', 'users.fname', 'users.lname', 'user_survey_respondent.survey_status')
            ->join('user_survey_respondent', 'users.id', '=', 'user_survey_respondent.respondent_id')
            ->where('users.id', '>', 1)
            ->where('user_survey_respondent.survey_id', $id)
            ->where('user_survey_respondent.notify_email_date', '<>', null)
        //->where('user_survey_respondent.reminder_email_date',null)
            ->whereIn('user_survey_respondent.survey_status', [1, 2, 4])
            ->where('user_survey_respondent.respondent_id', '>', 0)
            ->groupBy('users.email')
            ->get();

        return view('admin.distribute.index')
            ->with('participants', $participants)
            ->with('remind_participants', $remind_participants)
            ->with('respondents', $respondents)
            ->with('remind_respondents', $remind_respondents)
            ->with('survey_id', $id)
            ->with('from_email', $from_email)
            ->with('from_name', $from_name)
            ->with('title', 'Listing Participants to the survey')
            ->with('email_templates', $templates);

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

}
