@extends('layouts.default')

@section('content')
<div class="container">
  <div class="panel panel-default">
    <div class="panel-heading"> 
     <div class="row text-center">
	<h4><span style="color:black"><b>Survey Name :</b> </span><strong>{{$survey_name}}</strong></h4>
     </div>
    </div>

<div class="panel-body">

    <span class="participant_name">Participant Name:<b>{{$participant_fname}} {{$participant_lname}}</b></span>        
<br/>      
<b>Top 5 Strengths Statements</b>
<table class="table table-striped table-bordered dt-responsive nowrap survey-table" width="100%" cellspacing="0">
 <thead>
    <tr>
      <th>S.no</th>
      <th>Competency</th>
      <th>Statement</th>
      <th>Others Average </th>
      <th>Others Average (Round)</th>
      
    </tr>
 </thead>
 <tbody>
@if(count($data_desc)!=0)
<?php $s_no=1; ?>
	@foreach($data_desc as $key=>$result)
	<tr>
	<td>{{$s_no}}</td>	
	<td>{{$result->question_dimension}}</td>
	<td>{{$result->question_text}}</td>
	<td>{{$result->ravg}}</td>
	<td>{{$result->rround}}</td>

	</tr>
	<?php $s_no++; ?>
	@endforeach
@else
<tr><td colspan="3" class="text-center">No Results Found</td></tr>
@endif
 </tbody>
</table>

<br/>
<br/>
<b>Top 5 Areas Of Development Statements</b>
<table class="table table-striped table-bordered dt-responsive nowrap survey-table" width="100%" cellspacing="0">
 <thead>
    <tr>
      <th>S.no</th>
      <th>Competency</th>
      <th>Statement</th>
      <th>Others Average </th>
      <th>Others Average (Round)</th>
      
    </tr>
 </thead>
 <tbody>
@if(count($data_asc)!=0)
<?php $s_no=1; ?>
	@foreach($data_asc as $key=>$result)
	<tr>
	<td>{{$s_no}}</td>	
	<td>{{$result->question_dimension}}</td>
	<td>{{$result->question_text}}</td>
	<td>{{$result->ravg}}</td>
	<td>{{$result->rround}}</td>

	</tr>
	<?php $s_no++; ?>
	@endforeach
@else
<tr><td colspan="3" class="text-center">No Results Found</td></tr>
@endif
 </tbody>
</table>




</div>

  </div>
</div>
<style>
.participant_name {
font: medium icon;
line-height:3.5;
}
</style>
@endsection


