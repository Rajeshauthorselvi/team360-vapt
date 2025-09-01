<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="shortcut icon"  href={{ URL::asset('images/fav_icon.png') }} type="image/png" />

	<title>@if(isset($title)){{$title}}@endif</title>
	
   {!! HTML::script('script/jquery.js') !!}
   {!! HTML::script('script/jqueryui.js') !!}
   {!! HTML::script('script/bootstrap.min.js') !!}
   {!! HTML::style('css/bootstrap.min.css') !!}
   {!! HTML::style('css/jqueryui.css') !!}
   {!! HTML::script('script/bootstrapValidator.min.js') !!}
   {!! HTML::style('css/bootstrapValidator.min.css') !!}
   {!! HTML::style('css/font-awesome/css/font-awesome.min.css') !!}
   {{ HTML::script('script/sweetalert.min.js') }}
  {{ HTML::style('css/sweetalert.css') }}
   


@if(Auth::user()->id==1)
   {!! HTML::style('css/admin-common.css') !!}
   {!! HTML::script('script/admin-common.js') !!}
@endif


@if(Auth::user()->id>1)
   {!! HTML::style('css/user-common.css') !!}
   {!! HTML::script('script/user-common.js') !!}
@endif

</head>
<body>

@if(Auth::check())


<!-- Admin Section -->

@if(Auth::user()->id==1)

@include('layouts.header_admin')
@yield('content')
@include('layouts.footer_admin')


@endif




<!-- User Section -->

@if(Auth::user()->id>1)

@include('layouts.header_user')
@yield('content')
@include('layouts.footer_user')

@endif







@endif

</body>
</html> 

