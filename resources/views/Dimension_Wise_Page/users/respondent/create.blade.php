@extends('default.users.layouts.default')
@section('content')
<div class="container">
<div class="row">

<div class="col-sm-8 col-sm-offset-2 create">
  <h3 class="text-center">{{$title}}</h3>
  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#add-manually">Add New</a></li>
    <li><a data-toggle="tab" href="#add-import">Bulk Import</a></li>
  </ul>

  <div class="tab-content">
  <br />
    <div id="add-manually" class="tab-pane fade in active">
    @if ($errors->any())
      <div class="alert alert-danger fade in">
          <a href="#" class="close" data-dismiss="alert">&times;</a>
          <strong>Error!</strong> A problem has been occurred while submitting form.<br>
          <ul> {!! implode('', $errors->all('<li class="text-danger">:message</li>')) !!}</ul>
      </div>
     @endif
     <?php $participant_id = $participant_details->id;?>

     <form action="{{ route('manage-respondent.store', config('site.survey_slug')) }}" method="POST" id="add-participants" class="form-horizontal">
        @csrf
        <input type="hidden" name="survey_id" value="{{ $survey_id }}">
        <input type="hidden" name="participant_id" value="{{ $participant_id }}">

        <div class="form-group col-sm-12">
            <label for="fname" class="col-sm-5 col-md-5">First Name</label>
            <div class="col-sm-12">
                <input type="text" name="fname" id="fname" class="form-control" placeholder="First Name">
            </div>
        </div>

        <div class="form-group col-sm-12">
            <label for="lname" class="col-sm-5 col-md-5">Last Name</label>
            <div class="col-sm-12">
                <input type="text" name="lname" id="lname" class="form-control" placeholder="Last Name">
            </div>
        </div>

        <div class="form-group col-sm-12">
            <label for="email" class="col-sm-5 col-md-5">Email</label>
            <div class="col-sm-12">
                <input type="email" name="email" id="email" class="form-control" placeholder="Email">
            </div>
        </div>

        <div class="form-group col-sm-12">
            <label for="rater" class="col-sm-7 col-md-5">Rater / Respondent</label>
            <div class="col-sm-12">
                <select name="rater" id="rater" class="form-control">
                    @foreach($survey_rater_list as $key => $value)
                        <option value="{{ $key }}">{{ $value }}</option>
                    @endforeach
                </select>
            </div>
        </div>

        <div class="form-group" align="center">
            <a href="{{ route('manage-respondent.index', config('site.survey_slug')) }}" class="btn btn-danger">Cancel</a>
            <button type="submit" class="btn btn-submit">Save</button>
        </div>
    </form>

  </div>



 <div id="add-import" class="tab-pane fade">
     @if(Session::get('msg'))

      <div class="alert alert-danger import-process-error">
        <a class="close" onclick="$('.alert').hide()">x</a>
        @if(Session::get('msg')!="Heading Mismatch line @ 1 . (p_email,r_fname,r_lname,r_email) Plz enter this format.")
           <strong>Whoops! Some error occurred.</strong><br><br>
        <ul>
          @foreach(Session::get('msg') as $value)
            <li>{{$value}}</li>
          @endforeach
        </ul>
        @else
          {{Session::get('msg')}}
        @endif
      </div>
    @endif
    <form action="{{ URL::route('import_Respondent',[config('site.survey_slug'),"participant_id=$participant_id"]) }}" class="form-horizontal" method="POST" id="import_process" enctype="multipart/form-data">
      <input type="hidden" name="_token" value="{{ csrf_token() }}">
      <div class="instructions">
        <div class="group-text-info pull-left col-sm-6">
        <p class="text-info">Upload .xls .xlsx file with following headers to update the participant list. <b>(r_fname,r_lname,r_email,r_type)</b></p>

        </div>
        <div class="raters pull-right col-sm-6">
          <table class="table table-bordered text-center">
            <thead>
              <th>Rater Id</th>
              <th>Rater</th>
            </thead>
            <tbody>
              @foreach($rater_list as $rater)
                <tr>
                  <td>{{$rater->rater_id}}</td>
                  <td>{{$rater->rater}}</td>
                </tr>
              @endforeach
            </tbody>
          </table>
        </div>
        <p class="text-info col-sm-12" >Download sample users <a href="{{URL::route('manage-respondent.show',[config('site.survey_slug'),0,'action=download-sample-respondent-import'])}}" class="btn btn-primary"><i class="fa fa-file-excel-o"></i> Download</a></p>
      </div>
      <div class="col-sm-12" style="clear: both;">
      <input type="hidden" name="survey_id" value="{{ $survey_id }}">
            <input type="file" class="form-control filestyle" data-buttonName="btn-primary" placeholder="File type:xls,xlsx" name="import_file" id="upload" accept=".xls, .xlsx"/>
      </div>
      <br>
      <br>
    <div class="form-group col-sm-12" align="center" style="clear: both;">
     <a href="{{ route('manage-respondent.index',config('site.survey_slug')) }}" class="btn btn-danger">Cancel</a>
        <button type="submit" class="btn btn-submit">Save</button>
    </div>


    </form>
    </div>


  </div>



 </div>
</div>
</div>
<script src="{{ asset('script/bootstrap-filestyle.js') }}"></script>

<style media="screen">
.help-block{
  color: #C9302C;
}
.nav-tabs li.bv-tab-error>a {
  color: #555;
}
.nav.nav-tabs a
{
  border:1px solid #ddd;
}
.nav > li.active a ,.nav > li.active a:hover,.nav > li.active a:focus {
    background-color: #e6e7e8;
}

	.glyphicon-copy{
		color: #2041bd;
	}
	.nav > li.active a, .nav > li.active a:hover, .nav > li.active a:focus {
    background-color: #286090;
    border-color: #286090;
    color: #ffffff;
}

.rater {
    background-color: white;
    padding: 5px;
    width: 100%;
}
footer{
  position: fixed;
}
.create{
  margin-bottom: 79px;
}
.tab-content{
  float: left;
  width: 100%;
}
#add-participants{
  padding: 10px;
}
.form-group.col-sm-12 {
    padding-left: 46px;
    margin-top: 10px;
}
/*.nav.nav-tabs li {
    padding-left: 10px;
}*/

</style>

<script type="text/javascript">
  var survey_id="{{$survey_id}}";
  var participant_id="{{$participant_id}}";
  var participant_email = "<?php echo $participant_details->email; ?>";
  var routeurl="{!!URL::route('manage-respondent.show',[config('site.survey_slug'),'validate-user-respondent'])!!}";
</script>
<script src="{{ asset('script/add_respondent.js') }}"></script>
<script src="{{ asset('script/sweetalert.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">

@endsection

