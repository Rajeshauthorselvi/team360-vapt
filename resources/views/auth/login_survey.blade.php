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
</head>
<body>

<div class="container">

    <div id="loginbox" class="mainbox col-md-6 col-md-offset-3 col-sm-6 col-sm-offset-3">

        <div class="row">

        </div>

        <div class="panel panel-default" >
            <div class="panel-heading">
                <div class="panel-title text-center">Login Form</div>
            </div>

            <div class="panel-body" >


                {!! Form::open(['route' => 'login-survey','Method'=>'POST','class'=>'form-horizontal','id'=>'form','name'=>'form']) !!}
                  @if ($errors->any())
 <div class='alert alert-danger'>
  @foreach ( $errors->all() as $error )
   <p>{{ $error }}</p>
  @endforeach
 </div>
@endif
                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-user"></i></span>

                        {{Form::email('email',null,['class'=>'form-control','id'=>'user-email','placeholder'=>'Email Address'])}}
                    </div>

                    <div class="input-group">
                        <span class="input-group-addon"><i class="glyphicon glyphicon-lock"></i></span>

                         {{Form::password('password',['class'=>'form-control','id'=>'password','placeholder'=>'password'])}}
                    </div>

                    <div class="form-group">
                        <!-- Button -->
                        <div class="col-sm-12 controls">
                            <button type="submit" href="#" class="btn btn-primary pull-right"><i class="glyphicon glyphicon-log-in"></i> Log in</button>
                        </div>
                    </div>

                {!! Form::close() !!}

            </div>
        </div>
    </div>
</div>



</body>
</html>
