<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Bootstrap plugin
 */
class BootStrap
{
    /**
     * Common root paths/directories.
     *
     * @var $module_roots
     */
    protected  $module_roots ;
    /**
     * Main class constructor.
     */
    public function __construct()
    {
        $this->module_roots = Main::$module_roots;
        $this->load_supported_features();
    }
    
    /**
     * Load plugin features.
     */
    public function load_supported_features()
    {
        $root = $this->module_roots['dir'];
        // Load plugin constants/data.
        require_once $root . 'lib/classes/class-constants.php';
        $custom_plugin_data = new Constants( $this->module_roots );
        $plugin_data = get_plugin_data( $this->module_roots['file'], false, false );
        // Data to pass to certain classes.
        $new_features_json = '';
        if ( file_exists( $root . 'lib/assets/misc/new-features.json' ) ) {
            $new_features_json = file_get_contents( $root . 'lib/assets/misc/new-features.json' );
        }
        require_once $root . 'lib/classes/class-utility.php';
        $utility = new Utility( $this->module_roots, $custom_plugin_data );
        $new_features_arr = Utility::filter_and_decode_json( $new_features_json );
        // Import plugin framework classes (fw = framework).
        if ( !class_exists( '\\WPGO_Plugins\\Plugin_Framework\\Utilities_FW' ) ) {
            require_once $root . 'api/classes/utilities.php';
        }
        $utilities_fw = new \WPGO_Plugins\Plugin_Framework\Utilities_FW( $this->module_roots );
        // Disable the Freemius feedback popup that appears when deactivating plugin.
        ss_fs()->add_filter( 'show_deactivation_feedback_form', function () {
            return false;
        } );
        // Enqueue framework scripts.
        if ( !class_exists( '\\WPGO_Plugins\\Plugin_Framework\\Enqueue_Framework_Scripts' ) ) {
            require_once $root . 'api/classes/enqueue-scripts.php';
        }
        new \WPGO_Plugins\Plugin_Framework\Enqueue_Framework_Scripts(
            $this->module_roots,
            $new_features_arr,
            $plugin_data,
            $custom_plugin_data
        );
        // Enqueue plugin scripts.
        require_once $root . 'lib/classes/enqueue-scripts.php';
        new Enqueue_Scripts(
            $this->module_roots,
            $utilities_fw,
            $new_features_arr,
            $plugin_data,
            $custom_plugin_data
        );
        // Import plugin framework classes (fw = framework).
        if ( !class_exists( '\\WPGO_Plugins\\Plugin_Framework\\Settings_Templates_FW' ) ) {
            require_once $root . 'api/templates/settings/settings.php';
        }
        $settings_fw = new \WPGO_Plugins\Plugin_Framework\Settings_Templates_FW( $this->module_roots );
        if ( !class_exists( '\\WPGO_Plugins\\Plugin_Framework\\New_Features_Templates_FW' ) ) {
            require_once $root . 'api/templates/settings/new-features.php';
        }
        $new_features_fw = new \WPGO_Plugins\Plugin_Framework\New_Features_Templates_FW( $this->module_roots );
        // Plugin framework hooks.
        if ( !class_exists( '\\WPGO_Plugins\\Plugin_Framework\\Hooks_FW' ) ) {
            require_once $root . 'api/classes/hooks.php';
        }
        // We don't (yet) have a constants.php class so just creating a mini version to pass required data.
        //$custom_plugin_data                  = new \stdClass;
        //$custom_plugin_data->filter_prefix   = 'simple_sitemap';
        //$custom_plugin_data->donation_link   = 'https://www.paypal.com/donate?hosted_button_id=FBAG4ZHA4TTUC';
        //$custom_plugin_data->main_menu_label = 'Simple Sitemap';
        new \WPGO_Plugins\Plugin_Framework\Hooks_FW( $this->module_roots, $custom_plugin_data, ss_fs() );
        // Plugin settings pages.
        require_once $root . 'lib/classes/plugin-admin-pages/class-settings.php';
        new Settings(
            $this->module_roots,
            $plugin_data,
            $custom_plugin_data,
            $utility,
            $settings_fw,
            $new_features_arr
        );
        require_once $root . 'lib/classes/plugin-admin-pages/class-settings-new-features.php';
        new Settings_New_Features(
            $this->module_roots,
            $new_features_arr,
            $plugin_data,
            $custom_plugin_data,
            $utility,
            $new_features_fw
        );
        require_once $root . 'lib/classes/plugin-admin-pages/class-settings-welcome.php';
        new Settings_Welcome(
            $this->module_roots,
            $plugin_data,
            $custom_plugin_data,
            $utility
        );
        // Register blocks.
        require_once $root . 'lib/classes/register-blocks.php';
        new Register_Blocks( $this->module_roots );
        // Sitemap shortcodes.
        require_once $root . 'lib/classes/shortcodes/shortcodes.php';
        new Shortcodes( $this->module_roots );
        // Run upgrade routine when plugin updated to new version.
        if ( !class_exists( '\\WPGO_Plugins\\Plugin_Framework\\Upgrade_FW' ) ) {
            require_once $root . 'api/classes/upgrade.php';
        }
        new \WPGO_Plugins\Plugin_Framework\Upgrade_FW( $this->module_roots, $custom_plugin_data );
        // Localize plugin.
        require_once $root . 'shared/localize.php';
        new Localize( $this->module_roots );
        // Links on the main plugin index page.
        require_once $root . 'shared/links.php';
        new Links( $this->module_roots );
        // Register endpoints.
        require_once $root . 'shared/rest-api-endpoints.php';
        new Custom_Sitemap_Endpoints( $this->module_roots );
        // Plugin hooks.
        require_once $root . 'shared/hooks.php';
        // Walker class to render hierarchical pages.
        require_once $root . 'shared/class-wpgo-walker-page.php';
    }

}
/* End class definition */