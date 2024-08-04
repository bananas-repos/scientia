<?php
/**
 * scientia
 *
 * Copyright 2023 - 2024 Johannes Keßler
 *
 * https://www.bananas-playground.net/projekt/scientia/
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see http://www.gnu.org/licenses/gpl-3.0.
 */

/**
 * get endpoint. Accepts only GET and returns data
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

# validate key
if(!isset($_SERVER['HTTP_X_ASL']) || empty($_SERVER['HTTP_X_ASL']) || !isset(UPLOAD_SECRET[$_SERVER['HTTP_X_ASL']])) {
    header('X-PROVIDED-BY: scientia');
    http_response_code(400);
    exit();
}


$_requestMode = "list";
if(isset($_GET['p']) && !empty($_GET['p'])) {
    $_requestMode = trim($_GET['p']);
    $_requestMode = Summoner::validate($_requestMode,'nospace') ? $_requestMode : "list";
}
$_id = '';
if(isset($_GET['id']) && Summoner::validate($_GET['id'], 'shortlink',4)) {
    $_id = trim($_GET['id']);
    $_view = 'entry';
}

# default response
$contentType = 'Content-Type: application/json; charset=utf-8';
$httpResponseCode = 200;
$contentBody = array (
    'data' => array(),
    'message' => '',
    'status' => $httpResponseCode
);

## DB connection
$DB = new mysqli(DB_HOST, DB_USERNAME,DB_PASSWORD, DB_NAME);
if ($DB->connect_errno) exit('Can not connect to MySQL Server');
$DB->set_charset("utf8mb4");
$DB->query("SET collation_connection = 'utf8mb4_bin'");
$driver = new mysqli_driver();
$driver->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;

require_once 'lib/entry.class.php';
$Entry = new Entry($DB);

switch($_requestMode) {
    case "entry":
        if(!empty($_id)) {
            $contentBody['data'][] = $Entry->loadById($_id);
        }
    break;

    case "list":
    default:
        $contentBody['data'] = $Entry->list();
}

## return
header('X-PROVIDED-BY: scientia');
header($contentType);
http_response_code($httpResponseCode);
echo json_encode($contentBody);
$DB->close();
