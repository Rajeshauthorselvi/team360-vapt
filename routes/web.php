<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of the routes that are handled
| by your application. Just tell Laravel the URIs it should respond
| to using a Closure or controller method. Build something great!
|
*/


Route::get('dummy-execution', 'AddusersController@DummyExecution');
Route::group(['middleware' => ['guest']], function () {
    Route::group(['prefix' => '{survey_name}'], function () {
        Route::get('login', ['as' => 'login', 'uses' => 'UserSurveyController@create']);
        Route::post('login', ['as' => 'user_login', 'uses' => 'UserSurveyController@store']);
    });

    Route::get('login', ['as' => 'login', 'uses' => 'LoginController@index']);
    Route::post('login', ['as' => 'login', 'uses' => 'LoginController@post_login']);
    Route::get('/', ['as' => 'clogin', 'uses' => 'LoginController@index']);

    Route::post('reset', ['as' => 'reset_pass_index', 'uses' => 'LoginController@ResetPasswordindex']);

    Route::group(['prefix' => 'os'], function () {
        Route::get('{open_survey_name}', ['as' => 'opensurvey', 'uses' => 'LoginController@open_survey']);
        Route::post('ostore', ['as' => 'ostore', 'uses' => 'LoginController@store_open_survey']);
        Route::get('thankyou/{open_survey_name}', ['as' => 'othankyou', 'uses' => 'LoginController@thankyou_screen']);
    });
});


Route::get('status_report', ['as' => 'status.report', 'uses' => 'DownloadStatusReport@SurveyReport']);
Route::get('status_summary', ['as' => 'summary.report', 'uses' => 'DownloadStatusReport@SummaryReport']);

