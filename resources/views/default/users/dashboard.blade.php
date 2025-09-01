@extends('default.users.layouts.default')
@section('content')

<div class="site-content">
<div class="container">
	<div class="row">
		<div class="col-md-12">
			@if (isset($user_survey_info) && isset($user_survey_info[0]->survey_id) && $user_survey_info[0]->survey_id==4)
				<div class="welcome">
					<p>
						<p>Welcome to the Voice of Internal Customer Survey</p>
						<p>Let’s contribute to the success of this intervention by giving honest and transparent inputs/feedback. Please note all individual responses will remain anonymous. Please be reassured that the data will be used solely for developmental purposes only.</p>
					</p>
					<p>&nbsp;</p>
				</div>
			@endif
				<table class="survey-table text-center" >
			 	<thead>
			 	<tr>
					<th class="text-center">S.No</th>
					<th class="text-left">Survey Name</th>

					<th class="text-center">Status</th>
					@if(isset($user_survey_info[0]->survey_id) && ($user_survey_info[0]->survey_id==44 || $user_survey_info[0]->survey_id==45) && $participant_manager_rater==0)
					<th class="text-center">Respondents</th>
					@endif
					<th class="text-center">Actions</th>
				</tr>
				</thead>
				<tbody>
			@if(count($user_survey_info) > 0 )
				@foreach($user_survey_info as $key => $usinfo)
				<tr>
				<td class="sno hidden-xs">{{$key +1}}</td>
				<td data-label="Survey Title" class="<?php echo ($key==0) ? $key : '' ;?> text-left">
					<span class="allocate_table_content"> <b>{{$usinfo->title}}</b>
						<span  style="text-transform: uppercase;" >
								@if(strtolower($usinfo->rater)=="self")
									@if(config('site.survey_slug')=='bvcboard')
										(BOARD EVALUATION)
									@else
										(Self-assessment)
									@endif
								@else
									@if(config('site.survey_slug')=='skechers')
										(Evaluating {{ucfirst($usinfo->fname) .' '.ucfirst($usinfo->lname)}})

									@elseif(config('site.survey_slug')=='bvcboard')
										(FEEDBACK ON {{ucfirst($usinfo->fname) .' '.ucfirst($usinfo->lname)}})

									@else
										(As a <span style="text-transform:uppercase">{{ $usinfo->rater }}</span> evaluating {{ucfirst($usinfo->fname) .' '.ucfirst($usinfo->lname)}})

									@endif
								@endif
						</span>
					</span>
				</td>

				<td class="text-center txt-captialize " data-label="Survey Status">
					<span class="allocate_table_content">
					<?php
						  if($usinfo->survey_status=='0') echo '<span >Closed</span>';
						  elseif($usinfo->survey_status=='1') echo '<span >Active</span>';
						  elseif($usinfo->survey_status=='2') echo '<span >Partly Completed</span>';
						  elseif($usinfo->survey_status=='3') echo '<span >Completed</span>';
						  elseif($usinfo->survey_status=='4') echo '<span >In-Active</span>';
					?>
					</span>
				</td>
				@if(isset($user_survey_info[0]->survey_id) && ($user_survey_info[0]->survey_id==44 || $user_survey_info[0]->survey_id==45) && $participant_manager_rater==0)
				<td class="text-center " data-label="Respondents">
						<span class="allocate_table_content">
					@if($usinfo->participant_rater_manage==1 AND $usinfo->respondent_id==0)
<!-- 							{!! html_entity_decode(link_to_route('manage-respondent.index', HTML::image('images/manage-icon.png') .' Manage',[config('site.survey_slug')],'class=manage')) !!}	 -->

							<a href="{{route('manage-respondent.index',[config('site.survey_slug')])}}" class="manage btn-link "><i class="fa fa-cog" aria-hidden="true"></i> Manage</a>

							@if($survey_exists)
<!-- 							{!! html_entity_decode(link_to_route('manage-email.index', HTML::image('images/mail-icon.png') .' Send Email',[config('site.survey_slug')])) !!} -->
							<a href="{{route('manage-email.index',[config('site.survey_slug')])}}">
								<i class="fa fa-envelope" aria-hidden="true"></i> Send Email
							</a>
							@endif

					@else
						-
					@endif
					</span>
				</td>
				@endif
				 
				<?php

				$user_exam_info=array(config('site.survey_slug'),'rater='.$usinfo->rater_id,'participant='.$usinfo->participant_id);

				?>

				@if($usinfo->survey_status==1)
				<td class="text-center " data-label="Action">
					<span class="allocate_table_content">
						<a href="{{URL::route('user.index',$user_exam_info)}}" class="btn btn-submit">Start</a>
					</span>
				</td>
				@elseif($usinfo->survey_status==2)
				<td class="text-center " data-label="Action">
					<span class="allocate_table_content">
						<a href="{{URL::route('user.index',$user_exam_info)}}" class="btn btn-submit">Continue</a>
					</span>
				</td>
				@else
				<td class="text-center"  data-label="Action"><span class="allocate_table_content">-</span></td>
				@endif
				<td style="border:0px solid transparent;" class="visible-xs"></td>
				</tr>
				@endforeach
			@else
			<tr><td colspan="6" class="text-center table_content">No Survey found!</td></tr>
			@endif
			</tbody>
			</table>
		</div>
	</div>
</div>
</div>

<style type="text/css">
	footer{position: relative;}

   .inner-header .container {
	  margin-top: 30px;
	}

</style>
<script type="text/javascript">
	if ($(window).width() < 514) {
        $('html').addClass('mobile');
    }
    else {
        if (!init) {
            $('html').removeClass('mobile');
        }
    }
</script>
<style type="text/css">
	@media(min-width: 768px) and (max-width: 800px){
		.visible-xs{
			display: block !important;
		}
	}
</style>
@endsection

