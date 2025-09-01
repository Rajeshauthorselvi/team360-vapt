<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use View;
use Validator;
class ThemeController extends Controller
{
    public function show($id)
    {
        
    	$themes=DB::table('themes')->get();

    	$default_theme=DB::table('surverys')->where('id',$id)->first();

    	return View::make('admin.themes.show')
    	->with('survey_name',$default_theme->title)
        ->with('themes',$themes)
    	->with('default_theme',$default_theme->survey_theme_id)
        ->with('per_page',$default_theme->question_per_page)
    	->with('survey_id',$id)
    	->with('title','Survey Theme Customization');
    }
    public function store(Request $request)
    {
    	 $rules=array(
            'theme'=>'required',
            'survey_id'=>'required'
            );
        $input=$request->all();
        $validator=Validator::make($input,$rules);
        if($validator->passes())
        {
        	$survey_id=$request->get('survey_id');
        	$survey_theme_id=$request->get('theme');
        	DB::table('surverys')->where('id',$survey_id)->update([
                'survey_theme_id'=>$survey_theme_id,
                'question_per_page'=>isset($request->question_per_page)?$request->question_per_page:0
            ]);

            $redirect=$request->get('redirect');
            if($redirect=="home") return redirect()->route('admin.dashboard');
            return redirect()->route('addusers.show',$survey_id);
        }
        else
        {
        	return redirect()->back()->withErrors($validator)->withInput();
        }
    }
}
