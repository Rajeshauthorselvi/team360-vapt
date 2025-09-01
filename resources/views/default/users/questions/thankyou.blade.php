@extends('default.users.layouts.default')
@section('content')


<div class="col-sm-12  col-md-8 col-md-offset-2 col-xs-12" id="welcome-section">
<div class="site-content">
<div class="welcome-box">
  <div class="welcome-body">
  {!! $thankyou_text !!}
  </div>
  <div class="welcome-footer">
    <a class='btn btn-primary btn-lg' href="{{URL::route('user.dashboard',[config('site.survey_slug')])}}">Back to Home</a>
  </div>
</div>
</div>




</div>
@endsection
