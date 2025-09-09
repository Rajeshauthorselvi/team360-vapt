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
    <button type="button" class="btn btn-primary previousPage" id="previous" style="display:none">< Previous</button>
    <button type="button" class="btn btn-primary nextPage" id="next">Next ></button>
    <input type="submit" class="btn btn-primary submitBtn hidden" id="form-submit" value="Submit">

</div>
</div>
@endif
</div>
