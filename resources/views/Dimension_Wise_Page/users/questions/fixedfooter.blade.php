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
    <button type="button" class="btn btn-primary" id="form-save">Save</button>
    <!-- <button type="button" class="btn btn-primary" id="form-submit">Next</button> -->
    <button type="button" class="btn btn-primary hidden" id="previous">&lt; Previous</button>
    <button type="button" class="btn btn-primary" id="next">Next &gt;</button>
    <button type="button" class="btn btn-primary hidden" id="form-submit">Submit</button>


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
