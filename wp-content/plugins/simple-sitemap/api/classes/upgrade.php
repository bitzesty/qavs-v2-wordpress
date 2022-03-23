<?php

namespace WPGO_Plugins\Plugin_Framework;

/*
 *    Run upgrade routine(s) when plugin updated to new (higher) version
 */

class Upgrade_FW {

	protected $module_roots;

	/* Class constructor. */
	public function __construct( $module_roots, $custom_plugin_data ) {

		$this->module_roots       = $module_roots;
		$this->custom_plugin_data = $custom_plugin_data;

    add_action( 'plugins_loaded', array( &$this, 'upgrade_routine' ) );
	}

	/**
	 * Setup transient for admin notice to be displayed
	 */
	public function upgrade_routine() {

		// At the moment the 'upgrade routine consists only of handling the new features numbered icon.

		// only run upgrade routine on admin pages but not on post editor (for performance)
		if ( ! is_admin() || isset( $_GET['post'] ) ) {
			return;
		}

		$opt_pfx             = $this->custom_plugin_data->db_option_prefix;
		$plugin_data         = get_plugin_data( $this->module_roots['file'], false, false );
		$current_version     = $plugin_data['Version'];
		$global_plugin_options = get_option( $opt_pfx . '_options', array() );
		$stored_version      = isset( $global_plugin_options['plugin_version'] ) ? $global_plugin_options['plugin_version'] : '0.0.0';

		// echo "<pre style='margin-left:200px;'>UPGRADE.PHP [Current: " . $current_version . "][Stored: " . $stored_version . "]<br>";
		// print_r($global_plugin_options);
		// echo "</pre>";

		// // Only run on plugin settings pages, plugin main index page, and Dashboard > Updates page
		// if (isset($_GET['page'])) {
  	// 	echo "<pre style='margin-left:200px;'>";
	  // 	print_r($this->custom_plugin_data->plugin_slug);
    //   print_r($_GET['page']);

  	// 	if (strpos($_GET['page'], $this->custom_plugin_data->plugin_slug) !== false && strpos($_GET['page'], '-new-features') !== false) {
	  // 	echo '<br>ON NEW FEATURES PAGE<br>';
		//   }

    //   echo "</pre>";
    // }

    // $settings_page_prefix = $this->custom_plugin_data->plugin_slug;
		  // $pos = strpos($_GET['page'], $settings_page_prefix);
		  // if ($pos !== 0) {
		  // return;
		  // }
		// }
		// } else {
		// return;
		// }

		// Run upgrade routine if current plugin version is not equal to stored version.
		if ( version_compare( $current_version, $stored_version, '=' ) ) {

			// Only run on new features page.
			if ( isset( $_GET['page'] ) && strpos( $_GET['page'], $this->custom_plugin_data->plugin_slug ) !== false && strpos($_GET['page'], '-new-features') !== false) {
				// echo ">>>>>>>>>>>>>>>>>>>> DON'T RUN UPGRADE ROUTINE - SETTING DISPLAY NUMBERED ICON TO FALSE<br>";
				$global_plugin_options['new_features_numbered_icon'] = 'false';
				update_option( $opt_pfx . '_options', $global_plugin_options );
				return;
			} else {
				// echo ">>>>>>>>>>>>>>>>>>>> DON'T RUN UPGRADE ROUTINE - STRAIGHT RETURN<br>";
				return;
			}
		} else {
			// If a new plugin version has been detected scan for new features and add numbered icon to plugin menu/tab
			// echo ">>>>>>>>>>>>>>>>>>>> SETTING TO TRUE<br>";
			$global_plugin_options['new_features_numbered_icon'] = 'true';
			$global_plugin_options['plugin_version']             = $current_version;
			update_option( $opt_pfx . '_options', $global_plugin_options );
		}
	}

	public static function calc_new_features( $opt_pfx, $new_features_arr, $plugin_data ) {
		// Calc numbered icon and send to JS
		$new_features_number     = 0;
		$global_plugin_options = get_option( $opt_pfx . '_options', array() );

    // echo '<pre style="margin-left:250px;">';
    // print_r($global_plugin_options);
    // echo '</pre>';
 
		$display_numbered_icon   = isset( $global_plugin_options['new_features_numbered_icon'] ) ? $global_plugin_options['new_features_numbered_icon'] : 'false';

		if ( $display_numbered_icon === 'true' ) {
			// echo ">>>>>>>>>>>>>>>>>>>> display_numbered_icon [" . $display_numbered_icon . "]<br>";
			foreach ( $new_features_arr as $key => $new_feature ) {
				if ( $plugin_data['Version'] === $new_feature->version || $new_feature->version === 'latest' ) {
					$new_features_number++;
				}
			}
		}

		// echo ">>>>>>>>>>>>>>>>>>>> NEW FEATURES ARR: <br>";
		// echo "<pre style='margin-left:150px;'>";
		// print_r($new_features_arr);
		// echo "</pre>";
		// echo ">>>>>>>>>>>>>>>>>>>> DISPLAY NUMBERED ICON: [" . $display_numbered_icon . "][" . gettype($display_numbered_icon) . "]<br>";
		// echo ">>>>>>>>>>>>>>>>>>>> OPT_PFX: [" . $opt_pfx . "]<br>";
		// echo ">>>>>>>>>>>>>>>>>>>> TOTAL NUMBER [" . $new_features_number . "]<br>";

		return $new_features_number;
	}

} /* End class definition */
