@extends('layouts.default')
@section('content')

<div class="container">
    <div class="row setup-content" id="step-6">

        <div class="col-xs-12">
            <div class="form-wrapper">
                <div class="form-steps-wizard step6"> </div>


                <div class="col-md-12 well">
                    <h3 class="need-margin-bottom-forstrip text-center">Send Email</h3>


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

                    <ul class="nav nav-tabs">
                        <li class="active"><a data-toggle="tab" href="#notification">Notification</a></li>
                        <li><a data-toggle="tab" href="#remainder">Reminder</a></li>
                    </ul>

                    <div class="tab-content">
                        <?php
                            $if_manage=DB::table('surverys')->where('id',$survey_id)->value('participant_rater_manage');
                            $if_admin=DB::table('surverys')->where('id',$survey_id)->value('admin_survey_flag');
                        ?>

                        <div id="notification" class="tab-pane fade in active">
                            <input type="hidden" value="participant" name="participant">
                            <input type="radio" name="users" value="notification-participant"
                                id="pa-participant" /><label for="pa-participant">Participant</label>
                            @if(($if_manage!='1') || ($if_admin=='1'))
                            <input type="radio" name="users" value="notification-respondent" id="pa-respondent" /><label
                                for="pa-respondent">Respondent</label>
                            @endif
                        </div>
                        <div id="remainder" class="tab-pane fade in">
                            <input type="hidden" value="remainder" name="participant">
                            <input type="radio" name="re-user" value="reminder-participant" id="re-participant" />
                            <label for="re-participant">Participant</label>
                            @if(($if_manage!='1') || ($if_admin=='1'))
                            <input type="radio" name="re-user" value="reminder-respondent" id="re-respondent" /><label
                                for="re-respondent">Respondent</label>
                            @endif
                        </div>

                    </div>

                    <br />
                    <div id="add-notification-participant" class="desc">

                        <form method="POST" action="{{ route('distribute.store') }}" id="distribute-participants" class="form-horizontal" enctype="multipart/form-data">
                            @csrf

                        <input type="hidden" name="send_email" value="notification-participant">


                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="text" name="sender_name" value="{{ $from_name }}" class="form-control" placeholder="From Name" oninput="sanitizeInput(this)">
                            </div>
                        </div>




                        <?php $send_email_from=(isset($from_email)) ? $from_email : null; ?>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="email" name="from_email_participant" value="{{ $send_email_from }}" class="form-control" placeholder="From Email">
                            </div>
                        </div>



                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="email" name="cc_participant" value="" class="form-control" placeholder="CC">

                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="text" name="copy_email_participant" value="" class="form-control" placeholder="BCC">

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="text" name="replay_to" value="" class="form-control" placeholder="Replay To">

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="participant-wraper">
                                    <?php $count=1;$i=1;?>

                                    @foreach($participants as $participant)
                                    @if($count==1)
                                    <div class="split-12">
                                        <label><input type="checkbox" checked="checked" id="select_all_case1" /> Select
                                            all</label>
                                    </div>
                                    @endif
                                    <div class="split-4">
                                        <input type="checkbox" name="bcc_participant[]" value="{{ $participant->email }}" id="chk{{ $count }}" class="case1" checked>

                                        <label for="{{'chk'.$count}}">{{$participant->fname .' '.$participant->lname}}({{$participant->email}})</label>
                                    </div>
                                    @if($i==250)


                                    <div class="col-md-9 ">
                                        <hr>
                                    </div>
                                    <?php $i=0; ?>
                                    @endif
                                    <?php $count++;$i++?>
                                    @endforeach

                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <?php
                                    if (isset($email_templates['notification-participant']['subject'])) {
                                        $subject=$email_templates['notification-participant']['subject'];
                                    }
                                    else{
                                        $subject=null;
                                    }

                                ?>
                                <input type="text" name="subject_participant" value="{{ $subject }}" class="form-control" placeholder="Subject" id="subject">

                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="lnbrd">
                                    <?php
                                        if (isset($email_templates['notification-participant']['content'])) {
                                        $template=$email_templates['notification-participant']['content'];
                                        }
                                        else{
                                        $template='[fname][lname][Surveys list][Login Details]';
                                        }

                                    ?>
                                    <textarea name="message_body_participant" id="message_body_participant" class="textarea form-control">{{ $template }}</textarea>
                                </div>

                            </div>
                        </div>



                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="lnbrd">
                                    <input type="checkbox" name="attachment" id="attachment"> <label
                                        for="attachment">Attach File</label>
                                </div>
                            </div>
                        </div>

                        <div id="attach">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="lnbrd">
                                        <input type="file" class="filestyle" name="attachment_doc"
                                            accept=".pdf,.doc,.docx" data-buttonName="btn-primary">
                                    </div>
                                </div>
                            </div>
                        </div>



                        <small><strong>Note :[fname]</strong> use this shortcode to yield first name in the section.
                            <br />
                            <span style="padding-left:3em"><strong>[lname]</strong> use this shortcode to yield last
                                name in the section </span><br />
                            <span style="padding-left:3em"><strong>[Surveys list]</strong> use this shortcode to yield
                                survey information in the section </span><br />
                            <span style="padding-left:3em"><strong>[Login Details]</strong> use this shortcode to yield
                                login information in the section</span></small>
                        <br>
                        <?php   $actionurl=URL::route('addusers.show',$survey_id); ?>
                        <div class="form-group" style="margin-top: 10px;">
                            <div class="col-sm-12">
                                <input type="hidden" name="survey_id" value="{{ $survey_id }}">

                                <a href="{{ $actionurl }}" class="btn btn-danger btn-md">Cancel</a>
                                <button type="submit" class="btn btn-success">Send Email</button>
                            </div>

                        </div>

                        </form>

                    </div>

                    <!--   -->


                    <div id="add-notification-respondent" class="desc" style="display: none;">

                        <form method="POST" action="{{ route('distribute.store') }}" id="distribute-respondent" class="form-horizontal" enctype="multipart/form-data">
                            @csrf

                            <input type="hidden" name="send_email" value="notification-respondent">

                        <?php $send_email_from=(isset($from_email)) ? $from_email : null; ?>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="email" name="from_email_respondent" value="{{ $send_email_from }}" class="form-control" placeholder="From Email">
                            </div>
                        </div>



                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="email" name="cc_respondent" value="" class="form-control" placeholder="CC">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="text" name="copy_email_respondent" value="" class="form-control" placeholder="BCC">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="text" name="replay_to" value="" class="form-control" placeholder="Replay To">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="participant-wraper">
                                    <?php $count=1; $i=1;?>
                                    @foreach($respondents as $respondent)
                                    @if($count==1)
                                    <div class="split-12">
                                        <label><input type="checkbox" checked="checked" id="select_all_case2" /> Select
                                            all</label>
                                    </div>
                                    @endif
                                    <div class="split-4">
                                        <input type="checkbox" name="bcc_respondent[]" value="{{ $respondent->email }}" id="chk{{ $count }}" class="case2" checked>
                                        <label for="{{'chk'.$count}}">{{$respondent->fname.'
                                            '.$respondent->lname}}({{$respondent->email}})</label>
                                    </div>
                                    @if($i==250)


                                    <div class="col-md-9 ">
                                        <hr>
                                    </div>
                                    <?php $i=0; ?>
                                    @endif
                                    <?php $count++;$i++?>

                                    @endforeach

                                </div>
                            </div>
                        </div>


                        <div class="form-group">
                            <div class="col-sm-12">
                                <?php
                                if (isset($email_templates['notification-respondent']['subject'])) {
                                    $subject=$email_templates['notification-respondent']['subject'];
                                }else{
                                    $subject=null;
                                }

                                ?>
                                <input type="text" name="subject_respondent" value="{{ $subject }}" class="form-control" placeholder="Subject" id="subject">

                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="lnbrd">
                                    <?php
                                        if (isset($email_templates['notification-respondent']['content'])) {
                                            $template=$email_templates['notification-respondent']['content'];
                                        }else{
                                            $template='[fname][lname][Surveys list][Login Details]';
                                        }

                                    ?>
                                    <textarea name="message_body_respondent" id="message_body_respondent" class="textarea form-control">{{ $template }}</textarea>
                                </div>

                            </div>
                        </div>



                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="lnbrd">
                                    <input type="checkbox" name="attachment" id="attachment_respondent"> <label
                                        for="attachment_respondent">Attach File</label>
                                </div>
                            </div>
                        </div>

                        <div id="attach_respondent">
                            <div class="form-group">
                                <div class="col-sm-12">
                                    <div class="lnbrd">
                                        <input type="file" class="filestyle" name="attachment_doc"
                                            accept=".pdf,.doc,.docx" data-buttonName="btn-primary">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <small><strong>Note :[fname]</strong> use this shortcode to yield first name in the section.
                            <br />
                            <span style="padding-left:3em"><strong>[lname]</strong> use this shortcode to yield last
                                name in the section </span><br />
                            <span style="padding-left:3em"><strong>[Surveys list]</strong> use this shortcode to yield
                                survey information in the section </span><br />
                            <span style="padding-left:3em"><strong>[Login Details]</strong> use this shortcode to yield
                                login information in the section</span></small>
                        <br>
                        <?php   $actionurl=URL::route('addusers.show',$survey_id); ?>
                        <div class="form-group" style="margin-top: 10px;">
                            <div class="col-sm-12">
                                <input type="hidden" name="survey_id" value="{{ $survey_id }}">
                                <a href="{{$actionurl}}" class="btn btn-danger btn-md">Cancel</a>
                                <button type="submit" class="btn btn-success">Send Email</button>
                            </div>
                        </div>

                        </form>

                    </div>



                    <!--   -->
                    <div id="add-reminder-participant" class="desc" style="display: none;">
                        <form method="POST" action="{{ route('distribute.store') }}" id="distribute-participants-reminder" class="form-horizontal">
                            @csrf


                            <input type="hidden" name="send_email" value="remainder-participant">



                        <?php $send_email_from=(isset($from_email)) ? $from_email : null; ?>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="email" name="from_email_for_reminder_participant" value="{{ $send_email_from }}" class="form-control" placeholder="From Email">
                            </div>
                        </div>



                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="email" name="cc_for_reminder_participant" value="" class="form-control" placeholder="CC">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="text" name="copy_email_for_reminder_participant" value="" class="form-control" placeholder="BCC">

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="text" name="replay_to" value="" class="form-control" placeholder="Replay To">

                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="participant-wraper col-sm-offset-1">
                                    <?php $count=1;$i=1;?>
                                    @foreach($remind_participants as $participant)
                                    @if($count==1)
                                    <div class="split-12">
                                        <label><input type="checkbox" checked="checked" id="select_all_another" />
                                            Select all</label>
                                    </div>
                                    @endif
                                    <div class="split-4">
                                        <input type="checkbox" name="bcc_for_reminder_participant[]" value="{{ $participant->email }}" id="chkl{{ $count }}" class="case_another" checked>
                                        <label for="{{'chkl'.$count}}">{{$participant->fname.'
                                            '.$participant->lname}}({{$participant->email}})</label>
                                    </div>
                                    @if($i==250)
                                    <div class="col-md-9 ">
                                        <hr>
                                    </div>
                                    <?php $i=0; ?>
                                    @endif
                                    <?php $count++;$i++?>
                                    @endforeach

                                </div>
                            </div>
                        </div>




                        <div class="form-group">
                            <div class="col-sm-12">
                                <?php
                                    if (isset($email_templates['remainder-participant']['subject'])) {
                                        $subject=$email_templates['remainder-participant']['subject'];
                                    }else{
                                        $subject='';
                                    }
                                ?>
                                <input type="text" name="subject_for_reminder_participant" value="{{ $subject }}" class="form-control" placeholder="Subject" id="subject">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="lnbrd">
                                    <?php
                                        if (isset($email_templates['remainder-participant']['content'])) {
                                            $template=$email_templates['remainder-participant']['content'];
                                        }else{
                                            $template='[fname][lname][Surveys list][Login Details]';
                                        }
                                    ?>
                                    <textarea name="message_body_for_reminder_participant" id="message_body_for_reminder_participant" class="textarea form-control">{{ $template }}</textarea>
                                </div>

                            </div>
                        </div>

                        <small><strong>Note :[fname]</strong> use this shortcode to yield first name in the section.
                            <br />
                            <span style="padding-left:3em"><strong>[lname]</strong> use this shortcode to yield last
                                name in the section </span><br />
                            <span style="padding-left:3em"><strong>[Surveys list]</strong> use this shortcode to yield
                                survey information in the section </span><br />
                            <span style="padding-left:3em"><strong>[Login Details]</strong> use this shortcode to yield
                                login information in the section</span></small>
                        <br>
                        <div class="form-group" style="margin-top: 10px;">
                            <div class="col-sm-12">
                                <input type="hidden" name="survey_id" value="{{ $survey_id }}">



                                <a href="{{$actionurl}}" class="btn btn-danger btn-md">Cancel</a>

                                <button type="submit" class="btn btn-success">Send Email</button>

                            </div>
                        </div>

                        </form>

                    </div>

                    <!--   -->




                    <div id="add-reminder-respondent" class="desc" style="display: none;">
                        <form method="POST" action="{{ route('distribute.store') }}" id="distribute-respondent-reminder" class="form-horizontal">
                            @csrf


                        <input type="hidden" name="send_email" value="remainder-respondent">


                        <?php $send_email_from=(isset($from_email)) ? $from_email : null; ?>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="email" name="from_email_for_reminder_respondent" value="{{ $send_email_from }}" class="form-control" placeholder="From Email">

                            </div>
                        </div>



                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="email" name="cc_for_reminder_respondent" value="" class="form-control" placeholder="CC">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="text" name="copy_email_for_reminder_respondent" value="" class="form-control" placeholder="BCC">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="text" name="replay_to" value="" class="form-control" placeholder="Replay To">
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="participant-wraper col-sm-offset-1">
                                    <?php $count=1; $i=1;?>
                                    @foreach($remind_respondents as $participant)
                                    @if($count==1)
                                    <div class="split-12">
                                        <label><input type="checkbox" checked="checked" id="select_all_another_case2" />
                                            Select all</label>
                                    </div>
                                    @endif
                                    <div class="split-4">
                                        <input type="checkbox" name="bcc_for_reminder_respondent[]" value="{{ $participant->email }}" id="chkl{{ $count }}" class="case_another_2" checked>
                                        <label for="{{'chkl'.$count}}">{{$participant->fname.'
                                            '.$participant->lname}}({{$participant->email}})</label>
                                    </div>
                                    @if($i==250)
                                    <div class="col-md-9 ">
                                        <hr>
                                    </div>
                                    <?php $i=0; ?>
                                    @endif
                                    <?php $count++;$i++?>
                                    @endforeach

                                </div>
                            </div>
                        </div>

                        <?php
                            if (isset($email_templates['remainder-respondent']['subject'])) {
                                $subject=$email_templates['remainder-respondent']['subject'];
                            }else{
                                $subject=null;
                            }

                        ?>
                        <div class="form-group">
                            <div class="col-sm-12">
                                <input type="text" name="subject_for_reminder_respondent" value="{{ $subject }}" class="form-control" placeholder="Subject" id="subject">
                            </div>
                        </div>

                        <div class="form-group">
                            <div class="col-sm-12">
                                <div class="lnbrd">
                                    <?php
                                    if (isset($email_templates['remainder-respondent']['content'])) {
                                        $template=$email_templates['remainder-respondent']['content'];
                                    }else{
                                        $template='[fname][lname][Surveys list][Login Details]';
                                    }

                                    ?>
                                   <textarea name="message_body_for_reminder_respondent" id="message_body_for_reminder_respondent" class="textarea form-control">{{ $template }}</textarea>
                                </div>

                            </div>
                        </div>

                        <small><strong>Note :[fname]</strong> use this shortcode to yield first name in the section.
                            <br />
                            <span style="padding-left:3em"><strong>[lname]</strong> use this shortcode to yield last
                                name in the section </span><br />
                            <span style="padding-left:3em"><strong>[Surveys list]</strong> use this shortcode to yield
                                survey information in the section </span><br />
                            <span style="padding-left:3em"><strong>[Login Details]</strong> use this shortcode to yield
                                login information in the section</span></small>
                        <br>
                        <div class="form-group" style="margin-top: 10px;">
                            <div class="col-sm-12">
                                <input type="hidden" name="survey_id" value="{{ $survey_id }}">

                                <a href="{{$actionurl}}" class="btn btn-danger btn-md">Cancel</a>

                                <button type="submit" class="btn btn-success">Send Email</button>

                            </div>
                        </div>

                        </form>

                    </div>

                </div>



            </div>
        </div>
    </div>
</div>




<script src="{{ asset('script/bootstrap-filestyle.js') }}"></script>
<link rel="stylesheet" href="{{ asset('css/bootstrap3-wysihtml5.min.css') }}">
<script src="{{ asset('script/bootstrap3-wysihtml5.js') }}"></script>


<style media="screen">
    .nav-tabs li.bv-tab-error>a {
        color: #555;
    }

    .nav.nav-tabs a {
        border: 1px solid #eee;
    }

    .nav>li.active a,
    .nav>li.active a:hover,
    .nav>li.active a:focus {
        background-color: #286090;
        border-color: #286090;
        color: #ffffff;
    }
</style>
<script type="text/javascript">
    $('#attach').hide();
	$('#attachment').change(function(){
		if($(this). prop("checked") == true){
			$('#attach').show();
		}
		else{
			$('#attach').hide();
		}
	});


$('#attach_respondent').hide();
	$('#attachment_respondent').change(function(){
		if($(this). prop("checked") == true){
			$('#attach_respondent').show();
		}
		else{
			$('#attach_respondent').hide();
		}
	});
</script>
<script type="text/javascript">
    $(document).ready(function(){
		$(":file").filestyle({btnClass: "btn-success"});

        $('#distribute-participants').bootstrapValidator({
          framework: 'bootstrap',

          icon: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
          fields: {
            from_email_participant: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  },
                  emailAddress: {
                        message: 'The value is not a valid email address'
                    }

                }
            },
              /*cc: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }


                }
            },
	copy_email: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }


                }
            },*/
 attachment_doc:{
            	  validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  },
                   file: {
                        extension: 'pdf,doc,docx',
                        message: 'The selected file is not valid'
                    }
                }
            },
              subject_participant: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }

                }
            },
           message_body_participant: {
           	group: '.lnbrd',
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }

                }
            },
            'bcc_participant[]': {
                validators: {
                 choice: {
                        min: 1,
                        message: 'Please select atleast one'
                    }

                }
            },
          }
});


 $('#distribute-participants-reminder').bootstrapValidator({
          framework: 'bootstrap',

          icon: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
          fields: {
            from_email_for_reminder_participant: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  },
                  emailAddress: {
                        message: 'The value is not a valid email address'
                    }

                }
            },
             /* cc_for_reminder: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }


                }
            },


 	copy_email_for_reminder: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }


                }
            },
*/


              subject_for_reminder_participant: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }

                }
            },
           message_body_for_reminder_participant: {
           	group: '.lnbrd',
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }

                }
            },
            'bcc_for_reminder_participant[]': {
                validators: {
                  choice: {
                        min: 1,
                        message: 'Please select atleast one'
                    }

                }
            },
          }
});





        $('#distribute-respondent').bootstrapValidator({
          framework: 'bootstrap',

          icon: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
          fields: {
            from_email_respondent: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  },
                  emailAddress: {
                        message: 'The value is not a valid email address'
                    }

                }
            },
              /*cc: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }


                }
            },
	copy_email: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }


                }
            },*/

