<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<META HTTP-EQUIV="Pragma" CONTENT="no-cache">
<META HTTP-EQUIV="Expires" CONTENT="-1">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="viewport" content="width=device-width, initial-scale=1">

<link rel="shortcut icon"  href="{{ URL::asset('images/fav_icon.png') }}" type="image/png" />

	<title>@if(isset($title)){{$title}}@endif</title>
    <script src="{{ asset('script/jquery.js') }}"></script>
    <script src="{{ asset('script/jqueryui.js') }}"></script>
    <script src="{{ asset('script/bootstrap.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/jqueryui.css') }}">
    <script src="{{ asset('script/bootstrapValidator.min.js') }}"></script>
    <link rel="stylesheet" href="{{ asset('css/bootstrapValidator.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/font-awesome/css/font-awesome.min.css') }}">

    <link rel="stylesheet" href="{{ asset('css/themes/' . config('site.theme_slug')) }}">
    <script src="{{ asset('script/page_wise/paginga.jquery.js') }}"></script>
    <script src="{{ asset('script/page_wise/page_wise_questions.js') }}"></script>

</head>
<body>

@include('default.users.layouts.header')
<div class="page-wrapper">
@yield('content')
</div>
@include('default.users.layouts.footer')


<script type="text/javascript">
   $(document).ready(function(){
      var window_height=$(window).height();
      var position = $('.page-wrapper').position();
      var current_position=Math.ceil(position.top);
      var variable_height=Math.ceil(window_height-current_position-50);
      if(variable_height > 0 ) $('.page-wrapper').css('min-height',variable_height+"px");

   })
</script>

</body>
</html>

