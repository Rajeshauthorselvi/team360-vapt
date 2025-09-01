@extends('layouts.default')

@section('content')
<div class="container">
   <div class="panel panel-default">
	 <div class="panel-heading"> <div class="row text-center">
	<h3><span style="color:#286090"><b>Survey Name :</b> </span><strong>{{$survey_name}}</strong></h3>
	</div></div>
<div class="panel-body">
	<table class="table table-bordered survey-table">
	<th>S.No</th>
	<th>Name</th>
	<th>Email</th>
	<th>Respondents Count</th>
	<th>Status</th>
	<th>Report</th>
	<?php $s_no=1; ?>
	@foreach($complete_user_Datas as $completed_user_Data)

		<tr>
			<td class="text-center">{{$s_no}}</td>
			<td>{{$completed_user_Data->fname.' '.$completed_user_Data->lname}}</td>
			<td>{{$completed_user_Data->email}}</td>
			<td class="text-center">
			<?php
			  $count_complete_respondent=DB::table('user_survey_respondent')
			        ->where('survey_id',$survey_id)
			        ->where('participant_id',$completed_user_Data->user_id)
			        ->where('survey_status','=',3)
			        ->where('respondent_id','<>',0)
			        ->count();

				$respondents_count = DB::table('user_survey_respondent')
							->where('user_survey_respondent.respondent_id','<>',0)
							->where('user_survey_respondent.participant_id',$completed_user_Data->user_id)
							->where('user_survey_respondent.survey_id',$survey_id)
							->count();
				?>
				{{$count_complete_respondent.'/'.$respondents_count}}
	   		</td>
	   		
			<td class="text-center">
					<?php
			          if($completed_user_Data->survey_status=='0') 	   echo '<span class="alert-danger">Closed</span>';
			          elseif($completed_user_Data->survey_status=='1') echo '<span class="alert-info">Active</span>';
			          elseif($completed_user_Data->survey_status=='2') echo '<span class="alert-warning">Partly Completed</span>';
			          elseif($completed_user_Data->survey_status=='3') echo '<span class="alert-success">Completed</span>';
			          elseif($completed_user_Data->survey_status=='4') echo '<span class="alert-danger">In-Active</span>';
        			?>
			</td>
			<td class="text-center">
				@if($completed_user_Data->survey_status=='3') 
					<a href="{{ route('report_dashboard',['user_id'=>$completed_user_Data->user_id,'survey_id'=>$survey_id]) }}" class="btn btn-primary" data-toggle="tooltip" title="View Reports">
						<i class="fa fa-eye" aria-hidden="true"></i>
					</a>
				@endif
			</td>
		</tr>
		<?php $s_no++; ?>
	@endforeach
</table>
   </div>
</div>
</div>
<style type="text/css">
th{
	text-align: center;
}

tbody tr > td{
	vertical-align: middle !important;
}
</style>
<script type="text/javascript">
$(document).ready(function() {
	$('[data-toggle="tooltip"]').tooltip();
});
</script>
@endsection
