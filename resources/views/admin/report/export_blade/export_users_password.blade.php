<table class="table table-striped table-bordered">
    <thead>
        <tr style="background-color:#000099;color:#fff;font-size: 10px">
            <th>S.No</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>Email</th>
            <th>Respondent Type</th>
            <th>Status</th>
            <th>Password</th>

        </tr>

    </thead>
    <tbody>
        <?php $s_no = 0;
        $r_no = 0; ?>
        @if (count($survey_details) > 0)
            @foreach ($survey_details as $user_details)
                <?php if ($user_details->rater == 'self') {
                    $class = 'self';
                    $s_no++;
                    $r_no = 0;
                } else {
                    $class = 'no_self';
                    $r_no++;
                }

                ?>
                <tr class="{{ $class }}">
                    @if ($user_details->rater == 'self')
                        <td style="background-color: #99ccff !important;">{{ $s_no }}</td>
                        <td style="background-color: #99ccff !important;">{{ $user_details->fname }}</td>
                        <td style="background-color: #99ccff !important;">{{ $user_details->lname }}</td>
                        <td style="background-color: #99ccff !important;">{{ $user_details->email }}</td>
                        <td style="background-color: #99ccff !important;">
                            @if ($user_details->rater)
                                <span style="text-transform: capitalize">{{ Str::ucfirst($user_details->rater) }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td style="background-color: #99ccff !important;">
                            <?php
                            if ($user_details->survey_status == '0') {
                                echo '<span >Closed</span>';
                            } elseif ($user_details->survey_status == '1') {
                                echo '<span >Active</span>';
                            } elseif ($user_details->survey_status == '2') {
                                echo '<span >Partly Completed</span>';
                            } elseif ($user_details->survey_status == '3') {
                                echo '<span >Completed</span>';
                            } elseif ($user_details->survey_status == '4') {
                                echo '<span >In-Active</span>';
                            }
                            ?>
                        </td>
                        <td style="background-color: #99ccff !important;">
                            @if (strlen($user_details->password) == 60 && preg_match('/^\$2y\$/', $user_details->password))
                            @else
                                {{ decrypt($user_details->password) }}
                            @endif
                        </td>
                    @else
                        <td><?php print numberToRoman($r_no); ?></td>

                        <td>{{ $user_details->fname }}</td>
                        <td>{{ $user_details->lname }}</td>
                        <td>{{ $user_details->email }}</td>
                        <td>
                            @if ($user_details->rater)
                                <span style="text-transform: capitalize">{{ $user_details->rater }}</span>
                            @else
                                -
                            @endif
                        </td>
                        <td>
                            <?php
                            if ($user_details->survey_status == '0') {
                                echo '<span >Closed</span>';
                            } elseif ($user_details->survey_status == '1') {
                                echo '<span >Active</span>';
                            } elseif ($user_details->survey_status == '2') {
                                echo '<span >Partly Completed</span>';
                            } elseif ($user_details->survey_status == '3') {
                                echo '<span >Completed</span>';
                            } elseif ($user_details->survey_status == '4') {
                                echo '<span >In-Active</span>';
                            }
                            ?>
                        </td>
                        <td>
                            @if (strlen($user_details->password) == 60 && preg_match('/^\$2y\$/', $user_details->password))
                            @else
                                {{ decrypt($user_details->password) }}
                            @endif
                        </td>
                    @endif


                </tr>
            @endforeach
        @else
            <tr>
                <td colspan="6" class="text-center">No Results Found</td>
            </tr>

        @endif
    </tbody>
</table>

<?php

function numberToRoman($num)
{
    // Make sure that we only use the integer portion of the value
    $n = intval($num);
    $result = '';

    // Declare a lookup array that we will use to traverse the number:
    $lookup = ['M' => 1000, 'CM' => 900, 'D' => 500, 'CD' => 400, 'C' => 100, 'XC' => 90, 'L' => 50, 'XL' => 40, 'X' => 10, 'IX' => 9, 'V' => 5, 'IV' => 4, 'I' => 1];

    foreach ($lookup as $roman => $value) {
        // Determine the number of matches
        $matches = intval($n / $value);

        // Store that many characters
        $result .= str_repeat($roman, $matches);

        // Substract that from the number
        $n = $n % $value;
    }

    // The Roman numeral should be built, return it
    return $result;
}

?>
