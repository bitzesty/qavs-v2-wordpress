<?php

namespace WPGO_Plugins\Plugin_Framework;

/**
 * Common function which could be used in dependent plugins defined here.
 */

class Utilities_FW {
	
	protected $module_roots;
	
	/* Class constructor. */
	public function __construct($module_roots) {

		$this->module_roots = $module_roots;
	}

	/**
	 * Determine and return the script/style url & version number.
	 *
	 * @param String $file_rel   Filename relative to plugin root folder.
	 * @param String $plugin_ver Plugin version.
	 */
	public function get_enqueue_version( $file_rel, $plugin_ver ) {

		$url = $this->module_roots['uri'] . $file_rel;

		// Use 'filemtime' in development.
		if ( WP_DEBUG ) {
			$ver = filemtime( $this->module_roots['dir'] . $file_rel );
		} else { // Use plugin version in production.
			$ver = $plugin_ver;
		}

		return array(
			'uri' => $url,
			'ver' => $ver,
		);
	}

} /* End class definition */
