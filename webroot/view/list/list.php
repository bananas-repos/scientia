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
$groupByFormat = $_date->format('Y');
if(!empty($_requestDateProvided)) {
	$_intervalStart = false;
	$_intervalEnd = false;

	if($_requestDateProvided === 'ymd') {
		$_intervalStart = new DateInterval('P1D');
		$_intervalEnd = new DateInterval('P2D');
		$queryLimit = "";
		$groupByFormat = $_date->format('Y-m-d');
	}
	elseif ($_requestDateProvided === 'ym') {
		$_intervalStart = new DateInterval('P1M');
		$_intervalEnd = new DateInterval('P2M');
		$queryLimit = "";
		$groupByFormat = $_date->format('Y-m');
	}
	elseif ($_requestDateProvided === 'y') {
		$_intervalStart = new DateInterval('P1Y');
		$_intervalEnd = new DateInterval('P2Y');
	}

	if(!empty($_intervalStart) && !empty($_intervalEnd)) {

		$_date->sub($_intervalStart);
		$_f = $_date->format("Y-m-d");
		$_date->add($_intervalEnd);
		$_e = $_date->format("Y-m-d");

		$queryStr .= " WHERE `date` >= '".$_f."' AND `date` <= '".$_e."'";
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
