<?php

$fh = fopen('php://stdin', 'r');
fscanf($fh, '%d', $testCases);

for ($counter = 1; $counter <= $testCases; $counter++) {
    fscanf($fh, '%d', $arraySize);
    $array = array_map('trim', explode(' ', fgets($fh)));
    $contiguous = null;
    $noncontiguous = null;
    $allNonNegative = true;

    foreach ($array as $number) {
        if ($number < 0) {
            $allNonNegative = false;
            break;
        }
    }

    if ($allNonNegative) {
        $contiguous = array_sum($array);
        $noncontiguous = $contiguous;
    } else {
        $allNegative = true;

        foreach ($array as $number) {
            if ($number >= 0) {
                $allNegative = false;
                break;
            }
        }

        if ($allNegative) {
            sort($array);
            $contiguous = array_reverse($array)[0];
            $noncontiguous = $contiguous;
        } else {
            $subSums = [];

            for ($offset = 0; $offset < $arraySize; $offset++) {
                for ($length = 1; $length <= $arraySize - $offset; $length++) {
                    if (isset($subSums[$offset][$length - 1])) {
                        $subSum = $subSums[$offset][$length - 1] + $array[$offset + $length - 1];
                    } else {
                        $subSum = array_sum(array_slice($array, $offset, $length));
                        $subSums[$offset][$length] = $subSum;
                    }

                    if ($contiguous < $subSum) {
                        $contiguous = $subSum;
                    }
                }

                if ($array[$offset] > 0) {
                    $noncontiguous += $array[$offset];
                }
            }
        }
    }

    echo "$contiguous $noncontiguous \n";
}
