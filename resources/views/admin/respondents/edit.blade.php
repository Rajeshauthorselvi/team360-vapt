@extends('layouts.default')

@section('content')


    <div class="container">
        <div class="row">
            <div class="col-sm-offset-2 col-sm-8">
                <div id="add-manually" class="tab-pane fade in active">

                    <h3 class="text-center">Edit - Respondent Details</h3>
                    @if ($errors->any())
                        <div class="alert alert-danger fade in">

                            <a href="#" class="close" data-dismiss="alert">&times;</a>

                            <strong>Error!</strong> A problem has been occurred while submitting form.<br>
                            <ul>
                                {!! implode('', $errors->all('<li class="text-danger">:message</li>')) !!}

                            </ul>
                        </div>
                    @endif

                    {!! Form::model($user, ['method' => 'PATCH', 'route' => ['respondent.update', $respondent_id, 'survey_id' => $survey_id], 'id' => 'add-participants', 'class' => 'form-horizontal']) !!}

                    <div class="form-group">

                        {{ Form::label('title', 'First Name', ['class' => 'col-sm-2 ']) }}
                        <div class="col-sm-10">
                            {{ Form::text('fname', null, ['class' => 'form-control', 'placeholder' => 'First Name','oninput'=>'sanitizeInput(this)']) }}
                        </div>

                    </div>
                    <div class="form-group">

                        {{ Form::label('title', 'Last Name', ['class' => 'col-sm-2 ']) }}
                        <div class="col-sm-10">
                            {{ Form::text('lname', null, ['class' => 'form-control', 'placeholder' => 'Last Name','oninput'=>'sanitizeInput(this)']) }}
                        </div>

                    </div>
                    <div class="form-group">

                        {{ Form::label('title', 'Email', ['class' => 'col-sm-2 ']) }}
                        <div class="col-sm-10">
                            {{ Form::email('email', null, ['class' => 'form-control', 'placeholder' => 'Email']) }}
                        </div>

                    </div>

                    <div class="form-group">

                        {{ Form::label('title', 'Rater / Respondent', ['class' => 'col-sm-2 ']) }}
                        <div class="col-sm-10">
                            {{-- {{Form::text('rater',null,['class'=>'form-control','placeholder'=>'Rater/Respondent'])}} --}}

                            <select class="form-control" name=rater>
                                <option value="{{ $rater_id }}" selected>{{ $rater }}</option>
                                @foreach ($raters as $rater_details)
                                    <option value="{{ $rater_details->rater_id }}">{{ $rater_details->rater }}</option>
                                @endforeach
                            </select>

                        </div>

                    </div>




                    <div class="form-group" id="optionTemplate">
                        <div class="col-sm-12">
                            {{ Form::hidden('survey_id', $survey_id) }}
                            {{ Form::hidden('participant_id', $participant_id) }}
                            <!-- <a href="javascript:void(0);" class="addButton btn btn-primary pull-right" title="Add field"><span class="glyphicon glyphicon-plus">Add</span></a>-->
                        </div>
                    </div>

                    <div class="form-group text-center col-sm-12" align="center">
                        <a href="{{ route('respondent.show', [$participant_id, 'survey_id' => $survey_id]) }}"
                            class="btn btn-danger">Cancel</a>

                        <button type="submit" class="btn btn-success">update</button>


                    </div>
                    {!! Form::close() !!}
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        $(document).ready(function() {
            $('#add-participants')
                .bootstrapValidator({
                    framework: 'bootstrap',
                    icon: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        email: {
                            validators: {
                                notEmpty: {
                                    message: 'The Field required and cannot be empty'
                                },
                                emailAddress: {
                                    message: 'The value is not a valid email address'
                                },
                                remote: {
                                    message: 'This Respondent already exist',
                                    url: "{!! URL::route('checkparticipant') !!}",
                                    data: {
                                        survey_id: "{{ $survey_id }}",
                                        participant_id: "{{ $participant_id }}",
                                        respondent_id: "{{ $respondent_id }}",
                                        email: $("input[name='email']").val(),
                                        from:'edit_respondent'
                                    }
                                }
                            }
                        },
                        fname: {
                            validators: {
                                notEmpty: {
                                    message: 'The Field required and cannot be empty'
                                }
                            }
                        },
                        rater: {
                            validators: {
                                notEmpty: {
                                    message: 'The Field required and cannot be empty'
                                }
                            }
                        }
                    }
                });


            $('#import_process')
                .bootstrapValidator({
                    framework: 'bootstrap',
                    icon: {
                        valid: 'glyphicon glyphicon-ok',
                        invalid: 'glyphicon glyphicon-remove',
                        validating: 'glyphicon glyphicon-refresh'
                    },
                    fields: {
                        import_file: {
                            validators: {
                                notEmpty: {
                                    message: 'The Field required and cannot be empty'
                                },
                                file: {
                                    extension: 'xls,xlsx',
                                    message: 'Please select xls or xlsx file formats only'
                                }
                            }
                        },
                    }
                })



        });
    </script>

@endsection
<style>
    .rater {
        background-color: white;
        padding: 5px;
        width: 100%;
    }

</style>
