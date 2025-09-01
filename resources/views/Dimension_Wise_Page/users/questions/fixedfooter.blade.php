<div class="" id="fixed-footer">
<div class="progress-section">

	<div class="text-info">
	<small>
		Page
		<span class="current-page">1</span>
		of
		<span class="total-page"></span> 
	</small>
	</div>
</div>
@if(count($questions)>0)
<div class="action-panel">
<div class="actions pull-right">
{{Form::button('Save',['class'=>'btn btn-primary ','id'=>'form-save'])}} 
{{-- {{Form::button('Next',['class'=>'btn btn-primary ','id'=>'form-submit'])}} --}}
{{Form::button('< Previous',['class'=>'btn btn-primary hidden','id'=>'previous'])}}
{{Form::button('Next >',['class'=>'btn btn-primary ','id'=>'next'])}}
{{Form::button('Submit',['class'=>'btn btn-primary hidden','id'=>'form-submit'])}}

</div>
</div>
@endif
</div>

<style type="text/css">
.actions .btn {
	float: left;
	margin-right: 5px;
}
</style>