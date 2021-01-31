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
 * Class Summoner
 *
 * A static helper class
 */
class Summoner {
    /**
     * validate the given string with the given type. Optional check the string
     * length
     *
     * @param string $input The string to check
     * @param string $mode How the string should be checked
     * @param mixed $limit If int given the string is checked for length
     *
     * @return bool
     *
     * @see http://de.php.net/manual/en/regexp.reference.unicode.php
     * http://www.sql-und-xml.de/unicode-database/#pc
     *
     * the pattern replaces all that is allowed. the correct result after
     * the replace should be empty, otherwise are there chars which are not
     * allowed
     */
    static function validate($input,$mode='text',$limit=false) {
        // check if we have input
        $input = trim($input);

        if($input == "") return false;

        $ret = false;

        switch ($mode) {
            case 'mail':
                if(filter_var($input,FILTER_VALIDATE_EMAIL) === $input) {
                    return true;
                }
                else {
                    return false;
                }
			break;

            case 'url':
                if(filter_var($input,FILTER_VALIDATE_URL) === $input) {
                    return true;
                }
                else {
                    return false;
                }
			break;

            case 'nospace':
                // text without any whitespace and special chars
                $pattern = '/[\p{L}\p{N}]/u';
			break;

            case 'nospaceP':
                // text without any whitespace and special chars
                // but with Punctuation other
                # http://www.sql-und-xml.de/unicode-database/po.html
                $pattern = '/[\p{L}\p{N}\p{Po}\-]/u';
			break;

            case 'digit':
                // only numbers and digit
                // warning with negative numbers...
                $pattern = '/[\p{N}\-]/';
			break;

            case 'pageTitle':
                // text with whitespace and without special chars
                // but with Punctuation
                $pattern = '/[\p{L}\p{N}\p{Po}\p{Z}\s-]/u';
			break;

            # strange. the \p{M} is needed.. don't know why..
            case 'filename':
                $pattern = '/[\p{L}\p{N}\p{M}\-_\.\p{Zs}]/u';
			break;

            case 'text':
            default:
                $pattern = '/[\p{L}\p{N}\p{P}\p{S}\p{Z}\p{M}\s]/u';
        }

        $value = preg_replace($pattern, '', $input);

        if($value === "") {
            $ret = true;
        }

        if(!empty($limit)) {
            # isset starts with 0
            if(isset($input[$limit])) {
                # too long
                $ret = false;
            }
        }

        return $ret;
    }

    /**
     * check if a string starts with a given string
     *
     * @param string $haystack
     * @param string $needle
     * @return boolean
     */
    static function startsWith($haystack, $needle) {
        $length = strlen($needle);
        return (substr($haystack, 0, $length) === $needle);
    }

    /**
     * check if a string ends with a given string
     *
     * @param string $haystack
     * @param string $needle
     * @return boolean
     */
    static function endsWith($haystack, $needle) {
        $length = strlen($needle);
        if ($length == 0) {
            return true;
        }

        return (substr($haystack, -$length) === $needle);
    }


	/**
	 * create a short string based on a integer
	 *
	 * @see https://www.jwz.org/base64-shortlinks/
	 * @param int $id
	 * @return string
	 */
    static function b64sl_pack_id($id) {
    	error_log($id);
        $id = intval($id);
        $ida = ($id > 0xFFFFFFFF ? $id >> 32 : 0);	// 32 bit big endian, top
        $idb = ($id & 0xFFFFFFFF);			// 32 bit big endian, bottom
        $id = pack ('N', $ida) . pack ('N', $idb);
        $id = preg_replace('/^\000+/', '', "$id");	// omit high-order NUL bytes
        $id = base64_encode ($id);
        $id = str_replace ('+', '-', $id);		// encode URL-unsafe "+" "/"
        $id = str_replace ('/', '_', $id);
        $id = preg_replace ('/=+$/', '', $id);	// omit trailing padding bytes
        return $id;
    }

    /**
     * Decode a base64-encoded big-endian integer of up to 64 bits.
     *
     * @see https://www.jwz.org/base64-shortlinks/
     * @param int $id
     * @return false|int|string|string[]
     */
    static function b64sl_unpack_id($id) {
        $id = str_replace ('-', '+', $id);		// decode URL-unsafe "+" "/"
        $id = str_replace ('_', '/', $id);
        $id = base64_decode ($id);
        while (strlen($id) < 8) { $id = "\000$id"; }	// pad with leading NULs
        $a = unpack ('N*', $id);			// 32 bit big endian
        $id = ($a[1] << 32) | $a[2];			// pack top and bottom word
        return $id;
    }

	/**
	 * simulate the Null coalescing operator in php5
	 *
	 * this only works with arrays and checking if the key is there and echo/return it.
	 *
	 * http://php.net/manual/en/migration70.new-features.php#migration70.new-features.null-coalesce-op
	 *
	 * @param $array
	 * @param $key
	 * @return bool|mixed
	 */
	static function ifset($array,$key) {
		return isset($array[$key]) ? $array[$key] : false;
	}

	/**
	 * a very simple HTTP_AUTH authentication.
	 * Needs FRONTEND_USERNAME and FRONTEND_PASSWORD defined
	 */
	static function simpleAuth() {
		if (!isset($_SERVER['PHP_AUTH_USER']) || !isset($_SERVER['PHP_AUTH_PW'])
			|| $_SERVER['PHP_AUTH_USER'] !== FRONTEND_USERNAME || $_SERVER['PHP_AUTH_PW'] !== FRONTEND_PASSWORD
		) {
			header('WWW-Authenticate: Basic realm="Protected area"');
			header('HTTP/1.0 401 Unauthorized');
			echo 'No Access.';
			exit;
		}
	}
}
