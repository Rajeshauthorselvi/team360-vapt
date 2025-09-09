@extends('layouts.default')

@section('content')
    <?php //dd($responses);
    ?>
    <div class="container">
        <div class="panel panel-default">
            <div class="panel-heading">
                <div class="row text-center">
                    <h4><span><b>Survey Name :</b> </span><strong>{{ $survey_name }}</strong></h4>
                </div>
            </div>
            <div class="panel-body">
                @if (count($responses) > 0)
                    <div class="pull-right">

                        <span class="btn btn-primary">
                            <a href="{{ route('text_response_ques.ques_export', ['survey_id' => $survey_id, 'survey_name' => $survey_name]) }}">
                                <span class="glyphicon glyphicon-download-alt"></span>
                                Download Questions
                                <span class="fa fa-file-excel-o" aria-hidden="true"></span>
                            </a>
                        </span>

                        <button type="submit" class="btn btn-primary" id="submit"><span
                                class="glyphicon glyphicon-download-alt"></span> Download <span class="fa fa-file-excel-o"
                                aria-hidden="true"></span></button>
                    </div>
                @endif
                <div class="col-sm-3">

                    @if (count($filter_participants) > 0)
                    <form method="POST" action="{{ route('post.text_response', ['survey_id' => $survey_id]) }}" class="form-horizontal">
                        @csrf


                        <select name="users[]" multiple class="users">
                            @foreach ($filter_participants as $key => $users)
                                <option value="{{ $key }}">{{ $users }} </option>
                            @endforeach
                        </select>

                        <button type="submit" name="button" class="btn btn-primary filter">Filter</button>

                    </form>
                    @endif
                </div>
                <br>
                <br>


                <div class="table-responsive col-md-12">
                    <table class="table table-striped table-bordered" cellspacing="0" width="100%" id="text_response">
                        <thead>
                            <th class="competency-header text-center"></th>
                            <th class="competency-header text-center"></th>
                            <th class="competency-header text-center"></th>
                            <th class="competency-header text-center"></th>
                            @if (count($responses) > 0)

                                @if (count($question_id) > 0)
                                    @foreach ($question_id as $dimension => $order)
                                        <?php $r_count = count($order); ?>
                                        <th colspan="{{ $r_count }}" style="text-align: center;">
                                            {!! $dimension !!}</th>
                                    @endforeach
                                @endif

                            @endif
                        </thead>
                        <thead>
                            <th class="header text-center">S.No</th>
                            <th class="header text-center">Name</th>
                            <th class="header text-center">Email</th>
                            <th class="header text-center">Rater-type</th>
                            @if (count($responses) > 0)
                                @if (count($question_id) > 0)
                                    <?php $s_no = 1; ?>
                                    @foreach ($question_id as $dimension => $order)
                                        @foreach ($order as $order_value)
                                            <th class="text-center">Q{{ $s_no }}</th>
                                            <?php $s_no++; ?>
                                        @endforeach
                                    @endforeach
                                @endif
                            @endif

                        </thead>


                        <?php $s_no = 1; ?>
                        @if (count($responses) > 0)
                            <?php //dd($responses);
                            ?>
                            @foreach ($responses as $display_no => $response)
                                <tr>
                                    <td>{{ $s_no }}</td>
                                    <td>

                                        <?php
                                        if ($response->respondent_id != 0) {
                                            $f_l_name = DB::table('users')->selectRaw('GROUP_CONCAT(COALESCE(CONCAT(fname, " ", lname), fname)) AS user_name')->where('id', $response->participant_id)->value('user_name');

                                        } else {
                                            $f_l_name = $response->fname.' '.$response->lname;
                                        }
                                        ?>

                                        {{ $f_l_name }}
                                    </td>
                                    <td>{{ $response->email }}</td>
                                    <td>{{ Str::ucfirst($response->rater) }}</td>

                                    @if (isset($response->responses))
                                        @foreach ($response->responses as $key => $question_text)
                                            @if (count($question_text) > 0)
                                                <td>
                                                    @foreach ($question_text as $key => $each_question_text)
                                                        {!! $each_question_text !!}
                                                    @endforeach
                                                </td>
                                            @else
                                                <td class="text-center"> - </td>
                                            @endif
                                        @endforeach
                                    @endif

                                </tr>
                                <?php $s_no++; ?>
                            @endforeach
                        @else
                            <tr>
                                <td colspan="4" class="text-center">No Results Found</td>
                            </tr>
                        @endif


                    </table>
                </div>
            </div>
        </div>
    </div>
<!-- DataTables CSS -->
<link rel="stylesheet" href="{{ asset('css/dataTable/jquery.dataTables.min.css') }}">
<link rel="stylesheet" href="{{ asset('css/dataTable/dataTables.bootstrap4.min.css') }}">

<!-- DataTables JS -->
<script src="{{ asset('script/dataTable/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('js/dataTables.bootstrap4.min.js') }}"></script>


    <script type="text/javascript">
        $(document).ready(function() {

            var user_ids = <?php echo json_encode($user_name); ?>;

            $.each(user_ids, function(i, elem) {
                $('[value="' + elem + '"').prop('selected', true);
            });


            $('#text_response').DataTable({
                "bSort": false
            });
        });

        $("#submit").click(function() {

            var users = $('.users').val();
            var survey_id = "{{ $survey_id }}";
            var survey_name = "{{ $survey_name }}";

            var url = "{{ URL::route('export.text_response') }}?survey_id=" + survey_id + "&users=" + users;
            window.location.href = url;

        });
    </script>

    <style>
        .table-responsive {
            padding-top: 10px;
        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:hover {
            background: white;
            border: 1px solid white;
            color: #fff !important;
        }

        table.dataTable.no-footer {
            border-bottom: 1px solid #dddddd;
        }

        table.dataTable thead th,
        table.dataTable thead td {
            border-bottom: 1px solid #dddddd;
            padding: 10px 18px;
        }

        th {
            color: white;
            background: #2041BD;
        }

        .header {
            border-top: 0px none ! important;
        }

        .competency-header {
            border-bottom: 0px none ! important;

        }

        .pagination>li>a:focus,
        .pagination>li>a:hover,
        .pagination>li>span:focus,
        .pagination>li>span:hover {

            border-color: white !important;

        }

        .dataTables_wrapper .dataTables_paginate .paginate_button:active {
            box-shadow: none;
            outline: none;
        }

        .users {
            width: 75%;
        }

        a {
            color: white;
            text-decoration: none;
        }

        a:hover,
        a:focus {
            color: white;
            text-decoration: none;
        }
    </style>
@endsection
