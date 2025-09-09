<div class="" id="fixed-footer">
<div class="progress-section">
	<?php
	$percentage=$totalquestions=0;
	if(count($questions)>0)
	{
	  $totalquestions=count($questions);
	  $percentage=floor(($rcount/$totalquestions)*100);
	}
	?>

	<div class="text-info">
	<small><span data-count={{$rcount}} id="answered">{{$rcount}}</span> of <span id="total-questions">{{$totalquestions}}</span> answered</small>
	</div>

	<div class="progress">
	  <div class="progress-bar" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width:{{$percentage}}%"></div>
	</div>
</div>
@if(count($questions)>0)
<div class="action-panel">
<div class="actions pull-right">
<button type="button" class="btn btn-primary" id="form-save">Save</button>
<button type="button" class="btn btn-primary" id="form-submit">Submit</button>
<button class="btn btn-primary " id="prev-btn" value="1"><span class="glyphicon glyphicon-chevron-up" ></span></button>
<button class="btn btn-primary " id="next-btn" value="2"><span class="glyphicon glyphicon-chevron-down"></span></button>
</div>
</div>
@endif
</div>
