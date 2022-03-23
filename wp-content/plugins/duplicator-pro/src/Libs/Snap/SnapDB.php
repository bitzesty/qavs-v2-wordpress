<?php

/**
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\Libs\Snap;

/**
 * old: SnapDB
 */
class SnapDB
{
    const CONN_MYSQL                      = 'mysql';
    const CONN_MYSQLI                     = 'mysqli';
    const CACHE_PREFIX_PRIMARY_KEY_COLUMN = 'pkcol_';
    const DB_ENGINE_MYSQL                 = 'MySQL';
    const DB_ENGINE_MARIA                 = 'MariaDB';
    const DB_ENGINE_PERCONA               = 'Percona';

    private static $cache = array();

    /**
     * Undocumented function
     *
     * @param mysqli|resource $dbh         database connection
     * @param string          $tableName   table name
     * @param null|callable   $logCallback log callback
     *
     * @return string|string[] // return array if primary key is composite key
     */
    public static function getUniqueIndexColumn($dbh, $tableName, $logCallback = null)
    {
        $cacheKey = self::CACHE_PREFIX_PRIMARY_KEY_COLUMN . $tableName;

        if (!isset(self::$cache[$cacheKey])) {
            $query  = 'SHOW COLUMNS FROM `' . self::realEscapeString($dbh, $tableName) . '` WHERE `Key` IN ("PRI","UNI")';
            if (($result = self::query($dbh, $query)) === false) {
                if (is_callable($logCallback)) {
                    call_user_func($logCallback, $dbh, $result, $query);
                }
                throw new \Exception('SHOW KEYS QUERY ERROR: ' . self::error($dbh));
            }

            if (is_callable($logCallback)) {
                call_user_func($logCallback, $dbh, $result, $query);
            }

            if (self::numRows($result) == 0) {
                self::$cache[$cacheKey] = false;
            } else {
                $primary        = false;
                $excludePrimary = false;
                $unique         = false;

                while ($row = self::fetchAssoc($result)) {
                    switch ($row['Key']) {
                        case 'PRI':
                            if ($primary === false) {
                                $primary = $row['Field'];
                            } else {
                                if (is_scalar($primary)) {
                                    $primary = array($primary);
                                }
                                $primary[] = $row['Field'];
                            }

                            if (preg_match('/^(?:var)?binary/i', $row['Type'])) {
                                // exclude binary or varbynary columns
                                $excludePrimary = true;
                            }
                            break;
                        case 'UNI':
                            if (!preg_match('/^(?:var)?binary/i', $row['Type'])) {
                                // exclude binary or varbynary columns
                                $unique = $row['Field'];
                            }
                            break;
                        default:
                            break;
                    }
                }
                if ($primary !== false && $excludePrimary === false) {
                    self::$cache[$cacheKey] = $primary;
                } elseif ($unique !== false) {
                    self::$cache[$cacheKey] = $unique;
                } else {
                    self::$cache[$cacheKey] = false;
                }
            }
            self::freeResult($result);
        }

        return self::$cache[$cacheKey];
    }

    /**
     *
     * @param  array           $row
     * @param  string|string[] $indexColumns
     *
     * @return string|string[]
     */
    public static function getOffsetFromRowAssoc($row, $indexColumns, $lastOffset)
    {
        if (is_array($indexColumns)) {
            $result = array();
            foreach ($indexColumns as $col) {
                $result[$col] = isset($row[$col]) ? $row[$col] : 0;
            }
            return $result;
        } elseif (strlen($indexColumns) > 0) {
            return isset($row[$indexColumns]) ? $row[$indexColumns] : 0;
        } else {
            return $lastOffset + 1;
        }
    }

