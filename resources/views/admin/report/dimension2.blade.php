@extends('layouts.default')

@section('content')
<?php
foreach ($dimension_2_datas as $key => $dimension_2_data) {
	foreach ($dimension_2_data as $key => $dimension_2) {
		$datas[$dimension_2->question_dimension][$dimension_2->rater_id]=$dimension_2;
	}
}
 ?>
<div class="container">
	<table class="table table-bordered survey-table">
	<thead>
		<tr>
			<th>Dimension Number</th>
			<th>Dimension Name</th>
			@foreach($raters as $rater)
				<th>{{$rater->rater}}@if($rater->rater!="self")(N={{$rater->count_completed_user}}) @endif</th>
			@endforeach
		</tr>
	</thead>
	<tbody>
		<?php $dimension_count=1; ?>
		@foreach($datas as $question_dimension=>$dimension_2_data)
			<tr>
				<td>{{$dimension_count}} </td>
				<td>{{$question_dimension}}</td>
				@foreach($raters as $rater)
					<td>
@if(isset($dimension_2_data[$rater->rater_id]->ravg))
{{$dimension_2_data[$rater->rater_id]->ravg}}
@else
-
@endif</td>
				@endforeach
			</tr>
			@php $dimension_count++; @endphp
		@endforeach
	</tbody>
</table>
</div>

@endsection
