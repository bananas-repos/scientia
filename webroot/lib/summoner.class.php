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
     * @param string $limit If int given the string is checked for length
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
    static function validate(string $input, string $mode='text', string $limit=''): bool {
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

            case 'shortlink':
                // special char string based on https://www.jwz.org/base64-shortlinks/
                $pattern = '/[\p{L}\p{N}\-_]/u';
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
    static function startsWith(string $haystack, string $needle): bool {
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
    static function endsWith(string $haystack, string $needle): bool {
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
    static function b64sl_pack_id(int $id): string {
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
     * @param string $id
     * @return int
     */
    static function b64sl_unpack_id(string $id): int {
        $id = str_replace ('-', '+', $id);		// decode URL-unsafe "+" "/"
        $id = str_replace ('_', '/', $id);
        $id = base64_decode ($id);
        while (strlen($id) < 8) { $id = "\000$id"; }	// pad with leading NULs
        $a = unpack ('N*', $id);			// 32 bit big endian
        $id = ($a[1] << 32) | $a[2];			// pack top and bottom word
        return $id;
    }

    /**
     * a very simple HTTP_AUTH authentication.
     * Needs FRONTEND_USERNAME and FRONTEND_PASSWORD defined
     */
    static function simpleAuth(): void {
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
