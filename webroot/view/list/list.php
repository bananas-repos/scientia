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

$TemplateData['entries'] = array();

require_once 'lib/entry.class.php';
$Entry = new Entry($DB);

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
$_intervalStart = '';
$_intervalEnd = '';
if(!empty($_requestDateProvided)) {
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
} else {
    $_requestDateProvided = 'Y';
}

$entries = $Entry->list($searchTerm, $_intervalStart, $_intervalEnd);
foreach($entries as $k=>$entry) {
    $_d = new DateTime($entry['date']);
    $_breadcrumb = array();
    foreach($breadcrumb as $_b) {
        $_breadcrumb[] = $_d->format($_b);
    }
    $TemplateData['entries'][$_d->format($_requestDateProvided)]['breadcrumb'] = $_breadcrumb;
    $TemplateData['entries'][$_d->format($_requestDateProvided)]['e'][$entry['ident']] = $entry;
    $TemplateData['entries'][$_d->format($_requestDateProvided)]['e'][$entry['ident']]['link'] = str_replace('-','/',$entry['date']).'/'.$entry['ident'];
}
