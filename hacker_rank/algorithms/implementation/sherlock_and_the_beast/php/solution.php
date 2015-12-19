<?php

$handle = fopen('php://stdin', 'r');
fscanf($handle, '%d', $testCase);
$output = '';

for ($i = 0; $i < $testCase; $i++) {
    fscanf($handle, '%d', $digits);

    if ($digits % 3 !== 0 &&
        $digits % 5 !== 0 &&
        $digits < 5
    ) {
        $output .= "-1\n";
    } else {
        $factor = (int) floor($digits / 3);

        while (($digits - $factor * 3) % 5 !== 0) {
            $factor--;
        }

        $fives = $factor * 3;
        $threes = $digits - $fives;

        if ($fives < 0 || $threes < 0) {
            $output .= "-1\n";
        } else {
            $output .= str_repeat('5', $fives);
            $output .= str_repeat('3', $threes);
            $output .= "\n";
        }
    }
}

echo $output;
