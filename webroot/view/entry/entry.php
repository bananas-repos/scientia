<?php

require_once 'lib/entry.class.php';
$Entry = new Entry($DB);

$TemplateData['data'] = '';
if(!empty($_id)) {
	$TemplateData['data'] = $Entry->load($_year,$_month,$_day,$_id);
}

if(isset($_POST['submitForm']) && isset($_POST['fdata'])) {
	$fdata = $_POST['fdata'];
	if(isset($fdata['entry']) && Summoner::validate($fdata['entry'])) {
		$_dataToSave = trim($fdata['entry']);

		if(!empty($_id)) {
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
