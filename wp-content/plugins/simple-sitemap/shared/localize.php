<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Localize plugin.
 */
class Localize {

	/**
	 * Common root paths/directories.
	 *
	 * @var $module_roots
	 */
	protected $module_roots;

	/**
	 * Main class constructor.
	 *
	 * @param array $module_roots Root plugin path/dir.
	 */
	public function __construct( $module_roots ) {

		$this->module_roots = $module_roots;

		add_action( 'plugins_loaded', array( &$this, 'localize_plugin' ) );
	}

	/**
	 * Add Plugin localization support.
	 */
	public function localize_plugin() {

		load_plugin_textdomain( 'simple-sitemap', false, basename( dirname( $this->module_roots['file'] ) ) . '/languages' );
	}

} /* End class definition */
