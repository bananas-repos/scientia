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
