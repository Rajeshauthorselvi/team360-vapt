@extends('layouts.default')

@section('content')
<div class="container">
   <div class="panel panel-default">
   <div class="panel-heading"> <div class="row text-center">
  <h4><span><b>Survey Name :</b> </span><strong>{{$survey_name}}</strong></h4>
  </div></div>
<div class="panel-body">
    <div class="col-sm-3">
        @if(count($participant_id)>0)
        <form method="POST" action="{{ route('post.raw_response', ['survey_id' => $survey_id]) }}" class="form-horizontal">
            @csrf
          <select name="users[]" multiple class="users">
            @foreach($participant_id as $key=>$users)
                <option value="{{$users}}">{{$key}} </option>
            @endforeach
          </select>
          <button type="submit" name="button" class="btn btn-primary submit">Filter</button>
        </form>
        @endif
     </div>
   <div class="col-sm-9">

	 @if(!empty($responses))
	    <div class="text-right">
            <span class="btn btn-primary"><a href="{{ route('raw_core_ques.ques_export', ['survey_id' => $survey_id, 'survey_name' => $survey_name]) }}">
                <span class="glyphicon glyphicon-download-alt"></span>
                Download Questions
                <span class="fa fa-file-excel-o" aria-hidden="true"></span>
            </a>
            </span>

	        <button type="submit" name="button" class="btn btn-primary" id="submit"><span class="glyphicon glyphicon-download-alt"></span> Download 		<span class="fa fa-file-excel-o" aria-hidden="true"></span></button>
	    </div>
	  @endif
   </div>

 <div class="col-sm-12">
  <div class="table-responsive">
  	<table class="table table-striped table-bordered" cellspacing="0" width="100%"  id="raw_score">
		<thead>
			<tr>
			<th  class="competency-header"></th>
			<th  class="competency-header"></th>
			<th  class="competency-header"></th>
			<th  class="competency-header"></th>
			<?php $alpha=range('a', 'z'); ?>
			@if($responses!='')
			@if(count($question_dimension)>0)
				@foreach($question_dimension as $key=>$value)
					<?php $count=explode('|',$key);
					$question_count=count($count); ?>
					<th colspan="{{$question_count}}" style="text-align: center;" >{!!ucfirst($value)!!}</th>
				@endforeach
			@endif
			@endif

			</tr>
			<tr>
				<th class="header">S.No</th>
				<th class="header">Name</th>
				<th class="header">Email</th>
				<th class="header">Rater-type</th>

				@if($responses!='')

				@if(count($question_id)>0)

<?php $s_no=1; ?>
				@foreach($question_id as $key=>$order_value)
				@foreach($order_value as $key=>$order)


 <?php $count=explode('|',$order); $r_count=count($count);?>
 @if($r_count==1)
		<th class="header">Q{{$s_no}}</th>

 @elseif($r_count>1)
<?php $alpha_range=range('A', 'Z'); ?>
@for($i=0;$i<$r_count;$i++ )
		<th class="header">Q{{$s_no}}({{$alpha_range[$i]}})</th>

@endfor
@endif


			 <?php $s_no++; ?>
				@endforeach
				@endforeach


				@endif
			@endif


			</tr>
		</thead>
		<tbody>
		<?php $no=1; ?>

		@if($responses!='')

		@if(count($responses)>0)
			@foreach($responses as $user_survey_id=>$user_info)
				<tr>
					<td>{{$no}}</td>
					<td>{{ (isset($user_info['username'])) ? $user_info['username'] : '' }}</td>
					<td>{{ (isset($user_info['email'])) ? $user_info['email'] : '' }}</td>
					<td>{{ (isset($user_info['rater_type'])) ? ucfirst($user_info['rater_type']) : '' }}</td>

					@foreach($user_info['response'] as $question_order)
						@if (count($question_order)>0)
							@foreach($question_order as $question_id =>$option_weight)
								<td>
								@if(isset($option_weight))
								{{$option_weight}}
								@else
								-  test
								@endif
								</td>
							@endforeach
						@else
							<td>-</td>
						@endif
					@endforeach
				</tr>
			<?php $no++;?>
			@endforeach
		@endif
@else
<tr><td colspan="4" class="text-center" >No Results Found</td></tr>
		@endif



		</tbody>


	</table>
    </div></div>
</div>
</div>
</div>

<!-- DataTables CSS -->
<link rel="stylesheet" href="{{ asset('css/dataTable/jquery.dataTables.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/dataTable/dataTables.bootstrap4.min.css') }}">

<!-- DataTables JS -->
<script src="{{ asset('script/dataTable/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>

<script type="text/javascript">
$(document).ready(function() {



 $('#raw_score').DataTable({
      "bSort": false
    });



var user_ids = <?php echo json_encode($user_name); ?>;
$.each(user_ids, function (i, elem) {
    $('[value="'+elem+'"').prop('selected', true);
});
});

$( "#submit" ).click(function() {
  var users=$('.users').val();
            var survey_id="{{$survey_id}}";
            var survey_name="{{$survey_name}}";

            var url="{{ URL::route('export.rawscore_report')}}?survey_id="+survey_id+"&users="+users;
            window.location.href = url;
});


</script>

<style>
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: white;
    border: 1px solid white;
    color: white !important;
}
table.dataTable.no-footer {
   border-bottom: 2px solid #dddddd;
}
table.dataTable thead th, table.dataTable thead td {
    border-bottom: 2px solid #dddddd;
    padding: 10px 18px;
}
th{
color:white;
background:#2041BD;
}
.header{
text-align: center; border-top: 0px none ! important;
}
.competency-header{
text-align: center; border-bottom: 0px none ! important;

}
.participant{
 text-transform: capitalize;
}
.pagination > li > a:focus, .pagination > li > a:hover, .pagination > li > span:focus, .pagination > li > span:hover {

    border-color: white !important;

}
.dataTables_wrapper .dataTables_paginate .paginate_button:active {
    box-shadow: none;
    outline:  none;
}
.users{
    width: 75%;
}
.submit{
  float: right;
  margin-top: 42px;
}
a:hover,a:focus{
 color: white;
text-decoration:none;
}
a{
 color: white;
text-decoration:none;
}
</style>
@endsection
