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

require_once 'lib/entry.class.php';
$Entry = new Entry($DB);

$TemplateData['data'] = array();
if(!empty($_id)) {
	$TemplateData['data'] = $Entry->load($_year,$_month,$_day,$_id);
	$TemplateData['data']['breadcrumb'] = array($_year,$_month,$_day);
}

if(isset($_POST['submitForm']) && isset($_POST['fdata'])) {
	$fdata = $_POST['fdata'];
	if(isset($fdata['entry']) && Summoner::validate($fdata['entry'])) {
		$_dataToSave = trim($fdata['entry']);

		if(!empty($_id) && isset($_POST['deleteEntry']) && $_POST['deleteEntry'] == "yes") {
			$do = $Entry->delete($_id);
			$_r = '/';
		}
		elseif(!empty($_id)) {
			$do = $Entry->update($_dataToSave,$_id);
			$_r = '/'.$_year.'/'.$_month.'/'.$_day.'/'.$_id;
		}
		else {
			$do = $Entry->create($_dataToSave);
			$_r = date('/Y/m/d/').$do;;
		}

		if($do !== false) {
			$TemplateData['refresh'] = $_r;
		}
	}
}
