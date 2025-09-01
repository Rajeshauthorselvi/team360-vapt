<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Form</title>
    {!! HTML::script('script/jquery.js') !!}
    {!! HTML::script('script/bootstrap.min.js') !!}
    {!! HTML::style('css/bootstrap.min.css') !!}
    {!! HTML::style('css/login.css') !!}
    {!! HTML::script('script/bootstrapValidator.min.js') !!}
    {!! HTML::style('css/bootstrapValidator.min.css') !!}
    {!! HTML::style('css/font-awesome/css/font-awesome.min.css') !!}



    <?php
   $file_name=$themes;

$left_logo=DB::table('surverys')->where('id',$survey_id)->value('logo');
$right_logo=DB::table('surverys')->where('id',$survey_id)->value('right_logo');
$header_text=DB::table('surverys')->where('id',$survey_id)->value('header_text');
$footer_text=DB::table('surverys')->where('id',$survey_id)->value('footer_text');
?>
</head>
<?php  if($survey_id!=null){

?>
<header>
    <div class="navbar navbar-default navbar-fixed-top">

        <div class="col-sm-3 logo"> {{ HTML::image('storage/surveys/'.$left_logo,'') }}</div>
        <div class="col-sm-6 header_text">{!! $header_text !!}</div>
        <div class="col-sm-3 logo  right_logo"> {{ HTML::image('storage/surveys/'.$right_logo,'') }}</div>



    </div>
</header>
<?php } ?>

<body>


    <div class="container">
        <div class="login_box">

            <?php $class='col-md-offset-3'; ?>
            @if(isset($themes))


            <?php $class='col-md-offset-3' ?>
            @if ($file_name=="survey_2.css")
            <!--   <div class="banner-position col-sm-6">
            <img class="img-responsive" src="{{url('/')}}/storage/surveys/network2.jpg" alt="" width="420" height="291">
          </div>-->
            @elseif($file_name=="survey_3.css")

            <!--     <div class="banner-position col-sm-6 hidden-xs hidden-sm">
              <h1> Welcome To The <b>AS</b> Survey</h1>
            <img src="{{url('/')}}/storage/surveys/feedback360.png" alt="Feedback Survey" width="420" height="291">
          </div> -->
            @endif
            <!--   <?php
          if ($file_name=="survey_1.css") $class='col-md-offset-3 ';
          elseif ($file_name=="survey_2.css" || $file_name=="survey_3.css") $class='col-xs-12';

        ?>-->

            @endif
            <div id="loginbox" class="mainbox col-md-6   col-md-offset-3">

                <div class="panel panel-info">
                    <div class="panel-heading">
                        <div class="panel-title ">

                            LOGIN FORM
                        </div>
                    </div>

                    <div class="panel-body login-form">


                        @if(Session::has('success'))
                        <div class="alert alert-success fade in">
                            {{ Session::get('success')}}
                        </div>
                        @endif
                        {!! Form::open(['route' =>'login','Method'=>'POST','class'=>'form-horizontal','id'=>'form','name'=>'form']) !!}
                        @if ($errors->any())
                        <div class='alert alert-danger'>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span></button>
                            @foreach ( $errors->all() as $error )
                                <p>{{ $error }}</p>
                            @endforeach
                        </div>
                        @endif
                        <div class="form-group">
                            <!--  <label for="email">Email Address:</label> -->
                            <div class="input-group col-sm-12">
                                <!--   <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>-->
                                {{Form::email('email',null,['class'=>'form-control','id'=>'user-email','placeholder'=>'Email Address'])}}
                            </div>
                        </div>
                        <div class="form-group">
                            <!--  <label for="password">Password: </label>-->
                            <div class="input-group col-sm-12">
                                <!--    <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>-->

                                {{Form::password('password',['class'=>'form-control','id'=>'password','placeholder'=>'password'])}}
                            </div>
                        </div>
                        @if (env('APP_ENV')=="production")
                            <!-- Google reCAPTCHA -->
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                        @endif
                        <div class="form-group">
                            <!-- Button -->
                            <button type="submit" href="#" class="btn btn-submit  ">
                                <!--<i class="btn icon-btn-save btn-submit "></i>-->LOGIN
                            </button>
                            <a href="#" class="pull-right forgot" data-target="#pwdModal" data-toggle="modal">Forgot my password ?</a>
                        </div>


                    </div>
                    {{Form::hidden('survey_url',Request::segment(1))}}
                    {!! Form::close() !!}

                </div>
            </div>
        </div>
    </div>
    @if(isset($footer_text) && !empty($footer_text))
    <footer class="footer">
        <span class=" footer_text">{!! $footer_text !!}</span>
    </footer>
    </footer>
    @endif

    <div id="pwdModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
                    <h1 class="text-center">What's My Password?</h1>
                </div>
                <div class="modal-body">
                    <div class="col-md-12">
                        <div class="panel">
                            <div class="panel-body">

                                <p>If you have forgotten your password you can reset it here.</p>

                                @if(Session::has('error'))
                                <div class="alert alert-danger fade in">
                                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                            aria-hidden="true">&times;</span></button>
                                    {{ Session::get('error')}}
                                </div>
                                @endif

                                <div class="panel-body">
                                    {!! Form::open(['route' =>
                                    'reset_pass_index','Method'=>'POST','class'=>'form-horizontal','id'=>'reset-pass','name'=>'form'])
                                    !!}
                                    <div class="form-group">
                                        {{Form::text('resetemail',null,['class'=>'form-control
                                        input-lg','id'=>'','placeholder'=>'Email Address'])}}
                                    </div>


                                    <div class="form-group">
                                        <input class="btn btn-lg btn-primary btn-block" value="Send My Password"
                                            type="submit">
                                    </div>
                                    {!! Form::close() !!}
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">

                </div>
            </div>
        </div>
    </div>
    @if(Session::has('error'))
    <script type="text/javascript">
        $(window).load(function(){
          $('#pwdModal').modal('show');
      });
    </script>
    @endif

    <!-- Add reCAPTCHA script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>

    <script type="text/javascript">
        $(document).ready(function(){


  window.setTimeout(function() {
      $(".alert").fadeTo(500, 0).slideUp(500, function(){
          $(this).remove();
      });
  }, 4000);

     $('#reset-pass')
      .bootstrapValidator({
          framework: 'bootstrap',
          icon: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
          fields: {
            resetemail:{
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  },
                  emailAddress: {
                      message: 'The value is not a valid email address'
                  }
                }
            }

          }
});

$('#form')
 .bootstrapValidator({
     framework: 'bootstrap',
     icon: {
         valid: 'glyphicon glyphicon-ok',
         invalid: 'glyphicon glyphicon-remove',
         validating: 'glyphicon glyphicon-refresh'
     },
     fields: {
       email:{
           validators: {
             notEmpty: {
                 message: 'The Field required and cannot be empty'
             },
             emailAddress: {
                 message: 'The value is not a valid email address'
             }
           }
       },
       password:{
           validators: {
             notEmpty: {
                 message: 'The Field required and cannot be empty'
             }
           }
       }

     }
});
 });


    </script>
    <style media="screen">
        #pwdModal .panel-body {
            padding-top: 10px;
        }

        .modal-body {
            padding: 0;
        }

        .login-form {
            padding: 30px 30px 0;
        }

        .logo>img {
            height: 100px;

        }

        .navbar-fixed-bottom,
        .navbar-fixed-top {

            position: relative !important;
        }
    </style>
</body>

</html>
