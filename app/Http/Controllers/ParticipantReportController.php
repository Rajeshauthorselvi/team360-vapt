<?php

namespace App\Http\Controllers;

use DB;
use Illuminate\Http\Request;

class ParticipantReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

//get participant details for index
    public function GetParticipantDetails($survey_id)
    {

        $participants = DB::table('users')
            ->select('user_survey_respondent.*', 'users.*', 'user_survey_respondent.id as user_survey_respondent_id')
            ->join('user_survey_respondent', 'users.id', '=', 'user_survey_respondent.participant_id')
            ->where('users.id', '>', 1)
            ->where('user_survey_respondent.respondent_id', '=', 0)
            ->where('user_survey_respondent.survey_id', $survey_id)->get();

        return $participants;
    }

//view participant details
    public function show(Request $request)
    {
        $path_url = $request->path();
        $url = explode('/', $path_url);
        $survey_id = $url[1];

        $participants = $this->GetParticipantDetails($survey_id);

        return view('admin.report.index')
            ->with('data', $participants)
            ->with('survey_id', $survey_id)
            ->with('title', 'List Of Participants');
    }

//dimension1
    public function index(Request $request)
    {
        $input = $request->all();
        $survey_id = $input['survey_id'];
        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');
        $participant_id = $input['participant_id'];
        $participant_fname = DB::table('users')->where('id', $participant_id)->value('fname');
        $participant_lname = DB::table('users')->where('id', $participant_id)->value('lname');

        $user_survey_respondent_id = DB::table('user_survey_respondent')->where('survey_id', $survey_id)->where('participant_id', $participant_id)->where('respondent_id', '!=', 0)->where('user_survey_respondent.survey_status', 3)->pluck('id')->toArray();

        $others_count = count($user_survey_respondent_id);

        $user_survey_participant_id = DB::table('user_survey_respondent')->where('survey_id', $survey_id)->where('participant_id', $participant_id)->where('respondent_id', 0)->where('user_survey_respondent.survey_status', 3)->value('id');

        $dimension = DB::table('questions')
            ->select('question_dimension')
            ->where('survey_id', $survey_id)
            ->whereNotIn('display_order', ['0', '-1'])
            ->groupBy('question_dimension')->get();

        foreach ($dimension as $each_dimension) {
//for self
            $self = DB::table('options')
                ->join('questions', 'questions.id', '=', 'options.question_id')->join('responses', 'responses.option_id', '=', 'options.id')
                ->join('user_survey_respondent', 'responses.user_survey_respondent_id', '=', 'user_survey_respondent.id')
                ->where('responses.user_survey_respondent_id', $user_survey_participant_id)
                ->where('questions.question_dimension', '=', $each_dimension->question_dimension)
                ->where('options.option_weight', '<>', 0);

            $self_options_avg = $self->avg('options.option_weight');
//$self_options_sum[$each_dimension->question_dimension]=$self->sum('options.option_weight');
            //$self_options[]=$self->get();

//for others
            $others = DB::table('options')
                ->join('questions', 'questions.id', '=', 'options.question_id')->join('responses', 'responses.option_id', '=', 'options.id')
                ->join('user_survey_respondent', 'responses.user_survey_respondent_id', '=', 'user_survey_respondent.id')
                ->whereIn('responses.user_survey_respondent_id', $user_survey_respondent_id)
                ->where('questions.question_dimension', '=', $each_dimension->question_dimension)
                ->where('options.option_weight', '<>', 0);

            $others_options_avg = $others->avg('options.option_weight');

            $average[$each_dimension->question_dimension] = array($self_options_avg, $others_options_avg);

        }

        return view('admin.report.dimension1')
            ->with('participant_id', $participant_id)
            ->with('participant_fname', $participant_fname)
            ->with('participant_lname', $participant_lname)
            ->with('survey_id', $survey_id)
            ->with('survey_name', $survey_name)
            ->with('others_count', $others_count)
            ->with('average', $average)
            ->with('title', 'Dimension1');
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

//item wise others details
    public function GetItemwiseDetails($survey_id, $participant_id, $sort)
    {

        $result_basic = DB::table('user_survey_respondent as usr')
            ->select(DB::raw('usr.id as usr_id,usr.survey_id,usr.participant_id,usr.respondent_id,usr.rater_id,rs.option_id,rs.question_id,q.question_dimension,q.question_text,o.option_weight,r.rater,COUNT(usr.participant_id) as rcount,sum(o.option_weight) as rsum,AVG(o.option_weight) as ravg, ROUND(AVG(o.option_weight),1) as rround '))
            ->join('responses as rs', 'rs.user_survey_respondent_id', '=', 'usr.id')
            ->join('questions as q', 'rs.question_id', '=', 'q.id')
            ->join('options as o', 'rs.option_id', '=', 'o.id')
            ->join('rater as r', 'usr.rater_id', '=', 'r.id')
            ->where('usr.survey_id', $survey_id)
            ->where('usr.participant_id', $participant_id)
            ->where('usr.survey_status', 3)
            ->where('q.display_order', '>', 0)
            ->whereNotIn('q.question_type', ['text', 'textarea'])
            ->where('usr.respondent_id', '>', 0)
            ->groupBY('usr.participant_id')
            ->groupBY('q.question_text');

        if ($sort == 'unsort') {
            $results = $result_basic->OrderBy('q.display_order')->get();
        } elseif ($sort == 'sort') {
            $results = $result_basic->OrderBy('rround', 'DESC')->get();
        }

        return $results;
    }

//item wise others
    public function create(Request $request)
    {

        $input = $request->all();
        $survey_id = $input['survey_id'];
        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');
        $participant_id = $input['participant_id'];
        $participant_fname = DB::table('users')->where('id', $participant_id)->value('fname');
        $participant_lname = DB::table('users')->where('id', $participant_id)->value('lname');

        $results = $this->GetItemwiseDetails($survey_id, $participant_id, 'unsort');

        return view('admin.report.itemwiseothers')
            ->with('data', $results)
            ->with('participant_id', $participant_id)
            ->with('participant_fname', $participant_fname)
            ->with('participant_lname', $participant_lname)
            ->with('survey_id', $survey_id)
            ->with('survey_name', $survey_name)
            ->with('title', 'Item Wise Others');
    }

//item wise others sort
    public function itemwise_others_sort(Request $request)
    {

        $input = $request->all();
        $survey_id = $input['survey_id'];
        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');
        $participant_id = $input['participant_id'];
        $participant_fname = DB::table('users')->where('id', $participant_id)->value('fname');
        $participant_lname = DB::table('users')->where('id', $participant_id)->value('lname');

        $results = $this->GetItemwiseDetails($survey_id, $participant_id, 'sort');

        return view('admin.report.itemwiseothers')
            ->with('data', $results)
            ->with('participant_id', $participant_id)
            ->with('participant_fname', $participant_fname)
            ->with('participant_lname', $participant_lname)
            ->with('survey_id', $survey_id)
            ->with('survey_name', $survey_name)
            ->with('title', 'Item Wise Others Sort');
    }

//get details top  and bottom
    public function GetTopandBottomDetails($survey_id, $participant_id, $order)
    {

        $result_basic = DB::table('user_survey_respondent as usr')
            ->select(DB::raw('usr.id as usr_id,usr.survey_id,usr.participant_id,usr.respondent_id,usr.rater_id,rs.option_id,rs.question_id,q.question_dimension,q.question_text,o.option_weight,r.rater,COUNT(usr.participant_id) as rcount,sum(o.option_weight) as rsum,AVG(o.option_weight) as ravg, ROUND(AVG(o.option_weight),1) as rround '))
            ->join('responses as rs', 'rs.user_survey_respondent_id', '=', 'usr.id')
            ->join('questions as q', 'rs.question_id', '=', 'q.id')
            ->join('options as o', 'rs.option_id', '=', 'o.id')
            ->join('rater as r', 'usr.rater_id', '=', 'r.id')
            ->where('usr.survey_id', $survey_id)
            ->where('usr.participant_id', $participant_id)
            ->where('usr.survey_status', 3)
            ->where('q.display_order', '>', 0)
            ->whereNotIn('q.question_type', ['text', 'textarea'])
            ->where('usr.respondent_id', '>', 0)
            ->groupBY('usr.participant_id')
            ->groupBY('q.question_text');
        if ($order == 'desc') {
            $result = $result_basic->OrderBy('rround', 'DESC')->take(5)->get();
        } elseif ($order == 'asc') {
            $result = $result_basic->OrderBy('rround', 'asc')->take(5)->get();
        }

        return $result;

    }

//top  and bottom
    public function top_and_bottom(Request $request)
    {

        $input = $request->all();
        $survey_id = $input['survey_id'];
        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');
        $participant_id = $input['participant_id'];
        $participant_fname = DB::table('users')->where('id', $participant_id)->value('fname');
        $participant_lname = DB::table('users')->where('id', $participant_id)->value('lname');

        $results_desc = $this->GetTopandBottomDetails($survey_id, $participant_id, 'desc');

        $results_asc = $this->GetTopandBottomDetails($survey_id, $participant_id, 'asc');

//dd($results_asc);

        return view('admin.report.topandbottom')
            ->with('data_desc', $results_desc)
            ->with('data_asc', $results_asc)
            ->with('participant_id', $participant_id)
            ->with('participant_fname', $participant_fname)
            ->with('participant_lname', $participant_lname)
            ->with('survey_id', $survey_id)
            ->with('survey_name', $survey_name)
            ->with('title', 'Top and bottom');
    }

    public function getCDReport($survey_id, $participant_id, $role)
    {

        $selects = array('questions.id as question_id', 'questions.question_dimension', 'questions.question_text', 'options.option_weight', 'round(avg(options.option_weight),1) as ravg');

        $report = DB::table('user_survey_respondent')
            ->selectRaw(implode(',', $selects))
            ->leftjoin('responses', 'user_survey_respondent.id', 'responses.user_survey_respondent_id')
            ->leftjoin('options', 'responses.option_id', 'options.id')
            ->leftjoin('questions', 'responses.question_id', 'questions.id')
            ->whereNotIn('question_type', ['text', 'textarea'])
            ->where('user_survey_respondent.survey_id', $survey_id)
            ->where('user_survey_respondent.survey_status', 3);
        if ($role == "0") {
            $report->where('user_survey_respondent.respondent_id', '=', 0);
        } else if ($role == "1") {
            $report->where('user_survey_respondent.respondent_id', '<>', 0);
        }

        $report->where('user_survey_respondent.participant_id', $participant_id)
            ->groupBY('user_survey_respondent.participant_id', 'questions.question_text');

        $report->OrderBy('questions.id');

        $result = $report->get()->keyBy('question_id')->toArray();

        return $result;
    }
//converging_diverging
    public function converging_diverging(Request $request)
    {

        $input = $request->all();
        $survey_id = $input['survey_id'];
        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');
        $participant_id = $input['participant_id'];
        $participant_fname = DB::table('users')->where('id', $participant_id)->value('fname');
        $participant_lname = DB::table('users')->where('id', $participant_id)->value('lname');

        $self = $this->getCDReport($survey_id, $participant_id, 0);
        $others = $this->getCDReport($survey_id, $participant_id, 1);

//$selfonly=$self->keyBy('question_id')->toArray();
        //$othersonly=$others->keyBy('question_id')->toArray();

        $questions = DB::table('questions')->where('survey_id', $survey_id)->where('display_order', '>', 0)->whereNotIn('question_type', ['text', 'textarea'])->pluck('question_text', 'id')->toArray();

        $self_avg = $others_avg = array();

        foreach ($questions as $key => $val) {
//$self_avg[$el$val->question_id]=$val->ravg;
            if (isset($self[$key]->question_id)) {
                $self_avg[$self[$key]->question_id] = $self[$key]->ravg;
            }

            if (isset($others[$key]->question_id)) {
                $others_avg[$others[$key]->question_id] = $others[$key]->ravg;
            }

        }

        $diff = array_keys(array_intersect_key($self_avg, $others_avg));

        $arr1 = $arr2 = $arr3 = $question_details = array();
        foreach ($diff as $questionid) {

            $arr1[$questionid] = $self_avg[$questionid];
            $arr2[$questionid] = $others_avg[$questionid];
            $arr3[$questionid] = $self_avg[$questionid] - $others_avg[$questionid];
            $question_details[$questionid] = $questions[$questionid];

        }

        arsort($arr3);
        $smallestvalue = array_slice($arr3, 0, 5, true);
        asort($arr3);
        $largestvalue = array_slice($arr3, 0, 5, true);

//dd($smallestvalue);

        return view('admin.report.converging_diverging')
            ->with('smallestvalue', $smallestvalue)
            ->with('largestvalue', $largestvalue)
            ->with('questions', $questions)
            ->with('self_avg', $self_avg)
            ->with('others_avg', $others_avg)
            ->with('question_details', $question_details)
            ->with('participant_id', $participant_id)
            ->with('participant_fname', $participant_fname)
            ->with('participant_lname', $participant_lname)
            ->with('survey_id', $survey_id)
            ->with('survey_name', $survey_name)
            ->with('title', 'Converging and Diverging');
    }

//gap_report
    public function gap_report(Request $request)
    {

        $input = $request->all();
        $survey_id = $input['survey_id'];
        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');
        $participant_id = $input['participant_id'];
        $participant_fname = DB::table('users')->where('id', $participant_id)->value('fname');
        $participant_lname = DB::table('users')->where('id', $participant_id)->value('lname');

        $self = $this->getCDReport($survey_id, $participant_id, 0);
        $others = $this->getCDReport($survey_id, $participant_id, 1);

//$selfonly=$self->keyBy('question_id')->toArray();
        //$othersonly=$others->keyBy('question_id')->toArray();

        $questions = DB::table('questions')->where('survey_id', $survey_id)->where('display_order', '>', 0)->whereNotIn('question_type', ['text', 'textarea'])->pluck('question_text', 'id')->toArray();

        $self_avg = $others_avg = array();

        foreach ($questions as $key => $val) {
//$self_avg[$el$val->question_id]=$val->ravg;
            if (isset($self[$key]->question_id)) {
                $self_avg[$self[$key]->question_id] = $self[$key]->ravg;
            }

            if (isset($others[$key]->question_id)) {
                $others_avg[$others[$key]->question_id] = $others[$key]->ravg;
            }

        }

        $diff = array_keys(array_intersect_key($self_avg, $others_avg));

        $arr1 = $arr2 = $arr3 = $question_details = array();
        foreach ($diff as $questionid) {

            $arr1[$questionid] = $self_avg[$questionid];
            $arr2[$questionid] = $others_avg[$questionid];
            $arr3[$questionid] = $others_avg[$questionid] - $self_avg[$questionid];
            $question_details[$questionid] = $questions[$questionid];

        }

        arsort($arr3);
        $smallestvalue = array_slice($arr3, 0, 10, true);

//dd($smallestvalue);

        return view('admin.report.gap_report')
            ->with('smallestvalue', $smallestvalue)
            ->with('questions', $questions)
            ->with('self_avg', $self_avg)
            ->with('others_avg', $others_avg)
            ->with('question_details', $question_details)
            ->with('participant_id', $participant_id)
            ->with('participant_fname', $participant_fname)
            ->with('participant_lname', $participant_lname)
            ->with('survey_id', $survey_id)
            ->with('survey_name', $survey_name)
            ->with('title', 'Gap Report');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

//dimension2
    public function edit($id, Request $request)
    {
        $input = $request->all();

        $survey_id = $input['survey_id'];
        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');
        $participant_id = $id;
        $participant_fname = DB::table('users')->where('id', $participant_id)->value('fname');
        $participant_lname = DB::table('users')->where('id', $participant_id)->value('lname');

        $user_survey_respondent = DB::table('user_survey_respondent')->join('rater', 'rater.id', '=', 'user_survey_respondent.rater_id')->where('survey_id', $survey_id)->where('participant_id', $participant_id)->where('respondent_id', '!=', 0)->where('user_survey_respondent.survey_status', 3)->pluck('rater.rater', 'user_survey_respondent.id')->toArray();

//dd($user_survey_respondent);

        $dimension = DB::table('questions')
            ->select('question_dimension')
            ->where('survey_id', $survey_id)
            ->whereNotIn('display_order', ['0', '-1'])
            ->whereNotIn('question_type', ['text', 'textarea'])
            ->groupBy('question_dimension')->get();

//dd($dimension);
        //dd($user_survey_respondent);
        $others_count = array_count_values($user_survey_respondent);

        $self_results = DB::select(DB::raw("SELECT usr.id as usr_id,usr.survey_id,usr.participant_id,usr.respondent_id,usr.rater_id,rs.option_id,rs.question_id,q.question_dimension,o.option_weight,r.rater,COUNT(usr.participant_id) as rcount,sum(o.option_weight) as rsum,AVG(o.option_weight) as ravg, ROUND(AVG(o.option_weight),1) as rround FROM user_survey_respondent usr LEFT JOIN responses rs on rs.user_survey_respondent_id=usr.id LEFT JOIN questions q on q.id=rs.question_id LEFT JOIN options o on o.id=rs.option_id LEFT JOIN rater r on usr.rater_id=r.id WHERE usr.survey_id=$survey_id and usr.participant_id=$participant_id and usr.survey_status=3 and q.display_order > 0 and q.question_type NOT IN('text','textarea') GROUP BY usr.participant_id,usr.respondent_id,q.question_dimension  HAVING usr.respondent_id=0 ORDER BY usr.participant_id
 "));

//foreach ($others_count as $key => $others) {

        $results = DB::select(DB::raw("SELECT usr.id as usr_id,usr.survey_id,usr.participant_id,usr.respondent_id,usr.rater_id,rs.option_id,rs.question_id,q.question_dimension,o.option_weight,r.rater,COUNT(usr.participant_id) as rcount,sum(o.option_weight) as rsum,AVG(o.option_weight) as ravg, ROUND(AVG(o.option_weight),1) as rround FROM user_survey_respondent usr LEFT JOIN responses rs on rs.user_survey_respondent_id=usr.id LEFT JOIN questions q on q.id=rs.question_id LEFT JOIN options o on o.id=rs.option_id LEFT JOIN rater r on usr.rater_id=r.id WHERE usr.survey_id=$survey_id and usr.participant_id=$participant_id   and usr.survey_status=3 and q.display_order > 0 and q.question_type NOT IN('text','textarea') and  usr.respondent_id>0  GROUP BY usr.participant_id,usr.rater_id,q.question_dimension ORDER BY usr.participant_id "));

//}

        $final_result = array_merge($self_results, $results);

        return view('admin.report.dimension2')
            ->with('participant_id', $participant_id)
            ->with('participant_fname', $participant_fname)
            ->with('participant_lname', $participant_lname)
            ->with('survey_id', $survey_id)
            ->with('survey_name', $survey_name)
            ->with('results', $results)
            ->with('self_results', $self_results)
            ->with('others_count', $others_count)
            ->with('final_result', $final_result)
            //  ->with('all',$all)
            ->with('dimension', $dimension)
            //  ->with('others_options_get',$others_options_get)
            //  ->with('average',$average)
            ->with('title', 'Dimension2');
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
    public function getRaters($survey_id = 0, $participant_id = 0)
    {

        $raters = DB::table('surverys as s')
            ->select('s.id as survey_id', 's.title', 'sr.rater_id', 'r.rater', DB::raw('count(CASE WHEN usr.survey_status = 3 THEN 0 END) as count_completed_user'))
            ->leftjoin('survey_rater as sr', 'sr.survey_id', '=', 's.id')
            ->leftjoin('rater as r', 'r.id', '=', 'sr.rater_id')
            ->leftjoin('user_survey_respondent as usr', 'r.id', 'usr.rater_id')
            ->where('s.id', $survey_id)
            ->where('usr.participant_id', $participant_id)
            ->groupBY('r.id')
            ->OrderBy('r.id')
            ->get()
            ->toArray();

        return $raters;
    }

    public function DimensionReports($survey_id, $participant_id, $rater_id)
    {
        $qtype = array('text', 'textarea');
        $sql = DB::table('user_survey_respondent as usr')
            ->select('usr.id as usr_id',
                'usr.survey_id',
                'usr.participant_id',
                'usr.respondent_id',
                'usr.rater_id',
                'rs.option_id',
                'rs.question_id',
                'q.question_text',
                'q.question_dimension',
                DB::raw('AVG(o.option_weight) as ravg')
            )
            ->leftjoin('responses as rs', 'rs.user_survey_respondent_id', '=', 'usr.id')
            ->leftjoin('questions as q', 'q.id', '=', 'rs.question_id')
            ->leftjoin('question_grouping as qp', 'q.id', '=', 'qp.question_id')
            ->leftjoin('options as o', 'o.id', '=', 'rs.option_id')
            ->leftjoin('rater as r', 'usr.rater_id', '=', 'r.id')
            ->where('usr.survey_id', $survey_id)
            ->where('usr.survey_status', '3')
            ->where('q.display_order', '>', '0')
            ->whereNotIn('q.question_type', $qtype)
            ->where('participant_id', $participant_id)
            ->where('usr.rater_id', $rater_id)
            ->groupBY('q.question_dimension')
            ->OrderBy('q.question_dimension', 'q.id')
            ->get();

        return $sql;
    }
    public function DimensionTwoController(Request $request)
    {
        $input = $request->all();

        $survey_id = $input['survey_id'];
        $survey_name = DB::table('surverys')->where('id', $survey_id)->value('title');
        $participant_id = $input['participant_id'];
        $participant_fname = DB::table('users')->where('id', $participant_id)->value('fname');
        $participant_lname = DB::table('users')->where('id', $participant_id)->value('lname');

        $raters = $this->getRaters($survey_id, $participant_id);
        foreach ($raters as $key => $rater) {
            $all_values[$rater->rater_id] = $this->DimensionReports($survey_id, $participant_id, $rater->rater_id);
        }

        return view('admin.report.dimension2')
            ->with('participant_id', $participant_id)
            ->with('participant_fname', $participant_fname)
            ->with('participant_lname', $participant_lname)
            ->with('survey_id', $survey_id)
            ->with('survey_name', $survey_name)
            ->with('title', 'Dimension 2')
            ->with('dimension_2_datas', $all_values)
            ->with('raters', $raters);

    }
    public function ReportDashboard(Request $request)
    {

        $participant_id = $request->get('participant_id');

        $survey_id = $request->get('survey_id');
        return view('admin.report.report_dashboard')->with('survey_id', $survey_id)->with('participant_id', $participant_id)->with('title', 'Report Dashboard');

    }
}
