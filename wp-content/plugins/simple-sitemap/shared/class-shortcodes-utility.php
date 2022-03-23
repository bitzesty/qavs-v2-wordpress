<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Shortcodes utility class
 */
class Shortcode_Utility {

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
	}

	/**
	 * Create class instance.
	 *
	 * @param Array $module_roots Root plugin path/dir.
	 */
	public static function create_instance( $module_roots ) {
		if ( ! self::$instance ) {
			self::$instance = new Shortcode_Utility( $module_roots );
		}
		return self::$instance;
	}

	/**
	 * Get class instance.
	 */
	public static function get_instance() {
		if ( ! self::$instance ) {
			die( 'Error: Class instance hasn\'t been created yet.' );
		}
		return self::$instance;
	}

	/**
	 * Create class instance.
	 *
	 * @param Bool   $show_label Optionally show the post type label.
	 * @param String $post_type Post type.
	 * @param String $post_type_tag Post type tag.
	 * @param String $post_type_label_font_size Post type label font size.
	 */
	public static function get_post_type_label( $show_label, $post_type, $post_type_tag, $post_type_label_font_size ) {

		$post_type_label_styles = '';
		if ( ! empty( $post_type_label_font_size ) ) {
			$post_type_label_styles = $post_type_label_styles . 'font-size:' . $post_type_label_font_size . ';';
		}
		if ( ! empty( $post_type_label_styles ) ) {
			$post_type_label_styles = ' style="' . $post_type_label_styles . '"';
		}

		$label = '';
		// Conditionally show label for each post type.
		if ( 'true' === $show_label ) {
			$post_type_obj  = get_post_type_object( $post_type );
			$post_type_name = $post_type_obj->labels->name;
			$label          = '<' . $post_type_tag . ' class="post-type"' . $post_type_label_styles . '>' . $post_type_name . '</' . $post_type_tag . '>';
		}

		return $label;
	}

	/**
	 * Get post type label styles.
	 *
	 * @param String $post_type_label_padding Post type label padding.
	 */
	public static function get_post_type_label_styles( $post_type_label_padding ) {

		$post_type_label_styles = '';
		if ( ! empty( $post_type_label_padding ) ) {
			$post_type_label_styles = $post_type_label_styles . 'padding:' . $post_type_label_padding . ';';
		}
		if ( ! empty( $post_type_label_styles ) ) {
			$post_type_label_styles = ' style="' . $post_type_label_styles . '"';
		}

		return $post_type_label_styles;
	}

	/**
	 * Get query args.
	 *
	 * @param Array  $args Query args.
	 * @param String $post_type Post type.
	 */
	public static function get_query_args( $args, $post_type ) {

		$extra_query_args = apply_filters( '_simple_sitemap_extra_query_args', array(), $args );

		$args['tax_query'] = empty( $args['tax_query'] ) ? '' : $args['tax_query'];

		return array_merge(
			array(
				'post_type'      => $post_type,
				'order'          => $args['order'],
				'orderby'        => $args['orderby'],
				'tax_query'      => $args['tax_query'],
				'posts_per_page' => -1,
			),
			$extra_query_args
		);
	}

	/**
	 * Render the sitemap list items.
	 *
	 * @param Array $args Shortcode/block args.
	 * @param Array $post_type Post type.
	 * @param Array $query_args Query args used to retrieve posts.
	 */
	public static function render_list_items( $args, $post_type, $query_args ) {

		$sitemap_query = new \WP_Query( $query_args );

		if ( $sitemap_query->have_posts() ) :

			if ( 'page' === $post_type ) :
				echo self::list_pages( $sitemap_query->posts, $args );
			else :
				$horizontal_sep_arr = apply_filters(
					'_simple_sitemap_horizontal_separator_v2',
					array(
						'ul_class'       => 'simple-sitemap-' . $post_type . ' main',
						'page_depth'     => $args['page_depth'],
						'horizontal_sep' => '',
					),
					$args
				);

				$horizontal_sep     = $horizontal_sep_arr['horizontal_sep'];
				$ul_class           = $horizontal_sep_arr['ul_class'];
				$args['page_depth'] = $horizontal_sep_arr['page_depth'];

				echo '<ul class="' . esc_attr( $ul_class ) . '">';

				// Start of the loop.
				while ( $sitemap_query->have_posts() ) :
					$sitemap_query->the_post();

					// Post visibility.
					if ( apply_filters( '_simple_sitemap_visibility', false, get_the_ID(), $args ) ) {
						continue;
					}

					// Check if we're on the last post.
					if ( ( $sitemap_query->current_post + 1 ) == $sitemap_query->post_count ) {
						$horizontal_sep = '';
					}

					$image_html     = apply_filters( '_simple_sitemap_image_html', '', null, $args );
					$separator_html = apply_filters( '_simple_sitemap_separator_html', '', $args );

					// @todo Can combine this into one line in the future when minimum PHP version allows using function return value as an argument.
					$title_text = get_the_title();

					$title_text = Hooks::simple_sitemap_title_text( $title_text, get_the_ID() );

					$permalink = get_permalink();
					$title     = self::get_the_title( $title_text, $permalink, $args );
					$title     = Hooks::simple_sitemap_title_link_text( $title, get_the_ID() );

					$excerpt = $args['show_excerpt'] == 'true' ? '<' . $args['excerpt_tag'] . ' class="excerpt">' . get_the_excerpt() . '</' . $args['excerpt_tag'] . '>' : '';

					// Render list item.
					// @todo add this to a template (static method?) so we can reuse it in this and other classes?
					echo '<li class="sitemap-item">';
					echo $image_html;
					echo $title;
					echo $excerpt;
					echo $separator_html;
					echo $horizontal_sep;
					echo '</li>';

				endwhile; // end of post loop -->

				echo '</ul>';

				echo '</div>';

				// Put pagination functions here.
				wp_reset_postdata();
			endif;

		else :

			$post_type_obj  = get_post_type_object( $post_type );
			$post_type_name = strtolower( $post_type_obj->labels->name );

			echo '<p class="no-posts">Sorry, no ' . $post_type_name . ' found.</p>';
			echo '</div>';

		endif;
	}

	/**
	 * Get the post title.
	 *
	 * @param String $title_text Title text.
	 * @param String $permalink Permalink.
	 * @param Array  $args Sitemap args.
	 * @param Bool   $parent_page Parent page.
	 * @param String $parent_page_link Parent page link.
	 */
	public static function get_the_title( $title_text, $permalink, $args, $parent_page = false, $parent_page_link = '1' ) {

		$links       = $args['links'];
		$title_open  = $args['title_open'];
		$title_close = $args['title_close'];
		$rel         = apply_filters( '_simple_sitemap_nofollow', '', $args );

		if ( ! empty( $title_text ) ) {
			if ( $links == 'true' && $parent_page === false ) {
				$title = $title_open . '<a href="' . esc_url( $permalink ) . '"' . $rel . '>' . wp_kses_post( $title_text ) . '</a>' . $title_close;
			} elseif ( $links == 'true' && $parent_page && $parent_page_link != '1' ) {
				$title = $title_open . '<a href="' . esc_url( $permalink ) . '"' . $rel . '>' . wp_kses_post( $title_text ) . '</a>' . $title_close;
			} else {
				$title = $title_open . wp_kses_post( $title_text ) . $title_close;
			}
		} else {
			if ( $links == 'true' && $parent_page === false ) {
				$title = $title_open . '<a href="' . esc_url( $permalink ) . '"' . $rel . '>' . '(no title)' . '</a>' . $title_close;
			} elseif ( $links == 'true' && $parent_page && $parent_page_link != '1' ) {
				$title = $title_open . '<a href="' . esc_url( $permalink ) . '"' . $rel . '>' . '(no title)' . '</a>' . $title_close;
			} else {
				$title = $title_open . '(no title)' . $title_close;
			}
		}

		return $title;
	}

	/**
	 * Walk the page tree.
	 *
	 * @param Array $pages Pages.
	 * @param Int   $depth Depth.
	 * @param Array $r Sitemap args.
	 */
	public static function walk_page_tree( $pages, $depth, $r ) {

		$walker           = new WPGO_Walker_Page();
		$walker->ssp_args = $r;

		foreach ( (array) $pages as $page ) {
			if ( $page->post_parent ) {
				$r['pages_with_children'][ $page->post_parent ] = true;
			}
		}

		$args = array( $pages, $depth, $r );
		return call_user_func_array( array( $walker, 'walk' ), $args );
	}

	/**
	 * List pages.
	 *
	 * @param Array $pages Pages.
	 * @param Array $args Sitemap args.
	 */
	public static function list_pages( $pages, $args ) {

		$output = '';

		if ( empty( $pages ) ) {
			return $output;
		}

		$class = apply_filters( '_simple_sitemap_horizontal_separator_v3', 'simple-sitemap-page main', $args );

		$output  = '<ul class="' . $class . '">';
		$output .= self::walk_page_tree( $pages, $args['page_depth'], $args );
		$output .= '</ul>';
		$output .= '</div>';

		return $output;
	}

	/**
	 * Convert block booleans to string booleans.
	 *
	 * Example: true (bool) => 'true' (string)
	 * Example: false (bool) => 'false' (string)
	 *
	 * @param array $args Sitemap args.
	 */
	public static function format_booleans( $args ) {

		// Make a copy.
		$tmp = $args;

		foreach ( $tmp as $key => $value ) {
			if ( 'boolean' === gettype( $value ) ) {
				if ( true === $value ) {
					$tmp[ $key ] = 'true';
				}
				if ( false === $value ) {
					$tmp[ $key ] = 'false';
				}
			}
		}

		return $tmp;
	}

}
