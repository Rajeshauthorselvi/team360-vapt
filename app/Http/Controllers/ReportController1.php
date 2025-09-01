<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Lava;
class ReportController1 extends Controller
{

    public function QuestiondimensionBased(Request $request)
    {
    	$survey_id=$request->get('survey_id');
    	$user_id=$request->get('user_id');
    	$question_dimensions=DB::table('questions')
    						->where('survey_id',$survey_id)
    						->where('question_dimension','<>','')
    						->groupBy('question_dimension')
                            ->orderBy('display_order')
    						->pluck('question_dimension','id as question_id');

    	return view('admin.report.question_dimension')
                ->with('title','Question Dimensions')
    			->with('survey_id',$survey_id)
    			->with('user_id',$user_id)
    			->with('question_dimension',$question_dimensions);
    }
public function ReportQuestionDimensionQuerys($survey_id,$user_id,$rater='',$question_dimension='')
{
		$selects=array('rater.id as rater_id','rater.rater','avg(options.option_weight) as total','questions.question_text','questions.id as question_id');
	    $report=DB::table('user_survey_respondent')
	    		->selectRaw(implode(',',$selects))
		        ->leftjoin('responses','user_survey_respondent.id','=','responses.user_survey_respondent_id')
		        ->leftjoin('questions','questions.id','responses.question_id')
		        ->leftjoin('options','options.id','responses.option_id')
		        ->leftjoin('rater','user_survey_respondent.rater_id','rater.id')
		        ->where('questions.display_order','>',0)
		        ->whereNotIn('questions.question_type',['text','textarea'])
		        ->where('user_survey_respondent.survey_status',3)
		        ->where('questions.question_dimension',$question_dimension)
		        ->where('rater.rater',$rater)
		        ->where('user_survey_respondent.survey_id',$survey_id)
		    	->where('user_survey_respondent.participant_id',$user_id)
		    	->groupBy('rater.rater','questions.question_text')
		    	->get()->toArray();

		return $report;
}
public function AllQuestions($survey_id,$question_dimension='')
{
      $all_questions=DB::table('questions')
                  ->where('survey_id',$survey_id)
                  ->whereNotIn('question_type',['text','textarea'])
                  ->where('questions.question_dimension',$question_dimension)
                  ->pluck('question_text','id as question_id')->toArray();
        return $all_questions;
}
public function getRaters($survey_id=0,$user_id=0){

  $raters=DB::table('surverys as s')
            ->select('s.id as survey_id','s.title','sr.rater_id','r.rater',DB::raw('count(CASE WHEN usr.survey_status = 3 THEN 0 END) as count_completed_user'))
            ->leftjoin('survey_rater as sr','sr.survey_id','=','s.id')
            ->leftjoin('rater as r','r.id','=','sr.rater_id')
            ->leftjoin('user_survey_respondent as usr','r.id','usr.rater_id')
            ->where('s.id',$survey_id)
            ->where('usr.participant_id',$user_id)
            ->groupBY('r.id')
            ->OrderBy('r.id')
            ->pluck('rater.rater','rater_id')
            ->toArray();

  return $raters;
}
    public function ReportQuestionDimension(Request $request)
    {
    	$survey_id=$request->get('survey_id');
    	$user_id=$request->get('user_id');
    	$question_dimension=$request->get('question_dimension');
      $all_rater=$this->getRaters($survey_id,$user_id);
    	$all_questions=$this->AllQuestions($survey_id,$question_dimension);

    	foreach ($all_rater as $rater_id => $rater) {
    		$values[$rater_id]=$this->ReportQuestionDimensionQuerys($survey_id,$user_id,$rater,$question_dimension);

    	}


    $survey_name=DB::table('surverys')->where('id',$survey_id)->value('title');

		return view('admin.report.dimension_based_report')
			   ->with('reports',$values)
         ->with('survey_name',$survey_name)
			   ->with('question_dimension',$question_dimension)
			   ->with('title','Report')
          ->with('all_questions',$all_questions)
			   ->with('all_raters',$all_rater);
    }
}
