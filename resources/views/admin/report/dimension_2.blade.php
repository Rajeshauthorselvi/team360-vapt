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
	<div class="dimension-heading container-fluid">
 	Dimension Report 2
 </div>
 <br>
 <br>
 <div>
 	<ul>
 		<li>This report provides a dimension level comparison of your ratings broken down by self, boss/superior, peers, and direct reports.</li>
 	</ul>
 </div>

<div class="col-sm-offset-2">
	<div class="text-left details_survey"><label class="">Survey Name:</label>{{$survey_name}}</div>
	<div class="text-left details_survey"><label>Batch Name:</label>{{$survey_name}}</div>
	<div class="text-left details_survey"><label>Participant Name:</label>{{$user_name}}</div>
</div>

<table class="table table-bordered survey-table">
	<thead>
		<tr>
			<th>Dimension Number</th>
			<th>Dimension Name</th>
			@foreach($raters as $rater)
				<th>{{$rater->rater}}@if($rater->rater!="self")(N={{$count_completed[$rater->rater_id]}})@endif</th>
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
				<td>@if(isset($dimension_2_data[$rater->rater_id]->ravg))
						{{$dimension_2_data[$rater->rater_id]->ravg}}
					@else
						-
					@endif
				</td>
				@endforeach
			</tr>
			<?php $dimension_count++; ?>
		@endforeach
	</tbody>
</table>
<input id="switch" type="checkbox" data-toggle="toggle" data-on-label="Horizontal" data-off-label="<i class='icon-remove'></i>">

<div class="column">
	<div id="poll_div" class="column-chart text-center"></div>
	{!! Lava::render('ColumnChart', 'Votes', 'perf_div') !!}
</div>
<div class="bar" style="display:none">
	<div id="perf_div" class="bar-chart"></div>
	{!! Lava::render('BarChart', 'Votes', 'poll_div') !!} 
</div>

</div>
<style type="text/css">
th{
	text-align: center;
}
.toggle.btn {
  min-height: 34px;
  min-width: 75px;
}
.toggle-off.btn {
  padding-left: 20px;
}
.text-left.details_survey > label {
  padding-right: 10px;
  text-align: right;
  width: 16%;
}	
.dimension-heading{
	text-align: center;
	padding: 15px;
	font-weight: bold;
	background-color: #E6E7E8;
	font-size: 13pt;
}
</style>
<script type="text/javascript">
$(document).ready(function() {
$('.toggle-off').html('Bar');
$('.toggle-on').html('Column');

$('#switch').change(function() {
  if($(this).is(':checked'))
  {
    $('.bar').show();
    $('.column').hide();
  }
  else{
    $('.bar').hide();
    $('.column').show();
  }
  
});

});
</script>


@endsection