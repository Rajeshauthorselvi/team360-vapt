<header>

<div class="container">

<div class="row">

<div class="col-md-6  col-xs-6 col-sm-6 logo ">


{!! html_entity_decode(HTML::linkRoute('admin.dashboard',HTML::image('images/logo.jpg','Logo',['class'=>'img-responsive']))) !!}


</div>


<div class="col-md-6  col-xs-6 col-sm-6 link ">

<div class="text-right">
<a href="{{URL::to('admin-dashboard')}}" title="Home" class="btn-link text-danger" >Go Dashboard</a>
&nbsp;
<a href="{{URL::to('logout')}}" title="Logout" class="btn-link text-danger" >Logout</a>
</div>



</div>
</div>
</div>
<div class="container-fluid">

<div class="row">

    <div class="welcome-strip text-center">
        Welcome <strong>Admin</strong>
        <?php
            if (isset($survey_id)) {
                $survey_name=DB::table('surverys')->where('id',$survey_id)->value('title');
            }
         ?>
         <div class="pull-right">
            {!! isset($survey_name)?'Survey Name: <b>'.$survey_name.'</b>':'' !!}
         </div>
    </div>

</div>

</div>



</header>
