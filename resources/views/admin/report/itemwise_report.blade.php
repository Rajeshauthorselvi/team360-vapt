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
<table  id="active_survey" class="table table-striped table-bordered dt-responsive nowrap survey-table" width="100%" cellspacing="0">
<thead>
	<tr>
		<th class="no-sort">S.No</th>
		<th class="no-sort">Competency</th>
		<th class="no-sort">Statement</th>
		<th class="no-sort">Self Rating</th>
		<th class="no-sort">Average of Ratings by Others</th>
		<th>Difference</th>
	</tr>
</thead>
<?php $s_no=1; ?>
<?php
	foreach ($all_questions as $key => $question) {
		$all_question[$question->question_id][]=$question;
	}

foreach ($respondent_datas as $key => $value) {
		$data_respondent[$value->question_id]=$value;
}
foreach ($self_datas as $key => $value) {
		$data_self[$value->question_id]=$value;
}
// dd($data_respondent);
?>
<tbody>
	<?php $row_count=1;?>
	@foreach($all_question as $question_id=>$question)
		<tr>
			<td>{{$row_count}}</td>
			@foreach($question as $key=>$ques)
				<td>{{$ques->question_dimension}}</td>
				<td>{{$ques->question_text}}</td>
				<td class="text-center">
					@if(isset($data_self[$question_id]->option_weight))
						<?php $self_weight=$data_self[$question_id]->option_weight; ?>
						{{$data_self[$question_id]->option_weight}}
					@else
						-
						<?php $self_weight='0'; ?>
					@endif
				</td>
				<td class="text-center">
					@if(isset($data_respondent[$question_id]->option_total))
						<?php $respondent_weight=$data_respondent[$question_id]->option_total; ?>
						{{$data_respondent[$question_id]->option_total}}
					@else
						-
						<?php $respondent_weight='0'; ?>
					@endif
				</td>
				<td class="text-center">{{$self_weight-$respondent_weight}}</td>
			@endforeach
		</tr>
		<?php $row_count++;  ?>
	@endforeach
</tbody>
</table>
</div>
</div>
</div>
 @if($sorting_type=="sort")
<script type="text/javascript">
$(document).ready(function() {
    var t = $('#active_survey').DataTable( {
    	"pageLength": 50,
        "order": [ 5, 'desc' ],
        "aoColumnDefs": [
          { 'bSortable': false, 'aTargets': [ 0,1,2,3,4 ] }
       ]
    } );
    t.on( 'order.dt search.dt', function () {
        t.column(0, {search:'applied', order:'applied'}).nodes().each( function (cell, i) {
            cell.innerHTML = i+1;
        });
    } ).draw();
} );
</script>
@else
<script type="text/javascript">
 $('#active_survey').DataTable({
      "bSort": false,
      "pageLength": 50
    });
 </script>
@endif
<style type="text/css">
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
  background: none !important;
  border: medium none !important;
  color: white;
}
</style>
@endsection