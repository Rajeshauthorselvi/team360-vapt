<?php

namespace App\Http\Controllers;
use DB;
use Illuminate\Http\Request;

class previewController extends Controller
{
    public function index(Request $request)
    {
      $theme_id=$request->get('theme_id');
      $survey_id=$request->get('survey_id');

      $check_url=DB::table('themes')
      ->where('id',$theme_id)
      ->get();

       $theme=DB::table('surverys')
           ->leftjoin('themes','surverys.survey_theme_id','themes.id')
           ->where('surverys.id',$survey_id)
           ->value('file');

      $check_datas=DB::table('surverys')->select('surverys.title as survey_name','surverys.survey_theme_id',
      'surverys.logo','surverys.url')->where('id',$survey_id)->get();

      return view('admin.preview.login_preview')->with('survey_datas',$check_datas)->with('theme_file',$check_url)->with('survey_id',$survey_id)->with('themes',$theme);
    }

    public function questionpreview(Request $request)
    {
	$survey_id=$request->get('survey_id');
	$survey_details=DB::table('surverys')->find($survey_id);
	$theme_id=$request->get('theme_id');
	$themes=DB::table('themes')->where('themes.id',$theme_id)->value('file');
        $get_logo=DB::table('surverys')->where('id',$survey_id)->value('logo');
  	$welcome_text=DB::table('questions')->where('survey_id',$survey_id)->where('display_order',0)->value('question_text');


        $dimension_hide=DB::table('surverys')->where('id',$survey_id)->value('dimension_hide');


      $questions=DB::table('questions')

               ->select(DB::raw('surverys.id,GROUP_CONCAT(question_text SEPARATOR "|") as question_text,GROUP_CONCAT(questions.id SEPARATOR "|") as question_id,questions.question_type,questions.question_required,questions.question_dimension'))

               ->join('surverys','questions.survey_id','=','surverys.id')

                 ->where('questions.survey_id',$survey_id)

               //->where('questions.question_enabled',1)

               ->where('display_order','>',0)
               ->orderBy('display_order','ASC')
               ->groupBy('display_order')
               ->get();


  foreach ($questions as $key => $question) {

        $exploded=explode('|', $question->question_id);

       // var_dump($exploded);

        if(count($exploded)>1){

        foreach ($exploded as $key => $iquestion_id) {
      

        $option_values[$iquestion_id]=DB::table('options')->where('question_id',$iquestion_id)->orderBy('id','ASC')->pluck('id','option_text');

        }
        $question->optionth=DB::table('options')->where('question_id',$iquestion_id)->orderBy('id','ASC')->pluck('option_text');
       

      }
      else
      {
        //$option_values="";
         $option_values=DB::table('options')->orderBy('id','ASC')->whereIn('question_id',$exploded)->get();
      }




         $question->options=$option_values;

         unset($option_values);

       }

      
      $user_response=array();
    return view('admin.preview.question_preview')
     ->with('title','Take Survey')
     ->with('survey_details',$survey_details)
     ->with('welcome_text',$welcome_text)
     ->with('responses',$user_response)
     ->with('logo',$get_logo)
     ->with('questions',$questions)
     ->with('dimension_hide',$dimension_hide)
     ->with('themes',$themes);


    }
}
