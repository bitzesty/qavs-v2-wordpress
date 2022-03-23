<?php

/**
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\Libs\Snap;

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class SnapUtil
{

    public static function getArrayValue(&$array, $key, $required = true, $default = null)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        } else {
            if ($required) {
                throw new \Exception("Key {$key} not present in array");
            } else {
                return $default;
            }
        }
    }

    /**
     * Gets the calling function name from where this method is called
     *
     * @return  string   Returns the calling function name from where this method is called
     */
    public static function getCallingFunctionName($backTraceBack = 0)
    {
        $callers     = debug_backtrace();
        $backTraceL1 = 1 + $backTraceBack;
        $backTraceL2 = 2 + $backTraceBack;
        $result      = '[' . str_pad(basename($callers[$backTraceL1]['file']), 25, '_', STR_PAD_RIGHT) . ':' . str_pad($callers[$backTraceL1]['line'], 4, ' ', STR_PAD_LEFT) . ']';
        if (isset($callers[$backTraceL2]) && (isset($callers[$backTraceL2]['class']) || isset($callers[$backTraceL2]['function']))) {
            $result .= ' [';
            $result .= isset($callers[$backTraceL2]['class']) ? $callers[$backTraceL2]['class'] . '::' : '';
            $result .= isset($callers[$backTraceL2]['function']) ? $callers[$backTraceL2]['function'] : '';
            $result .= ']';
        }

        return str_pad($result, 80, '_', STR_PAD_RIGHT);
    }

    public static function getWorkPercent($startingPercent, $endingPercent, $totalTaskCount, $currentTaskCount)
    {
        if ($totalTaskCount > 0) {
            $percent = $startingPercent + (($endingPercent - $startingPercent) * ($currentTaskCount / (float) $totalTaskCount));
        } else {
            $percent = $startingPercent;
        }

        return min(max($startingPercent, $percent), $endingPercent);
    }

    /**
     *
     * @staticvar bool $isPHP7Plus
     * @return bool
     */
    public static function isPHP7Plus()
    {
        static $isPHP7Plus = null;
        if (is_null($isPHP7Plus)) {
            $isPHP7Plus = version_compare(PHP_VERSION, '7.0.0', '>=');
        }
        return $isPHP7Plus;
    }

    /**
     * Groups an array into arrays by a given key, or set of keys, shared between all array members.
     *
     * Based on {@author Jake Zatecky}'s {@link https://github.com/jakezatecky/array_group_by array_group_by()} function.
     * This variant allows $key to be closures.
     *
     * @param array $array   The array to have grouping performed on.
     * @param mixed $key,... The key to group or split by. Can be a _string_, an _integer_, a _float_, or a _callable_.
     *                       - If the key is a callback, it must return a valid key from the array.
     *                       - If the key is _NULL_, the iterated element is skipped.
     *                       - string|int callback ( mixed $item )
     *
     * @return array|null Returns a multidimensional array or `null` if `$key` is invalid.
     */
    public static function arrayGroupBy(array $array, $key)
    {
        if (!is_string($key) && !is_int($key) && !is_float($key) && !is_callable($key)) {
            trigger_error('array_group_by(): The key should be a string, an integer, or a callback', E_USER_ERROR);
            return null;
        }
        $func    = (!is_string($key) && is_callable($key) ? $key : null);
        $_key    = $key;
        // Load the new array, splitting by the target key
        $grouped = array();
        foreach ($array as $value) {
            $key = null;
            if (is_callable($func)) {
                $key = call_user_func($func, $value);
            } elseif (is_object($value) && isset($value->{$_key})) {
                $key = $value->{$_key};
            } elseif (isset($value[$_key])) {
                $key = $value[$_key];
            }
            if ($key === null) {
                continue;
            }
            $grouped[$key][] = $value;
        }
        // Recursively build a nested grouping if more parameters are supplied
        // Each grouped array value is grouped according to the next sequential key
        if (func_num_args() > 2) {
            $args = func_get_args();
            foreach ($grouped as $key => $value) {
                $params        = array_merge(array($value), array_slice($args, 2, func_num_args()));
                $grouped[$key] = call_user_func_array(array(__CLASS__, 'arrayGroupBy'), $params);
            }
        }
        return $grouped;
    }

    /**
     * Converts human readable types (10GB) to bytes
     *
     * @param string $from   A human readable byte size such as 100MB
     *
     * @return int  Returns and integer of the byte size
     */
    public static function convertToBytes($from)
    {
        if (is_numeric($from)) {
            return $from;
        }

        $number = substr($from, 0, -2);
        switch (strtoupper(substr($from, -2))) {
            case "KB":
                return $number * 1024;
            case "MB":
                return $number * pow(1024, 2);
            case "GB":
                return $number * pow(1024, 3);
            case "TB":
                return $number * pow(1024, 4);
            case "PB":
                return $number * pow(1024, 5);
        }

        $number = substr($from, 0, -1);
        switch (strtoupper(substr($from, -1))) {
            case "K":
                return $number * 1024;
            case "M":
                return $number * pow(1024, 2);
            case "G":
                return $number * pow(1024, 3);
            case "T":
                return $number * pow(1024, 4);
            case "P":
                return $number * pow(1024, 5);
        }
        return $from;
    }

    /**
     *  Sanitize input for XSS code
     *
     *  @param string $val      The value to sanitize
     *
     *  @return string Returns the input value cleaned up.
     */
    public static function sanitize($input)
    {
        return filter_var($input, FILTER_SANITIZE_STRING);
    }

    /**
     * remove all non stamp chars from string
     *
     * @param string $string
     * @return string
     */
    public static function sanitizeNSChars($string)
    {
        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F]/u', '', $string);
    }

    /**
     * remove all non stamp chars from string and newline
     * trim string
     *
     * @param string $string
     * @return string
     */
    public static function sanitizeNSCharsNewline($string)
    {
        return preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F-\x9F\r\n]/u', '', $string);
    }

    /**
     * remove all non stamp chars from string and newline
     * trim string
     *
     * @param string $string
     * @return string
     */
    public static function sanitizeNSCharsNewlineTrim($string)
    {
        return trim(self::sanitizeNSCharsNewline($string));
    }

    /**
     * Determines whether a PHP ini value is changeable at runtime.
     *
     * @since 4.6.0
     *
     * @staticvar array $ini_all
     *
     * @link https://secure.php.net/manual/en/function.ini-get-all.php
     *
     * @param string $setting The name of the ini setting to check.
     * @return bool True if the value is changeable at runtime. False otherwise.
     */
    public static function isIniValChangeable($setting)
    {
        // if ini_set is disabled can change the values
        if (!function_exists('ini_set')) {
            return false;
        }

        if (function_exists('isIniValChangeable')) {
            return isIniValChangeable($setting);
        }

        static $ini_all;

        if (!isset($ini_all)) {
            $ini_all = false;
            // Sometimes `ini_get_all()` is disabled via the `disable_functions` option for "security purposes".
            if (function_exists('ini_get_all')) {
                $ini_all = ini_get_all();
            }
        }

        // Bit operator to workaround https://bugs.php.net/bug.php?id=44936 which changes access level to 63 in PHP 5.2.6 - 5.2.17.
        if (isset($ini_all[$setting]['access']) && ( INI_ALL === ( $ini_all[$setting]['access'] & 7 ) || INI_USER === ( $ini_all[$setting]['access'] & 7 ) )) {
            return true;
        }

        // If we were unable to retrieve the details, fail gracefully to assume it's changeable.
        if (!is_array($ini_all)) {
            return true;
        }

        return false;
    }

    /**
     * The val value returns if it is between min and max otherwise it returns min or max
     *
     * @param int $val
     * @param int $min
     * @param int $max
     * @return int
     */
    public static function getIntBetween($val, $min, $max)
    {
        return min((int) $max, max((int) $min, (int) $val));
    }

    /**
     * Find matching string from $strArr1 and $strArr2 until first numeric occurence
     *
     * @param array   $strArr1                  array of strings
     * @param array   $strArr2                  array of strings
     * @return string matching str which will be best for replacement
     */
    public static function getMatchingStrFromArrayElemsUntilFirstNumeric($strArr1, $strArr2)
    {
        $matchingStr   = '';
        $strPartialArr = array();
        foreach ($strArr1 as $str1) {
            $str1_str_length     = strlen($str1);
            $tempStr1Chars       = str_split($str1);
            $tempPartialStr      = '';
            // The flag is for whether non-numeric character passed after numeric character occurence in str1. For ex. str1 is utf8mb4, the flag wil be true when parsing m after utf8.
            $numericCharPassFlag = false;
            $charPositionInStr1  = 0;
            while ($charPositionInStr1 < $str1_str_length) {
                if ($numericCharPassFlag && !is_numeric($tempStr1Chars[$charPositionInStr1])) {
                    break;
                }
                if (is_numeric($tempStr1Chars[$charPositionInStr1])) {
                    $numericCharPassFlag = true;
                }
                $tempPartialStr .= $tempStr1Chars[$charPositionInStr1];
                $charPositionInStr1++;
            }
            $strPartialArr[] = $tempPartialStr;
        }
        foreach ($strPartialArr as $strPartial) {
            if (!empty($matchingStr)) {
                break;
            }
            foreach ($strArr2 as $str2) {
                if (0 === stripos($str2, $strPartial)) {
                    $matchingStr = $str2;
                    break;
                }
            }
        }

        return $matchingStr;
    }

    /**
     * Find matching string from $strArr1 and $strArr2
     *
     * @param array   $strArr1                  array of strings
     * @param array   $strArr2                  array of strings
     * @param boolean $match_until_first_numeric only match until first numeric occurrence
     * @return string matching str which will be best for replacement
     */
    public static function getMatchingStrFromArrayElemsBasedOnUnderScore($strArr1, $strArr2)
    {
        $matchingStr = '';

        $str1PartialFirstArr        = array();
        $str1PartialFirstArr        = array();
        $str1PartialStartNMiddleArr = array();
        $str1PartialMiddleNLastArr  = array();
        $str1PartialLastArr         = array();
        foreach ($strArr1 as $str1) {
            $str1PartialArr        = explode('_', $str1);
            $str1_parts_count      = count($str1PartialArr);
            $str1PartialFirstArr[] = $str1PartialArr[0];
            $str1LastPartIndex     = $str1_parts_count - 1;
            if ($str1LastPartIndex > 0) {
                $str1PartialLastArr[]         = $str1PartialArr[$str1LastPartIndex];
                $str1PartialStartNMiddleArr[] = substr($str1, 0, strripos($str1, '_'));
                $str1PartialMiddleNLastArr[]  = substr($str1, stripos($str1, '_') + 1);
            }
        }
        for ($caseNo = 1; $caseNo <= 5; $caseNo++) {
            if (!empty($matchingStr)) {
                break;
            }
            foreach ($strArr2 as $str2) {
                switch ($caseNo) {
                    // Both Start and End match
                    case 1:
                        $str2PartialArr    = explode('_', $str2);
                        $str2FirstPart     = $str2PartialArr[0];
                        $str2PartsCount    = count($str2PartialArr);
                        $str2LastPartIndex = $str2PartsCount - 1;
                        if ($str2LastPartIndex > 0) {
                            $str2LastPart = $str2PartialArr[$str2LastPartIndex];
                        } else {
                            $str2LastPart = '';
                        }
                        if (!empty($str2LastPart) && !empty($str1PartialLastArr) && in_array($str2FirstPart, $str1PartialFirstArr) && in_array($str2LastPart, $str1PartialLastArr)) {
                            $matchingStr = $str2;
                        }
                        break;
                    // Start Middle Match
                    case 2:
                        $str2PartialFirstNMiddleParts = substr($str2, 0, strripos($str2, '_'));
                        if (in_array($str2PartialFirstNMiddleParts, $str1PartialStartNMiddleArr)) {
                            $matchingStr = $str2;
                        }
                        break;
                    // End Middle Match
                    case 3:
                        $str2PartialMiddleNLastParts = stripos($str2, '_') !== false ? substr($str2, stripos($str2, '_') + 1) : '';
                        if (!empty($str2PartialMiddleNLastParts) && in_array($str2PartialMiddleNLastParts, $str1PartialMiddleNLastArr)) {
                            $matchingStr = $str2;
                        }
                        break;
                    // Start Match
                    case 4:
                        $str2PartialArr = explode('_', $str2);
                        $str2FirstPart  = $str2PartialArr[0];
                        if (in_array($str2FirstPart, $str1PartialFirstArr)) {
                            $matchingStr = $str2;
                        }
                        break;
                    // End Match
                    case 5:
                        $str2PartialArr    = explode('_', $str2);
                        $str2PartsCount    = count($str2PartialArr);
                        $str2LastPartIndex = $str2PartsCount - 1;
                        if ($str2LastPartIndex > 0) {
                            $str2LastPart = $str2PartialArr[$str2LastPartIndex];
                        } else {
                            $str2LastPart = '';
                        }
                        if (!empty($str2LastPart) && in_array($str2LastPart, $str1PartialLastArr)) {
                            $matchingStr = $str2;
                        }
                        break;
                }
                if (!empty($matchingStr)) {
                    break;
                }
            }
        }
        return $matchingStr;
    }

    /**
     * Gets a specific external variable by name and optionally filters it
     * @param string $variable_name <p>Name of a variable to get.</p>
     * @param int $filter <p>The ID of the filter to apply. The Types of filters manual page lists the available filters.</p> <p>If omitted, <b><code>FILTER_DEFAULT</code></b> will be used, which is equivalent to <b><code>FILTER_UNSAFE_RAW</code></b>. This will result in no filtering taking place by default.</p>
     * @param mixed $options <p>Associative array of options or bitwise disjunction of flags. If filter accepts options, flags can be provided in "flags" field of array.</p>
     * @return mixed <p>Value of the requested variable on success, <b><code>FALSE</code></b> if the filter fails, or <b><code>NULL</code></b> if the <code>variable_name</code> variable is not set. If the flag <b><code>FILTER_NULL_ON_FAILURE</code></b> is used, it returns <b><code>FALSE</code></b> if the variable is not set and <b><code>NULL</code></b> if the filter fails.</p>
     * @link http://php.net/manual/en/function.filter-input.php
     * @see filter_var(), filter_input_array(), filter_var_array()
     * @since PHP 5 >= 5.2.0, PHP 7
     */
    public static function filterInputRequest($variable_name, $filter = FILTER_DEFAULT, $options = null)
    {
        if (isset($_GET[$variable_name]) && !isset($_POST[$variable_name])) {
            return filter_input(INPUT_GET, $variable_name, $filter, $options);
        }

        return filter_input(INPUT_POST, $variable_name, $filter, $options);
    }

    /**
     * Gets external variables and optionally filters them
     * <p>This function is useful for retrieving many values without repetitively calling <code>filter_input()</code>.</p>
     * @param mixed $definition <p>An array defining the arguments. A valid key is a <code>string</code> containing a variable name and a valid value is either a filter type, or an <code>array</code> optionally specifying the filter, flags and options. If the value is an array, valid keys are <i>filter</i> which specifies the filter type, <i>flags</i> which specifies any flags that apply to the filter, and <i>options</i> which specifies any options that apply to the filter. See the example below for a better understanding.</p> <p>This parameter can be also an integer holding a filter constant. Then all values in the input array are filtered by this filter.</p>
     * @param bool $add_empty <p>Add missing keys as <b><code>NULL</code></b> to the return value.</p>
     * @return mixed <p>An array containing the values of the requested variables on success. If the input array designated by <code>type</code> is not populated, the function returns <b><code>NULL</code></b> if the <b><code>FILTER_NULL_ON_FAILURE</code></b> flag is not given, or <b><code>FALSE</code></b> otherwise. For other failures, <b><code>FALSE</code></b> is returned.</p><p>An array value will be <b><code>FALSE</code></b> if the filter fails, or <b><code>NULL</code></b> if the variable is not set. Or if the flag <b><code>FILTER_NULL_ON_FAILURE</code></b> is used, it returns <b><code>FALSE</code></b> if the variable is not set and <b><code>NULL</code></b> if the filter fails. If the <code>add_empty</code> parameter is <b><code>FALSE</code></b>, no array element will be added for unset variables.</p>
     * @link http://php.net/manual/en/function.filter-input-array.php
     * @see filter_input(), filter_var_array()
     * @since PHP 5 >= 5.2.0, PHP 7
     */
    public static function filterInputRequestArray($definition = null, $add_empty = true)
    {
        if (!is_array($definition) || count($definition) === 0) {
            return array();
        }
        $getKeys  = is_array($_GET) ? array_keys($_GET) : array();
        $postKeys = is_array($_POST) ? array_keys($_POST) : array();
        $keys     = array_keys($definition);

        if (count(array_intersect($keys, $getKeys)) && !count(array_intersect($keys, $postKeys))) {
            $type = INPUT_GET;
        } else {
            $type = INPUT_POST;
        }

        $result = filter_input_array($type, $definition, $add_empty);

        if (!is_array($result)) {
            $result = array();
            foreach ($keys as $key) {
                $result[$key] = null;
            }
        }
        return $result;
    }

    /**
     * Implemented array_key_first
     *
     * @link https://www.php.net/manual/en/function.array-key-first.php
     * @param array $arr
     * @return int|string|null
     */
    public static function arrayKeyFirst($arr)
    {
        if (!function_exists('array_key_first')) {
            foreach ($arr as $key => $unused) {
                return $key;
            }
            return null;
        } else {
            return array_key_first($arr);
        }
    }

    /**
     * Get number of bit supported by PHP
     *
     * @return string
     */
    public static function getArchitectureString()
    {
        return (PHP_INT_SIZE * 8) . '-bit';
    }

    public static function inArrayExtended($haystack, $callback)
    {
        if (!is_callable($callback)) {
            return null;
        }

        foreach ($haystack as $value) {
            if (call_user_func($callback, $value)) {
                return true;
            }
        }

        return false;
    }
}
