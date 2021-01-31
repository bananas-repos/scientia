<?php
/**
 * scientia
 *
 * Copyright 2021 Johannes Keßler
 *
 * https://www.bananas-playground.net/projekt/scientia/
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
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

Summoner::simpleAuth();

# database object
$DB = false;

## DB connection
$DB = new mysqli(DB_HOST, DB_USERNAME,DB_PASSWORD, DB_NAME);
if ($DB->connect_errno) exit('Can not connect to MySQL Server');
$DB->set_charset("utf8mb4");
$DB->query("SET collation_connection = 'utf8mb4_unicode_ci'");
$driver = new mysqli_driver();
$driver->report_mode = MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT;

# default values
$_view = 'list';
$_year = date('Y');
$_month = date('m');
$_day = date('d');
$_id = false;
$_requestDateProvided = '';
$TemplateData = array();

if(isset($_GET['y']) && Summoner::validate($_GET['y'], 'digit')) {
	$_year = trim($_GET['y']);
	$_requestDateProvided .= 'y';
}
if(isset($_GET['m']) && Summoner::validate($_GET['m'], 'digit')) {
	$_month = trim($_GET['m']);
	$_requestDateProvided .= 'm';
}
if(isset($_GET['d']) && Summoner::validate($_GET['d'], 'digit')) {
	$_day = trim($_GET['d']);
	$_requestDateProvided .= 'd';
}
if(isset($_GET['p']) && Summoner::validate($_GET['p'], 'nospace') && $_GET['p'] == "new") {
	$_view = 'entry';
}
if(isset($_GET['id']) && Summoner::validate($_GET['id'], 'nospace',4)) {
	$_id = trim($_GET['id']);
	$_view = 'entry';
}

try {
	$_date = new DateTime("$_year-$_month-$_day");
} catch (Exception $e) {
	$_date = new DateTime();
}

require_once 'view/'.$_view.'/'.$_view.'.php';

# header information
header('Content-type: text/html; charset=UTF-8');
if(isset($TemplateData['refresh']) && !empty($TemplateData['refresh'])) {
	header('Location: '.PATH_WEBROOT.$TemplateData['refresh']);
	exit();
}

require_once 'view/_head.php';
require_once 'view/'.$_view.'/'.$_view.'.html';
require_once 'view/_foot.php';

$DB->close();
