<?php

namespace App\Http\Controllers;

use App\Exports\AllQuestionExport;
use App\Http\Controllers\Controller;
use Arr;
use DB;
use Excel;
use Illuminate\Http\Request;
use Redirect;
use Response;
use Session;
use View;

class QuestionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $response = array();
        $question_ids = $request->get('item');
        $display_order = 1;
        foreach ($question_ids as $question_id) {
            $question_id = explode(',', $question_id);
            DB::table('questions')->whereIn('id', $question_id)->update(['display_order' => $display_order]);
            $question_id = implode(',', $question_id);
            $response[$question_id] = $display_order;

            $display_order++;
        }

        return \Response::json($response);

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request)
    {
        if ($request->ajax()) {
            $question_type = $request->get('question_type');
            $display_order = $request->get('display_order');
            // $question_dimension=$request->get('question_dimension');
            $survey_id = $request->get('survey_id');

            $question_dimension = DB::table('questions')->where('survey_id', $survey_id)->where('display_order', '>', 0)->orderby('display_order', 'DESC')->value('question_dimension');

            $display_order_desc = DB::table('options as o')->join('questions as q', 'q.id', '=', 'o.question_id')->where('q.survey_id', $survey_id)->where('q.display_order', '>', 0)->where('q.question_type', '<>', 'grid')->orderby('q.display_order', 'DESC')->value('q.display_order');

            $question_options = DB::table('options as o')->join('questions as q', 'q.id', '=', 'o.question_id')->where('q.survey_id', $survey_id)->where('q.display_order', $display_order_desc)->where('o.option_weight', '<>', 0)->where('q.question_type', '<>', 'grid')->pluck('o.option_text', 'o.option_weight')->toArray();
            ksort($question_options);

            $display_order_desc_grid = DB::table('options as o')->join('questions as q', 'q.id', '=', 'o.question_id')->where('q.survey_id', $survey_id)->where('q.display_order', '>', 0)->where('q.question_type', '=', 'grid')->orderby('q.display_order', 'DESC')->value('q.display_order');

            $question_options_grid = DB::table('options as o')->join('questions as q', 'q.id', '=', 'o.question_id')->where('q.survey_id', $survey_id)->where('q.display_order', $display_order_desc_grid)->where('o.option_weight', '<>', 0)->where('q.question_type', '=', 'grid')->pluck('o.option_text', 'o.option_weight')->toArray();
            ksort($question_options_grid);

            $question_dimension_already_exists = DB::table('questions')->where('survey_id', $survey_id)->where('question_type', '=', 'grid')->where('display_order', '>', 0)->pluck('question_dimension')->toArray();
//dd($question_dimension_already_exists);
            $view_name = "admin.question.addnew";
            if ($question_type == "grid") {
                $view_name = "admin.question.addnewgrid";
            }
            return view($view_name)

                ->with('display_order', $display_order)
                ->with('question_dimension', $question_dimension)
                ->with('question_options', $question_options)
                ->with('question_options_grid', $question_options_grid)
                ->with('question_dimension_already_exists', $question_dimension_already_exists)
                ->with('survey_id', $survey_id)
                ->with('question_type', $question_type);

        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        Arr::forget($input, ['_token', '_wysihtml5_mode', '_method', '_question_id']);

        $insert = array(
            'question_text' => $input['question'],
            'survey_id' => $input['survey_id'],
            'question_type' => $input['question_type'],
            'display_order' => $input['display_order'],
            'question_required' => 1,
            'question_dimension' => '',
        );

        $question_id = $request->get('question_id');
        if (empty($question_id)) {
            $question_id = DB::table('questions')->insertGetId($insert);
        } else {

            DB::table('questions')->where('id', $question_id)->update($insert);
        }

        $welcome_text = strip_tags($input['question']);
        if (strlen(trim($welcome_text)) > 110) {
            $welcome_text = substr($welcome_text, 0, 110) . "...";
        }

        $response = array(
            'status' => 'success',
            'question_id' => $question_id,
            'welcome_text' => $welcome_text,
            'question_nature' => $input['question_type'],
            'display_order' => $input['display_order'],
            'question_dimension' => '',
            'question_required' => 1,
        );

        return \Response::json($response);
    }

    /**
     * Display the specified resource.

     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {

        $questions = DB::table('questions')
            ->select(DB::raw('*,GROUP_CONCAT(question_text,"") as question_text,GROUP_CONCAT(id,"") as id'))
            ->where('survey_id', $id)->orderby('display_order')
            ->groupBy('display_order')
            ->get();
        //dd($questions);

        $welcome = DB::table('questions')->where('survey_id', $id)->where('display_order', 0)->value('question_text');
        $thank_u = DB::table('questions')->where('survey_id', $id)->where('display_order', -1)->value('question_text');

        $response = 0;
        $user_survey_respondentids = DB::table('user_survey_respondent')->where('survey_id', $id)->pluck('id');

        foreach ($user_survey_respondentids as $user_survey_respondentid) {
            $response += DB::table('responses')->where('user_survey_respondent_id', $user_survey_respondentid)->count();
        }

        return view('admin.question.create')
            ->with('survey_id', $id)
            ->with('questions', $questions)
            ->with('response', $response)
            ->with('welcome', $welcome)
            ->with('thank_u', $thank_u)
            //->with('question_dimension_already_exists',$question_dimension_already_exists)
            ->with('title', 'Adding Questions to Survey');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit(Request $request, $id)
    {
        if ($request->ajax()) {
            $question_id = $request->get('question_id');
            $survey_id = DB::table('questions')->where('id', $question_id)->value('survey_id');
            $response = 0;
            $user_survey_respondentids = DB::table('user_survey_respondent')->where('survey_id', $survey_id)->pluck('id');

            foreach ($user_survey_respondentids as $user_survey_respondentid) {
                $response += DB::table('responses')->where('user_survey_respondent_id', $user_survey_respondentid)->count();
            }

            $question = DB::table('questions')->where('id', $question_id)->get()->toArray();
            $question_type = (isset($question[0]->question_type)) ? $question[0]->question_type : "";

            if ($question_type == "grid") {
                $question_dimension = (isset($question[0]->question_dimension)) ? $question[0]->question_dimension : "";
                $question_required = (isset($question[0]->question_required)) ? $question[0]->question_required : "";
                $display_order = (isset($question[0]->display_order)) ? $question[0]->display_order : "";

                $option_values = DB::table('options')->where('question_id', $question_id)->orderBy('option_weight', 'asc')->get();
                $question = DB::table('questions')->where('question_type', 'grid')->where('question_dimension', $question_dimension)->where('survey_id', $survey_id)->orderby('id')->get()->toArray();
                return view('admin.question.editgrid')
                    ->with('response', $response)
                    ->with('options', $option_values)
                    ->with('display_order', $display_order)
                    ->with('survey_id', $survey_id)
                    ->with('question_dimension', $question_dimension)
                    ->with('question_required', $question_required)
                    ->with('questions', $question);
            }

            $option_values = DB::table('options')->where('question_id', $question_id)->orderBy('option_weight', 'asc')->get();

            if (!empty($question)) {
                $question[0]->options = $option_values;
            }

            return view('admin.question.edit')
                ->with('response', $response)
                ->with('questions', $question);

        }

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
        if ($request->ajax()) {
            $update = [
                'question_text' => $request->get('welcome_text'),
                'question_required' => $request->get('question_required'),
                'display_order' => $request->get('display_order'),
                'question_dimension' => $request->get('question_dimension'),
            ];
            $question_id = $request->get('question_id');
            DB::table('questions')->where('id', $question_id)->update($update);
            $welcome_text = strip_tags($update['question_text']);
            if (strlen(trim($welcome_text)) > 110) {
                $welcome_text = substr($welcome_text, 0, 110) . "...";
            }
            $response = array(
                'status' => 'success',
                'question_id' => $question_id,
                'welcome_text' => $welcome_text,
                'welcome_text_original' => $update['question_text'],
                'action' => 'update',
                'question_nature' => $request->get('question_nature'),
                'display_order' => $update['display_order'],
                'question_dimension' => $update['question_dimension'],
                'question_required' => $update['question_required'],
            );
            return \Response::json($response);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        if ($request->ajax()) {
            $question_id = explode(',', $request->question_id);
            DB::table('options')->where('question_id', $question_id)->delete();
            DB::table('questions')->whereIn('id', $question_id)->delete();

            $response = array(
                'status' => 'success',
                'msg' => 'Record Deleted successfully',
            );
            return \Response::json($response);
        }

    }

    public function questionEditGrid(Request $request)
    {
        $question_id = $request->get('question_id');
        $question_text = $request->get('question');
        $question_required = $request->get('question_required');
        $question_type = $request->get('question_type');
        $question_options = $option_weight = "";
        $question_options = $request->get('fields');
        $option_weight = $request->get('option_weight');
        $display_order = $request->get('display_order') + 1;
        $question_dimension = $request->get('question_dimension');
        $question_contianer = array();
        $qtext = '';
        if ($question_id != "") {
            $question_id = explode(',', $question_id);
            $display_order = $request->get('display_order');
            DB::table('questions')->whereIn('id', $question_id)->delete();
            DB::table('options')->where('question_id', $question_id)->delete();
        }

        foreach ($question_text as $key => $question) {

            $input = array(
                'question_type' => $question_type,
                'display_order' => $display_order,
                'question_dimension' => $question_dimension,
                'question_required' => $question_required,
                'survey_id' => $request->get('survey_id'),
                'question_text' => $question,
            );

            $question_id = DB::table('questions')->insertGetId($input);
            if (!empty($option_weight) && !empty($question_options)) {

                foreach ($question_options as $key => $option_text) {
                    $option_details = [
                        'option_text' => $option_text,
                        'option_weight' => $option_weight[$key],
                        'question_id' => $question_id,
                    ];
                    DB::table('options')->insert($option_details);
                }

            }
            $qtext .= strip_tags($question) . ",";
            if (strlen(trim($qtext)) > 100) {
                $qtext = substr($qtext, 0, 100) . "...";
            }

            // $display_order++;
        }

        $question_contianer = array(
            'status' => 'success',
            'question_text' => rtrim($qtext, ","),
            'question_nature' => $question_type,
            'question_id' => $question_id,
            'display_order' => $display_order,
            'question_dimension' => $question_dimension,

        );

        $response = $question_contianer;
        return \Response::json($response);

    }

    public function questionEdit(Request $request)
    {
        // dd($request->all());

        $question_id = $request->get('question_id');
        $question_text = $request->get('question');
        $question_required = $request->get('question_required');
        $question_type = $request->get('question_type');
        $question_types = ['checkbox', 'dropdown', 'radio'];
        $question_options = $option_weight = "";
        if (in_array($question_type, $question_types)) {
            $question_options = $request->get('fields');
            $option_weight = $request->get('option_weight');
        }
        $display_order = $request->get('display_order');
        $question_dimension = $request->get('question_dimension');
        $question_with_other = $request->get('addother');

        $input = array(
            'question_text' => $question_text,
            'question_type' => $question_type,
            'display_order' => $display_order,
            'question_dimension' => $question_dimension,
            'question_required' => $question_required,
        );
        if ($question_id != "") {

            DB::table('questions')->where('id', $question_id)->update($input);
            if (!empty($option_weight) && !empty($question_options)) {
                DB::table('options')->where('question_id', $question_id)->delete();
                foreach ($question_options as $key => $option_text) {
                    $option_details = [
                        'option_text' => $option_text,
                        'option_weight' => $option_weight[$key],
                        'question_id' => $question_id,
                    ];
                    DB::table('options')->insert($option_details);
                }
                if ($question_type == "radio" and $question_with_other == 1) {
                    $option_details_others = [
                        'option_text' => 'others',
                        'option_weight' => 0,
                        'question_id' => $question_id,
                    ];
                    DB::table('options')->insert($option_details_others);
                }
            }
            $question_text = strip_tags($question_text);
            if (strlen(trim($question_text)) > 100) {
                $question_text = substr($question_text, 0, 100) . "...";
            }
            $response = array(
                'status' => 'success',
                'question_text' => $question_text,
                'question_nature' => $question_type,
                'question_id' => $question_id,
                'question_dimension' => $question_dimension,

            );
            return \Response::json($response);
        } else {
            $qexists = DB::table('questions')->where('question_dimension', $question_dimension)->where('question_text', $question_text)->where('survey_id', $request->get('survey_id'))->exists();

            if ($qexists) {
                $response = array(
                    'status' => 'success',
                    'error' => 'Question Already Exists for the given survey',

                );
                return \Response::json($response);
            }

            $input['display_order'] = $display_order + 1;
            $input['survey_id'] = $request->get('survey_id');
            $question_id = DB::table('questions')->insertGetId($input);
            if (!empty($option_weight) && !empty($question_options)) {

                foreach ($question_options as $key => $option_text) {
                    $option_details = [
                        'option_text' => $option_text,
                        'option_weight' => $option_weight[$key],
                        'question_id' => $question_id,
                    ];
                    DB::table('options')->insert($option_details);
                }
                if ($question_type == "radio" and $question_with_other == 1) {
                    $option_details_others = [
                        'option_text' => 'others',
                        'option_weight' => 0,
                        'question_id' => $question_id,
                    ];
                    DB::table('options')->insert($option_details_others);
                }
            }

            $question_text = strip_tags($question_text);
            if (strlen(trim($question_text)) > 100) {
                $question_text = substr($question_text, 0, 100) . "...";
            }
            $response = array(
                'status' => 'success',
                'question_text' => $question_text,
                'question_nature' => $question_type,
                'question_id' => $question_id,
                'display_order' => $display_order + 1,
                'question_dimension' => $question_dimension,

            );
            return \Response::json($response);
        }

    }
    public function downloadQuesController(Request $request)
    {
        $survey_id = $request->get('survey_id');
        $survey_name = $request->get('survey_name');
        $sheet_name = 'Question-' . str_replace(' ', '', $survey_name) . '-' . strtotime("now");
        return Excel::download(new AllQuestionExport($survey_id), $sheet_name . '.xls');
    }

    public function checkQuestion(Request $request)
    {
        $survey_id = $request->get('survey_id');
        $question_dimension = $request->get('question_dimension');
        $question_type = $request->get('question_type');

        $datas = DB::table('questions')
            ->select('question_dimension')
            ->where('question_type', $question_type)
            ->where('question_dimension', $question_dimension)
            ->where('survey_id', $survey_id)
            ->get();

        if (count($datas) > 0) {
            foreach ($datas as $key => $questions) {
                $questions_texts[] = $questions->question_dimension;
            }
            $isAvailable = false;
        } else {
            $isAvailable = true;
        }

        echo json_encode(array(
            'valid' => $isAvailable,
        ));
    }

    public function QuestionGroupController(Request $request)
    {

        $survey_id = $request->get('survey_id');

        $exist = DB::table('question_grouping')->where('survey_id', $survey_id)->count();

        $survey_id = $request->get('survey_id');

        $questions = DB::table('questions')->select('question_text', 'id', 'question_dimension', 'question_type')
            ->whereNotIn('display_order', [0, -1])
            ->where('survey_id', $survey_id)
            ->get()
            ->toArray();

        $count_completed_status = DB::table('user_survey_respondent')->where('survey_id', $survey_id)->where('survey_status', 3)->count();

        $questions1 = DB::table('questions')->select('question_dimension', 'question_type', DB::raw("GROUP_CONCAT(question_text SEPARATOR '~') AS question_text"), DB::raw("GROUP_CONCAT(id SEPARATOR '~') AS question_id"))
            ->where('survey_id', $survey_id)
            ->where('display_order', '>', '0')
            ->orderBy('display_order')
            ->groupBy('display_order')
            ->get();

        // dd($question_arr);

        $raters = DB::table('rater')
            ->select('rater.id as rater_id', 'rater.rater')
            ->leftjoin('survey_rater', 'rater.id', '=', 'survey_rater.rater_id')
            ->where('survey_rater.survey_id', $survey_id)
            ->get();
        if ($exist == 0) {
            $datas = '';
        } else {
            $datas = DB::table('question_grouping')->where('survey_id', $survey_id)->select('question_id', 'rater_id')->get();

        }

//dd($questions1);

        return view('admin.question.question_group_create')
            ->with('title', 'Question Grouping')
            ->with('questions', $questions1)
            ->with('count_completed_status', $count_completed_status)
            ->with('survey_id', $survey_id)
            ->with('raters', $raters)
            ->with('options', $datas);

    }

    public function QuestionpostController(Request $request)
    {

        $survey_id = $request->get('survey_id');

        $exist = DB::table('question_grouping')->where('survey_id', $survey_id)->count();

        if ($exist != 0) {
            DB::table('question_grouping')->where('survey_id', $survey_id)->delete();
        }
        // dd($request->all());
        $ques_rater_id = $request->get('q_r_id');
        foreach ($ques_rater_id as $key => $value) {
            $explode_datas = explode('|', $value);
            $question_id = $explode_datas[0];
            $rater_id = $explode_datas[1];
            DB::table('question_grouping')->insert(['question_id' => $question_id, 'rater_id' => $rater_id, 'survey_id' => $survey_id]);
        }

        // Session::set('message', 'Questions are grouped to raters successfully!...');

        // return redirect()->route('questions_group','survey_id='.$survey_d);

        return redirect()->route('theme.show', $survey_id);
    }
    public function DeleteAllQuestions(Request $request)
    {

        $survey_id = $request->get('survey_id');
        $all_questions = DB::table('questions')->where('survey_id', $survey_id)->pluck('id');

        foreach ($all_questions as $key => $question) {
            DB::table('options')->where('question_id', $question)->delete();
        }

        DB::table('questions')->where('survey_id', $survey_id)->delete();
        Session::flash('success', 'All Questions has been deleted');
        return Redirect::back();

    }
}
