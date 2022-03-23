<?php

/**
 * Debug menu page controller
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\Controllers;

use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Controllers\AbstractMenuPageController;

class DebugPageController extends AbstractMenuPageController
{

    protected function __construct()
    {
        $this->parentSlug   = ControllersManager::MAIN_MENU_SLUG;
        $this->pageSlug     = ControllersManager::DEBUG_SUBMENU_SLUG;
        $this->pageTitle    = __('Testing Interface', 'duplicator-pro');
        $this->menuLabel    = __('Debug', 'duplicator-pro');
        $this->capatibility = self::getDefaultCapadibily();
        $this->menuPos      = 40;

        add_filter('duplicator_render_page_content_' . $this->pageSlug, array($this, 'renderContent'));
    }

    public function isEnabled()
    {
        $global = \DUP_PRO_Global_Entity::get_instance();
        return $global->debug_on;
    }

    public function renderContent($currentLevelSlugs)
    {
        require(DUPLICATOR____PATH . '/debug/main.php');
    }
}
