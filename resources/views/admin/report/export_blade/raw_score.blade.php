

<table>
	<thead>
				<tr>
			<th  class="competency-header"></th>
			<th  class="competency-header"></th>
			<th  class="competency-header"></th>
			<th  class="competency-header"></th>
			<?php $alpha=range('a', 'z'); ?>
			@if(count($raw_score_question_dimension)>0)
				@foreach($raw_score_question_dimension as $key=>$value)
					<?php $count=explode('|',$key);
					$question_count=count($count); ?>
					<th colspan={{$question_count}} style="text-align: center;" >{{ ucfirst($value) }}</th>
				@endforeach
			@endif


			</tr>
			<tr>
			<th  class="competency-header">S.no</th>
			<th  class="competency-header" style="width: 200px">Name</th>
			<th  class="competency-header" style="width: 200px">Email</th>
			<th  class="competency-header">Rater type</th>
			@if($raw_score_responses!='')
				@if(count($raw_score_question_id)>0)


<?php $s_no=1; ?>

				@foreach($raw_score_question_id as $key=>$order_value)
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
	@if(count($raw_score_responses)>0 && ($raw_score_responses!=''))
				@foreach($raw_score_responses as $user_survey_id=>$user_info)
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
										-
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
</tbody>

	</table>


    <?php //exit; ?>
