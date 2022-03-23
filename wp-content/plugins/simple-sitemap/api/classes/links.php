<?php

namespace WPGO_Plugins\Plugin_Framework;

/*
*	WordPress plugin index page links and admin notices
*/

class Plugin_Links_FW {
	
	protected $module_roots;
	
	/* Class constructor. */
	public function __construct($module_roots, $plugin_data, $custom_plugin_data, $utility) {
		
		$this->module_roots = $module_roots;
		$this->custom_plugin_data = $custom_plugin_data;

		add_filter( 'plugin_action_links', array( &$this, 'plugin_settings_link' ), 10, 2 );			
	}
	
	// Display a Settings link on the main Plugins page
	public function plugin_settings_link( $links, $file ) {
		
		if ($file === basename(dirname($this->module_roots['file'])) . '/' . basename($this->module_roots['file'])) {
			$custom_link = '<a href="' . $this->custom_plugin_data->welcome_url . '">' . __( 'Get Started' ) . '</a>';
			array_unshift( $links, $custom_link );
		}
		return $links;
	}

} /* End class definition */