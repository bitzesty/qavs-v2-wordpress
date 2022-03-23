<?php

/**
 * Class for the [simple-sitemap-group] shortcode and block.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Class definition.
 */
class Simple_Sitemap_Group_Shortcode {

	/**
	 * Store static class instance.
	 *
	 * @var $instance
	 */
	protected static $instance;

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

		add_shortcode( 'simple-sitemap-group', array( &$this, 'render_shortcode' ) );
		add_shortcode( 'ssg', array( &$this, 'render_shortcode' ) );
	}

	/**
	 * Create plugin instance.
	 *
	 * @param Array $module_roots Root plugin path/dir.
	 */
	public static function create_instance( $module_roots ) {
		if ( ! self::$instance ) {
			self::$instance = new Simple_Sitemap_Group_Shortcode( $module_roots );
		}
		return self::$instance;
	}

	/**
	 * Get plugin instance.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			die( 'Error: Class instance hasn\'t been created yet.' );
		}
		return self::$instance;
	}

	/**
	 * Render sitemap from an editor block.
	 *
	 * @param Array $attributes Blocks attributes.
	 */
	public function render_block( $attributes ) {
		// manually set this to true as we're rendering a block
		$attributes['gutenberg_block'] = true;
		return $this->render( $attributes );
	}

	/**
	 * Render sitemap from an editor shortcode.
	 *
	 * @param Array $attributes Shortcode attributes.
	 */
	public function render_shortcode( $attributes ) {

		// For a sitemap shortcode set 'gutenberg_block' to false in case it has been set to true manually.
		if ( ! is_array( $attributes ) ) {
			$attributes = array();
		}
		$attributes['gutenberg_block'] = false;

		return $this->render( $attributes );
	}

	/**
	 * Render sitemap group.
	 *
	 * @param Array $attributes Sitemap attributes.
	 */
	public function render( $attributes ) {

		// Initialize.
		$render_err = '';

		// If $attributes are coming from a shortcode parse here.
		if ( ! ( isset( $attributes['gutenberg_block'] ) && ( true === $attributes['gutenberg_block'] ) ) ) {

			// Attributes come from the shortcode.
			$args = shortcode_atts(
				array(
					'page_depth'    => 0,
					'tax'           => 'category', // single taxonomy that must be associated with a post type.
					'title_tag'     => '',
					'show_excerpt'  => 'false',
					'excerpt_tag'   => 'div',
					'links'         => 'true',
					'orderby'       => 'title',
					'order'         => 'asc',
					'post_type_tag' => 'h3',
					'show_label'    => 'true',
					'container_tag' => 'ul',
					'num_terms'     => 0,
				),
				$attributes,
				'simple-sitemap-group'
			);

			$args['attr_source'] = 'shortcode';

		} else {

			// Attributes come from the block.
			$args                = $attributes;
			$args['attr_source'] = 'block';

			if ( empty( $args['block_taxonomy'] ) ) {
				$render_err = '<h5 style="line-height:1.25em;">Please select a post type that supports taxonomies.</h5>';
			} else {
				$args['tax'] = $args['block_taxonomy'];
			}

			$post_type_label_font_size = apply_filters( '_simple_sitemap_group_post_type_label_fs', '', $args );

			$args = Shortcode_Utility::format_booleans( $args );
		}

		$args['shortcode_type'] = 'group'; // undocumented.

		// Escape tag names.
		$args['container_tag'] = tag_escape( $args['container_tag'] );
		$args['title_tag']     = tag_escape( $args['title_tag'] );
		$args['excerpt_tag']   = tag_escape( $args['excerpt_tag'] );
		$args['post_type_tag'] = tag_escape( $args['post_type_tag'] );

		// Format attributes as necessary.

		$args['id'] = uniqid(); // help avoid conflicts if using multiple sitemaps on the same page. e.g. 5d026c6168954.

		// Force 'ul' or 'ol' to be used as the container tag.
		$allowed_container_tags = array( 'ul', 'ol' );
		if ( ! in_array( $args['container_tag'], $allowed_container_tags ) ) {
			$args['container_tag'] = 'ul';
		}

		$container_format_class = apply_filters( '_simple_sitemap_group_list_icon', '', $args );
		$render_class           = apply_filters( '_simple_sitemap_group_render_class', '', $args );

		$post_type_arr = apply_filters( '_simple_sitemap_group_post_type', array( 'post', $render_err ), $args );
		$term_orderby  = apply_filters( '_simple_sitemap_group_term_orderby', 'name', $args );
		$term_order    = apply_filters( '_simple_sitemap_group_term_order', 'asc', $args );
		$num_terms = $args['num_terms'];

		$post_type  = $post_type_arr[0];
		$render_err = $post_type_arr[1];

		// ******************
		// ** OUTPUT START **
		// ******************

		// Start output caching (so that existing content in the [simple-sitemap] post doesn't get shoved to the bottom of the post.
		ob_start();

		if ( $render_err ) {
			return $render_err;
		}

		// Output styles.
		$container_css_id    = '#simple-sitemap-container-' . $args['id'];
		$container_css_class = '.simple-sitemap-container-' . $args['id']; // Applies styles to group sitemap.
		$sitemap_styles      = apply_filters( '_simple_sitemap_group_styles', '', $args, $container_css_id, $container_css_class );

		echo '<style type="text/css">';
		echo $sitemap_styles;
		echo '</style>';

		$sitemap_unique_id = 'simple-sitemap-container-' . $args['id'];
		$container_classes = 'simple-sitemap-container ' . $sitemap_unique_id . $render_class . $container_format_class;
		echo '<div id="' . $sitemap_unique_id . '" class="' . esc_attr( $container_classes ) . '">';

		// Set opening and closing title tag.
		if ( ! empty( $args['title_tag'] ) ) {
			$args['title_open']  = '<' . $args['title_tag'] . '>';
			$args['title_close'] = '</' . $args['title_tag'] . '>';
		} else {
			$args['title_open']  = '';
			$args['title_close'] = '';
		}

		$post_type_label = Shortcode_Utility::get_post_type_label( $args['show_label'], $post_type, $args['post_type_tag'], '' );

		$list_item_wrapper_class = 'simple-sitemap-wrap' . $render_class;
		echo $post_type_label;

		$taxonomy_arr = get_object_taxonomies( $post_type );

		// Sort via specified taxonomy.
		if ( ! empty( $args['tax'] ) && in_array( $args['tax'], $taxonomy_arr ) ) {

			$term_attr = array(
				'orderby' => $term_orderby,
				'order'   => $term_order,
				'number'  => $num_terms,
			);

			$terms = get_terms( $args['tax'], $term_attr );
			foreach ( $terms as $term ) {

				if ( apply_filters( '_simple_sitemap_group_include_exclude_terms', false, strtolower( $term->name ), $args ) ) {
					continue;
				}

				echo '<div class="' . esc_attr( $list_item_wrapper_class ) . ' ' . esc_attr( strtolower( $term->slug ) ) . '">';

				$args['tax_query'] = array(
					array(
						'taxonomy' => $args['tax'],
						'field'    => 'slug',
						'terms'    => $term,
					),
				);

				$term_html = '<h3 class="term-tag">' . $term->name . '</h3>';
				$term_html = apply_filters( '_simple_sitemap_group_tax_links', $term_html, $term->name, $term->slug, $args );
				echo $term_html;

				$query_args = Shortcode_Utility::get_query_args( $args, $post_type );
				Shortcode_Utility::render_list_items( $args, $post_type, $query_args );
			}
		} else {
			echo 'No posts found.';
		}

		echo '</div>'; // .simple-sitemap-container

		// @todo check we still need this
		echo '<br style="clear: both;">'; // Make sure content after the sitemap is rendered properly if taken out.

		$sitemap = ob_get_contents();
		ob_end_clean();

		// ****************
		// ** OUTPUT END **
		// ****************

		return $sitemap;
	}
}
