<?php

namespace WPGO_Plugins\Plugin_Framework;

/*
 *    Enqueue plugin scripts
 */

class Enqueue_Framework_Scripts {

	/* Class constructor. */
	public function __construct( $module_roots, $new_features_arr, $plugin_data, $custom_plugin_data ) {
		$this->module_roots       = $module_roots;
		$this->new_features_arr   = $new_features_arr;
		$this->plugin_data        = $plugin_data;
		$this->custom_plugin_data = $custom_plugin_data;
		$this->enq_pfx            = $this->custom_plugin_data->enqueue_prefix;

		$this->js_deps = array( 'wp-element', 'wp-i18n', 'wp-hooks', 'wp-components', 'wp-blocks', 'wp-editor', 'wp-compose' );
		$this->js_deps = array( 'wp-plugins', 'wp-element', 'wp-edit-post', 'wp-i18n', 'wp-api-request', 'wp-data', 'wp-hooks', 'wp-plugins', 'wp-components', 'wp-blocks', 'wp-editor', 'wp-compose' );

		// Priority of 8 here should always enqueue these before main plugin scripts. This is necessary as these scripts are used as dependencies.
		add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_settings_scripts' ), 8 );
		//add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_scripts' ), 8 );
	}

	/* Scripts for all admin pages. This is necessary as we need to modify the main admin menu from JS. */
	public function enqueue_admin_scripts( $hook ) {
		// Here, $this will refer to the specific plugin that's invoking it.
		// $all_admin_pages_js_rel = 'api/assets/js/all-admin-pages-fw.js';
		// $all_admin_pages_js_url = plugins_url( $all_admin_pages_js_rel, $this->module_roots['file'] );
		// $all_admin_pages_js_ver = filemtime( $this->module_roots['dir'] . $all_admin_pages_js_rel );

		// Keep the handle generic so only one instance is enqueued (e.g. if multiple WPGO plugins are installed).
		// wp_enqueue_script( 'wpgo-all-admin-pages-fw-js', $all_admin_pages_js_url, array(), $all_admin_pages_js_ver, true );
	}

	/* Scripts just for the plugin settings page. */
	public function enqueue_admin_settings_scripts( $hook ) {

		// Don't try to enqueue if $_GET['page'] not set.
		if ( ! isset( $_GET['page'] ) ) {
			return;
		}

		// Only enqueue scripts on the plugin settings page(s) (and Freemius pages).
    $pos = strpos( $hook, $this->custom_plugin_data->css_prefix );

    // echo '<pre style="margin-left:250px;">';
    // print_r($_GET);
    // echo "pos: " . $pos . "<br>";
    // echo "hook: " . $hook . "<br>";
    // echo "settings page hook top: " . $this->custom_plugin_data->settings_page_hook_top . "<br>";
    // echo "settings page hook sub: " . $this->custom_plugin_data->settings_page_hook_sub . "<br>";
    // echo "menu_type: " . $this->custom_plugin_data->menu_type . "<br>";
    // echo "css_prefix: " . $this->custom_plugin_data->css_prefix . "<br>";
    // //print_r($var);
    // echo '</pre>';

    if ( 'sub' === $this->custom_plugin_data->menu_type ) {
      // @todo I think we can probably use the same test for sub settings pages as in the 'else' conditional
      // below but this needs proper testing. 
			// Only enqueue scripts on the plugin settings page(s) (and Freemius pages).
      $pos = strpos($hook, $this->custom_plugin_data->settings_page_hook);
      if ($pos !== 0) {
          return;
      }
 		} else {
			// Return if we're not on a plugin settings page.
			if ( $pos === false ) {  
				return;
			}
		}

		$admin_settings_fw_js_rel = 'api/assets/js/admin-settings-fw.js';
		$admin_settings_fw_js_url = plugins_url( $admin_settings_fw_js_rel, $this->module_roots['file'] );
		$admin_settings_fw_js_ver = filemtime( $this->module_roots['dir'] . $admin_settings_fw_js_rel );

		$admin_settings_fw_css_rel = 'api/assets/css/admin-settings-fw.css';
		$admin_settings_fw_css_url = plugins_url( $admin_settings_fw_css_rel, $this->module_roots['file'] );
		$admin_settings_fw_css_ver = filemtime( $this->module_roots['dir'] . $admin_settings_fw_css_rel );

		wp_enqueue_script( 'wpgo-admin-settings-fw-js', $admin_settings_fw_js_url, array(), $admin_settings_fw_js_ver, true );

		// Styles for plugin admin settings page.
		wp_enqueue_style( $this->enq_pfx . '-admin-settings-fw-css', $admin_settings_fw_css_url, array(), $admin_settings_fw_css_ver );
	}

} /* End class definition */
