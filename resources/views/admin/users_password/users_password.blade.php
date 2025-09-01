@extends('layouts.default')

@section('content')

<div class="container">
    <div class="panel panel-default">
	    <div class="panel-heading">
		    <div class="row text-center">
		      <h4><span ><b>Survey Name :</b> </span><strong>{{$survey_name}}</strong></h4>
		    </div>
	  	</div>
	  	<div class="panel-body">
	  		@if(count($survey_details)>0)
		      {!! Form::open(array( 'route' => ['users-password.store'],'method'=>'POST','class'=>'form-horizontal')) !!}

		      <input type="hidden" name="survey_id" value="{{$survey_id}}">
		      <input type="hidden" name="survey_name" value="{{$survey_name}}">

		      <div class="pull-right">
		    <button type="submit" name="button" class="btn btn-primary"><span class="glyphicon glyphicon-download-alt"></span> Download <span class="fa fa-file-excel-o"></span></button>
		      </div>
		      {!! Form::close() !!}
		   <br>
		    <br>
		    <br>
		    @endif
		<table class="table  table-bordered" cellspacing="0" width="100%" id="survey_status">
		<thead>
		  <tr>
		    <th>S.No</th>
		    <th>Name</th>
		    <th>Email</th>
		    <th>Respondent Type</th>
		    <th>Status</th>
		    <th>Email sent date</th>
		    <th>Password</th>

		  </tr>

		</thead>
			<tbody>
				    @if(count($survey_details)>0)
				     <?php $s_no=1; ?>
  					@foreach($survey_details as $user_details)
  					  <?php
					    if($user_details->rater=="self") $class="self";
					    else $class="no_self";
					  ?>
					  <tr class="{{$class}}">
					      <td>{{$s_no}}</td>
					      <td>{{$user_details->fname .' '. $user_details->lname}}</td>
					      <td>{{$user_details->email}}</td>
					      <td>
					      	@if($user_details->rater) {{Str::ucfirst($user_details->rater)}} @else - @endif
					      </td>
					      <td>
					        <?php
					          if($user_details->survey_status=='0') echo '<span class="alert-danger">Closed</span>';
					          elseif($user_details->survey_status=='1') echo '<span class="alert-info">Active</span>';
					          elseif($user_details->survey_status=='2') echo '<span class="alert-warning">Partly Completed</span>';
					          elseif($user_details->survey_status=='3') echo '<span class="alert-success">Completed</span>';
					          elseif($user_details->survey_status=='4') echo '<span class="alert-danger">In-Active</span>';
					        ?>
					      </td>
					        @if($user_details->notify_email_date)

					          <td>{!! HTML::image('images/calendar-icon.png')." ".date('d/m/Y',strtotime($user_details->notify_email_date))." ".HTML::image('images/time-icon.png')." ".date('g:i:A',strtotime($user_details->notify_email_date))!!} </td>
					        @else
					        <td class="text-center">-</td>
					        @endif

							<td class="last_submitted_date text-center">

								@if (isset($user_details->password))

									<?php
										try {
											$password = decrypt($user_details->password);
										} catch (Exception $e) {
											$password ="";
										}
									?>

									{{ $password }}
								@else
									-
								@endif


					  </tr>
					   <?php $s_no++; ?>
  					@endforeach
  					@endif
			</tbody>
		</table>
	  	</div>
    </div>
</div>
{!! HTML::script('script/dataTable/jquery.dataTables.min.js') !!}
 {!! HTML::style('css/dataTable/jquery.dataTables.min.css') !!}


 {!! HTML::script('js/dataTables.bootstrap4.min.js') !!}
 {!! HTML::style('css/dataTable/dataTables.bootstrap4.min.css') !!}

<script type="text/javascript">

$(document).ready(function() {



 $('#survey_status').DataTable({
      "bSort": false
    });

});

</script>
<style type="text/css">


	.self
	{
	  background-color: #eee !important;

	}

</style>
@endsection
