<?php

/**
 * Version Lite Base functionalities
 *
 * Name: Duplicator LITE base
 * Version: 1
 * Author: Snap Creek
 * Author URI: http://snapcreek.com
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\Addons\LiteBase;

defined('ABSPATH') || exit;

class LiteBase extends \Duplicator\Core\Addons\AbstractAddonCore
{

    public function init()
    {
        // TEMP CLASS TO TEST LITE VERSION
        require_once __DIR__ . '/License.php';
        // empty

        add_filter('duplicator_menu_pages', array($this, 'addGoProMenuPage'));
    }

    public function addGoProMenuPage($MenuPages)
    {
        $MenuPages[] = GoProPageController::getInstance();
        return $MenuPages;
    }

    public function canEnable()
    {
        return false;
    }

    public static function getAddonFile()
    {
        return __FILE__;
    }

    public static function getAddonPath()
    {
        return __DIR__;
    }
}