Route::group(['middleware' => ['auth', 'admin']], function () {

    Route::get('validate_survey', ['as' => 'check_survey', 'uses' => 'SurveyController@checkSurvey']);
    Route::get('validate_ques', ['as' => 'check_question', 'uses' => 'QuestionController@checkQuestion']);
    Route::get('validate_respondent', ['as' => 'checkparticipant', 'uses' => 'RespondentController@checkparticipant']); //check respondent email already exist
    Route::get('checkparticipant_email', ['as' => 'checkparticipant_email', 'uses' => 'AddusersController@checkparticipant_email']); //check participant already exists for this survey
    Route::get('searchajax', array('as' => 'searchajax', 'uses' => 'SurveyController@autoComplete'));
    Route::resource('questions', 'QuestionController');
    Route::get('delete-all', ['as' => 'delete.questions', 'uses' => 'QuestionController@DeleteAllQuestions']);
    Route::resource('survey', 'SurveyController');
    Route::get('delete_survey', ['as' => 'delete.survey', 'uses' => 'SurveyController@DeleteSurvey']);
    Route::patch('copy-survey', ['as' => 'copy-survey', 'uses' => 'SurveyController@copysurvey']);
    Route::get('home', ['as' => 'admin.dashboard', 'uses' => 'SurveyController@dashboard']);
    Route::get('admin-dashboard', ['as' => 'admin.dashboard', 'uses' => 'SurveyController@dashboard']);
    Route::resource('addusers', 'AddusersController');
    Route::post('delete_users',['as'=>'delete.users','uses'=>'AddusersController@DeleteUsers']);
    Route::resource('respondent', 'RespondentController');
    Route::get('single_delete_respondent',['as'=>'single.delete.respondent','uses'=>'RespondentController@SingleDeleteRespondent']);
    Route::post('delete_respondent',['as'=>'delete.respondent','uses'=>'RespondentController@DeleteRespondents']);

//---reports---//
    Route::resource('participantreport', 'ParticipantReportController');
    Route::get('report_dashboard', ['as' => 'report_dashboard', 'uses' => 'ParticipantReportController@ReportDashboard']);
    Route::get('itemwise_others_sort', ['as' => 'itemwise_others_sort', 'uses' => 'ParticipantReportController@itemwise_others_sort']);
    Route::get('topandbottom', ['as' => 'topandbottom', 'uses' => 'ParticipantReportController@top_and_bottom']);
    Route::get('converging_diverging', ['as' => 'converging_diverging', 'uses' => 'ParticipantReportController@converging_diverging']);
    Route::get('gap_report', ['as' => 'gap_report', 'uses' => 'ParticipantReportController@gap_report']);
    Route::get('dimension2', ['as' => 'dimension2', 'uses' => 'ParticipantReportController@DimensionTwoController']);
    Route::get('status-summary',['as'=>'status.status_summary','uses'=>'StatusReportcontroller@StatusSummaryController']);
    Route::get('status_summary_export',['as'=>'export.status_summary','uses'=>'ExportStatusController@StatusSummary']);

//---reports---//

    Route::post('importusers', ['as' => 'importusers', 'uses' => 'AddusersController@importusers']);
    Route::post('importRespondent', ['as' => 'importRespondent', 'uses' => 'RespondentController@importRespondent']);

    Route::get('import-respondent', ['as' => 'respondent.only_importrespondent', 'uses' => 'RespondentController@only_importrespondent']);
    Route::post('import-respondents', ['as' => 'import_respondents', 'uses' => 'RespondentController@only_importrespondent_store']);
    Route::get('respondent-download', ['as' => 'respondent.download', 'uses' => 'RespondentController@respondent_download']);

    Route::resource('distribute', 'SurveyDistributeController');

    Route::post('status_reminder', ['as' => 'status_reminder', 'uses' => 'SurveyDistributeController@status_reminder']);  //status reminder in distribute

    Route::resource('import-questions', 'QuestionImportController');
    Route::resource('theme', 'ThemeController');

    Route::resource('test', 'TestController');  //dhinesh

    Route::post('questionEdit', ['as' => 'questionEdit', 'uses' => 'QuestionController@questionEdit']);

    Route::post('questionEditGrid', ['as' => 'questionEditGrid', 'uses' => 'QuestionController@questionEditGrid']);

    Route::get('questions_group', ['as' => 'questions_group', 'uses' => 'QuestionController@QuestionGroupController']);

    Route::post('questions_post_group', ['as' => 'questions_post_group', 'uses' => 'QuestionController@QuestionpostController']);


    Route::get('resend', ['as' => 'resend.resendacess', 'uses' => 'ResendAcess@ResendAccessDetails']);

    Route::get('reopen', ['as' => 'addusers.Reopen_survey', 'uses' => 'AddusersController@Reopen_survey']);
    Route::get('clear_response', ['as' => 'addusers.Clear_response', 'uses' => 'AddusersController@Clear_response']);


    Route::get('reopen_survey', ['as' => 'respondent.reopen_survey', 'uses' => 'RespondentController@reopen_survey']);
    Route::get('clear_responses', ['as' => 'respondent.clear_response', 'uses' => 'RespondentController@clear_response']);


    Route::get('status-report', ['as' => 'status.status_report', 'uses' => 'StatusReportcontroller@ReportController']);
    Route::post('export_status_report', ['as' => 'export.status_report', 'uses' => 'ExportStatusController@ExportStatus']);

    Route::get('text_response', ['as' => 'text.text_response', 'uses' => 'StatusReportcontroller@textresponseIndex']);
    Route::post('text_response', ['as' => 'post.text_response', 'uses' => 'StatusReportcontroller@textresponseIndex']);
    Route::get('export_text_response', ['as' => 'export.text_response', 'uses' => 'ExportStatusController@text_response']);

    Route::get('rawscore', ['as' => 'status.rawscore', 'uses' => 'StatusReportcontroller@RawscoreController']);
    Route::post('raw_response', ['as' => 'post.raw_response', 'uses' => 'StatusReportcontroller@RawscoreController']);
    Route::get('raw_response', ['as' => 'post.raw_response', 'uses' => 'StatusReportcontroller@RawscoreController']);
    Route::get('export_rawscore_report', ['as' => 'export.rawscore_report', 'uses' => 'ExportStatusController@RawscoreExport']);

//Route::get('participant_report',['as'=>'participant.participant_report','uses'=>'StatusReportcontroller@participant_report']);

    Route::get('question_export', ['as' => 'ques.ques_export', 'uses' => 'QuestionController@downloadQuesController']);


    Route::get('question_exportss', ['as' => 'raw_core_ques.ques_export', 'uses' => 'StatusReportcontroller@download_raw_score_QuesController']);

    Route::get('question_exportsss', ['as' => 'text_response_ques.ques_export', 'uses' => 'StatusReportcontroller@download_text_response_QuesController']);


    Route::get('prev_login', ['as' => 'prev_login', 'uses' => 'previewController@index']);
    Route::get('prev_ques', ['as' => 'prev_question', 'uses' => 'previewController@questionpreview']);


    /*dhinesh */
//Multiple Delete
//Route::delete('users_destroy', ['as'=>'users.destroy', 'uses'=>'SurveyController@Deletemultiple']);


    /*Report*/

//Route::get('report_dashboard',['as'=>'report_dashboard', 'uses'=>'ReportController@ReportDashboard']);

//Dimension Report 1
    Route::get('diminsion1', ['as' => 'diminsion1', 'uses' => 'ReportController@DimensionOneController']);
// Route::get('diminsion1_download',['as'=>'diminsion1_download', 'uses'=>'ReportController@DiminsionReportDownload']);

//Dimension Report 2
    Route::get('diminsion2', ['as' => 'diminsion2', 'uses' => 'ReportController@DimensionTwoController']);

//Dimension Item report
    Route::get('diminsion_item', ['as' => 'diminsion_item', 'uses' => 'ReportController@DimensionItemController']);
//open Ended Report
    Route::get('diminsion_open_ended', ['as' => 'diminsion_open_ended', 'uses' => 'ReportController@OpenEndedReport']);

//Item Wise Self (Self & Others)
    Route::get('item_wise_self', ['as' => 'item_wise_self', 'uses' => 'ReportController@ItemWise']);

//report Based on Dimension
    Route::get('question_dimension_based', ['as' => 'question_dimension_based', 'uses' => 'ReportController1@QuestiondimensionBased']);
    Route::get('question_dimension', ['as' => 'question_dimension', 'uses' => 'ReportController1@ReportQuestionDimension']);

    Route::resource('users-password', 'SurveyUsersPasswordController');

});

