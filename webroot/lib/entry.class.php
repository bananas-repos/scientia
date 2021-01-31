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
	private $_DB;

	/**
	 * Entry constructor.
	 *
	 * @param mysqli $db
	 */
	public function __construct($db) {
		$this->_DB = $db;
	}

	/**
	 * Create a new entry with given data
	 * Data is not validated anymore
	 *
	 * @param string $data
	 * @return mixed
	 */
	public function create($data) {
		$ret = false;

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
	 * @return array|null
	 */
	public function load($y, $m, $d, $id) {
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
	 * @param array $data
	 * @param string $id
	 * @return mixed
	 */
	public function update($data,$id) {
		$ret = false;

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
	 * Create unique words from the given data
	 *
	 * @todo ignores
	 *
	 * @param $data
	 * @return array
	 */
	private function _words($data) {
		preg_match_all('/\w{3,}+/',$data,$matches);
		return array_unique($matches[0]);
	}
}
