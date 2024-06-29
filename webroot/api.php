<?php
/**
 * scientia
 *
 * Copyright 2023 - 2024 Johannes Keßler
 *
 * https://www.bananas-playground.net/projekt/scientia/
 *
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the COMMON DEVELOPMENT AND DISTRIBUTION LICENSE
 *
 * You should have received a copy of the
 * COMMON DEVELOPMENT AND DISTRIBUTION LICENSE (CDDL) Version 1.0
 * along with this program.  If not, see http://www.sun.com/cddl/cddl.html
 */

mb_http_output('UTF-8');
mb_internal_encoding('UTF-8');
ini_set('error_reporting',-1); // E_ALL & E_STRICT

## check request
$_urlToParse = filter_var($_SERVER['QUERY_STRING'],FILTER_UNSAFE_RAW, FILTER_FLAG_STRIP_LOW);
if(!empty($_urlToParse)) {
    # see http://de2.php.net/manual/en/regexp.reference.unicode.php
    if(preg_match('/[\p{C}\p{M}\p{Sc}\p{Sk}\p{So}\p{Zl}\p{Zp}]/u',$_urlToParse) === 1) {
        die('Malformed request. Make sure you know what you are doing.');
    }
}

## config
require_once('config/config.php');

## set the error reporting
ini_set('log_errors',true);
ini_set('error_log',PATH_SYSTEMOUT.'/error.log');
if(DEBUG === true) {
    ini_set('display_errors',true);
}
else {
    ini_set('display_errors',false);
}

# time settings
date_default_timezone_set(TIMEZONE);

# required libs
require_once('lib/summoner.class.php');


if(DEBUG) error_log("Dump SERVER ".var_export($_SERVER,true));
## check if request is valid
$_create = false;
$filteredData = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && $_SERVER['CONTENT_TYPE'] === 'application/json; charset=UTF-8') {
    $payload = json_decode(file_get_contents('php://input'), true);
    if(DEBUG) error_log("[DEBUG] Dump payload ".var_export($payload,true));
    if(!empty($payload)) {
        if(isset($payload['asl']) && !empty($payload['asl'])
            && isset($payload['data']) && !empty($payload['data'])
            && isset(UPLOAD_SECRET[$payload['asl']])
        ) {
            if(DEBUG) error_log("[DEBUG] Valid payload so far");
            $filteredData = filter_var($payload['data'],FILTER_SANITIZE_FULL_SPECIAL_CHARS);
            if(!empty($filteredData)) {
                if(DEBUG) error_log("[DEBUG] Validated payload");
                $_create = true;
            }
        }
    }
}

## default response
$contentType = 'Content-Type: application/json; charset=utf-8';
$httpResponseCode = 200;
$contentBody = array(
    'message' => '',
    'status' => $httpResponseCode
);

## break here secret empty or false
if($_create === false) {
    header('X-PROVIDED-BY: scientia');
    header($contentType);
    http_response_code($httpResponseCode);
    echo json_encode($contentBody);
    exit();
}

## DB connection
$DB = new mysqli(DB_HOST, DB_USERNAME,DB_PASSWORD, DB_NAME);
if ($DB->connect_errno) exit('Can not connect to MySQL Server');
$DB->set_charset("utf8mb4");
$DB->query("SET collation_connection = 'utf8mb4_bin'");
$driver = new mysqli_driver();
$driver->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;

require_once 'lib/entry.class.php';
$Entry = new Entry($DB);
$do = $Entry->create($filteredData);
if(!empty($do)) {
    $contentBody['message'] = INSTALL_URL . PATH_WEBROOT . date('/Y/m/d/').$do;
}
else {
    $hash = md5($do.time());
    error_log("[ERROR] $hash Can not create. ". var_export($do,true));
    $contentBody['message'] = "Something went wrong. $hash";
    $contentBody['status'] = 500;
}

# return
header('X-PROVIDED-BY: scientia');
header($contentType);
http_response_code($httpResponseCode);
echo json_encode($contentBody);
$DB->close();
