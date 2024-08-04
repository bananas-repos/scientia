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
