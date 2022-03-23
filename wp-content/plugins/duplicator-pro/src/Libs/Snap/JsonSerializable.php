<?php

/**
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 * this file isn't under PSR4 autoloader standard
 *
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

if (!interface_exists('JsonSerializable')) {
    define('SNAP_WP_JSON_SERIALIZE_COMPATIBLE', true);

    /**
     * JsonSerializable interface.
     *
     * Compatibility shim for PHP <5.4
     *
     */
    interface JsonSerializable
    {

        public function jsonSerialize();
    }

}
