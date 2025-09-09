<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\SurveyController;
use App\Http\Controllers\AddusersController;
use App\Http\Controllers\QuestionController;
use App\Http\Controllers\QuestionImportController;
use App\Http\Controllers\TestController;
use App\Http\Controllers\ThemeController;
use App\Http\Controllers\SurveyDistributeController;
use App\Http\Controllers\DownloadStatusReport;
use App\Http\Controllers\RespondentController;
use App\Http\Controllers\ParticipantReportController;
use App\Http\Controllers\StatusReportcontroller;
use App\Http\Controllers\ExportStatusController;
use App\Http\Controllers\ResendAcess;
use App\Http\Controllers\previewController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\UserSurveyController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\ReportController1;
use App\Http\Controllers\SurveyUsersPasswordController;
use App\Http\Controllers\UserRespondentController;
use App\Http\Controllers\UserDistributeController;
use App\Http\Controllers\LoginController;


Route::get('dummy-execution', 'AddusersController@DummyExecution');
Route::group(['middleware' => ['guest']], function () {
    Route::group(['prefix' => '{survey_name}'], function () {
        Route::get('login', [UserSurveyController::class, 'create'])->name('login');
        Route::post('login', [UserSurveyController::class, 'store'])->name('user_login');

    });

    Route::get('login', [LoginController::class, 'index'])->name('login');
    Route::post('login', [LoginController::class, 'post_login'])->name('login');
    Route::get('/', [LoginController::class, 'index'])->name('clogin');

    Route::post('reset', [LoginController::class, 'ResetPasswordindex'])->name('reset_pass_index');

    Route::group(['prefix' => 'os'], function () {
        Route::get('{open_survey_name}', [LoginController::class, 'open_survey'])->name('opensurvey');
        Route::post('ostore', [LoginController::class, 'store_open_survey'])->name('ostore');
        Route::get('thankyou/{open_survey_name}', [LoginController::class, 'thankyou_screen'])->name('othankyou');
    });
});


Route::get('status_report', [DownloadStatusReport::class,'SurveyReport'])->name('status.report');
Route::get('status_summary', [DownloadStatusReport::class,'SummaryReport'])->name('summary.report');

