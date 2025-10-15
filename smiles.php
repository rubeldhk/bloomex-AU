<?php
$allowedOrigins = array(
    'https://dev.bloomex.com.au',
    'https://stage.bloomex.com.au',
    'https://bloomex.com.au',
    ''
);

if (in_array($_SERVER['HTTP_ORIGIN'], $allowedOrigins)) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);
    echo number_format('1301719');
}