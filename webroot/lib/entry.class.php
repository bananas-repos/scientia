<?php
/**
 * scientia
 *
 * Copyright 2022 - 2024 Johannes Keßler
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

/**
 * Class Entry
 *
 * Entry loading and creation
 */
class Entry {
    /**
     * the global DB object
     *
     * @var mysqli
     */
    private mysqli $_DB;

    /**
     * Entry constructor.
     *
     * @param mysqli $db
     */
    public function __construct(mysqli $db) {
        $this->_DB = $db;
    }

    /**
     * Create a new entry with given data
     * Data is not validated anymore
     *
     * @param string $data
     * @return string
     */
    public function create(string $data): string {
        $ret = '';

        $_words = implode(' ', $this->_words($data));
        $_ident = Summoner::b64sl_pack_id(rand(111111, 999999));
        $queryStr = "INSERT INTO `".DB_PREFIX."_entry` SET
                        `created` = NOW(),
                        `date` = CURRENT_DATE(),
                        `ident` = '".$this->_DB->real_escape_string($_ident)."',
                        `body` = '".$this->_DB->real_escape_string($data)."',
                        `words` = '".$this->_DB->real_escape_string($_words)."'";
        if(QUERY_DEBUG) error_log("[QUERY] ".__METHOD__." query: ".var_export($queryStr,true));

        try {
            $this->_DB->query($queryStr);
            $ret = $_ident;
        }
        catch(Exception $e) {
            error_log("[ERROR] ".__METHOD__." catch: ".$e->getMessage());
        }

        return $ret;
    }

    /**
     * Load an entry by given $id. Use date info to make sure that the context is correct
     *
     * @param string $y Year Y
     * @param string $m Month m
     * @param string $d Day d
     * @param string $id Id of the entry
     * @return array
     */
    public function load(string $y, string $m, string $d, string $id): array {
        $ret = array();

        if(!empty($id) && !empty($y) && !empty($m) && !empty($d)) {
            $queryStr = "SELECT `created`,`modified`,`body`
                            FROM `".DB_PREFIX."_entry`
                            WHERE `ident` = '".$this->_DB->real_escape_string($id)."'
                                AND `date` = '".$this->_DB->real_escape_string($y.'-'.$m.'-'.$d)."'";
            if(QUERY_DEBUG) error_log("[QUERY] ".__METHOD__." query: ".var_export($queryStr,true));
            try {
                $query = $this->_DB->query($queryStr);
                if($query !== false && $query->num_rows > 0) {
                    $ret = $query->fetch_assoc();
                }
            }
            catch(Exception $e) {
                error_log("[ERROR] ".__METHOD__." catch: ".$e->getMessage());
            }
        }

        return $ret;
    }

    /**
     * Update an entry by given $id and $data
     *
     * @param string $data
     * @param string $id
     * @return string
     */
    public function update(string $data, string $id): string {
        $ret = '';

        if(!empty($data) && !empty($id)) {
            $_words = implode(' ', $this->_words($data));
            $queryStr = "UPDATE `".DB_PREFIX."_entry` SET						
                            `body` = '".$this->_DB->real_escape_string($data)."',
                            `words` = '".$this->_DB->real_escape_string($_words)."'
                            WHERE `ident` = '".$this->_DB->real_escape_string($id)."'";
            if(QUERY_DEBUG) error_log("[QUERY] ".__METHOD__." query: ".var_export($queryStr,true));
            try {
                $this->_DB->query($queryStr);
                $ret = $id;
            }
            catch(Exception $e) {
                error_log("[ERROR] ".__METHOD__." catch: ".$e->getMessage());
            }
        }

        return $ret;
    }

    /**
     * Delete given id from _entry table
     *
     * @param string $id
     * @return bool
     */
    public function delete(string $id): bool {
        $ret = false;

        if(!empty($id)) {
            $queryStr = "DELETE FROM `".DB_PREFIX."_entry`
                            WHERE `ident` = '".$this->_DB->real_escape_string($id)."'";
            if(QUERY_DEBUG) error_log("[QUERY] ".__METHOD__." query: ".var_export($queryStr,true));
            try {
                $this->_DB->query($queryStr);
                $ret = true;
            }
            catch(Exception $e) {
                error_log("[ERROR] ".__METHOD__." catch: ".$e->getMessage());
            }
        }

        return $ret;
    }

    /**
     * Create unique words from the given data
     *
     * @param $data string
     * @return array
     * @todo ignores
     *
     */
    private function _words(string $data): array {
        preg_match_all('/\w{3,}+/u',$data,$matches);
        return array_unique($matches[0]);
    }
}
