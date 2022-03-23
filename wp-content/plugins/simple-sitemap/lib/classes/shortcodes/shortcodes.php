<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Bootstrap class for the sitemap shortcodes.
 */
class Shortcodes {

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
		$this->load_shortcodes();

		// Allow shortcodes to be used in widgets (the callbacks are WordPress core functions).
		add_filter( 'widget_text', 'shortcode_unautop' );
		add_filter( 'widget_text', 'do_shortcode' );
	}

	/**
	 * Load shortcodes.
	 */
	public function load_shortcodes() {

		$root = $this->module_roots['dir'];

		require_once $root . 'shared/class-shortcodes-utility.php';
		Shortcode_Utility::create_instance( $this->module_roots );

		// [simple-sitemap] shortcode
		require_once $root . 'lib/classes/shortcodes/simple-sitemap-shortcode.php';
		Simple_Sitemap_Shortcode::create_instance( $this->module_roots );

		// [simple-sitemap-group] shortcode
		require_once $root . 'lib/classes/shortcodes/simple-sitemap-group-shortcode.php';
		Simple_Sitemap_Group_Shortcode::create_instance( $this->module_roots );
	}

} /* End class definition */
