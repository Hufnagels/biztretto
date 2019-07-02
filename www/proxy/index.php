<?php

    header('Access-Control-Max-Age:' . 5 * 60 * 1000);
    header("Access-Control-Allow-Origin: *");
    header('Access-Control-Request-Method: *');
    header('Access-Control-Allow-Methods: OPTIONS, GET');
    header('Access-Control-Allow-Headers *');
    header("Content-Type: application/javascript");

// Url params
    $url = trim(htmlentities(urldecode($_GET['url'])));//$_GET['url'];
    $callback = $_GET['callback'];
//print_r($url);
/*
 * TODO
 * check location (local/remote - like youtube)
 * check object type : (image/video)
 */
    $type = pathinfo($url, PATHINFO_EXTENSION);
    $data = file_get_contents($url);
    $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
    $re_encoded_image = sprintf($base64);

    print "{$callback}(" . json_encode($re_encoded_image) . ")";
    exit;