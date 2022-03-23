<?php

/**
 * Impost installer page controller
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\Controllers;

use Duplicator\Core\Controllers\ControllersManager;
use Duplicator\Core\Controllers\AbstractSinglePageController;

class ImportInstallerPageController extends AbstractSinglePageController
{

    protected function __construct()
    {
        $this->pageSlug     = ControllersManager::IMPORT_INSTALLER_PAGE;
        $this->pageTitle    = __('Install package', 'duplicator-pro');
        $this->capatibility = 'import';

        add_filter('duplicator_render_page_content_' . $this->pageSlug, array($this, 'renderContent'));
    }

    public function isEnabled()
    {
        if (defined('DUPLICATOR_PRO_DISALLOW_IMPORT')) {
            return !((bool) DUPLICATOR_PRO_DISALLOW_IMPORT);
        } else {
            return true;
        }
    }

    public function renderContent($currentLevelSlugs)
    {
        \DUP_PRO_CTRL_import_installer::controller();
    }
}
