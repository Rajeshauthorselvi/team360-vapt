@extends('layouts.default')

@section('content')
<div class="container">
<table class="table table-bordered survey-table">
	<th>S.No</th>
	<th>Links</th>
	<tr>
<!--
		<td>1</td>

		<td><a href="{{ route('participantreport.index',['participant_id'=>$participant_id,'survey_id'=>$survey_id]) }}"><i class="glyphicon glyphicon-tasks" aria-hidden="true"></i> Dimension 1</a></td>
	</tr>
-->
	<tr>
		<td>1</td>
		<td><a href="{{ route('diminsion1',['user_id'=>$participant_id,'survey_id'=>$survey_id]) }}"><i class="fa fa-bar-chart" aria-hidden="true"></i> Dimension 1</a></td>
	</tr>
	<tr>
		<td>2</td>
		<td><a href="{{ route('diminsion2',['user_id'=>$participant_id,'survey_id'=>$survey_id]) }}"><i class="fa fa-bar-chart" aria-hidden="true"></i> Dimension 2</a></td>
	</tr>

	<tr>
		<td>3</td>
		<td><a href="{{ route('participantreport.create',['participant_id'=>$participant_id,'survey_id'=>$survey_id]) }}"><i class="glyphicon glyphicon-tasks" aria-hidden="true"></i> Item Wise Others</a></td>
	</tr>
	<tr>
		<td>4</td>
		<td><a href="{{ route('itemwise_others_sort',['participant_id'=>$participant_id,'survey_id'=>$survey_id]) }}"><i class="glyphicon glyphicon-tasks" aria-hidden="true"></i> Item Wise Others(sort)</a></td>
	</tr>

	<tr>
		<td>5</td>
		<td><a href="{{ route('topandbottom',['participant_id'=>$participant_id,'survey_id'=>$survey_id]) }}"><i class="glyphicon glyphicon-tasks" aria-hidden="true"></i> Top and Bottom</a></td>
	</tr>

	<tr>
		<td>6</td>
		<td><a href="{{ route('converging_diverging',['participant_id'=>$participant_id,'survey_id'=>$survey_id]) }}"><i class="glyphicon glyphicon-tasks" aria-hidden="true"></i>Converging and Diverging</a></td>
	</tr>

	<tr>
		<td>7</td>
		<td><a href="{{ route('gap_report',['participant_id'=>$participant_id,'survey_id'=>$survey_id]) }}"><i class="glyphicon glyphicon-tasks" aria-hidden="true"></i>Gap Report</a></td>
	</tr>
<tr>
		<td>8</td>
		<td><a href="{{ route('diminsion_item',['user_id'=>$participant_id,'survey_id'=>$survey_id]) }}"><i class="fa fa-bar-chart" aria-hidden="true"></i> Item Report</a></td>
	</tr>
	<tr>
		<td>9</td>
		<td><a href="{{ route('diminsion_open_ended',['user_id'=>$participant_id,'survey_id'=>$survey_id]) }}"><i class="fa fa-bar-chart" aria-hidden="true"></i> Open Ended Report</a></td>
	</tr>
	<tr>
		<td>10</td>
		
		<td><a href="{{ route('item_wise_self',['user_id'=>$participant_id,'survey_id'=>$survey_id,'sort'=>'unsort']) }}"><i class="fa fa-bar-chart" aria-hidden="true"></i> Item Wise-Self vs Others</a></td>
	</tr>
		<tr>
		<td>11</td>
		<td><a href="{{ route('item_wise_self',['user_id'=>$participant_id,'survey_id'=>$survey_id,'sort'=>'sort']) }}"><i class="fa fa-bar-chart" aria-hidden="true"></i> Item Wise-Self vs Oth (Sorted)</a></td>
	</tr>
	</tr>
		<tr>
		<td>12</td>
		<td><a href="{{ route('question_dimension_based',['user_id'=>$participant_id,'survey_id'=>$survey_id]) }}"><i class="fa fa-bar-chart" aria-hidden="true"></i> Report (Based on Question Dimension)</a></td>
	</tr>


	
	</table>

				
</div>
@endsection
