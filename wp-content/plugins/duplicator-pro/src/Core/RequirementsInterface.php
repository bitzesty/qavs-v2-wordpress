<?php

/**
 * Interface that collects the functions of initial checks on the requirements to run the plugin
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\Core;

if (!interface_exists('Duplicator\Core\RequirementsInterface', false)) {

    interface RequirementsInterface
    {

        public static function canRun($pluginFile);

        public static function getAddsHash();
    }

}
