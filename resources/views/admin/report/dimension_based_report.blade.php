@extends('layouts.default')

@section('content')

<?php
// dd($all_questions);
foreach ($reports as $key => $ireport) {
	foreach ($ireport as $key => $report) {
		$reports_collection[$report->question_id][$report->rater_id]=$report;
	}
}

$colspan=count($all_raters)+2;
?>
<div class="container">
  <div class="panel panel-default">
    <div class="panel-heading">
     <div class="row text-center">
	<h4><span style="color:black"><b>Survey Name :</b> </span><strong>{{$survey_name}}</strong></h4>
     </div>
    </div>

<div class="panel-body">
<table class="table table-bordered survey-table">
	<thead>
		<tr class="tr">
			<th class="text-center" colspan="{{$colspan}}">{{$question_dimension}}</th>
		</tr>
		<tr>
		<th>S.No</th>
		<th>Statements</th>
			@foreach($all_raters as $rater)
				<th>{{$rater}}</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php $row_count=1; ?>
		@foreach($all_questions as $question_id=>$question)
		<tr>
			<td>{{$row_count}}</td>
			<td>{{$question}}</td>
			@foreach($all_raters as $rater_id=>$rater)
			<td class="text-center">
				@if(isset($reports_collection[$question_id][$rater_id]->total))
                {{round($reports_collection[$question_id][$rater_id]->total,1)}}
				@else
					-
				@endif
			</td>
			@endforeach
		</tr>
		<?php $row_count++; ?>
		@endforeach
	</tbody>
</table>
</div>
</div>
</div>
<style type="text/css">
.tr th{
	background-color: #e6e7e8;
	color: #000;
}

</style>
@endsection


