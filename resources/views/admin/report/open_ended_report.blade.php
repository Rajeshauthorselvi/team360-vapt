@extends('layouts.default')

@section('content')
<?php
	foreach($open_end_results as $open_results){
		$datas[$open_results->question_text][]=$open_results->rater.'|'.$open_results->text_response;
	}
 ?>
<div class="container">
	
	@if(count($datas)>0)
	<?php $s_no=1 ?>
	@foreach($datas as $key=>$data)
		<table class="table table-bordered survey-table">
		<tr class="table-row" style="background-color: #f7f7f7 !important;">
			<th>#{{$s_no}}</th>
			<th >{{$key}}</th>
		</tr>
		<tr>
			<th>User Type</th>
			<th>Open Ended Responses</th>
		</tr>
		@foreach($data as $open_result)
		<?php $open_datas=explode('|', $open_result); ?>
		<tr>
			<td><strong>{{$open_datas[0]}}</strong></td>
			<td>{{$open_datas[1]}}</td>
			
		</tr>
		@endforeach
		
			
		</table>
		<?php $s_no++; ?>
	@endforeach
	@else
	<table class="table table-bordered survey-table">
		<tr>
			<th>User Type</th>
			<th>Open Ended Responses</th>
		</tr>
		<tr>
			<td colspan="2" class="text-center">No Results Found</td>
		</tr>
	</table>
	@endif
</div>
<style type="text/css">

.table-row th{
	background-color: #F7F7F7 !important;
	color: #000;
}

tr:nth-child(even) {background: #F7F7F7}
tr:nth-child(odd) {background: #FFF}
</style>
@endsection