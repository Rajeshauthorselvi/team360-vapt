@extends('layouts.default')
@section('content')
<?php
	foreach ($all_questions as $key => $question) {
		$datas[$question->question_dimension][]=$question;
	}
foreach ($item_report as $key => $ireport) {
	foreach ($ireport as $key => $report) {
		$reports_collection[$report->question_id][$report->rater_id]=$report->ravg;

		$reports_collection_avg[$report->question_dimension][$report->rater_id][]=$report->ravg;
	}
}
 ?>
<div class="container">
<div class="dimension-heading container-fluid">
  Item Report
 </div>
 <br>
 <br>
 <div>
  <ul>
    <li>This report provides an in depth presentation of your data for each dimension assessed in the 360-degree evaluation.</li>
  </ul>
 </div>

<div class="col-sm-offset-2">
  <div class="text-left details_survey"><label class="">Survey Name:</label>{{$survey_name}}</div>
  <div class="text-left details_survey"><label>Batch Name:</label>{{$survey_name}}</div>
  <div class="text-left details_survey"><label>Participant Name:</label>{{$user_name}}</div>
</div>

<?php $dimension_count=1; $colspan=count($raters)+1; $row_count=1; ?>
	@foreach($datas as $question_dimension=>$questions)
		<table class="table table-bordered survey-table">
			<thead>
				<tr>
					<th>#{{$dimension_count}}</th>
					<th colspan="{{$colspan}}">{{$question_dimension}}</th>
				</tr>
				<tr>
					<th>Item Number</th>
					<th>Item Description</th>
					@foreach($raters as $rater)
						<th>{{$rater->rater}}@if($rater->rater!="self")(N={{$count_completed[$rater->rater_id]}})@endif</th>
					@endforeach
				</tr>
			</thead>
			<tbody>
				<?php $s_no=1;?>
				@foreach($questions as $key=>$question)
					<tr>
						<td>{{$s_no++}}</td>
						<td>{{$question->question_text}}</td>
					@foreach($raters as $rater)
					<td class="text-center">
						@if(isset($reports_collection[$question->question_id][$rater->rater_id])) 
							{{$reports_collection[$question->question_id][$rater->rater_id]}}
						@else
							-
						@endif
					</td>
					@endforeach
					</tr>
				@endforeach
					<tr class="average">
							<td colspan="2" class="text-right ">Average: </td>
							@foreach($raters as $rater)
							<td class="text-center">
							<?php 
								if(isset($reports_collection_avg[$question_dimension][$rater->rater_id])){
									$sum_value=array_sum($reports_collection_avg[$question_dimension][$rater->rater_id]);
									$count_values=count($reports_collection_avg[$question_dimension][$rater->rater_id]);

									$total_value=$sum_value/$count_values;
								}
								else
								{
									$total_value='0';
								}
							?>
							{{round($total_value,1)}}
							</td>
							@endforeach
					</tr>
			</tbody>
		</table>
		<?php $dimension_count++; ?>
	@endforeach
</div>
<style type="text/css">

.average{
	background-color: #C7C7C7;
	font-weight: bold;
}
.dimension-heading{
	text-align: center;
	padding: 15px;
	font-weight: bold;
	background-color: #E6E7E8;
	font-size: 13pt;
}
.text-left.details_survey > label {
  padding-right: 10px;
  text-align: right;
  width: 16%;
}
</style>
@endsection