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
