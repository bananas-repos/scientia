<?php
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
