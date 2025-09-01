@extends('layouts.default')

@section('content')
<div class="container">
	<table class="table table-bordered survey-table">
		<th>S.No</th>
		<th>Question Dimension</th>
		<?php $s_no=1; ?>
		@foreach($question_dimension as $key=>$dimension)
		<tr>
			<td>{{$s_no}}</td>
			<td>
				<a href="{{ route('question_dimension',['user_id'=>$user_id,'survey_id'=>$survey_id,'question_dimension'=>$dimension]) }}">
					{{$dimension}}
				</a>
			</td>
		</tr>
		<?php $s_no++; ?>
		@endforeach
	</table>
</div>
@endsection