<footer>

<div class="container">

<div class="row">

<div class="col-md-12 ">

	<?php $footer_text=Auth::user()->getsurvey_Details()->footer_text; ?>
<!--<h5 class="text-center"><strong>&copy; {{date('Y')}} Ascendus. All Rights Reserved.</strong></h5> -->

	
	@if(isset($footer_text))
	{!! $footer_text!!}
	@endif
		
	

</div>

</div>

</div>



</footer>
