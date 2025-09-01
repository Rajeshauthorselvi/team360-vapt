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

<table class="table table-striped table-bordered dt-responsive nowrap survey-table" width="100%" cellspacing="0">
 <thead>
    <tr>
      <th>Dimension Number</th>
      <th>Dimension Name</th>
      <th>Self Average</th>
      <th>Self Average (round)</th>
      <th>Others Average(N={{$others_count}})</th>
      <th>Others Average (round)(N={{$others_count}})</th>
      
    </tr>
 </thead>
 <tbody>
@if(count($average)!=0)
<?php $s_no=1; ?>
	@foreach($average as $key=>$result)
	<tr>
	<td>{{$s_no}}</td>	
	<td>{{$key}}</td>
		@foreach($result as $avg)
		<td>{{$avg}}</td>
		<td><?php echo round($avg,2); ?></td>
		@endforeach
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


