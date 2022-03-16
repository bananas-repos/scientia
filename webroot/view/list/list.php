<?php
/**
 * scientia
 *
 * Copyright 2021 Johannes Keßler
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

$TemplateData['entries'] = array();

$queryStr = "SELECT `e`.`ident`, `e`.`date`, SUBSTRING(`e`.`body`,1,100) AS body FROM `".DB_PREFIX."_entry` AS e";
$queryLimit = " LIMIT 100";

// why?
// mysql knows the dates and validates them. There is no 2020-02-31
// the single date infos come from index.php
$groupByFormat = $_year;
if(!empty($_requestDateProvided)) {
	$_intervalStart = '';
	$_intervalEnd = '';

	if($_requestDateProvided === 'ymd') {
		$queryLimit = "";
		$groupByFormat = $_year.'-'.$_month.'-'.$_day;
		$_intervalStart = $groupByFormat;
		$_intervalEnd = $groupByFormat;

	}
	elseif ($_requestDateProvided === 'ym') {
		$queryLimit = "";
		$groupByFormat = $_year.'-'.$_month;
		$_intervalStart = $groupByFormat.'-01';
		$_tDate = new DateTime( $_intervalStart );
		$_monthDays = $_tDate->format( 't' );
		$_intervalEnd = $groupByFormat.'-'.$_monthDays;
	}
	elseif ($_requestDateProvided === 'y') {
		$_intervalStart = $groupByFormat.'-01-01';
		$_intervalEnd = $groupByFormat.'-12-31';
	}

	if(!empty($_intervalStart) && !empty($_intervalEnd)) {
		$queryStr .= " WHERE `date` >= '".$_intervalStart."' AND `date` <= '".$_intervalEnd."'";
	}
}

$queryStr .= " ORDER BY `created` DESC";
$queryStr .= $queryLimit;
if(QUERY_DEBUG) error_log("[QUERY] query: ".var_export($queryStr,true));

try {
	$query = $DB->query($queryStr);
	if($query !== false && $query->num_rows > 0) {
		while(($result = $query->fetch_assoc()) != false) {
			$_d = new DateTime($result['date']);
			$TemplateData['entries'][$_d->format($groupByFormat)][$result['ident']] = $result;
			$TemplateData['entries'][$_d->format($groupByFormat)][$result['ident']]['link'] = str_replace('-','/',$result['date']).'/'.$result['ident'];
		}
	}
}
catch(Exception $e) {
	error_log("[ERROR] catch: ".$e->getMessage());
}
