<header>

<div class="inner-header">

   
	<!--<div class="row"> -->
	<?php 
	$user_id=Auth::user()->id;
// var_dump($user_id);
	$logo=Auth::user()->getsurvey_Details()->logo;
	$right_logo=Auth::user()->getsurvey_Details()->right_logo;
	$fname=DB::table('users')->where('id',$user_id)->value('fname');
	$url=Auth::user()->getsurvey_Details()->url;
	$header_text=Auth::user()->getsurvey_Details()->header_text;
	$survy_id=Session::get('survey_id');


	 ?>


	
     <div class="navbar navbar-default ">
   <div  class="col-sm-3 logo"> {!! html_entity_decode(HTML::linkRoute('user.dashboard',HTML::image('storage/surveys/'.$logo,'Logo',['class'=>'img-responsive']),[$url],['class'=>'image-link'])) !!}</div>
  <div  class="col-sm-6 header_text">{!! $header_text !!}</div>
  <div  class="col-sm-3 logo  right_logo">{!! html_entity_decode(HTML::linkRoute('user.dashboard',HTML::image('storage/surveys/'.$right_logo,'',['class'=>'img-responsive']),[$url],['class'=>'image-link'])) !!}</div>

    </div>




	<div class="container-fluid">

	    <div class="row">

		<div class="welcome-strip welcome_text col-md-12 col-sm-12 col-xs-12 ">

		   <div class="col-md-12 ">

  <a href="{{URL::route('user.dashboard',[Auth::user()->getsurvey_Details()->url])}}" title="Home" ><span class="glyphicon glyphicon-home" style="color: white"></span></a> &nbsp;
			Welcome <strong><span  class="header_content_append"><span style=" text-transform: capitalize">{{Auth::user()->fname}}</span></span></strong>

			

	

			   <span class="pull-right pull_right_header">

  <a href="{{URL::route('change_password')}}" title="Change Password" class="btn-link " >Change Password</a> | <a href="{{URL::route('signout',Auth::user()->getsurvey_Details()->url)}}" title="Logout" class="btn-link " >Logout</a>
 			</span>


			</div>

		   </div>

		</div>

	    </div>

	</div>

</div>

</header>
