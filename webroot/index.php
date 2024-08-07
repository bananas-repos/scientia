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
require_once('lib/i18n.class.php');

Summoner::simpleAuth();

# i18n
$i18n = new I18n();

## DB connection
$DB = new mysqli(DB_HOST, DB_USERNAME,DB_PASSWORD, DB_NAME);
if ($DB->connect_errno) exit('Can not connect to MySQL Server');
$DB->set_charset("utf8mb4");
$DB->query("SET collation_connection = 'utf8mb4_bin'");
$driver = new mysqli_driver();
$driver->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;

# default values
$_view = 'list';
$_year = date('Y');
$_month = date('m');
$_day = date('d');
$_id = '';
$_requestDateProvided = '';
$TemplateData = array();

if(isset($_GET['y']) && Summoner::validate($_GET['y'], 'digit')) {
    $_year = trim($_GET['y']);
    $_requestDateProvided .= 'Y';
}
if(isset($_GET['m']) && Summoner::validate($_GET['m'], 'digit')) {
    $_month = trim($_GET['m']);
    $_requestDateProvided .= '-m';
}
if(isset($_GET['d']) && Summoner::validate($_GET['d'], 'digit')) {
    $_day = trim($_GET['d']);
    $_requestDateProvided .= '-d';
}
if(isset($_GET['p']) && Summoner::validate($_GET['p'], 'nospace') && $_GET['p'] == "new") {
    $_view = 'entry';
}
if(isset($_GET['id']) && Summoner::validate($_GET['id'], 'shortlink',4)) {
    $_id = trim($_GET['id']);
    $_view = 'entry';
}

require_once 'view/'.$_view.'/'.$_view.'.php';

# header information
header('Content-type: text/html; charset=UTF-8');
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");
if(isset($TemplateData['refresh']) && !empty($TemplateData['refresh'])) {
    header('Location: '.PATH_WEBROOT.$TemplateData['refresh']);
    exit();
}

require_once 'view/_head.php';
require_once 'view/'.$_view.'/'.$_view.'.html';
require_once 'view/_foot.php';

$DB->close();
