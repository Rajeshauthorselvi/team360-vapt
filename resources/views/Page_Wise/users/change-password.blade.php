@extends('Page_Wise.users.layouts.default')


@section('content')

<div class="site-content">
<div class="container bootstrap snippet">
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-6 col-md-offset-3">
            <form method="POST" action="{{ route('change-password', config('site.survey_slug')) }}" class="form-horizontal" id="change-password">
                @csrf
                <input type="hidden" name="_method" value="PATCH">
            <div class="panel panel-info  login-box">
                <div class="panel-heading">
                    <h3 class="panel-title">
                        Change Password
                    </h3>
                </div>
                <div class="panel-body">
                    <div class="col-xs-12 col-sm-12 col-md-12">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="password" name="password" class="form-control" id="password" placeholder="New password" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group">
                                <input type="password" name="confirm_password" class="form-control" id="password" placeholder="Confirm Password" autocomplete="off">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="input-group text-center">
                                <?php $back_url=URL::route('user.dashboard',config('site.survey_slug')); ?>
      <a href={{$back_url}} class="btn btn-danger ">BACK</a>
      &nbsp;&nbsp;
                                <button class="btn icon-btn-save btn-submit" type="submit">
                                <span class="btn-save-label"></span>UPDATE</button>
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
<script type="text/javascript">
	  $(document).ready(function(){
    $('#change-password')
        .bootstrapValidator({
            framework: 'bootstrap',
            icon: {
                valid: 'glyphicon glyphicon-ok',
                invalid: 'glyphicon glyphicon-remove',
                validating: 'glyphicon glyphicon-refresh'
            },
            fields: {
             confirm_password: {
                    validators: {
                        notEmpty: {
                            message: 'The Field required and cannot be empty'
                        },
		identical: {
                    field: 'password',
                    message: 'The password and its confirm are not the same'
                }
                    }
                },
                password: {
                    validators: {
                        notEmpty: {
                            message: 'The Field required and cannot be empty'
                        }

                    }
                },


            }
        })

  });
</script>
<style type="text/css">
    footer{position: fixed;}
</style>
@endsection