attachment_doc:{
            	  validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  },
                   file: {
                        extension: 'pdf,doc,docx',
                        message: 'The selected file is not valid'
                    }
                }
            },
              subject_respondent: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }

                }
            },
           message_body_respondent: {
           	group: '.lnbrd',
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }

                }
            },
            'bcc_respondent[]': {
                validators: {
                 choice: {
                        min: 1,
                        message: 'Please select atleast one'
                    }

                }
            },
          }
});




 $('#distribute-respondent-reminder').bootstrapValidator({
          framework: 'bootstrap',

          icon: {
              valid: 'glyphicon glyphicon-ok',
              invalid: 'glyphicon glyphicon-remove',
              validating: 'glyphicon glyphicon-refresh'
          },
          fields: {
            from_email_for_reminder_respondent: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  },
                  emailAddress: {
                        message: 'The value is not a valid email address'
                    }

                }
            },
             /* cc_for_reminder: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }


                }
            },


 	copy_email_for_reminder: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }


                }
            },
*/
              subject_for_reminder_respondent: {
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }

                }
            },
           message_body_for_reminder_respondent: {
           	group: '.lnbrd',
                validators: {
                  notEmpty: {
                      message: 'The Field required and cannot be empty'
                  }

                }
            },
            'bcc_for_reminder_respondent[]': {
                validators: {
                  choice: {
                        min: 1,
                        message: 'Please select atleast one'
                    }

                }
            },
          }
});



         $('#message_body_participant').wysihtml5({
		 	events: {
			        load: function () {
			            $('#message_body_participant').addClass('textnothide');

			    },
				change: function () {
				   $('#distribute-participants').bootstrapValidator('revalidateField', 'message_body_participant');
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


          $('#message_body_for_reminder_participant').wysihtml5({
		 	events: {
			        load: function () {
			            $('#message_body_for_reminder_participant').addClass('textnothide');

			    },
				change: function () {
				   $('#distribute-participants-reminder_participant').bootstrapValidator('revalidateField', 'message_body_for_reminder_participant');
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



         $('#message_body_respondent').wysihtml5({
		 	events: {
			        load: function () {
			            $('#message_body_respondent').addClass('textnothide');

			    },
				change: function () {
				   $('#distribute-participants').bootstrapValidator('revalidateField', 'message_body_respondent');
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


          $('#message_body_for_reminder_respondent').wysihtml5({
		 	events: {
			        load: function () {
			            $('#message_body_for_reminder_respondent').addClass('textnothide');

			    },
				change: function () {
				   $('#distribute-participants-reminder').bootstrapValidator('revalidateField', 'message_body_for_reminder_respondent');
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

	})
</script>
<style type="text/css">
    iframe.wysihtml5-sandbox {
        height: 214px !important;
    }

    .textnothide {
        display: block !important;
        height: 215px !important;
        position: absolute;
        width: 97.6%;
        z-index: -1;
    }

    .state-icon {
        left: -5px;
    }

    .list-group-item-primary {
        color: rgb(255, 255, 255);
        background-color: rgb(66, 139, 202);
    }

    .well .list-group {
        margin-bottom: 0px;
    }

    .well {
        margin-bottom: 0px;
    }

    li.list-group-item {
        float: left;
        margin-right: 1%;
        width: 32%;
    }

    .list-group-item:last-child,
    .list-group-item:first-child {
        border-radius: inherit;
    }

    .participant-wraper {
        float: left;
        width: 100%;
    }

    .split-4 {
        float: left;
        width: 50%;
    }


    hr {
        border: 0;
        height: 1px;
        background: #333 none repeat scroll 0 0;
    }
</style>

<script>
    $(document).ready(function() {

//To initial select Participant
$('#pa-participant').prop('checked','true');
//To initial select Participant

// var checked_val=$("input[type='radio']:checked"). val();

// $('#add-notification-participant').show();

/*notification tab radio buttion change to respondenr or participant */

$("input[name='users']").click(function() {
  check_value=$(this).val();
  if (check_value=="notification-participant") {
    $('#add-notification-participant').show();
    $('#add-notification-respondent').hide();
  }
  else if(check_value=='notification-respondent'){
        $('#add-notification-respondent').show();
        $('#add-notification-participant').hide();
  }

});
/*notification tab radio buttion change to respondenr or participant */

/*reminder tab radio buttion change to respondenr or participant */
$("input[name='re-user']").click(function() {
  check_value1=$(this).val();

  $('#add-notification-participant').hide()
  if (check_value1=="reminder-participant") {
    $('#add-reminder-participant').show();
    $('#add-reminder-respondent').hide();
  }
  else if(check_value1='reminder-respondent'){
    $('#add-reminder-respondent').show();
    $('#add-reminder-participant').hide();
  }

});
/*reminder tab radio buttion change to respondenr or participant */

/*Tab change to Show and Hide options*/

$('a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
  var target = $(e.target).attr("href") // activated tab
  if (target=="#remainder") {

     $('#re-participant').prop('checked','true');
     $('#add-reminder-participant').show();
     $('#add-notification-participant').hide();
     $('#add-notification-respondent').hide();
  }
  if (target=="#notification") {

     $('#pa-participant').prop('checked','true');
     $('#add-notification-participant').show();
     $('#add-reminder-participant').hide();
     $('#add-reminder-respondent').hide();
  };
});

/*Tab change to Show and Hide options*/

});


$(document).ready(function(){

/*Select all option to  Notification tab Participant section*/
$("#select_all_case1").change(function(){
    var status = this.checked;
    $('.case1').each(function(){
        this.checked = status;
    });

     $('#distribute-participants').bootstrapValidator('revalidateField', 'bcc_participant[]');
});

$('.case1').change(function(){

    if(this.checked == false){
        $("#select_all_case1")[0].checked = false;
    }
    if ($('.case1:checked').length == $('.case1').length ){
        $("#select_all_case1")[0].checked = true;
    }
});
/*Select all option to  Notification tab Participant section*/


/*Select all option to  Notification tab Respondent section*/

$("#select_all_case2").change(function(){
    var status = this.checked;
    $('.case2').each(function(){
        this.checked = status;
    });

     $('#distribute-respondent').bootstrapValidator('revalidateField', 'bcc_respondent[]');
});

$('.case2').change(function(){

    if(this.checked == false){
        $("#select_all_case2")[0].checked = false;
    }


    if ($('.case2:checked').length == $('.case2').length ){
        $("#select_all_case2")[0].checked = true;
    }
});

/*Select all option to  Notification tab Respondent section*/


/*Select all option to  Reminder tab Participant section*/

$("#select_all_another").change(function(){
    var status = this.checked;
    $('.case_another').each(function(){
        this.checked = status;
    });

     $('#distribute-participants-reminder').bootstrapValidator('revalidateField', 'bcc_for_reminder_participant[]');
});


$('.case_another').change(function(){

    if(this.checked == false){
        $("#select_all_another")[0].checked = false;
    }


    if ($('.case_another:checked').length == $('.case_another').length ){
        $("#select_all_another")[0].checked = true;
    }
});
/*Select all option to  Reminder tab Participant section*/


/*Select all option to  Reminder tab Respondent section*/
$("#select_all_another_case2").change(function(){
    var status = this.checked;
    $('.case_another_2').each(function(){
        this.checked = status;
    });

     $('#distribute-respondent-reminder').bootstrapValidator('revalidateField', 'bcc_for_reminder_respondent[]');
});

$('.case_another_2').change(function(){

    if(this.checked == false){
        $("#select_all_another_case2")[0].checked = false;
    }


    if ($('.case_another_2:checked').length == $('.case_another_2').length ){
        $("#select_all_another_case2")[0].checked = true;
    }
});
/*Select all option to  Reminder tab Respondent section*/

});
</script>
@endsection
