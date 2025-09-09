@extends('layouts.default')

@section('content')

<div class="container">
	<div class="panel panel-default">

   <div class="panel-heading">
	<div class="row text-center">
	<h4><strong>List of Participants</strong></h4>
	</div>
   </div>

   <div class="panel-body">
	<table id="stable" class="table table-striped table-bordered dt-responsive nowrap survey-table" width="100%" cellspacing="0">
	<thead>
		<tr>
		<th>S.No</th>
		<th>First Name</th>
		<th>Last Name</th>
		<th>Email</th>
		<th>Status</th>
		<th>Report</th>
		<th>Respondents Count</th>
		</tr>
	</thead>
	<tbody>

		@if(count($data)!=0)
		<?php $s_no=1; ?>
		@foreach($data as $key=>$result)
		<tr>

		<td>{{$key+1}}</td>
		<td>{{$result->fname}}</td>
		<td>{{$result->lname}}</td>
		<td>{{$result->email}}</td>
		<td>
		<?php
		if($result->survey_status=='0') echo '<span class="alert-danger">Closed</span>';
		elseif($result->survey_status=='1') echo '<span class="alert-info">Active</span>';
		elseif($result->survey_status=='2') echo '<span class="alert-warning">Partly Completed</span>';
		elseif($result->survey_status=='3') echo '<span class="alert-success">Completed</span>';
		elseif($result->survey_status=='4') echo '<span class="alert-danger">In-Active</span>';
		?>
		</td>
		@if($result->survey_status=='3')
		<td><a href="{{ route('report_dashboard',['participant_id'=>$result->participant_id,'survey_id'=>$survey_id]) }}" ><span class="glyphicon glyphicon-align-justify" style="color:#337ab7" data-toggle="tooltip" title="Reports"></span></a></td>
		@else
		<td>-</td>
		@endif
		<td>
		<?php
		$respondents_count = DB::table('user_survey_respondent')
		->where('user_survey_respondent.respondent_id','<>',0)
		->where('user_survey_respondent.participant_id',$result->participant_id)->where('user_survey_respondent.survey_id',$result->survey_id)->count();

		$respondents_completed_count = DB::table('user_survey_respondent')
		->where('user_survey_respondent.respondent_id','<>',0)->where('survey_status',3)
		->where('user_survey_respondent.participant_id',$result->participant_id)->where('user_survey_respondent.survey_id',$result->survey_id)->count();


		?>
		{{$respondents_completed_count}} out of {{$respondents_count}}
		</td>

		<?php $s_no++; ?>
		@endforeach
		@else
		<tr><td colspan="7" class="text-center">No Results Found</td></tr>
		@endif
	</tbody>
	</table>
   </div>

	</div>
</div>



<script src="{{ asset('script/dataTable/jquery.dataTables.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/dataTable/jquery.dataTables.min.css') }}">

<script src="{{ asset('script/sweetalert.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/sweetalert.css') }}">

<script src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/dataTable/dataTables.bootstrap4.min.css') }}">





<script type="text/javascript">
$(document).ready(function(){
$('#stable').DataTable({
"bSort": false
});

});
</script>

<script type="text/javascript">
$('[data-toggle="tooltip"]').tooltip();
</script>

<style type="text/css">
table.survey-table{float: inherit;}
table.dataTable.no-footer {border-bottom: none;}
</style>

<style>
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
background: white;
border: 1px solid white;
color: white !important;
}
table.dataTable.no-footer {
border-bottom: 2px solid #dddddd;
}
table.dataTable thead th, table.dataTable thead td {
border-bottom: 2px solid #dddddd;
padding: 10px 10px;
}
th{
color:white;
background:#2041BD;
}
.pagination > li > a:focus, .pagination > li > a:hover, .pagination > li > span:focus, .pagination > li > span:hover {
border-color: white !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:active {
box-shadow: none;
outline:  none;
}

</style>
@endsection
