<?php
/**
 * Class for the [simple-sitemap] shortcode and block.
 *
 * @package Simple_Sitemap
 */

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Class definition.
 */
class Simple_Sitemap_Shortcode {

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

		add_shortcode( 'simple-sitemap', array( &$this, 'render_shortcode' ) );
		add_shortcode( 'ss', array( &$this, 'render_shortcode' ) );
	}

	/**
	 * Create plugin instance.
	 *
	 * @param Array $module_roots Root plugin path/dir.
	 */
	public static function create_instance( $module_roots ) {
		if ( ! self::$instance ) {
			self::$instance = new Simple_Sitemap_Shortcode( $module_roots );
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
		// Manually set this to true as we're rendering a block.
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

		// If $attributes are coming from a shortcode parse here.
		if ( ! ( isset( $attributes['gutenberg_block'] ) && ( true === $attributes['gutenberg_block'] ) ) ) {

			// Attributes come from the shortcode.
			$args = shortcode_atts(
				array(
					'render'        => '',
					'page_depth'    => 0,
					'orderby'       => 'title',
					'order'         => 'asc',
					'show_excerpt'  => 'false',
					'show_label'    => 'true',
					'links'         => 'true',
					'types'         => 'page',

					// Following attributes don't have block support yet.
					'title_tag'     => '',
					'post_type_tag' => 'h3',
					'excerpt_tag'   => 'div',
					'container_tag' => 'ul',
				),
				$attributes,
				'simple-sitemap'
			);

			$args['attr_source'] = 'shortcode';

		} else {
			// Attributes come from the block.
			$args                = $attributes;
			$args['attr_source'] = 'block';

			// Set up block types coming from block.
			$args['types'] = '';
			$block_cpts    = json_decode( $args['block_post_types'] );
			if ( empty( $block_cpts ) ) {
				$block_err = '<h5>Please select one or more post types in the sitemap inspector panel.</h5>';
			} else {
				foreach ( $block_cpts as $cpt ) {
					$args['types'] .= $cpt->value . ', ';
				}
			}

			// Enable tabs depending on block settings.
			if ( true === $args['render_tab'] ) {
				$args['render'] = 'tab';
			} else {
				$args['render'] = '';
			}

			$args = Shortcode_Utility::format_booleans( $args );
		}

		// Format attributes as necessary.

		$args['id'] = uniqid(); // Helps avoid conflicts if using multiple sitemaps on the same page. e.g. 5d026c6168954.

		// Internal only.
		$args['shortcode_type'] = 'normal';

		// Escape tag names.
		$args['container_tag'] = tag_escape( $args['container_tag'] );
		$args['title_tag']     = tag_escape( $args['title_tag'] );
		$args['excerpt_tag']   = tag_escape( $args['excerpt_tag'] );
		$args['post_type_tag'] = tag_escape( $args['post_type_tag'] );

		$block_err = '';

		// Force 'ul' or 'ol' to be used as the container tag.
		$allowed_container_tags = array( 'ul', 'ol' );
		if ( ! in_array( $args['container_tag'], $allowed_container_tags ) ) {
			$args['container_tag'] = 'ul';
		}

		// Validate numeric values.
		$args['page_depth'] = intval( $args['page_depth'] );

		$container_format_class = apply_filters( '_simple_sitemap_list_icon', '', $args );
		$render_class           = empty( $args['render'] ) ? ' tab-disabled' : ' tab-enabled';

		// ******************
		// ** OUTPUT START **
		// ******************

		// Start output buffering (so that existing content in the [simple-sitemap] post doesn't get shoved to the bottom of the post.
		ob_start();

		if ( $block_err ) {
			return $block_err;
		}

		// Output styles.
		$container_css_id          = '#simple-sitemap-container-' . $args['id'];
		$container_css_class       = '.simple-sitemap-container-' . $args['id']; // Applies styles to tabbed AND normal sitemap.
		$container_tab             = $container_css_class . '.tab-enabled'; // Applies styles ONLY to tabbed sitemap.
		$sitemap_styles            = apply_filters( '_simple_sitemap_styles', '', $args, $container_css_id, $container_css_class );
		$tab_color                 = apply_filters( '_simple_sitemap_tab_color', '#ffffff', $args );
		$tab_header_bg             = apply_filters( '_simple_sitemap_tab_header_bg', '#de5737', $args );
		$post_type_label_padding   = apply_filters( '_simple_sitemap_post_type_label_pd', '10px 20px', $args );
		$post_type_label_font_size = apply_filters( '_simple_sitemap_post_type_label_fs', '', $args );

		$sitemap_tab_styles = '';
		if ( 'tab' === $args['render'] ) {
			$sitemap_tab_styles .= $container_tab . ' .panel { border-top: 4px solid ' . $tab_header_bg . '; } ';
			$sitemap_tab_styles .= $container_tab . ' input:checked + label { background-color: ' . $tab_header_bg . '; } ';
			$sitemap_tab_styles .= $container_tab . ' input:checked + label > * { color: ' . $tab_color . '; } ';
		}

		if ( ! empty( $sitemap_tab_styles ) || ! empty( $sitemap_styles ) ) {
echo '<style type="text/css">';
echo $sitemap_tab_styles;
echo $sitemap_styles;
echo '</style>';
		}

		$post_types            = array_map( 'trim', explode( ',', $args['types'] ) ); // Convert comma separated string to array
		$registered_post_types = get_post_types();

		$sitemap_unique_id = 'simple-sitemap-container-' . $args['id'];
		$container_classes = 'simple-sitemap-container ' . $sitemap_unique_id . $render_class . $container_format_class;
		echo '<div id="' . $sitemap_unique_id . '" class="' . esc_attr( $container_classes ) . '">';

		// Conditionally output tab headers.
		if ( 'tab' === $args['render'] ) :

			// Create tab headers.
			$header_tab_index = 1; // initialize to 1.
			foreach ( $post_types as $post_type ) {

				if ( ! array_key_exists( $post_type, $registered_post_types ) ) {
					break; // Bail if post type isn't valid.
				}

				$checked         = 1 === $header_tab_index ? 'checked' : '';
				$post_type_label = Shortcode_Utility::get_post_type_label( $args['show_label'], $post_type, $args['post_type_tag'], $post_type_label_font_size );

				$post_type_label_styles = Shortcode_Utility::get_post_type_label_styles( $post_type_label_padding );

				echo '<input type="radio" name="tab-' . $args['id'] . '" id="simple-sitemap-tab-' . $header_tab_index . '-' . $args['id'] . '" ' . $checked . '>
				<label' . $post_type_label_styles . ' for="simple-sitemap-tab-' . $header_tab_index . '-' . $args['id'] . '">' . $post_type_label . '</label>';

				$header_tab_index++;
			}

		endif;

		// Tab panel wrapper - open.
		if ( $args['render'] == 'tab' ) {
			echo '<div class="simple-sitemap-content">'; }

		// Conditionally create tab panels.
		$header_tab_index = 1; // Reset to 1.
		foreach ( $post_types as $post_type ) :

			if ( ! array_key_exists( $post_type, $registered_post_types ) ) {
				break; // bail if post type isn't valid.
			}

			// Set opening and closing title tag.
			if ( ! empty( $args['title_tag'] ) ) {
				$args['title_open']  = '<' . $args['title_tag'] . '>';
				$args['title_close'] = '</' . $args['title_tag'] . '>';
			} else {
				$args['title_open']  = '';
				$args['title_close'] = '';
			}

			$post_type_label = Shortcode_Utility::get_post_type_label( $args['show_label'], $post_type, $args['post_type_tag'], $post_type_label_font_size );

			// Tab panel wrapper - open.
			if ( 'tab' == $args['render'] ) {
				$list_item_wrapper_class = 'simple-sitemap-wrap simple-sitemap-tab-' . $header_tab_index . ' panel';
			} else {
				$list_item_wrapper_class = 'simple-sitemap-wrap';
			}

			$header_tab_index++;
			echo '<div class="' . esc_attr( $list_item_wrapper_class ) . '">';
			if ( 'tab' != $args['render'] ) {
				echo $post_type_label;
			}

			$query_args = Shortcode_Utility::get_query_args( $args, $post_type );
			Shortcode_Utility::render_list_items( $args, $post_type, $query_args );

		endforeach;

		// Tab panel wrapper - close.
		if ( $args['render'] == 'tab' ) {
			echo '</div>'; } // .simple-sitemap-content

		echo '</div>'; // .simple-sitemap-container

		// @todo check we still need this
		echo '<br style="clear: both;">'; // make sure content after the sitemap is rendered properly if taken out.

		$sitemap = ob_get_contents();
		ob_end_clean();

		// ****************
		// ** OUTPUT END **
		// ****************

		return $sitemap;
	}
}
