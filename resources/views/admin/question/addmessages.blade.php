

 <div id="question-modal" class="modal fade" role="dialog">







                            <div class="modal-dialog modal-lg">







                              <!-- Modal content-->



                              <div class="modal-content">







                      <form role="form" id="qedit-form">         



                   <div class="modal-body">





                   <?php $msg='';



                   if($display_order=="0") $msg="Welcome";

                   if($display_order=="-1") $msg="Thank you";

                   ?>

                   <div class="form-group">

                   <label class="col-sm-3">Add {{$msg}} Message <span class="qtn-required">*</span></label> 

                   <div class="col-sm-12">

                  

                   <input type="hidden" name="question_type" value="textarea">

                   <input type="hidden" name="display_order" value="{{$display_order}}">

                   <input type="hidden" name="survey_id" value="{{$survey_id}}">

                   <input type="hidden" name="question_id" value="{{$question_id}}">

                   <input type="hidden" name="_method" value="POST">

                   <input type="hidden" name="_token" value="{{csrf_token()}}">

                                  

                   {{Form::textarea('question',$question_text,['class'=>'form-control','id'=>'question-richeditor'])}}

                   </div></div>



                 

              







               



                                

                          

                                  

                    </div>







                                <div class="modal-footer">



                                <button type="button" class="btn btn-danger cancel" id="revert" data-dismiss="modal">Cancel</button>



                                <button type="submit" id="questionedit-submit-btnn" class="btn btn-success" >Save</button>



                                {{HTML::Image("images/img_load.gif","ajax_load",["class"=>"load-ajximage"])}}



                                </div>



                             {{Form::close()}}



                              </div>



                          



                            </div>

                           

</div>





<style type="text/css">

	.form-group {

    display: inline-block;

    position: relative;

    width: 100%;

}



iframe.wysihtml5-sandbox{height: 214px !important;}

.textnothide {

    display: block !important;

    height: 215px !important;

    position: absolute;

   width: 97.6% ;

    z-index: -1;

}



</style>

@if($response>0)
<style type="text/css">
#questionedit-submit-btnn{
    opacity: 0.3;
    pointer-events: none;
   }
.modal-body{pointer-events: none;}
</style>
@endif

<script type="text/javascript">


$(document).keyup(function(e) {
     if (e.keyCode == 27) { 
 $('.cancel').click(); 
    }
});

$(document).ready(function(){



  

		var fields= {    question: {

            group: '.lnbrd',

                validators: {

                  notEmpty: {

                      message: 'The Field required and cannot be empty'

                  }

                  

                }

            }

            };





	$('#qedit-form').bootstrapValidator({

            framework: 'bootstrap',

            icon: {

                valid: 'glyphicon glyphicon-ok',

                invalid: 'glyphicon glyphicon-remove',

                validating: 'glyphicon glyphicon-refresh'

            },

           	fields:fields



           })

        .on('submit', function (e) {

			      if (!e.isDefaultPrevented()){

			      	e.preventDefault();

			      	$.ajax({

					          url:"{{URL::route('questions.store')}}",

					          type:'POST',

					          dataType: 'json',

					          data:$('#qedit-form').serialize(),

					            beforeSend: function(){

					          $('.load-image').show();

					          },

					          complete: function(){

					              $('.load-image').hide();

					          },

					          success:function(data){



                       var dropsection='';

                       var dropclass='';



                      if(data.display_order=="0")

                     {

                        $('#question-type-textarea-richeditor').html('');

                        $('.field-type.intro').draggable( "disable" );
                          $('.field-type.intro').css('pointer-events','none');

                        $('.field-type.intro').css("opacity",0.3);

                         if( $('.drop-space-intro').prev('.response-area.intro').length)

                          {

                            $('.drop-space-intro').prev('.response-area.intro').remove();

                          }

                          dropsection='.drop-space-intro';

                          dropclass='response-area  intro';



                     }



                     if(data.display_order=="-1")

                     {

                        $('#question-type-textarea-richeditor').html('');

                        $('.field-type.outro').draggable( "disable" );
                        $('.field-type.outro').css('pointer-events','none');

                        $('.field-type.outro').css("opacity",0.3);

                         if( $('.drop-space-outro').prev('.response-area.outro').length)

                          {

                            $('.drop-space-outro').prev('.response-area.outro').remove();

                          }

                         dropsection='.drop-space-outro';

                         dropclass='response-area  outro';

                     }



                     if(data.display_order=="-1" || data.display_order=="0")

                     {

                        $(dropsection).before("<li rel="+data.question_id+" class='"+dropclass+"' ><div class=response-label>"+data.welcome_text+"</div><div class=response-action><a class='delete-question pull-right' href=#  title='Delete'><span class='glyphicon glyphicon-trash'></span></a></div></li>");

                       

                     

                       $(dropsection).hide();

                     }



					          

				              $('#question-modal').modal('hide');

				              $('body').removeClass('modal-open');

											$('.modal-backdrop').remove();





										



					          	

					          }



					      });

			      } 

			    

			  

        });



         $('#question-richeditor').wysihtml5({

              events: {

              load: function () {

                  $('#question-richeditor').addClass('textnothide');

            

          },

        change: function () {

           $('#qedit-form').bootstrapValidator('revalidateField', 'question');

        }

        },

                "font-styles": true, //Font styling, e.g. h1, h2, etc. Default true

                "emphasis": true, //Italics, bold, etc. Default true

                "lists": false, //(Un)ordered lists, e.g. Bullets, Numbers. Default true

                "html": false, //Button which allows you to edit the generated HTML. Default false

                "link": false, //Button to insert a link. Default true

                "image": false, //Button to insert an image. Default true,

                "color": false //Button to change color of font  

            });





        });



</script>
<style>
.help-block  {
    color: #A94448 ;
}
</style>

