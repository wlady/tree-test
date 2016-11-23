<?php
/**
 * Created by PhpStorm.
 * User: wlady2001
 * Date: 23.11.16
 * Time: 12:51
 */

$origin = 'Sum summus mus';

$str = strtolower(str_replace(' ', '', $origin));
$len = strlen($str);
if ($len % 2) {
    $str1 = substr($str, 0, $len/2);
    $str2 = strrev(substr($str, $len/2+1));
} else {
    list($str1, $str2) = str_split($str, $len/2);
    $str2 = strrev($str2);
}
if ($str1==$str2) {
    echo 'String is palindrome: ', $origin, "\n";
    exit;
}

$longest = '';
$len = strlen($origin);
for ($i=0; $i<$len; $i++) {
    for ($j=1; $j<$len-$i+1; $j++) {
        $find = substr($origin, $i, $j);
        $tmp = strrev($find);
        if (preg_match("/{$tmp}/i", $origin)) {
            if (strlen($longest)<strlen($find)) {
                $longest = $find;
            }
        }
    }
}
if (strlen($longest)) {
    echo 'The longest palindrome substring: ', $longest, "\n";
    exit;
}

echo 'String is not a palindrome: ', $origin[0], "\n";