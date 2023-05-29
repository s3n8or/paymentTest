<?php

$sessionId;

if (isset($_COOKIE['sessionId'])) {
    echo 'sessionId = ' . $_COOKIE['sessionId'];
} else {
    echo 'no session found';
}
