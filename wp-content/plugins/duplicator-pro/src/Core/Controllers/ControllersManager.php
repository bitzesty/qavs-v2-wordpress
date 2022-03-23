<?php

/**
 * Singlethon class that manages the various controllers of the administration of wordpress
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\Core\Controllers;

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Controllers\MainPageController;
use Duplicator\Controllers\PackagesPageController;
use Duplicator\Controllers\ImportPageController;
use Duplicator\Controllers\ImportInstallerPageController;
use Duplicator\Controllers\SettingsPageController;
use Duplicator\Controllers\StoragePageController;
use Duplicator\Controllers\DebugPageController;
use Duplicator\Controllers\ToolsPageController;
use Duplicator\Core\Views\TplMng;
use Duplicator\Libs\Snap\SnapUtil;

final class ControllersManager
{
    const MAIN_MENU_SLUG               = 'duplicator-pro';
    const PACKAGES_SUBMENU_SLUG        = 'duplicator-pro';
    const IMPORT_SUBMENU_SLUG          = 'duplicator-pro-import';
    const SCHEDULES_SUBMENU_SLUG       = 'duplicator-pro-schedules';
    const STORAGE_SUBMENU_SLUG         = 'duplicator-pro-storage';
    const TEMPLATES_SUBMENU_SLUG       = 'duplicator-pro-templates';
    const TOOLS_SUBMENU_SLUG           = 'duplicator-pro-tools';
    const SETTINGS_SUBMENU_SLUG        = 'duplicator-pro-settings';
    const DEBUG_SUBMENU_SLUG           = 'duplicator-pro-debug';
    const IMPORT_INSTALLER_PAGE        = 'duplicator-pro-import-installer';
    const QUERY_STRING_MENU_KEY_L1     = 'page';
    const QUERY_STRING_MENU_KEY_L2     = 'tab';
    const QUERY_STRING_MENU_KEY_L3     = 'subtab';
    const QUERY_STRING_MENU_KEY_ACTION = 'action';

    /**
     *
     * @var self
     */
    private static $instance = null;

    /**
     *
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }

        return self::$instance;
    }

    protected function __construct()
    {
        add_action('init', array($this, 'hookWpInit'));
    }

    public function hookWpInit()
    {
        foreach ($this->getMenuPages() as $menuPage) {
            if (!$menuPage->isEnabled()) {
                continue;
            }

            $menuPage->hookWpInit();
        }
    }

    /**
     * return true if current page is a duplicator page
     * @return boolean
     */
    public function isDuplicatorPage()
    {
        foreach ($this->getMenuPages() as $menuPage) {
            if (!$menuPage->isEnabled()) {
                continue;
            }

            if ($menuPage->isCurrentPage()) {
                return true;
            }
        }

        return false;
    }

    /**
     *
     * @return string|bool
     */
    public static function getMenuLevels()
    {
        return SnapUtil::filterInputRequestArray(
            array(
                    self::QUERY_STRING_MENU_KEY_L1 => array(
                        'filter'  => FILTER_SANITIZE_STRING,
                        'options' => array(
                            'default' => null
                        )
                    ),
                    self::QUERY_STRING_MENU_KEY_L2 => array(
                        'filter'  => FILTER_SANITIZE_STRING,
                        'options' => array(
                            'default' => null
                        )
                    ),
                    self::QUERY_STRING_MENU_KEY_L3 => array(
                        'filter'  => FILTER_SANITIZE_STRING,
                        'options' => array(
                            'default' => null
                        )
                    )
                )
        );
    }

    /**
     *
     * @return string|bool // return current action key or false if not exists
     */
    public static function getAction()
    {
        return SnapUtil::filterInputRequest(
            self::QUERY_STRING_MENU_KEY_ACTION,
            FILTER_SANITIZE_STRING,
            array(
                    'options' => array(
                        'default' => false
                    )
                )
        );
    }

    /**
     *
     * @param string $page
     * @param null|string $tabL1 // if not null check if is current tab
     * @param null|string $tabL2 // if not null check if is current tab
     * @return boolean // return true if is current page/subtabs
     */
    public static function isCurrentPage($page, $tabL1 = null, $tabL2 = null)
    {
        $levels = self::getMenuLevels();

        if ($page !== $levels[self::QUERY_STRING_MENU_KEY_L1]) {
            return false;
        }

        $controller = self::getPageControlleBySlug($page);
        // get defaults
        $menuSlugs  = $controller->getCurrentMenuSlugs();

        if (!is_null($tabL1) && (!isset($menuSlugs[1]) || $tabL1 !== $menuSlugs[1])) {
            return false;
        }

        if (!is_null($tabL1) && !is_null($tabL2) && (!isset($menuSlugs[2]) || $tabL2 !== $menuSlugs[2])) {
            return false;
        }

        return true;
    }

    public static function getPageUniqueId($page, $tabL1 = null, $tabL2 = null)
    {
        $result = 'dup_id_' . $page;

        if (!is_null($tabL1)) {
            $result .= '_' . $tabL1;
        }

        if (!is_null($tabL1) && !is_null($tabL2)) {
            $result .= '_' . $tabL2;
        }

        return $result;
    }

    public static function getUniqueIdOfCurrentPage()
    {
        $levels = self::getMenuLevels();
        return self::getPageUniqueId($levels[self::QUERY_STRING_MENU_KEY_L1], $levels[self::QUERY_STRING_MENU_KEY_L2], $levels[self::QUERY_STRING_MENU_KEY_L3]);
    }

    /**
     *
     * @return string
     */
    public static function getCurrentLink($extraData = array())
    {
        $levels = self::getMenuLevels();
        return self::getMenuLink(
            $levels[self::QUERY_STRING_MENU_KEY_L1],
            $levels[self::QUERY_STRING_MENU_KEY_L2],
            $levels[self::QUERY_STRING_MENU_KEY_L3],
            $extraData
        );
    }

    /**
     *
     * @param string $page
     * @param string $subL2
     * @param string $subL3
     * @return string
     */
    public static function getMenuLink($page, $subL2 = null, $subL3 = null, $extraData = array())
    {
        $data = $extraData;

        $data[self::QUERY_STRING_MENU_KEY_L1] = $page;

        if (!empty($subL2)) {
            $data[self::QUERY_STRING_MENU_KEY_L2] = $subL2;
        }

        if (!empty($subL3)) {
            $data[self::QUERY_STRING_MENU_KEY_L3] = $subL3;
        }

        if (is_multisite()) {
            $url = network_admin_url('admin.php');
        } else {
            $url = admin_url('admin.php');
        }
        $queryStr = http_build_query($data);
        return $url . '?' . $queryStr;
    }

    /**
     *
     * @return AbstractMenuPageController[]
     */
    public static function getMenuPages()
    {
        static $basicMenuPages = null;

        if (is_null($basicMenuPages)) {
            $basicMenuPages   = array();
            $basicMenuPages[] = MainPageController::getInstance();
            $basicMenuPages[] = PackagesPageController::getInstance();
            $basicMenuPages[] = ImportPageController::getInstance();
            $basicMenuPages[] = ImportInstallerPageController::getInstance();
            $basicMenuPages[] = StoragePageController::getInstance();
            $basicMenuPages[] = SettingsPageController::getInstance();
            $basicMenuPages[] = DebugPageController::getInstance();
            $basicMenuPages[] = ToolsPageController::getInstance();
        }

        return array_filter(
            apply_filters(
                'duplicator_menu_pages',
                $basicMenuPages
            ),
            function ($menuPage) {
                return is_subclass_of($menuPage, '\Duplicator\Core\Controllers\AbstractSinglePageController');
            }
        );
    }

    /**
     *
     * @return AbstractMenuPageController[]
     */
    protected static function getMenuPagesSortedByPos()
    {
        $menuPages = self::getMenuPages();

        uksort($menuPages, function ($a, $b) use ($menuPages) {
            if ($menuPages[$a]->getPosition() == $menuPages[$b]->getPosition()) {
                if ($a == $b) {
                    return 0;
                } elseif ($a > $b) {
                    return 1;
                } else {
                    return -1;
                }
            } elseif ($menuPages[$a]->getPosition() > $menuPages[$b]->getPosition()) {
                return 1;
            } else {
                return -1;
            }
        });
        return array_values($menuPages);
    }

    /**
     *
     * @param string $slug
     * @return boolean|AbstractMenuPageController
     */
    public static function getPageControlleBySlug($slug)
    {
        $menuPages = self::getMenuPages();
        foreach ($menuPages as $page) {
            if ($page->getSlug() === $slug) {
                return $page;
            }
        }

        return false;
    }

    public function registerMenu()
    {
        $menuPages = self::getMenuPagesSortedByPos();

        // before register main pages
        foreach ($menuPages as $menuPage) {
            if (!$menuPage->isEnabled() || !$menuPage->isMainPage()) {
                continue;
            }

            $menuPage->registerMenu();
        }

        // after register secondary pages
        foreach ($menuPages as $menuPage) {
            if (!$menuPage->isEnabled() || $menuPage->isMainPage()) {
                continue;
            }

            $menuPage->registerMenu();
        }
    }
}
