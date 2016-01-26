<?php

$fh = fopen('php://stdin', 'r');
fscanf($fh, '%d', $testCases);

for ($counter = 1; $counter <= $testCases; $counter++) {
    fscanf($fh, '%d', $arraySize);
    $array = explode(' ', fgets($fh));
    $array[count($array) - 1] = rtrim($array[count($array) - 1]);

    echo contiguous($array) . ' ' . noncontiguous($array) . PHP_EOL;
}

function greater($a, $b) {
    if ($a >= $b) {
        return $a;
    }

    return $b;
}

function greatest($array) {
    sort($array);
    return array_reverse($array)[0];
}

function noncontiguous($array) {
    $noncontiguous = array_sum(array_filter($array, function ($number) {
        return $number > 0;
    }));

    $allNegativeOrAllZero = $noncontiguous === 0;

    if ($allNegativeOrAllZero) {
        return greatest($array);
    }

    return $noncontiguous;
}

function contiguous($array) {
    $maxUpToIndex = $array[0];
    $contiguous = $maxUpToIndex;

    for ($i = 1; $i < count($array); $i++) {
        $maxUpToIndex = greater($array[$i], $maxUpToIndex + $array[$i]);
        $contiguous = greater($contiguous, $maxUpToIndex);
    }

    return $contiguous;
}