    /**
     * This function performs a select by structuring the primary key as offset if the table has a primary key.
     * For optimization issues, no checks are performed on the input query and it is assumed that the select has at least a where value.
     * If there are no conditions, you still have to perform an always true condition, for example
     * SELECT * FROM `copy1_postmeta` WHERE 1
     *
     * @param \mysqli|resource $dbh database  connection
     * @param string           $query         query string
     * @param string           $table         table name
     * @param int              $offset        row offset
     * @param int              $limit         limit of query, 0 no limit
     * @param mixed            $lastRowOffset last offset to use on next function call
     * @param null|callable    $logCallback   log callback
     *
     * @return mysqli_result
     * @throws \Exception // exception on query fail
     */
    public static function selectUsingPrimaryKeyAsOffset($dbh, $query, $table, $offset, $limit, &$lastRowOffset = null, $logCallback = null)
    {
        $where     = '';
        $orderby   = '';
        $offsetStr = '';
        $limitStr  = $limit > 0 ? ' LIMIT ' . $limit : '';

        if (($primaryColumn = self::getUniqueIndexColumn($dbh, $table, $logCallback)) == false) {
            $offsetStr = ' OFFSET ' . (is_scalar($offset) ? $offset : 0);
        } else {
            if (is_array($primaryColumn)) {
                // COMPOSITE KEY
                $orderByCols = array();
                foreach ($primaryColumn as $colIndex => $col) {
                    $orderByCols[] = '`' . $col . '` ASC';
                }
                $orderby = ' ORDER BY ' . implode(',', $orderByCols);
            } else {
                $orderby = ' ORDER BY `' . $primaryColumn . '` ASC';
            }
            $where = self::getOffsetKeyCondition($dbh, $primaryColumn, $offset);
        }
        $query .= $where . $orderby . $limitStr . $offsetStr;

        if (($result = self::query($dbh, $query)) === false) {
            if (is_callable($logCallback)) {
                call_user_func($logCallback, $dbh, $result, $query);
            }
            throw new \Exception('SELECT ERROR: ' . self::error($dbh) . ' QUERY: ' . $query);
        }

        if (is_callable($logCallback)) {
            call_user_func($logCallback, $dbh, $result, $query);
        }

        if (self::dbConnTypeByResult($result) === self::CONN_MYSQLI) {
            if ($primaryColumn == false) {
                $lastRowOffset = $offset + $result->num_rows;
            } else {
                if ($result->num_rows == 0) {
                    $lastRowOffset = $offset;
                } else {
                    $result->data_seek(($result->num_rows - 1));
                    $row = $result->fetch_assoc();
                    if (is_array($primaryColumn)) {
                        $lastRowOffset = array();
                        foreach ($primaryColumn as $col) {
                            $lastRowOffset[$col] = $row[$col];
                        }
                    } else {
                        $lastRowOffset = $row[$primaryColumn];
                    }
                    $result->data_seek(0);
                }
            }
        } else {
            if ($primaryColumn == false) {
                $lastRowOffset = $offset + mysql_num_rows($result);
            } else {
                if (mysql_num_rows($result) == 0) {
                    $lastRowOffset = $offset;
                } else {
                    mysql_data_seek($result, (mysql_num_rows($result) - 1));
                    $row = mysql_fetch_assoc($result);
                    if (is_array($primaryColumn)) {
                        $lastRowOffset = array();
                        foreach ($primaryColumn as $col) {
                            $lastRowOffset[$col] = $row[$col];
                        }
                    } else {
                        $lastRowOffset = $row[$primaryColumn];
                    }
                    mysql_data_seek($result, 0);
                }
            }
        }

        return $result;
    }

    /**
     * Depending on the structure type of the primary key returns the condition to position at the right offset
     *
     * @param \mysqli|resource $dbh
     * @param string|string[] $primaryColumn
     * @param mixed $offset
     * @return string
     */
    protected static function getOffsetKeyCondition($dbh, $primaryColumn, $offset)
    {
        $condition = '';

        if ($offset === 0) {
            return '';
        }

        // COUPOUND KEY
        if (is_array($primaryColumn)) {
            $isFirstCond = true;

            foreach ($primaryColumn as $colIndex => $col) {
                if (is_array($offset) && isset($offset[$col])) {
                    if ($isFirstCond) {
                        $isFirstCond = false;
                    } else {
                        $condition .= ' OR ';
                    }
                    $condition .= ' (';
                    for ($prevColIndex = 0; $prevColIndex < $colIndex; $prevColIndex++) {
                        $condition .= ' `' . $primaryColumn[$prevColIndex] . '` = "' . self::realEscapeString($dbh, $offset[$primaryColumn[$prevColIndex]]) . '" AND ';
                    }
                    $condition .= ' `' . $col . '` > "' . self::realEscapeString($dbh, $offset[$col]) . '")';
                }
            }
        } else {
            $condition = '`' . $primaryColumn . '` > "' . self::realEscapeString($dbh, (is_scalar($offset) ? $offset : 0)) . '"';
        }

        return (strlen($condition) ? ' AND (' . $condition . ')' : '');
    }

    /**
     * get current database engine (mysql, maria, percona)
     * @param \mysqli|resource $dbh
     *
     * @return string
     */
    public static function getDBEngine($dbh)
    {
        if (($result = self::query($dbh, "SHOW VARIABLES LIKE 'version%'")) === false) {
            // on query error assume is mysql.
            return self::DB_ENGINE_MYSQL;
        }

        $rows = array();
        while ($row  = self::fetchRow($result)) {
            $rows[] = $row;
        }
        self::freeResult($result);

        $version        = isset($rows[0][1]) ? $rows[0][1] : false;
        $versionComment = isset($rows[1][1]) ? $rows[1][1] : false;

        //Default is mysql
        if ($version === false && $versionComment === false) {
            return self::DB_ENGINE_MYSQL;
        }

        if (stripos($version, 'maria') !== false || stripos($versionComment, 'maria') !== false) {
            return self::DB_ENGINE_MARIA;
        }

        if (stripos($version, 'percona') !== false || stripos($versionComment, 'percona') !== false) {
            return self::DB_ENGINE_PERCONA;
        }

        return self::DB_ENGINE_MYSQL;
    }

