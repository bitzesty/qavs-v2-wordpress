<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Main WordPress plugin index page links and admin notices.
 */
class Links {

	/**
	 * Common root paths/directories.
	 *
	 * @var $module_roots
	 */
	protected $module_roots;

	/**
	 * Main class constructor.
	 *
	 * @param Array $module_roots Root plugin path/dir.
	 */
	public function __construct( $module_roots ) {

		$this->module_roots = $module_roots;

		add_filter( 'plugin_row_meta', array( &$this, 'plugin_action_links' ), 10, 2 );
		add_filter( 'plugin_action_links', array( &$this, 'plugin_settings_link' ), 10, 2 );
	}

	/**
	 * Display a Settings link on the main Plugins page.
	 *
	 * @param Array $links List of plugin links.
	 * @param Array $file Plugin file.
	 */
	public function plugin_action_links( $links, $file ) {

		if ( $file == 'simple-sitemap/simple-sitemap.php') {
			$freemius_upgrade_url = admin_url() . "admin.php?page=simple-sitemap-menu-pricing";
			$pccf_links = '<a href="' . $freemius_upgrade_url . '" title="More sitemap features"><b>More features</b></a>';
			array_push( $links, $pccf_links );
		}

		return $links;
	}

	/**
	 * Display a Settings link on the main Plugins page.
	 *
	 * @param Array $links List of plugin links.
	 * @param Array $file Plugin file.
	 */
	public function plugin_settings_link( $links, $file ) {

		if ( $file == 'simple-sitemap/simple-sitemap.php') {
			$pccf_links = '<a href="' . get_admin_url() . 'admin.php?page=simple-sitemap-menu-welcome">' . __( 'Get Started', 'simple-sitemap' ) . '</a>';
			array_unshift( $links, $pccf_links );
		}

		return $links;
	}

} /* End class definition */
