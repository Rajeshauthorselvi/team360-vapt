@extends('Page_Wise.users.layouts.default')
@section('content')

    <div class="site-content">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <table class="survey-table text-center">
                        <thead>
                            <tr>
                                <th class="text-center">S.No</th>
                                <th class="text-left">Survey Name</th>
                                <th>Evaluating</th>
                                @if ($show_relationship == 1)
                                    <th>Relationship</th>
                                @endif
                                {{-- <th class="text-center">Start Date</th> --}}
                                {{-- <th class="text-center">End Date</th> --}}
                                <th class="text-center">Status</th>
                                @if ($participant_rater_manage == 1)
                                    <th class="text-center">Respondents</th>
                                @endif
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @if (count($user_survey_info) > 0)
                                @foreach ($user_survey_info as $key => $usinfo)
                                    <tr>
                                        <td class="sno hidden-xs">{{ $key + 1 }}</td>
                                        <td data-label="Survey Name" class="<?php echo $key == 0 ? $key : ''; ?> text-left">
                                            <span class="allocate_table_content"> <b>{{ $usinfo->title }}</b>
                                            </span>
                                        </td>
                                        <td>
                                            @if (strtolower($usinfo->rater) == 'self')
                                                Yourself
                                            @else
                                                {{ ucfirst($usinfo->fname) . ' ' . ucfirst($usinfo->lname) }}

                                            @endif
                                        </td>
                                        @if ($show_relationship == 1)
                                            <td>
                                                @if (strtolower($usinfo->rater) == 'self')
                                                    Self
                                                @else
                                                    {{ $usinfo->rater }}
                                                @endif
                                            </td>
                                        @endif

                                        <td class="text-center txt-captialize " data-label="Survey Status">
                                            <span class="allocate_table_content">
                                                <?php
                                                if ($usinfo->survey_status == '0') {
                                                    echo '<span >Closed</span>';
                                                } elseif ($usinfo->survey_status == '1') {
                                                    echo '<span >Active</span>';
                                                } elseif ($usinfo->survey_status == '2') {
                                                    echo '<span >Partly Completed</span>';
                                                } elseif ($usinfo->survey_status == '3') {
                                                    echo '<span >Completed</span>';
                                                } elseif ($usinfo->survey_status == '4') {
                                                    echo '<span >In-Active</span>';
                                                }
                                                ?>
                                            </span>
                                        </td>
                                        @if ($participant_rater_manage == 1)
                                            <td class="text-center " data-label="Respondents">
                                                <span class="allocate_table_content">
                                                    @if ($usinfo->participant_rater_manage == 1 and $usinfo->respondent_id == 0)
                                                        <a href="{{ route('manage-respondent.index', [config('site.survey_slug')]) }}"
                                                            class="manage btn-link "><i class="fa fa-cog"
                                                                aria-hidden="true"></i> Manage</a>

                                                        @if ($survey_exists)

                                                            <a href="{{ route('manage-email.index', [config('site.survey_slug')]) }}">
                                                                <i class="fa fa-envelope" aria-hidden="true"></i> Send Email
                                                            </a>
                                                        @endif

                                                    @else
                                                        -
                                                    @endif
                                                </span>
                                            </td>
                                        @endif

                                        <?php
                                        $rater_id =$usinfo->rater_id;
                                        $participant_id =$usinfo->participant_id;
                                        $user_exam_info = [config('site.survey_slug'), 'rater=' . $rater_id, 'participant=' . $participant_id];
                                        ?>

                                        @if ($usinfo->survey_status == 1)
                                            <td class="text-center " data-label="Action">
                                                <span class="allocate_table_content">
                                                    <a href="{{ URL::route('user.index', $user_exam_info) }}"
                                                        class="btn btn-submit">Start</a>
                                                </span>
                                            </td>
                                        @elseif($usinfo->survey_status == 2)
                                            <td class="text-center " data-label="Action">
                                                <span class="allocate_table_content">
                                                    <a href="{{ URL::route('user.index', $user_exam_info) }}"
                                                        class="btn btn-submit">Continue</a>
                                                </span>
                                            </td>
                                        @else
                                            <td class="text-center" data-label="Action"><span
                                                    class="allocate_table_content">-</span></td>
                                        @endif
                                        <td style="border:0px solid transparent;" class="visible-xs"></td>
                                    </tr>
                                @endforeach
                            @else
                                <tr>
                                    <td colspan="6" class="text-center table_content">No Survey found!</td>
                                </tr>
                            @endif
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <style type="text/css">
        footer {
            position: relative;
        }

        .inner-header .container {
            margin-top: 30px;
        }

    </style>
    <script type="text/javascript">
        if ($(window).width() < 514) {
            $('html').addClass('mobile');
        } else {
            if (!init) {
                $('html').removeClass('mobile');
            }
        }
    </script>
    <style type="text/css">
        @media(min-width: 768px) and (max-width: 800px) {
            .visible-xs {
                display: block !important;
            }
        }

    </style>
@endsection
