<?php
/**
 * scientia
 *
 * Copyright 2022 Johannes Keßler
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
$_urlToParse = filter_var($_SERVER['QUERY_STRING'],FILTER_SANITIZE_STRING, FILTER_FLAG_STRIP_LOW);
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


## check if request is valid
$_create = false;
if(isset($_POST['asl']) && !empty($_POST['asl'])
    && isset($_FILES['data']) && !empty($_FILES['data'])
    && isset(SELFPASTE_UPLOAD_SECRET[$_POST['asl']])) {
    $_create = true;
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
    echo json_encode($data);
}

# database object
$DB = false;

## DB connection
$DB = new mysqli(DB_HOST, DB_USERNAME,DB_PASSWORD, DB_NAME);
if ($DB->connect_errno) exit('Can not connect to MySQL Server');
$DB->set_charset("utf8mb4");
$DB->query("SET collation_connection = 'utf8mb4_unicode_ci'");
$driver = new mysqli_driver();
$driver->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;