    /**
     *
     * @param resoruce|mysqli $dbh
     * @param string $string
     * @return string <p>Returns an escaped string.</p>
     */
    public static function realEscapeString($dbh, $string)
    {
        if (self::dbConnType($dbh) === self::CONN_MYSQLI) {
            return mysqli_real_escape_string($dbh, $string);
        } else {
            return mysql_real_escape_string($string, $dbh);
        }
    }

    /**
     *
     * @param resoruce|mysqli $dbh
     * @param string $query
     * @return mixed <p>Returns <b><code>FALSE</code></b> on failure. For successful <i>SELECT, SHOW, DESCRIBE</i> or <i>EXPLAIN</i> queries <b>mysqli_query()</b> will return a mysqli_result object. For other successful queries <b>mysqli_query()</b> will return <b><code>TRUE</code></b>.</p>
     */
    public static function query($dbh, $query)
    {
        if (self::dbConnType($dbh) === self::CONN_MYSQLI) {
            return mysqli_query($dbh, $query);
        } else {
            return mysql_query($query, $dbh);
        }
    }

    /**
     *
     * @param resoruce|mysqli_result $result
     * @return int
     */
    public static function numRows($result)
    {
        if (self::dbConnTypeByResult($result) === self::CONN_MYSQLI) {
            return $result->num_rows;
        } else {
            return mysql_num_rows($result);
        }
    }

    /**
     *
     * @param resoruce|mysqli_result $result
     * @return int <p>An integer representing the number of fields in a result set.</p>
     * @link http://php.net/manual/en/mysqli.field-count.php
     */
    public static function fetchRow($result)
    {
        if (self::dbConnTypeByResult($result) === self::CONN_MYSQLI) {
            return mysqli_fetch_row($result);
        } elseif (is_resource($result)) {
            return mysql_fetch_row($result);
        }
    }

    /**
     *
     * @param resoruce|mysqli_result $result
     * @return object <p>Returns an object which contains field definition information or <b><code>FALSE</code></b> if no field information for specified <i>fieldnr</i> is available.</p> <b>Object attributes</b>   Attribute Description     name The name of the column   orgname Original column name if an alias was specified   table The name of the table this field belongs to (if not calculated)   orgtable Original table name if an alias was specified   def The default value for this field, represented as a string   max_length The maximum width of the field for the result set.   length The width of the field, as specified in the table definition.   charsetnr The character set number for the field.   flags An integer representing the bit-flags for the field.   type The data type used for this field   decimals The number of decimals used (for numeric fields)
     * @link http://php.net/manual/en/mysqli-result.fetch-field-direct.php
     */
    public static function fetchAssoc($result)
    {
        if (self::dbConnTypeByResult($result) === self::CONN_MYSQLI) {
            return mysqli_fetch_assoc($result);
        } elseif (is_resource($result)) {
            return mysql_fetch_assoc($result);
        }
    }

    /**
     *
     * @param resoruce|mysqli_result $result
     * @return boolean
     */
    public static function freeResult($result)
    {
        if (self::dbConnTypeByResult($result) === self::CONN_MYSQLI) {
            return $result->free();
        } elseif (is_resource($result)) {
            return mysql_free_result($result);
        } else {
            $result = null;
            return true;
        }
    }

    /**
     *
     * @param resoruce|mysqli $dbh
     * @return string
     */
    public static function error($dbh)
    {
        if (self::dbConnType($dbh) === self::CONN_MYSQLI) {
            if ($dbh instanceof mysqli) {
                return mysqli_error($dbh);
            } else {
                return 'Unable to retrieve the error message from MySQL';
            }
        } else {
            if (is_resource($dbh)) {
                return mysql_error($dbh);
            } else {
                return 'Unable to retrieve the error message from MySQL';
            }
        }
    }

    /**
     *
     * @param resoruce|mysqli $dbh
     * @return string // self::CONN_MYSQLI|self::CONN_MYSQL
     */
    public static function dbConnType($dbh)
    {
        return (is_object($dbh) && get_class($dbh) == 'mysqli') ? self::CONN_MYSQLI : self::CONN_MYSQL;
    }

    /**
     *
     * @param resoruce|mysqli_result $result
     * @return type // self::CONN_MYSQLI|self::CONN_MYSQL
     */
    public static function dbConnTypeByResult($result)
    {
        return (is_object($result) && get_class($result) == 'mysqli_result') ? self::CONN_MYSQLI : self::CONN_MYSQL;
    }
}
