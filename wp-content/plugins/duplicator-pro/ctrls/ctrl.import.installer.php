<?php

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

class DUP_PRO_CTRL_import_installer
{

    /**
     *
     * @var bool
     */
    protected static $isError = false;

    /**
     *
     * @var string
     */
    protected static $errorMessage = '';

    /**
     *
     * @var DUP_PRO_Package_Importer
     */
    protected static $importObj = null;

    /**
     *
     * @var string
     */
    protected static $iframeSrc = null;

    /**
     * @return bool check if package is disallow from wp-config.php
     */
    public static function isDisallow()
    {
        if (defined('DUPLICATOR_PRO_DISALLOW_IMPORT')) {
            return (bool)DUPLICATOR_PRO_DISALLOW_IMPORT;
        } else {
            return false;
        }
    }

    /**
     * import installer controller
     *
     * @throws Exception
     */
    public static function controller()
    {
        try {
            if (self::isDisallow()) {
                throw new Exception(DUP_PRO_U::esc_html__('The import function is disabled'));
            }

            $archivePath     = filter_input(INPUT_GET, 'package', FILTER_SANITIZE_STRING);
            self::$importObj = new DUP_PRO_Package_Importer($archivePath);
            self::$iframeSrc = self::$importObj->prepareToInstall();
        } catch (Exception $e) {
            self::$isError      = true;
            self::$errorMessage = $e->getMessage();
        }
        self::doView();
    }

    /**
     * parse view for import-installer
     */
    protected static function doView()
    {
        if (self::$isError) {
            $errorMessage = self::$errorMessage;
            require(DUPLICATOR_PRO_PLUGIN_PATH . '/views/tools/import/import-installer-error.php');
        } else {
            $importObj = self::$importObj;
            $iframeSrc = self::$iframeSrc;
            require(DUPLICATOR_PRO_PLUGIN_PATH . '/views/tools/import/import-installer.php');
        }
    }

    public static function enqueueJs()
    {
        self::dequeueAllScripts();
        Duplicator\Core\Bootstrap::scripts();
        wp_enqueue_script('dup-pro-import-installer');
    }

    public static function enqueueCss()
    {
        Duplicator\Core\Bootstrap::styles();
        wp_enqueue_style('dup-pro-import');
    }

    /**
     * dequeue all scripts except jquery and dup-pro script
     *
     * @return boolean // false if scripts can't be dequeued
     */
    public static function dequeueAllScripts()
    {

        if (!function_exists('wp_scripts')) {
            return false;
        }

        $scripts = wp_scripts();
        foreach ($scripts->registered as $handle => $script) {
            if (
                strpos($handle, 'jquery') === 0 ||
                strpos($handle, 'dup-pro') === 0
            ) {
                continue;
            }
            wp_dequeue_script($handle);
        }

        return true;
    }
}
