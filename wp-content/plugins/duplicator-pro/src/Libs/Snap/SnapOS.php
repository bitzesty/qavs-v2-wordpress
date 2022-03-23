<?php

/**
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\Libs\Snap;

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class SnapOS
{
    const DEFAULT_WINDOWS_MAXPATH = 260;
    const DEFAULT_LINUX_MAXPATH   = 4096;

    /**
     * return true if current SO is windows
     *
     * @staticvar bool $isWindows
     * @return bool
     */
    public static function isWindows()
    {
        static $isWindows = null;
        if (is_null($isWindows)) {
            $isWindows = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN');
        }
        return $isWindows;
    }

    public static function isOSX()
    {
        static $isOSX = null;
        if (is_null($isOSX)) {
            $isOSX = (strtoupper(substr(PHP_OS, 0, 6)) === 'DARWIN');
        }
        return $isOSX;
    }

    /**
     *  return current SO path path len
     * @staticvar int $maxPath
     * @return int
     */
    public static function maxPathLen()
    {
        static $maxPath = null;
        if (is_null($maxPath)) {
            if (defined('PHP_MAXPATHLEN')) {
                $maxPath = PHP_MAXPATHLEN;
            } else {
                // for PHP < 5.3.0
                $maxPath = self::isWindows() ? self::DEFAULT_WINDOWS_MAXPATH : self::DEFAULT_LINUX_MAXPATH;
            }
        }
        return $maxPath;
    }
}
