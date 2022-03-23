<?php

/**
 * Version Pro Base functionalities
 *
 * Name: Duplicator PRO base
 * Version: 1
 * Author: Snap Creek
 * Author URI: http://snapcreek.com
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\Addons\ProBase;

defined('ABSPATH') || exit;

use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Controllers\SettingsPageController;

class LicensingController
{
    const L2_SLUG_LICENSING = 'licensing';

    public static function init()
    {
        add_filter('duplicator_sub_menu_items_' . ControllersManager::SETTINGS_SUBMENU_SLUG, array(__CLASS__, 'licenseSubMenu'));
        add_filter('duplicator_render_page_content_' . ControllersManager::SETTINGS_SUBMENU_SLUG, array(__CLASS__, 'renderLicenseContent'));
    }

    public static function licenseSubMenu($subMenus)
    {
        $subMenus[] = SettingsPageController::generateSubMenuItem(self::L2_SLUG_LICENSING, __('Licensing', 'duplicator-pro'), '', true, 100);
        return $subMenus;
    }

    public static function renderLicenseContent($currentLevelSlugs)
    {
        switch ($currentLevelSlugs[1]) {
            case self::L2_SLUG_LICENSING:
                require ProBase::getAddonPath() . '/template/licensing.php';
                break;
        }
    }
}
