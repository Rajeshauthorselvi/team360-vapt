<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Login Form</title>

    <!-- CSS Files -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('css/bootstrapValidator.min.css') }}">
    <link rel="stylesheet" href="{{ asset('css/font-awesome/css/font-awesome.min.css') }}">

    <!-- JS Files -->
    <script src="{{ asset('script/jquery.js') }}"></script>
    <script src="{{ asset('script/bootstrap.min.js') }}"></script>
    <script src="{{ asset('script/bootstrapValidator.min.js') }}"></script>

    <?php
    $file_name = $themes;
    $left_logo = DB::table('surverys')->where('id', $survey_id)->value('logo');
    $right_logo = DB::table('surverys')->where('id', $survey_id)->value('right_logo');
    $header_text = DB::table('surverys')->where('id', $survey_id)->value('header_text');
    $footer_text = DB::table('surverys')->where('id', $survey_id)->value('footer_text');
    ?>
</head>

<body>
    @if($survey_id)
    <header>
        <div class="navbar navbar-default navbar-fixed-top">
            <div class="col-sm-3 logo">{{ HTML::image('storage/surveys/'.$left_logo, '') }}</div>
            <div class="col-sm-6 header_text">{!! $header_text !!}</div>
            <div class="col-sm-3 logo right_logo">{{ HTML::image('storage/surveys/'.$right_logo, '') }}</div>
        </div>
    </header>
    @endif

    <div class="container">
        <div class="login_box">
            <?php $class = 'col-md-offset-3'; ?>
            <div id="loginbox" class="mainbox col-md-6 {{ $class }}">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        <div class="panel-title">LOGIN FORM</div>
                    </div>

                    <div class="panel-body login-form">
                        @if(Session::has('success'))
                        <div class="alert alert-success fade in">{{ Session::get('success') }}</div>
                        @endif

                        <form action="{{ route('login') }}" method="POST" class="form-horizontal" id="form">
                            @csrf

                            @if ($errors->any())
                            <div class="alert alert-danger">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                    <span aria-hidden="true">&times;</span>
                                </button>
                                @foreach ($errors->all() as $error)
                                <p>{{ $error }}</p>
                                @endforeach
                            </div>
                            @endif

                            <div class="form-group">
                                <input type="email" name="email" class="form-control" id="user-email"
                                    placeholder="Email Address">
                            </div>

                            <div class="form-group">
                                <input type="password" name="password" class="form-control" id="password"
                                    placeholder="Password">
                            </div>

                            @if (env('APP_ENV') == "production")
                            <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                            @endif

                            <div class="form-group">
                                <button type="submit" class="btn btn-submit">LOGIN</button>
                                <a href="#" class="pull-right forgot" data-target="#pwdModal" data-toggle="modal">
                                    Forgot my password?
                                </a>
                            </div>

                            <input type="hidden" name="survey_url" value="{{ Request::segment(1) }}">
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if(isset($footer_text) && !empty($footer_text))
    <footer class="footer">
        <span class="footer_text">{!! $footer_text !!}</span>
    </footer>
    @endif

    <!-- Password Reset Modal -->
    <div id="pwdModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal">×</button>
                    <h1 class="text-center">What's My Password?</h1>
                </div>
                <div class="modal-body">
                    <p>If you have forgotten your password you can reset it here.</p>

                    @if ($errors->any())
                    <div class="alert alert-danger">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        @foreach ($errors->all() as $error)
                        <p>{{ $error }}</p>
                        @endforeach
                    </div>
                    @endif

                    @if(Session::has('error'))
                    <div class="alert alert-danger fade in">
                        <button type="button" class="close" data-dismiss="alert">×</button>
                        {{ Session::get('error') }}
                    </div>
                    @endif

                    <form action="{{ route('reset_pass_index') }}" method="POST" class="form-horizontal" id="reset-pass">
                        @csrf
                    <div class="form-group">
                        <input type="text" name="resetemail" class="form-control input-lg" placeholder="Email Address">
                    </div>

                    @if (env('APP_ENV') == "production")
                    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                    @endif

                    <div class="form-group mt-2">
                        <input class="btn btn-lg btn-primary btn-block" type="submit" value="Send My Password">
                    </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- JS Scripts -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
    <script>
        $(document).ready(function () {
            // Auto-hide alerts
            setTimeout(() => $(".alert").fadeTo(500, 0).slideUp(500, () => $(this).remove()), 4000);

            // Show reset modal if error or session
            @if(Session::has('error') || (Session::has('from') && Session::get('from')=="reset"))
            $('#pwdModal').modal('show');
            @endif

            // Form validation
            $('#reset-pass').bootstrapValidator({
                fields: {
                    resetemail: {
                        validators: {
                            notEmpty: { message: 'The field is required' },
                            emailAddress: { message: 'Invalid email address' }
                        }
                    }
                }
            });

            $('#form').bootstrapValidator({
                fields: {
                    email: {
                        validators: {
                            notEmpty: { message: 'The field is required' },
                            emailAddress: { message: 'Invalid email address' }
                        }
                    },
                    password: {
                        validators: {
                            notEmpty: { message: 'The field is required' }
                        }
                    }
                }
            });
        });
    </script>

    <style>
        .login-form { padding: 30px 30px 0; }
        .logo > img { height: 100px; }
        .navbar-fixed-top, .navbar-fixed-bottom { position: relative !important; }
        #pwdModal .panel-body { padding-top: 10px; }
        .modal-body { padding: 0; }
    </style>
</body>

</html>
