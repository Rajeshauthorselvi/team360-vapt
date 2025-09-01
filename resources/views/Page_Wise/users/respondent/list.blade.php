@extends('Page_Wise.users.layouts.default')

@section('content')

<div class="container">
 <div class="row ">
<div class="col-xs-12 col-sm-12 col-md-12 list-page">

 
  <h3 class="need-margin-bottom-forstrip text-center">{{$title}}</h3>
               
    @if ($errors->any())
      <div class="alert alert-danger fade in">
          <a href="#" class="close" data-dismiss="alert">&times;</a>
          <strong>Error!</strong> A problem has been occurred while submitting form.<br>
          <ul> {!! implode('', $errors->all('<li class="text-danger">:message</li>')) !!}</ul>
      </div>
     @endif 

    <div class="action-panel pull-right">
      <a href="{{ route('manage-respondent.create',[config('site.survey_slug')]) }}" class="btn btn-submit"><i class="fa fa-plus" aria-hidden="true"></i>&nbsp;Add New</a>
      <a href="{{route('user.dashboard',config('site.survey_slug'))}}" class="btn btn-danger">Back</a>
    </div>

      @if(Session::has('msg'))
        @if(Session::get("mess_data")=="success")
        <div class="alert alert-success alert-dismissable" style="clear: both;">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Success!</strong> {!! Session::has('msg') ? Session::get("msg") : '' !!}
        </div>
        @elseif(Session::get("mess_data")=='error')
        <div class="alert alert-danger alert-dismissable" style="clear: both;">
          <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
          <strong>Error!</strong> 
          <ul class="list-unstyled">
              @foreach(Session::get('msg') as $error)
                <li>{{$error}} </li>
              @endforeach
          </ul>
        </div>
      @endif

  @endif

    @if(Session::has('mailstatus'))
    <?php 
    $mailstatus=Session::get("mailstatus"); ?>
    @if(count($mailstatus)==0)
    <div class="alert alert-success alert-dismissable" style="clear: both;">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Success!</strong> Mail Sent successfully.
    </div>
    @else
     <div class="alert alert-danger alert-dismissable" style="clear: both;">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Oops!</strong> Mail Not Sent.
    </div>
    @endif
    @endif
   @if(Session::has('reopen_survey_message'))
  
    <div class="alert alert-success alert-dismissable" style="clear: both;">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Survey Reopened successfully!</strong> 
    </div>
      @endif
   @if(Session::has('clear_response_message'))
  
    <div class="alert alert-success alert-dismissable" style="clear: both;">
    <a href="#" class="close" data-dismiss="alert" aria-label="close">&times;</a>
    <strong>Response cleared successfully!</strong> 
    </div>

      @endif
      
                          
    <div>
    <table id="stable"  class="survey-table text-center">
    <thead>
    <tr>
      <th>S.No</th>
      <th>First Name</th>
      <th>Last Name</th>
      <th>Email</th>
      {{-- <th>Status</th> --}}
      <th>Respondent Type</th>
      <th class="actions">Actions</th>
    </tr>
    </thead>


     @if(count($users) >0)

      @foreach($users as $key=>$result)

          <tr>
            <td class="sno hidden-xs">{{$key+1}}</td>
            <td  data-label="First Name" class="<?php echo ($key==0) ? $key : '' ;?>">
              <span class="allocate_table_content">{{$result->fname}}</span>
              </td>
            <td data-label="Last Name">
              <span class="allocate_table_content">{{$result->lname}}</span>
            </td>
            <td data-label="E-Mail">
              <span class="allocate_table_content">{{$result->email}}</span>
            </td>
            <!---<td data-label="survey Status">
              <span class="allocate_table_content">
                <?php
                  if($result->survey_status=='0') echo '<span >Closed</span>';
                  elseif($result->survey_status=='1') echo '<span >Active</span>';
                  elseif($result->survey_status=='2') echo '<span >Partly Completed</span>';
                  elseif($result->survey_status=='3') echo '<span >Completed</span>';
                  elseif($result->survey_status=='4') echo '<span>In-Active</span>';
                ?>
              </span>
           </td>-->
            <td data-label="Respondent-Type">
              <span class="allocate_table_content">{{$result->rater}}</span>
            </td>
            <td data-label="Edit" class="icon action-btn action">
              <span class="allocate_table_content">

              <a href="{{ route('manage-respondent.edit',[config('site.survey_slug'),0,'user_id='.$result->id]) }}" data-toggle="tooltip" title="Edit respondent details" class="btn btn-info icon_symbol"><span class="fa fa-edit"></span></a>
              @if($result->notify_email_date !=null)
              <a href="{{ route('resend.resendaccess',['respondent_id'=>$result->id,'survey_id'=>$survey_id]) }}" data-toggle="tooltip" title="Resend Access" class="btn btn-info icon_symbol"><span class="fa fa-refresh"></span></a>
              @endif

   
              <form class="form-inline" role="form" method="GET" action="{{URL::route('manage-respondent.show',[config('site.survey_slug'),0])}}">
              <input type="hidden" name="survey_id" value="<?= $survey_id?>">
               <input type="hidden" name="action" value="reopensurvey">
                                 
              <input type="hidden" name="respondent_id" value="<?= $result->id?>">
              @if($result->rcount > 1)
              <button class="reopen-survey btn btn-info icon_symbol" type="button" data-toggle="tooltip" title="Reopen Survey"><span class="fa fa-repeat"></span></button>
              @endif
              </form>
                	
                   
              <form class="form-clear" role="form" method="GET" action="{{URL::route('manage-respondent.show',[config('site.survey_slug'),0])}}">
            
                             <input type="hidden" name="action" value="emptyresponse">

              <input type="hidden" name="survey_id" value="<?= $survey_id?>">
              <input type="hidden" name="respondent_id" value="<?= $result->id?>">
              @if($result->rcount > 1)
              <button class="clear-response btn btn-info icon_symbol" type="button" data-toggle="tooltip" title="Clear Response"><span class="fa fa-remove"></span></button>
              @endif
              </form>


              {!! Form::open(['method' => 'DELETE','route' => ['manage-respondent.destroy',config('site.survey_slug'),$result->id],'class'=>'del_form']) !!}
                {{Form::hidden('respondent_id',$result->id)}}
                {{Form::hidden('survey_id',$survey_id)}}
                @if($result->rcount > 1)
                  {{Form::hidden('check_response_data','true',['class'=>'check_response_data'])}}
                @else
                  {{Form::hidden('check_response_data','false',['class'=>'check_response_data'])}}
                @endif

                  <button class="delete-user-survey btn btn-info icon_symbol " type="button" data-toggle="tooltip" title="Delete" ><span class="fa fa-trash-o"></span></button>
              {!! Form::close() !!}
              </span>    
              </td>
              <td data-label="" style="border:0px solid transparent;" class="last-child"></td>
                    </tr>
              @endforeach

            @else
             <tr><td class="text-center" colspan="7">No Respondents found!</td></tr>    
            @endif

          </table>
          </div>

</div>
</div>
</div>


 {{ HTML::script('script/sweetalert.min.js') }}
 {{ HTML::style('css/sweetalert.css') }}
 {!! HTML::script('js/dataTables.bootstrap4.min.js') !!}
 {!! HTML::style('css/dataTable/dataTables.bootstrap4.min.css') !!}
 {!! HTML::script('script/respondent_list.js') !!}


  <style type="text/css">
    footer { position: relative;margin-top: 40px;}
    #stable{
      width: 100% !important;
    }

  .action{
    border-bottom:1px solid #41536d;
  }
  @media(max-width: 800px){
  table.survey-table tbody td{
    border-bottom: none;
  }
}

</style>

@endsection