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