Route::group(['middleware' => ['auth', 'user']], function () {

    Route::group(['prefix' => '{survey_name}'], function () {

        /*	Route::get('login',function($survey_name){
                return redirect()->route('user.dashboard',$survey_name);
            });*/

        Route::resource('user', 'UserController');

        Route::get('thankyou', ['as' => 'thankyou_screen', 'uses' => 'UserController@create']);

        Route::get('user-dashboard', ['as' => 'user.dashboard', 'uses' => 'UserSurveyController@index']);

        Route::get('change-password/{user_id}', ['as' => 'change-password', 'uses' => 'UserSurveyController@show']);
        Route::patch('change-password/update', ['as' => 'change-password', 'uses' => 'UserSurveyController@update']);

        Route::get('signout', ['as' => 'signout', 'uses' => 'UserSurveyController@signout']);

        Route::resource('manage-respondent', 'UserRespondentController');
        Route::resource('manage-email', 'UserDistributeController');
        Route::post('import_Respondent', ['as' => 'import_Respondent', 'uses' => 'UserRespondentController@importRespondent']);

    });

    Route::get('resend_access', ['as' => 'resend.resendaccess', 'uses' => 'ResendAcess@ResendAccessDetails']);


});


Route::get('logout', function () {

    Auth::logout();
    Session::flush();
    return Redirect::to('login');

})->middleware('auth');


