@extends('layouts.default')

@section('content')
<?php 
foreach ($self as $key => $self_val) {
	$data_self[$self_val->question_dimension]=$self_val;
}
foreach ($respondent as $key => $res_val) {
	$data_respondent[$res_val->question_dimension]=$res_val;
}
// dd($self);
?>


<div class="container">
<div class="dimension-heading container-fluid">
  Dimension Report 1
 </div>
 <br>
 <br>
 <div>
  <ul>
    <li>
      This report provides a dimension level comparison of your self-ratings, the ratings that you received from all other sources, and the ratings that all of your classmates received (averaged across all sources).
    </li>
  </ul>
 </div>

<div class="col-sm-offset-2">
  <div class="text-left details_survey"><label>Survey Name:</label>{{$survey_name}}</div>
  <div class="text-left details_survey"><label>Batch Name:</label>{{$survey_name}}</div>
  <div class="text-left details_survey"><label>Participant Name:</label>{{$user_name}}</div>
</div>


<table class="table table-bordered survey-table" width="100%" cellspacing="0">
 <thead>
    <tr>
      <th>Dimension Number</th>
      <th>Dimension Name</th>
      <th>Self Average</th>
      <th>Others Average(N={{$total_complete}})</th>
    </tr>
 </thead>
 <tbody>
 	<?php
 		$i_no=1;
 	 ?>
 	@foreach($all_dimensions as $question_dimension)
 		<tr>
 			<td>{{$i_no}}</td>
 			<td>{{$question_dimension}}</td>
 			<td>
	 			@if(isset($data_self[$question_dimension]->ravg))
	 				{{$data_self[$question_dimension]->ravg}}
	 			@else
	 				-
	 			@endif
 			</td>
 			<td>
	 			@if(isset($data_respondent[$question_dimension]->ravg))
	 				{{$data_respondent[$question_dimension]->ravg}}
	 			@else
	 				-
	 			@endif
 			</td>
 		</tr>
 		<?php $i_no++; ?>
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