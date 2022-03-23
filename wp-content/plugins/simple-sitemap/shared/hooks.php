<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Hooks class.
 *
 */
class Hooks {

	/**
	 * Allows you to filter the plugin options defaults array.
	 *
	 * @param Array $defaults Plugin options defaults.
	 * @return Array Plugin defaults.
	 */
	public static function simple_sitemap_defaults( $defaults ) {
		return apply_filters( 'simple_sitemap_defaults', $defaults );
	}

	/**
	 * Allows you to filter the post title text.
	 *
	 * @param String $title Sitemap title.
	 * @param String $id Sitemap ID.
	 * @return Array Sitemap title text.
	 */
	public static function simple_sitemap_title_text( $title, $id ) {
		return apply_filters( 'simple_sitemap_title_text', $title, $id );
	}

	/**
	 * Allows you to filter the post title text.
	 *
	 * @param String $title_link Sitemap title link.
	 * @param String $id Sitemap ID.
	 * @return Array Sitemap title link.
	 */
	public static function simple_sitemap_title_link_text( $title_link, $id ) {
		return apply_filters( 'simple_sitemap_title_link_text', $title_link, $id );
	}

}