Route::group(['middleware' => ['auth', 'admin']], function () {

    Route::get('validate_survey', [SurveyController::class,'checkSurvey'])->name('check_survey');

    Route::get('validate_ques',[QuestionController::class,'checkQuestion'])->name('check_question');


    Route::get('validate_respondent',[RespondentController::class,'checkparticipant'])->name('checkparticipant');

    Route::get('checkparticipant_email',[AddusersController::class,'checkparticipant_email'])->name('checkparticipant_email');


    Route::get('searchajax', array('as' => 'searchajax', 'uses' => 'SurveyController@autoComplete'));
    Route::resource('questions', QuestionController::class);

    Route::get('delete-all', [QuestionController::class,'DeleteAllQuestions'])->name('delete.questions');

    Route::resource('survey', SurveyController::class);

    Route::get('delete_survey', [SurveyController::class,'DeleteSurvey'])->name('delete.survey');
    Route::patch('copy-survey', [SurveyController::class,'copysurvey'])->name('copy-survey');
    Route::get('home', [SurveyController::class,'dashboard'])->name('admin.dashboard');
    Route::get('admin-dashboard', [SurveyController::class,'dashboard'])->name('admin.dashboard');

    Route::resource('addusers', AddusersController::class);

    Route::post('delete_users',[AddusersController::class,'DeleteUsers'])->name('delete.users');
    Route::resource('respondent', RespondentController::class);
    Route::get('single_delete_respondent', [RespondentController::class, 'SingleDeleteRespondent'])->name('single.delete.respondent');
    Route::post('delete_respondent', [RespondentController::class, 'DeleteRespondents'])->name('delete.respondent');


//---reports---//
    Route::resource('participantreport', ParticipantReportController::class);
    Route::get('report_dashboard',[ParticipantReportController::class,'ReportDashboard'])->name('report_dashboard');
    Route::get('itemwise_others_sort',[ParticipantReportController::class,'itemwise_others_sort'])->name('itemwise_others_sort');
    Route::get('topandbottom',[ParticipantReportController::class,'top_and_bottom'])->name('topandbottom');
    Route::get('converging_diverging',[ParticipantReportController::class,'converging_diverging'])->name('converging_diverging');

    Route::get('gap_report',[ParticipantReportController::class,'gap_report'])->name('gap_report');
    Route::get('dimension2',[ParticipantReportController::class,'DimensionTwoController'])->name('dimension2');

    Route::get('status-summary',[StatusReportcontroller::class,'StatusSummaryController'])->name('status.status_summary');
    Route::get('status_summary_export',[ExportStatusController::class,'StatusSummary'])->name('export.status_summary');



//---reports---//

    Route::post('importusers', [AddusersController::class,'importusers'])->name('importusers');
    Route::post('importusers', [AddusersController::class,'importusers'])->name('importusers');
    Route::post('importRespondent', [AddusersController::class,'importusers'])->name('importusers');


    Route::post('importRespondent', [RespondentController::class, 'importRespondent'])->name('importRespondent');

    Route::get('import-respondent', [RespondentController::class, 'only_importrespondent'])->name('respondent.only_importrespondent');
    Route::post('import-respondents', [RespondentController::class, 'only_importrespondent_store'])->name('import_respondents');
    Route::get('respondent-download', [RespondentController::class, 'respondent_download'])->name('respondent.download');


    Route::resource('distribute', SurveyDistributeController::class);

    Route::post('status_reminder', [SurveyDistributeController::class,'status_reminder'])->name('status_reminder');

    Route::resource('import-questions', QuestionImportController::class);
    Route::resource('theme', ThemeController::class);

    Route::resource('test', TestController::class);  //dhinesh

    Route::post('questionEdit', [QuestionController::class,'questionEdit'])->name('questionEdit');
    Route::post('questionEditGrid', [QuestionController::class,'questionEditGrid'])->name('questionEditGrid');


    Route::get('questions_group', [QuestionController::class,'QuestionGroupController'])->name('questions_group');


    Route::post('questions_post_group', [QuestionController::class,'QuestionpostController'])->name('questions_post_group');

    Route::get('resend', [ResendAcess::class, 'ResendAccessDetails'])->name('resend.resendacess');

    Route::get('reopen', [AddusersController::class, 'Reopen_survey'])->name('addusers.Reopen_survey');
    Route::get('clear_response', [AddusersController::class, 'Clear_response'])->name('addusers.Clear_response');

    Route::get('reopen_survey', [RespondentController::class, 'reopen_survey'])->name('respondent.reopen_survey');
    Route::get('clear_responses', [RespondentController::class, 'clear_response'])->name('respondent.clear_response');

    Route::get('status-report', [StatusReportcontroller::class, 'ReportController'])->name('status.status_report');
    Route::post('export_status_report', [ExportStatusController::class, 'ExportStatus'])->name('export.status_report');

    Route::get('text_response', [StatusReportcontroller::class, 'textresponseIndex'])->name('text.text_response');
    Route::post('text_response', [StatusReportcontroller::class, 'textresponseIndex'])->name('post.text_response');
    Route::get('export_text_response', [ExportStatusController::class, 'text_response'])->name('export.text_response');

    Route::get('rawscore', [StatusReportcontroller::class, 'RawscoreController'])->name('status.rawscore');
    Route::post('raw_response', [StatusReportcontroller::class, 'RawscoreController'])->name('post.raw_response');
    Route::get('raw_response', [StatusReportcontroller::class, 'RawscoreController'])->name('post.raw_response');
    Route::get('export_rawscore_report', [ExportStatusController::class, 'RawscoreExport'])->name('export.rawscore_report');

//Route::get('participant_report',['as'=>'participant.participant_report','uses'=>'StatusReportcontroller@participant_report']);

Route::get('question_export', [QuestionController::class, 'downloadQuesController'])->name('ques.ques_export');

Route::get('question_exportss', [StatusReportcontroller::class, 'download_raw_score_QuesController'])->name('raw_core_ques.ques_export');

Route::get('question_exportsss', [StatusReportcontroller::class, 'download_text_response_QuesController'])->name('text_response_ques.ques_export');

Route::get('prev_login', [previewController::class, 'index'])->name('prev_login');
Route::get('prev_ques', [previewController::class, 'questionpreview'])->name('prev_question');



    /*dhinesh */
//Multiple Delete
//Route::delete('users_destroy', ['as'=>'users.destroy', 'uses'=>'SurveyController@Deletemultiple']);


    /*Report*/

//Route::get('report_dashboard',['as'=>'report_dashboard', 'uses'=>'ReportController@ReportDashboard']);

// Dimension Report 1
Route::get('diminsion1', [ReportController::class, 'DimensionOneController'])->name('diminsion1');
// Route::get('diminsion1_download', [ReportController::class, 'DiminsionReportDownload'])->name('diminsion1_download');

// Dimension Report 2
Route::get('diminsion2', [ReportController::class, 'DimensionTwoController'])->name('diminsion2');

// Dimension Item report
Route::get('diminsion_item', [ReportController::class, 'DimensionItemController'])->name('diminsion_item');

// Open Ended Report
Route::get('diminsion_open_ended', [ReportController::class, 'OpenEndedReport'])->name('diminsion_open_ended');

// Item Wise Self (Self & Others)
Route::get('item_wise_self', [ReportController::class, 'ItemWise'])->name('item_wise_self');

// Report Based on Dimension
Route::get('question_dimension_based', [ReportController1::class, 'QuestiondimensionBased'])->name('question_dimension_based');
Route::get('question_dimension', [ReportController1::class, 'ReportQuestionDimension'])->name('question_dimension');

Route::resource('users-password', SurveyUsersPasswordController::class);


});

Route::group(['middleware' => ['auth', 'user']], function () {

    Route::group(['prefix' => '{survey_name}'], function () {

        /*	Route::get('login',function($survey_name){
                return redirect()->route('user.dashboard',$survey_name);
            });*/

            Route::resource('user', UserController::class);

            Route::get('thankyou', [UserController::class, 'create'])->name('thankyou_screen');

            Route::get('user-dashboard', [UserSurveyController::class, 'index'])->name('user.dashboard');

            Route::get('change-password/{user_id}', [UserSurveyController::class, 'show'])->name('change-password');
            Route::patch('change-password/update', [UserSurveyController::class, 'update'])->name('change-password');

            Route::get('signout', [UserSurveyController::class, 'signout'])->name('signout');

            Route::resource('manage-respondent', UserRespondentController::class);
            Route::resource('manage-email', UserDistributeController::class);

            Route::post('import_Respondent', [UserRespondentController::class, 'importRespondent'])->name('import_Respondent');


    });

    Route::get('resend_access', [ResendAcess::class, 'ResendAccessDetails'])->name('resend.resendaccess');



});


Route::get('logout', function () {

    Auth::logout();
    Session::flush();
    return Redirect::to('login');

})->middleware('auth');


