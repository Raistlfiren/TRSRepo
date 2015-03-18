<?php

$lonsw = 2.0;
$latsw = 4.0;

$lonse = 3.0;
$latse = 4.0;

$lonnw = 2.0;
$latnw = 5.0;

$lonne = 3.0;
$latne = 5.0;

$quarters = "SwSwSwSw";

//Split string every two characters and stick it in an array
$qtr_array = str_split($quarters, 2);

foreach ($qtr_array as $qtr_seg) {
    //Change string to lower
    $qtr = strtolower($qtr_seg);

    if (! empty($qtr)) {

        if (substr($qtr,0,2) == "w") {
            $lonse = convertCoord($lonsw, $lonse);
            $lonne = convertCoord($lonnw, $lonne);
        } else {
            $lonsw = convertCoord($lonsw, $lonse);
            $lonnw = convertCoord($lonsw, $lonse);
        }

        if (substr($qtr,0,2) == "n") {
            $latsw = convertCoord($latsw, $latnw);
            $latse = convertCoord($latse, $latne);
        } else {
            $latnw = convertCoord($latsw, $latnw);
            $latne = convertCoord($latse, $latne);
        }
    }

}

echo ($lonsw + $lonse + $lonnw + $lonne)/4.0 . PHP_EOL;
echo ($latsw + $latse + $latnw + $latne)/4.0 . PHP_EOL;

function convertCoord($calc1, $calc2) {
    return ($calc1 + $calc2)/2.0;
}