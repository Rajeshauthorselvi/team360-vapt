<div id="pwdModal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
  <div class="modal-dialog">
  <div class="modal-content">
      <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
          <h3 class="text-center">Forgot Password</h3>
      </div>
      <div class="modal-body">
          <div class="col-md-12">
                <div class="panel">
                    <div class="panel-body">

                          <p>If you have forgotten your password you can reset it here.</p>

                          @if(Session::has('error'))
                            <div class="alert alert-danger fade in">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                              {{ Session::get('error')}}
                            </div>
                            @endif
                          @if(Session::has('success'))
                            <div class="alert alert-success fade in">
                                <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                              {{ Session::get('success')}}
                            </div>
                            @endif

                            <div class="panel-body">
                              {!! Form::open(['route' => 'reset_pass_index','Method'=>'POST','class'=>'form-horizontal','id'=>'reset-pass','name'=>'form']) !!}
                                    <div class="form-group">
                                        {{Form::email('resetemail',null,['class'=>'form-control input-lg','id'=>'','placeholder'=>'Email Address'])}}
                                    </div>
                              <div class="form-group">
                                <input class="btn btn-lg btn-primary btn-block" value="Send My Password" type="submit">
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
@if(Session::has('success'))
  <script type="text/javascript">
    $(window).load(function(){
          $('#pwdModal').modal('show');
      });
  </script>
@endif

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
    });
</script>
<style type="text/css">
@media(max-width: 400px){
    .modal-body > div{
    padding: 0;
  }
}
.modal-footer{
  border-top: none;
}

</style>