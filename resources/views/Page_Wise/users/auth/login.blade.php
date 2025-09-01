@extends('Page_Wise.users.layouts.default')

@section('content')


<div class="site-content">
<div class="container bootstrap snippet">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3">
           {!! Form::open(['route' =>['user_login',config('site.survey_slug')],'Method'=>'POST','class'=>'form-horizontal','id'=>'user-login','name'=>'userform']) !!}
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
                           <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        @foreach ( $errors->all() as $error )
                         <p>{{ $error }}</p>
                        @endforeach
                       </div>
                      @endif
                      
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::email('email',null,['class'=>'form-control','id'=>'email','placeholder'=>'Email','autocomplete'=>'off']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                {!! Form::password('password',['class'=>'form-control','id'=>'password','placeholder'=>'Password','autocomplete'=>'off']) !!}
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                
                                <button class="btn icon-btn-save btn-submit" type="submit">
                                <span class="btn-save-label"></span>Login</button>
                                <a class="pull-right" href="#" data-target="#pwdModal" data-toggle="modal">Forgot my password ?</a>
                            </div>

                        </div>
                    </div>
                </div>
            </div>
            {!! Form::close() !!}
        </div>
    </div>
</div>
</div>
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
<style type="text/css">
    footer{
        position: fixed;
    }
</style>
@include('Page_Wise.users.auth.forgotpwd')
@endsection

