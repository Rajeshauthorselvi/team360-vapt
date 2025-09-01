<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Mail;
use Redirect;
use Str;
class ResendAcess extends Controller
{
    public function ResendAccessDetails(Request $request)
    {
        $respondent_id = $request->get('respondent_id');
        $error_info = array();
        $participant_id = $request->get('participant_id');
        $survey_id = $request->get('survey_id');

        if ($respondent_id == '0') {
            $userinfo = DB::table('users')->find($participant_id);
        } else {
            $userinfo = DB::table('users')->find($respondent_id);
        }

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

        try {
            $encrypted = $userinfo->password;
            $decrypted = decrypt($userinfo->password);
        } catch (\Exception $e) {
            $decrypted = Str::random(8);
            $encrypted = encrypt($decrypted);
        }
        $password = $decrypted;

        $from_email=DB::table('surverys')->where('id',$survey_id)->orderby('id', 'DESC')->value('send_email_from');
        $data = [
            'email_form' => $from_email,
            'to_email' => $userinfo->email,
            'subject' => 'Resend Acess',
            'password'=>$password,
        ];
        $sender_name='Survey Support';
        Mail::send('email.forgot_email', $data, function ($message) use ($data,$sender_name) {
            $message->from($data['email_form'],$sender_name)
                ->to($data['to_email'])
                ->subject($data['subject']);
        });

        DB::table('users')
        ->where('email', $userinfo->email)
        ->update(['password' => $encrypted]);

        return redirect()->back()->with('mailstatus', $error_info);
        /*
        dd($decrypted,$encrypted,$data);
        DB::table('users')
            ->where('email', $userinfo->email)
            ->update(['password' => $encrypted]);

        $no = 1;
        if (count($result) > 0) {
            $rater_content = array();
            foreach ($result as $rater) {

                $participant_name = DB::table('users')->where('id', $rater->participant_id)->value('fname');
                if ($rater->rater != "self") {
                    $rater_content[] = '<p>' . $no . '. ' . ucfirst($participant_name) . '</p>';
                }
                $no++;
            }

            $rater_details = implode(' ', $rater_content);
            unset($rater_content);
            $content = '<br><b>You have been invited to respond to a 360° Feedback Survey for:</b><br/>';
        } else {
            $rater_details = '';
            $content = '';
        }


        if ($rater_details=="") {
            $content="";
        }
        try {
            $decrypted = decrypt($userinfo->password);
        } catch (\Exception $e) {
            $decrypted = Str::random(8);
        }
        $random_pwd = $decrypted;
        $survey_details = DB::table('surverys')->find($survey_id);
        $survey_url = url('/' . $survey_details->url) . '/login';

        $user_fname = '<span>' . ucfirst($userinfo->fname) . ' </span>';
        $user_lname = '<span>' . ucfirst($userinfo->lname) . ' </span>';

        $survey_info_replace = '<p>' . $user_fname . '' . $user_lname .(($content!="")?'<br/><br>'.$content:'<br/>' ). '' . $rater_details . '<br><b>Here is your login details: </b><br><br/> Link: ' . $survey_url . '<br><br> Email: ' . $userinfo->email . '<br><br>Password: ' . $random_pwd . '<br/><br/>Thank you</p>';
        $data = array(
            'send_email' => $userinfo->email,
            'from_email' => $survey_details->send_email_from,
            'subject' => 'Resend Acess',
            'message_body' => $survey_info_replace,
        );
        $sender_name = isset($survey_details->sender_name)?$survey_details->sender_name:'Survey Support';
        Mail::send(['html' => 'admin.distribute.sendemail'], $data, function ($message) use ($data, $sender_name) {
            $message->from($data['from_email'], $sender_name)
                ->to($data['send_email'])
                ->subject($data['subject']);

        });

        if (count(Mail::failures()) == 0) {
            $currenttime = date('Y-m-d H:i:s');
            $update = ['reminder_email_date' => $currenttime];
            $update_user_survey_respondent = DB::table('user_survey_respondent')->where('participant_id', $participant_id)->where('respondent_id', $respondent_id)->where('survey_id', $survey_id)->update($update);

        } else {
            $error_info[] = "";
        }

        return redirect()->back()->with('mailstatus', $error_info);
        */
    }
}
