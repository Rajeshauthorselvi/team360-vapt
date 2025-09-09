@extends('layouts.default')

@section('content')
<form action="{{ route('theme.store') }}" method="POST" role="form" class="form-horizontal container">
    @csrf
<div class="row setup-content" id="step-4">

    <div class="col-xs-12">
        <div class="form-wrapper">
            <div class="form-steps-wizard step4"> </div>


            <div class="col-md-12 well">
                <h3 class="need-margin-bottom-forstrip text-center">Choose Your Theme</h3>


                <!-- <form> -->

                @if ($errors->any())
                <div class="alert alert-danger fade in">

                    <a href="#" class="close" data-dismiss="alert">&times;</a>

                    <strong>Error!</strong> A problem has been occurred while submitting form.<br>
                    <ul>
                        {!! implode('', $errors->all('<li class="text-danger">:message</li>')) !!}

                    </ul>
                </div>
                @endif


                <div class="themes-all">

                    @if(!empty($themes))
                    @foreach($themes as $theme)
                    <div class=" col-sm-4">
                        <div class="themes">
                            <div class="theme-content">
                                {{$theme->title}}<br>
                                <img src="{{ asset($theme->image) }}" alt="Theme Image" class="img-responsive"><br>

                            </div>
                            <?php $default=null;
                                if($theme->id==$default_theme) $default=true;
                            ?>
                            <input type="radio" name="theme" value="{{ $theme->id }}" class="theme-select" {{ $default ? 'checked' : '' }}>

                            @if ($theme->id==2)
                            <div class="page_val">
                                <input type="text" name="question_per_page" value="{{ $per_page }}" class="form-control">
                                <span style="font-size:x-small;">* works best with single choice questions.</span>
                            </div>
                            @endif
                        </div>
                        <br>
                        <center>
                            <?php
                                $class=['class'=>'btn btn-primary'];
                                $tab_new=["target"=>"_blank"];
                                ?>


                            <div class="dropdown">
                                <button class="btn btn-primary dropdown-toggle" type="button"
                                    data-toggle="dropdown"><span class="fa fa-eye"></span> Preview
                                    <span class="caret"></span></button>
                                    <ul class="dropdown-menu">
                                        <li><a href="{{ route('prev_login', ['theme_id' => $theme->id, 'survey_id' => $survey_id]) }}" target="_blank">Login Preview</a></li>
                                        <li><a href="{{ route('prev_question', ['theme_id' => $theme->id, 'survey_id' => $survey_id]) }}" target="_blank">Question Preview</a></li>
                                    </ul>

                            </div>
                        </center>
                    </div>
                    @endforeach
                    @else
                    <div class="col-sm-4">
                        <div class="themes">
                        </div>
                    </div>
                    @endif
                    <div class="col-sm-6 col-sm-offset-3" style="margin-top: 25px;">
                        <center>
                            <input type="hidden" name="survey_id" value="{{ $survey_id }}">
                            <a href="{{URL::route('questions_group','survey_id='.$survey_id)}}"
                                class="btn btn-danger btn-md">Cancel</a>

                            <button id="activate-step-4" type="submit" class="btn btn-success btn-md">Save &
                                Next</button>
                        </center>
                    </div>


                </div>
            </div>
        </div>
    </div>
</div>
</form>


<div class="container">
    <div class="row">
        <div class="col-md-12">


            <script type="text/javascript">
                $(document).ready(function(){

		function calldefaultheme(){

		$('.themes').each(function(){
			if($(this).find('input[name=theme]').is(':checked'))
			{
				$(this).addClass('active');
			}
			else
			{
				$(this).removeClass('active');
			}
		});
	}
	calldefaultheme();
	$('input[type=radio][name=theme]').click(function(){calldefaultheme();});
	});


  $(document).on('click', '.theme-select', function(event) {

  });
            </script>


        </div>
    </div>
</div>

@endsection
