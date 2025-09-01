<?php

namespace App\Http\Controllers;



use Illuminate\Http\Request;
use DB;
use Excel;
use Lava;

class ReportController extends Controller
{

public function ReportDashboard(Request $request)
{

  $user_id=$request->get('user_id');
  $survey_id=$request->get('survey_id');
  return view('admin.report.report_dashboard')->with('title','Report Dashboard')->with('survey_id',$survey_id)->with('user_id',$user_id);

}




public function getRaters($survey_id=0,$user_id=0){

  $raters=DB::table('surverys as s')
            ->select('s.id as survey_id','s.title','sr.rater_id','r.rater','r.id as rater_id')
            ->leftjoin('survey_rater as sr','sr.survey_id','=','s.id')
            ->leftjoin('rater as r','r.id','=','sr.rater_id')
            ->leftjoin('user_survey_respondent as usr','r.id','usr.rater_id')
            ->where('s.id',$survey_id)
            ->where('usr.participant_id',$user_id)
            ->where('usr.survey_status','3')
            ->groupBY('r.id','r.rater')
            ->groupBY('usr.rater_id')
            ->OrderBy('r.id')
            ->get()
            ->toArray();

  return $raters;
}

public function getItemreport($survey_id=0,$user_id=0,$rater_id=0){


  $qtype=array('text','textarea');
  $sql= DB::table('user_survey_respondent as usr')
        ->select( 'usr.id as usr_id',
                  'usr.survey_id',
                  'usr.participant_id',
                  'usr.respondent_id',
                  'usr.rater_id',
                  'rs.option_id',
                  'rs.question_id',
                  'q.question_text',
                  'q.question_dimension',
                  DB::raw('ROUND(AVG(o.option_weight),1) as ravg')
                )
        ->leftjoin('responses as rs','rs.user_survey_respondent_id','=','usr.id')
        ->leftjoin('questions as q','q.id','=','rs.question_id')
        ->leftjoin('question_grouping as qp','q.id','=','qp.question_id')
        ->leftjoin('options as o','o.id','=','rs.option_id' )
        ->leftjoin('rater as r','usr.rater_id','=','r.id')
        ->where('usr.survey_id',$survey_id)
        ->where('usr.survey_status','3')
        ->where('q.display_order','>','0')
        ->whereNotIn('q.question_type',$qtype)
        ->where('participant_id',$user_id)
        ->where('usr.rater_id',$rater_id)
        ->groupBY('usr.participant_id','usr.rater_id','q.question_text')
        ->OrderBy('q.question_dimension')
        ->OrderBy('q.id')
        ->get();

  return $sql;


}
public function AllQuestions($survey_id)
{
      $all_questions=DB::table('questions')
                  ->select('id as question_id','question_text','question_dimension')
                  ->where('survey_id',$survey_id)
                  ->whereNotIn('question_type',['text','textarea'])
                  ->get()->toArray();

      return $all_questions;
}
public function DimensionItemController(Request $request)
{
    $survey_id=$request->get('survey_id');
    $user_id=$request->get('user_id');
    $raters = $this->getRaters($survey_id,$user_id);
    $count_completed=$this->CountStatus($survey_id,$user_id,'dimension_2');
    $item_report = array();
    foreach ($raters as $key => $rater) {
      $item_report[$rater->rater_id]=$this->getItemreport($survey_id,$user_id,$rater->rater_id);
    }
    $all_questions=$this->AllQuestions($survey_id);
    $survey_name=DB::table('surverys')->where('id',$survey_id)->value('title');
    $user_name=DB::table('users')->where('id',$user_id)->value(DB::raw("CONCAT_WS( ' ',fname, lname) AS name "));

     return view('admin.report.item_report')
           ->with('title','Item Report')
           ->with('raters',$raters)
           ->with('all_questions',$all_questions)
           ->with('survey_name',$survey_name)
           ->with('user_name',$user_name)
           ->with('count_completed',$count_completed)
           ->with('item_report',$item_report);

}

public function OpenEndedReport(Request $request)
{

  $survey_id=$request->get('survey_id');
  $user_id=$request->get('user_id');

  $datas=DB::table('user_survey_respondent')
        ->select('responses.text_response','rater.rater','questions.question_dimension','questions.question_text','questions.id as question_id')
        ->leftjoin('responses','user_survey_respondent.id','responses.user_survey_respondent_id')
        ->leftjoin('options','responses.option_id','options.id')
        ->leftjoin('questions','responses.question_id','questions.id')
        ->leftjoin('rater','user_survey_respondent.rater_id','rater.id')
        ->where('responses.text_response','!=','')
        ->where('user_survey_respondent.survey_id',$survey_id)
        ->where('user_survey_respondent.participant_id',$user_id)
        ->OrderBy('rater.id')
        ->get()
        ->toArray();

  return view('admin.report.open_ended_report')
        ->with('title','Open Ended Responses')->with('open_end_results',$datas);
}
public function ItemWiseReport($survey_id,$user_id,$role='',$sorting)
{

  $selects=array('questions.id as question_id','questions.question_dimension','questions.question_text','options.option_weight','round(avg(options.option_weight),1) as option_total');

  $report=DB::table('user_survey_respondent')
          ->selectRaw(implode(',', $selects))
          ->leftjoin('responses','user_survey_respondent.id','responses.user_survey_respondent_id')
          ->leftjoin('options','responses.option_id','options.id')
          ->leftjoin('questions','responses.question_id','questions.id')
          ->whereNotIn('question_type',['text','textarea'])
          ->where('user_survey_respondent.survey_id',$survey_id)
          ->where('user_survey_respondent.survey_status',3);
         if($role=="self")
            $report->where('user_survey_respondent.respondent_id','=',0);
          else if ($role=="respondent")
            $report->where('user_survey_respondent.respondent_id','<>',0);

           $report->where('user_survey_respondent.participant_id',$user_id)
                  ->groupBY('questions.question_text');

                $report->OrderBy('questions.id');

          $result=$report->get()->toArray();

      return $result;
}
public function ItemWise(Request $request)
{
  $survey_id=$request->get('survey_id');
  $user_id=$request->get('user_id');
  $sorting=$request->get('sort');

  $all_questions=$this->AllQuestions($survey_id);

  $self=$this->ItemWiseReport($survey_id,$user_id,'self',$sorting);
  $respondent=$this->ItemWiseReport($survey_id,$user_id,'respondent',$sorting);

 // dd($respondent);
$survey_name=DB::table('surverys')->where('id',$survey_id)->value('title');
  return view('admin.report.itemwise_report')
        ->with('title','Item Wise-Self vs Others')
        ->with('self_datas',$self)
        ->with('respondent_datas',$respondent)
        ->with('sorting_type',$sorting)
        ->with('survey_name',$survey_name)
        ->with('all_questions',$all_questions);
}
public function QuestionDimensions($survey_id)
{
   $all_dimensions=DB::table('questions')
                   ->where('survey_id',$survey_id)
                   ->whereNotIn('question_type',['text','textarea'])
                   ->where('display_order','>','0')
                   ->groupBY('question_dimension')
                   ->OrderBy('display_order')
                   ->pluck('question_dimension')
                   ->toArray();
    return $all_dimensions;
}
public function CountStatus($survey_id,$user_id,$dimension)
{
      $sql=DB::table('user_survey_respondent as usr')
              ->leftjoin('rater as r','usr.rater_id','r.id')
              ->where('usr.survey_id',$survey_id)
              ->where('usr.participant_id',$user_id)
              ->where('usr.respondent_id','>',0)
              ->where('usr.survey_status','=',3);
              if($dimension=="dimension_2"){
                  $results=$sql->groupBY('usr.rater_id')
                               ->pluck(DB::raw('count(rater_id) as rater_com_count'),'r.id');
              }
              else{
                  $results=$sql->count();
              }

  return $results;
}

public function DimensionOneController(Request $request)
{
    $survey_id=$request->get('survey_id');
    $user_id=$request->get('user_id');

    $survey_name=DB::table('surverys')->where('id',$survey_id)->value('title');
    $user_name=DB::table('users')->where('id',$user_id)->value(DB::raw("CONCAT_WS( ' ',fname, lname) AS name "));


    $self=$this->DimensionOneTwoQuerys($survey_id,$user_id,'self','','d1');
    $respondent=$this->DimensionOneTwoQuerys($survey_id,$user_id,'respondent','','d1');

    $count_completed=$this->CountStatus($survey_id,$user_id,'dimension_1');
    $all_dimensions=$this->QuestionDimensions($survey_id);

    $count_question_dimension=count($all_dimensions);
    $votes  = \Lava::DataTable();

    $votes->addStringColumn('Food Poll')
          ->addNumberColumn('Self')
          ->addRoleColumn('string', 'annotation')
          ->addNumberColumn('Others')
          ->addRoleColumn('string', 'annotation');

for ($i=0; $i < $count_question_dimension; $i++) {
    if (isset($self[$i]->ravg)) $datas_self=$self[$i]->ravg;
    else $datas_self=0;

    if (isset($respondent[$i]->ravg)) $datas_respondent=$respondent[$i]->ravg;
    else $datas_respondent=0;

    //$votes->addRow([$all_dimensions[$i],$datas_self,$datas_respondent],'green','20%');
    $param[]=[$all_dimensions[$i],$datas_self,$datas_self,$datas_respondent,$datas_respondent];


}



$votes->addRows($param);

Lava::BarChart('Votes',$votes,[

    'height'  => 300,
    'width'    =>700,

    ]);

Lava::ColumnChart('Votes', $votes,[
    'height' => 300,
    'width'   =>700,

    ]);

return view('admin.report.diminsion_1')
       ->with('title','Dimension 1')
       ->with('all_dimensions',$all_dimensions)
       ->with('respondent',$respondent)
       ->with('self',$self)
       ->with('total_complete',$count_completed)
       ->with('survey_name',$survey_name)
       ->with('user_name',$user_name);
}

public function DimensionOneTwoQuerys($survey_id,$user_id,$role='',$rater_id='',$dimension='')
{
  $qtype=array('text','textarea');
    $rater_type=DB::raw('round(AVG(o.option_weight),1) as ravg');
  $sql= DB::table('user_survey_respondent as usr')
        ->select( 'usr.id as usr_id',
                  'usr.survey_id',
                  'usr.participant_id',
                  'usr.respondent_id',
                  'usr.rater_id',
                  'rs.option_id',
                  'rs.question_id',
                  'q.question_text',
                  'q.question_dimension',
                  $rater_type
                )
        ->leftjoin('responses as rs','rs.user_survey_respondent_id','=','usr.id')
        ->leftjoin('questions as q','q.id','=','rs.question_id')
        ->leftjoin('options as o','o.id','=','rs.option_id')
        ->leftjoin('rater as r','usr.rater_id','=','r.id')
        ->where('usr.survey_id',$survey_id)
        ->where('usr.survey_status','3')
        ->where('q.display_order','>','0')
        ->whereNotIn('q.question_type',$qtype)
        ->where('participant_id',$user_id);

        if($rater_id!='')
          $sql->where('usr.rater_id',$rater_id);

        if($role=="self")
          $sql->where('usr.respondent_id','=','0');
        else if($role=="respondent")
          $sql->where('usr.respondent_id','>','0');

          if ($dimension == "d2") {
            $sql->groupBY('q.question_dimension')
                  ->orderBy('q.display_order');
          } elseif ($dimension == "d1") {
              $sql->groupBY('q.question_dimension')
                  ->OrderBy('q.display_order');
          }

        $result=$sql->get()->toArray();

  return $result;
}

public function DimensionTwoController(Request $request)
    {
    $survey_id=$request->get('survey_id');
    $user_id=$request->get('user_id');

    $count_completed=$this->CountStatus($survey_id,$user_id,'dimension_2');
    $user_name=DB::table('users')->where('id',$user_id)->value(DB::raw("CONCAT_WS( ' ',fname, lname) AS name "));

    $survey_name=DB::table('surverys')->where('id',$survey_id)->value('title');

    $raters=$this->getRaters($survey_id,$user_id);
    foreach ($raters as $key => $rater) {
        $all_values[$rater->rater_id]=$this->DimensionOneTwoQuerys($survey_id,$user_id,'',$rater->rater_id,'d2');
    }

$all_dimensions=$this->QuestionDimensions($survey_id);

  $votes  = \Lava::DataTable();
  $votes->addStringColumn('Report');

    foreach ($raters as $key => $rater) {
         $votes->addNumberColumn($rater->rater)
               ->addRoleColumn('string', 'annotation');
    }

foreach ($all_values as $key => $dimension_2_data) {
  foreach ($dimension_2_data as $key => $dimension_2) {
    $datas[$dimension_2->question_dimension][$dimension_2->rater_id]=$dimension_2;
  }
}
foreach($datas as $question_dimension=>$dimension_2_data){
  foreach($raters as $key=>$rater){
    if(isset($dimension_2_data[$rater->rater_id]->ravg))
       $rater_vals[$rater->rater][$question_dimension]=$dimension_2_data[$rater->rater_id]->ravg;
    else
       $rater_vals[$rater->rater][$question_dimension]='0';
  }
}
foreach ($rater_vals as $key => $rater_val) {
  foreach ($rater_val as $key => $rater) {
      $data_val_rater[$key][]=$rater;
  }
}

foreach ($data_val_rater as $key => $rater_val) {
    $implode_values=implode(',', $rater_val);
    $explode_values=explode(',',$implode_values);

    $dimension = array('dimension'=>$key);
    $data_merge=$explode_values;
    $merged_values= array_merge($dimension+$data_merge);
    $reindexed_array[] = array_values($merged_values);
}
//dd($reindexed_array);

$total_count=count($reindexed_array);

//for ($i=0; $i < $total_count; $i++){

  //$tarr=$reindexed_array[$i];


//dd($reindexed_array);

foreach ($reindexed_array as $k => $v) {

   $c=count($v);$res=array();

    for($i=0;$i<$c;$i++){

       if($i==0){
        array_push($res, $v[$i]);
      }
      else{
      array_push($res, $v[$i]);
      array_push($res, $v[$i]);

      }
    }
    $votes->addRow($res);
}



   Lava::BarChart('Votes',$votes,[
  // 'bar'=> ['groupWidth'=> '100%'],
    'height'  => 300,
    'width'    =>700
    ]);
   Lava::ColumnChart('Votes', $votes,[
    // 'bar'=> ['groupWidth'=> '100%'],
    'height' => 300,
    'width'   =>700
    ]);
    return view('admin.report.dimension_2')
           ->with('title','Dimension 2')
           ->with('dimension_2_datas',$all_values)
           ->with('survey_name',$survey_name)
           ->with('user_name',$user_name)
           ->with('count_completed',$count_completed)
           ->with('raters',$raters);

    }
  }
