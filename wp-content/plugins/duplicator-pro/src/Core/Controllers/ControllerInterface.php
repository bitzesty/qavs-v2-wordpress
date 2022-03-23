<?php

/**
 * Controller interface
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\Core\Controllers;

interface ControllerInterface
{

    public function hookWpInit();

    public function run();

    public function render();
}
