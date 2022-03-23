<?php

/**
 * Abstract class that manages a single page in wordpress administration without an entry in the menu.
 * The basic render function doesn't handle anything and all content must be generated in the content, including the wrapper.
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

namespace Duplicator\Core\Controllers;

use Duplicator\Core\Views\TplMng;

abstract class AbstractSinglePageController implements ControllerInterface
{
    /**
     *
     * @var self[]
     */
    private static $instances = array();
    protected $pageSlug       = '';
    protected $pageTitle      = '';
    protected $capatibility   = '';
    protected $renderData     = array();
    protected $menuHookSuffix = false;

    /**
     * Return controlle instance
     * @return static
     */
    public static function getInstance()
    {
        $class = get_called_class();
        if (!isset(self::$instances[$class])) {
            self::$instances[$class] = new static();
        }

        return self::$instances[$class];
    }

    /**
     * Class constructor
     */
    abstract protected function __construct();

    /**
     * Function calle on wordpress hook init
     *
     * @return void
     */
    public function hookWpInit()
    {
        // empty
    }

    /**
     *
     * @return boolean // if is false the controller isen't initialized
     */
    public function isEnabled()
    {
        return true;
    }

    /**
     * Return true if this controller is main page
     *
     * @return boolean
     */
    public function isMainPage()
    {
        return true;
    }

    /**
     * Return menu position
     *
     * @return int
     */
    public function getPosition()
    {
        return 0;
    }

    /**
     * Set template globa data values
     *
     * @return void
     */
    protected function setTemplateData()
    {
        $tplMng = TplMng::getInstance();
        $tplMng->setGlobalValue('pageTitle', $this->pageTitle);

        $currentMenuSlugs = $this->getCurrentMenuSlugs();
        $tplMng->setGlobalValue('currentLevelSlugs', $currentMenuSlugs);
    }

    /**
     * Execure controller actions
     *
     * @return void
     */
    protected function runActions()
    {
        if (($currentAction = ControllersManager::getAction()) === false) {
            return;
        }
        $tplMng  = TplMng::getInstance();
        $actions = $this->getActions();
        foreach ($actions as $action) {
            if (!$action instanceof PageAction) {
                continue;
            }
            if ($action->isCurrentAction($this->getCurrentMenuSlugs(), $currentAction)) {
                $resultData = array();
                $action->exec($resultData);
                $tplMng->updateGlobalData($resultData);
            }
        }
    }

    /**
     * Set controller action
     *
     * @return void
     */
    protected function setActionsAvaiables()
    {
        $actionsAvaiables = array();
        $actions          = $this->getActions();
        foreach ($actions as $action) {
            if (!$action instanceof PageAction) {
                continue;
            }

            if ($action->isPageOfCurrentAction($this->getCurrentMenuSlugs())) {
                $actionsAvaiables[$action->getKey()] = $action;
            }
        }
        TplMng::getInstance()->updateGlobalData(array('actions' => $actionsAvaiables));
    }

    /**
     * Excecute controller
     *
     * @return void
     */
    public function run()
    {
        $this->setTemplateData();
        $tplMng       = TplMng::getInstance();
        $tplData = apply_filters('duplicator_page_template_data_' . $this->pageSlug, $tplMng->getGlobalData());
        $tplMng->updateGlobalData($tplData);
        $this->setActionsAvaiables();
        $this->runActions();
        $this->render();
    }

    /**
     * Render page
     *
     * @return void
     */
    public function render()
    {
        TplMng::setStripSpaces(true);
        $tplMng = TplMng::getInstance();
        $tplMng->render('parts/messages');
        do_action('duplicator_render_page_content_' . $this->pageSlug, $this->getCurrentMenuSlugs());
    }

    /**
     * return avaiables action
     *
     * @return PageAction[]
     */
    public function getActions()
    {
        return apply_filters('duplicator_page_actions_' . $this->pageSlug, array());
    }

    /**
     * Return page slug
     *
     * @return string
     */
    public function getSlug()
    {
        return $this->pageSlug;
    }

    /**
     * Return current main page link
     *
     * @return string
     */
    public function getPageUrl()
    {
        return ControllersManager::getInstance()->getMenuLink($this->pageSlug);
    }

    /**
     * return menu page hook suffix
     *
     * @return string
     */
    public function getMenuHookSuffix()
    {
        return $this->menuHookSuffix;
    }

    /**
     * register admin page
     *
     * @return string
     */
    public function registerMenu()
    {
        if (!$this->isEnabled()) {
            return;
        }

        $pageTitle            = apply_filters('duplicator_page_title_' . $this->pageSlug, $this->pageTitle);
        $this->menuHookSuffix = add_submenu_page(null, $pageTitle, '', $this->capatibility, $this->pageSlug, array($this, 'run'));
        add_action('admin_print_styles-' . $this->menuHookSuffix, array($this, 'pageStyles'), 20);
        add_action('admin_print_scripts-' . $this->menuHookSuffix, array($this, 'pageScripts'), 20);
        return $this->menuHookSuffix;
    }

    /**
     * called on admin_print_styles-[page] hook
     *
     * @return void
     */
    public function pageStyles()
    {
    }

    /**
     * called on admin_print_scripts-[page] hook
     *
     * @return void
     */
    public function pageScripts()
    {
    }

    /**
     * return true if current page is this page
     *
     * @return bool
     */
    public function isCurrentPage()
    {
        $levels = ControllersManager::getMenuLevels();
        return (isset($levels[ControllersManager::QUERY_STRING_MENU_KEY_L1]) &&
            $levels[ControllersManager::QUERY_STRING_MENU_KEY_L1] === $this->pageSlug);
    }

    /**
     * return current slugs.
     *
     * @return string[]
     */
    protected function getCurrentMenuSlugs()
    {
        $levels = ControllersManager::getMenuLevels();

        $result    = array();
        $result[0] = $levels[ControllersManager::QUERY_STRING_MENU_KEY_L1];

        return $result;
    }

    /**
     *
     * @return string
     */
    public static function getDefaultCapadibily()
    {
        return apply_filters('wpfront_user_role_editor_duplicator_pro_translate_capability', 'export');
    }
}
