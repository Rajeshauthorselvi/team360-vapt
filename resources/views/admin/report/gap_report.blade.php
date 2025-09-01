@extends('layouts.default')

@section('content')

<?php
if(count($smallestvalue)!=0){
foreach($smallestvalue as $questionid=>$diff){
$question_dimension_small[$questionid]=DB::table('questions')->where('id',$questionid)->value('question_dimension');
}
}
?>

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
<b>Top 10 Largest Gap Between Self and Others Ratings</b>
<table class="table table-striped table-bordered dt-responsive nowrap survey-table" width="100%" cellspacing="0">
 <thead>
    <tr>
      <th>S.no</th>
      <th>Competency</th>
      <th>Statement</th>
      <th>Self Average </th>
      <th>Others Average </th>

      
    </tr>
 </thead>
 <tbody>

@if(count($smallestvalue)!=0)
<?php $s_no=1; ?>
@foreach($smallestvalue as $question_id=>$diff)
<tr>

<td>{{$s_no}}</td>

<td>{{$question_dimension_small[$question_id]}}</td>

<td>{{$question_details[$question_id]}}</td>

<td>{{$self_avg[$question_id]}}</td>

<td>{{$others_avg[$question_id]}}</td>


	
<?php $s_no++; ?>

@endforeach
</tr>

@else
<tr><td colspan="6" class="text-center">No Results Found</td></tr>
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


