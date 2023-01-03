<?php
/**
 * scientia
 *
 * Copyright 2023 Johannes Keßler
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

class I18n {
	/**
	 * @var string The lang code
	 */
	private string $_defaultLangToUse = 'en';

	/**
	 * @var array The loaded lang information from the file
	 */
	private array $_langData = array();

	/**
	 * i18n constructor.
	 */
	public function __construct() {
		$_langFile = PATH_ABSOLUTE.'/lib/i18n/'.$this->_defaultLangToUse.'.ini';
		if(defined('FRONTEND_LANGUAGE')) {
			$_langFile = PATH_ABSOLUTE.'/lib/i18n/'.FRONTEND_LANGUAGE.'.ini';
			if(file_exists($_langFile)) {
				$_langData = parse_ini_file($_langFile);
				if($_langData !== false) {
					$this->_langData = $_langData;
				}
			}
		}
		else {
			$_langData = parse_ini_file($_langFile);
			if($_langData !== false) {
				$this->_langData = $_langData;
			}
		}
	}

	/**
	 * Return text for given key for currently loaded lang
	 *
	 * @param string $key
	 * @return string
	 */
	public function t(string $key): string {
		$ret = $key;
		if(isset($this->_langData[$key])) {
			$ret = $this->_langData[$key];
		}
		return $ret;
	}
}
