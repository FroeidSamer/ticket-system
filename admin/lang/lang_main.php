<?php
include 'ar.php';
include 'en.php';

function tr($key)
{
    global $ar_words, $en_words;
    if ($_COOKIE['lang'] === 'ar') {
        if (isset($ar_words[$key])) {
            return $ar_words[$key];
        } else {
            return $key;
        }
    } elseif ($_COOKIE['lang'] === 'en') {
        if (isset($en_words[$key])) {
            return $en_words[$key];
        } else {
            return $key;
        }
    } else {
        return $key;
    }
}

?>
