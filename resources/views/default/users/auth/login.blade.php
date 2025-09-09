@extends('default.users.layouts.default')

@section('content')


<div class="site-content">
    <div class="container bootstrap snippet">
        <div class="row">
            <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3">
                <form method="POST" action="{{ route('user_login', config('site.survey_slug')) }}" class="form-horizontal" id="user-login" name="userform">
                @csrf
                <div class="panel panel-info  login-box">
                    <div class="panel-heading">
                        <h3 class="panel-title">
                            Login
                        </h3>
                    </div>
                    <div class="panel-body">
                        <div class="col-xs-12 col-sm-12 col-md-12">
                            @if ($errors->any())
                            <div class='alert alert-danger'>
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                        aria-hidden="true">&times;</span></button>
                                @foreach ( $errors->all() as $error )
                                <p>{{ $error }}</p>
                                @endforeach
                            </div>
                            @endif

                            <div class="form-group">
                                <div class="input-group">
                                    <input type="email" name="email" id="email" class="form-control" placeholder="Email" autocomplete="off">

                                </div>
                            </div>
                            <div class="form-group">
                                <div class="input-group">
                                    <input type="password" name="password" id="password" class="form-control" placeholder="Password" autocomplete="off">

                                </div>
                            </div>
                            @if (env('APP_ENV')=="production")
                            <!-- Google reCAPTCHA -->
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                            <br>
                            @endif
                            <div class="form-group">
                                <div class="input-group">

                                    <button class="btn icon-btn-save btn-submit" type="submit">
                                        <span class="btn-save-label"></span>Login</button>
                                    <a class="pull-right" href="#" data-target="#pwdModal" data-toggle="modal">Forgot my
                                        password ?</a>
                                </div>

                            </div>
                        </div>
                    </div>
                </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Add reCAPTCHA script -->
<script src="https://www.google.com/recaptcha/api.js" async defer></script>

<script type="text/javascript">
    $(document).ready(function(){
    $('#user-login').bootstrapValidator({
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
@if (Session::has('from') && Session::get('from')=="reset")
<script type="text/javascript">
    $(window).load(function(){
        $('#pwdModal').modal('show');
    });
</script>
@endif
<style type="text/css">
    footer {
        position: fixed;
    }

    .inner-header .container {
        margin-top: 30px;
    }
</style>
@include('default.users.auth.forgotpwd')
@endsection
