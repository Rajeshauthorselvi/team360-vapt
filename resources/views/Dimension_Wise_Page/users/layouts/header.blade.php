<header>

<div class="inner-header">

<div class="container">



<div class="row">

<?php
$left_logo=config('site.left_logo');
$right_logo=config('site.right_logo');
$header_text=config('site.header_text');
$footer_text=config('site.footer_text');
$header_class_name="col-md-3  col-xs-3 col-sm-6 logo";
if($left_logo!="" && $right_logo!="" && $header_text!="") $header_class_name="col-md-3 col-xs-3 col-sm-4 logo";
?>
<div class="full-width">
	@if(isset($left_logo))
	<div class="{{$header_class_name}}" align="left">
	@if(Auth::check())
	{!! html_entity_decode(HTML::linkRoute('user.dashboard',HTML::image('storage/surveys/'.$left_logo,'Logo',['class'=>'img-responsive']),[config('site.survey_slug')])) !!}
	@else
	{!! html_entity_decode(HTML::image('storage/surveys/'.$left_logo,'Logo',['class'=>'img-responsive'])) !!}
	@endif
	</div>
	
	@endif

	@if(isset($header_text))
	<!-- <div class="{{$header_class_name}} header-des hide-mob" align="center">{!! $header_text !!}</div> -->
	<div class="col-md-6  col-xs-6 col-sm-4 logo header-des hide-mob " align="center">{!! $header_text !!}</div>
	
	@endif


	@if(isset($right_logo) && $right_logo!="")
	{{-- <div class="{{$header_class_name}} hide-mob  pull-right" align="right"> --}}
	<div class="{{$header_class_name}} pull-right" align="right">
	@if(Auth::check())
	{!! html_entity_decode(HTML::linkRoute('user.dashboard',HTML::image('storage/surveys/'.$right_logo,'Logo',['class'=>'img-responsive']),[config('site.survey_slug')])) !!}
	@else
	{!! html_entity_decode(HTML::image('storage/surveys/'.$right_logo,'Logo',['class'=>'img-responsive'])) !!}
	@endif
	</div>
	@endif

</div>



</div>

</div>








</div>

</header>


<div class="welcome-strip">
	<div class="container">
		<div class="row">
		@if(Auth::check())
			<div class="col-md-12 ">
  				<a href="{{URL::route('user.dashboard',[config('site.survey_slug')])}}" title="Home" >
  					<span class="glyphicon glyphicon-home" ></span>
  				</a> &nbsp;
				 
				<span style=" text-transform: capitalize">{!! Session::get('name') !!}</span>
				<span class="pull-right">
					<a href="{{URL::route('change-password',[config('site.survey_slug')])}}" title="Change Password" class="btn-link " >Change Password</a> <span>|</span> <a href="{{URL::route('signout',config('site.survey_slug'))}}" title="Logout" class="btn-link " >Logout</a>
				</span>
			</div>
		@endif
		</div>
	</div>
</div>
<style type="text/css">
	.full-width{
		height: auto;
	}
</style>
