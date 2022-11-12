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

$queryStr = "SELECT e.ident, e.date, e.words, SUBSTRING(e.body,1,100) AS body FROM `".DB_PREFIX."_entry` AS e";
$queryLimit = " LIMIT 100";

$searchTerm = '';
if(isset($_POST['submitForm']) && isset($_POST['searchInput'])) {
	if(Summoner::validate($_POST['searchInput'])) {
		$searchTerm = trim($_POST['searchInput']);
	}
}

// why?
// mysql knows the dates and validates them. There is no 2020-02-31
// the single date infos come from index.php
$_groupByFormat = $_year;
$breadcrumb = array('Y');
if(!empty($_requestDateProvided)) {
	$_intervalStart = '';
	$_intervalEnd = '';

	if($_requestDateProvided === 'Y-m-d') {
		$queryLimit = "";
		$_groupByFormat = $_year.'-'.$_month.'-'.$_day;
		$_intervalStart = $_groupByFormat;
		$_intervalEnd = $_groupByFormat;
		$breadcrumb = array('Y','m','d');
	}
	elseif ($_requestDateProvided === 'Y-m') {
		$queryLimit = "";
		$_groupByFormat = $_year.'-'.$_month;
		$_intervalStart = $_groupByFormat.'-01';
		$_tDate = new DateTime( $_intervalStart );
		$_monthDays = $_tDate->format( 't' );
		$_intervalEnd = $_groupByFormat.'-'.$_monthDays;
		$breadcrumb = array('Y','m');
	}
	elseif ($_requestDateProvided === 'Y') {
		$_intervalStart = $_groupByFormat.'-01-01';
		$_intervalEnd = $_groupByFormat.'-12-31';
	}

	if(!empty($_intervalStart) && !empty($_intervalEnd)) {
		$queryStr .= " WHERE e.date >= '".$_intervalStart."' AND e.date <= '".$_intervalEnd."'";
		if(!empty($searchTerm)) {
			$queryStr .= " AND MATCH(e.words) AGAINST('".$DB->real_escape_string($searchTerm)."' IN BOOLEAN MODE)";
		}
	}
} else {
	$_requestDateProvided = 'Y';
	if(!empty($searchTerm)) {
		$queryStr .= " WHERE MATCH(e.words) AGAINST('".$DB->real_escape_string($searchTerm)."' IN BOOLEAN MODE)";
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
			$_breadcrumb = array();
			foreach($breadcrumb as $_b) {
				$_breadcrumb[] = $_d->format($_b);
			}
			$TemplateData['entries'][$_d->format($_requestDateProvided)]['breadcrumb'] = $_breadcrumb;
			$TemplateData['entries'][$_d->format($_requestDateProvided)]['e'][$result['ident']] = $result;
			$TemplateData['entries'][$_d->format($_requestDateProvided)]['e'][$result['ident']]['link'] = str_replace('-','/',$result['date']).'/'.$result['ident'];
		}
	}
}
catch(Exception $e) {
	error_log("[ERROR] catch: ".$e->getMessage());
}
