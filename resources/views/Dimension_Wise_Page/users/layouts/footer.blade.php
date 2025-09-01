<footer>

<div class="container">

<div class="row">

<div class="col-md-12 ">
<?php $footer_text=config('site.footer_text'); ?>

@if(isset($footer_text))
{!! $footer_text !!}
@else
<p class="text-center">&copy; {{date('Y')}} Ascendus. All Rights Reserved.</p>

@endif
	
		
	

</div>

</div>

</div>



</footer>
