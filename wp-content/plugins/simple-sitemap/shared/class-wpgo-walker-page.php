<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Custom walker class to render hierarchical pages.
 *
 * Backslash needed here as 'Walker' class is outside of the 'WPGO_Plugins\Simple_Sitemap' namespace.
 */
class WPGO_Walker_Page extends \Walker {

	public $ssp_args;

	/**
	 * @see Walker::$tree_type
	 * @var string
	 */
	public $tree_type = 'page';

	/**
	 * @see Walker::$db_fields
	 * @todo Decouple this.
	 * @var array
	 */
	public $db_fields = array(
		'parent' => 'post_parent',
		'id'     => 'ID',
	);

	/**
	 * @see Walker::start_lvl()
	 *
	 * @param String $output Passed by reference. Used to append additional content.
	 * @param Int    $depth  Depth of page. Used for padding.
	 * @param Array  $args
	 */
	public function start_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "\n$indent<ul class='children'>\n";
	}

	/**
	 * @see Walker::end_lvl()
	 *
	 * @param String $output Passed by reference. Used to append additional content.
	 * @param Int    $depth  Depth of page. Used for padding.
	 * @param Array  $args
	 */
	public function end_lvl( &$output, $depth = 0, $args = array() ) {
		$indent  = str_repeat( "\t", $depth );
		$output .= "$indent</ul>\n";
	}

	/**
	 * @see Walker::start_el()
	 *
	 * @param String $output       Passed by reference. Used to append additional content.
	 * @param Object $page         Page data object.
	 * @param Int    $depth        Depth of page. Used for padding.
	 * @param Array  $args
	 */
	public function start_el( &$output, $page, $depth = 0, $args = array(), $current_page = 0 ) {

		$tmp = Settings::get_plugin_options();

		$parent_page = false;

		if ( $depth ) {
			$indent = str_repeat( "\t", $depth );
		} else {
			$indent = '';
		}

		$css_class = array( 'page_item', 'page-item-' . $page->ID );

		if ( isset( $args['pages_with_children'][ $page->ID ] ) ) {
			$css_class[] = 'page_item_has_children';

			if ( isset( $tmp['chk_parent_page_link'] ) && $tmp['chk_parent_page_link'] == '1' ) {
				if ( ! empty( $tmp['txt_exclude_parent_pages'] ) ) {
					// Process IDs.
					$ids = explode( ',', $tmp['txt_exclude_parent_pages'] );
					if ( in_array( $page->ID, $ids ) ) {
						$parent_page = true;
					}
				} else {
					// Remove all parent page IDs.
					$parent_page = true;
				}
			} else {
				// Remove all parent page IDs.
				$parent_page = true;
			}
		}

		$css_classes = implode( ' ', $css_class );

		if ( '' === $page->post_title ) {
			/* translators: %d: ID of a post */
			$page->post_title = sprintf( __( '#%d (no title)' ), $page->ID );
		}

		$args['link_before'] = empty( $args['link_before'] ) ? '' : $args['link_before'];
		$args['link_after']  = empty( $args['link_after'] ) ? '' : $args['link_after'];

		// ******************
		// NEW RENDER - START
		// ******************

		$image_html     = apply_filters( '_simple_sitemap_image_html', '', $page->ID, $args );
		$separator_html = apply_filters( '_simple_sitemap_separator_html', '', $args );
		$horizontal_sep = apply_filters( '_simple_sitemap_horizontal_separator_v1', '', $args );
		$excerpt_text   = apply_filters( '_simple_sitemap_page_excerpt_text', $page->post_content, $args );

		// $title_text = $page->post_title;
		$title_text     = Hooks::simple_sitemap_title_text( $page->post_title, $page->ID );
		$permalink      = get_permalink( $page->ID );
		$title          = Shortcode_Utility::get_the_title( $title_text, $permalink, $args, $parent_page, $tmp['chk_parent_page_link'] );
		$title          = Hooks::simple_sitemap_title_link_text( $title, $page->ID );
		$excerpt        = $args['show_excerpt'] == 'true' ? '<' . $args['excerpt_tag'] . ' class="excerpt">' . $excerpt_text . '</' . $args['excerpt_tag'] . '>' : '';

		// echo "<pre>";
		// print_r($page);
		// echo "</pre>";

		// render list item
		// @todo add this to a template (static method?) so we can reuse it in this and other classes?
		$output .= $indent;
		$output .= '<li class="sitemap-item ' . $css_classes . '">';
		$output .= $image_html;
		$output .= $title;
		$output .= $excerpt;
		$output .= $separator_html;
		$output .= $horizontal_sep;

		// ****************
		// NEW RENDER - END
		// ****************

		// ******************
		// OLD RENDER - START
		// ******************

		/*
		$output .= $indent . sprintf(
			'<li class="%s"><a href="%s">%s%s%s</a>',
			$css_classes,
			get_permalink( $page->ID ),
			$args['link_before'],
			apply_filters( 'the_title', $page->post_title, $page->ID ),
			$args['link_after']
		);*/

		// ******************
		// OLD RENDER - END
		// ******************

		if ( ! empty( $args['show_date'] ) ) {
			if ( 'modified' == $args['show_date'] ) {
				$time = $page->post_modified;
			} else {
				$time = $page->post_date;
			}

			$date_format = empty( $args['date_format'] ) ? '' : $args['date_format'];
			$output     .= ' ' . mysql2date( $date_format, $time );
		}
	}

	/**
	 * @see Walker::end_el()
	 *
	 * @param string $output Passed by reference. Used to append additional content.
	 * @param object $page Page data object. Not used.
	 * @param int    $depth Depth of page. Not Used.
	 * @param array  $args
	 */
	public function end_el( &$output, $page, $depth = 0, $args = array() ) {
		$output .= "</li>\n";
	}

	/**
	 * Traverse elements to create list from elements.
	 *
	 * Display one element if the element doesn't have any children otherwise,
	 * display the element and its children. Will only traverse up to the max
	 * depth and no ignore elements under that depth. It is possible to set the
	 * max depth to include all depths, see walk() method.
	 *
	 * This method should not be called directly, use the walk() method instead.
	 *
	 * @param object $element           Data object.
	 * @param array  $children_elements List of elements to continue traversing.
	 * @param int    $max_depth         Max depth to traverse.
	 * @param int    $depth             Depth of current element.
	 * @param array  $args              An array of arguments.
	 * @param string $output            Passed by reference. Used to append additional content.
	 */
	public function display_element( $element, &$children_elements, $max_depth, $depth, $args, &$output ) {

		// [D.Gwyer, Custom code #1 - START]
		if ( apply_filters( '_simple_sitemap_visibility', false, $element->ID, $this->ssp_args ) ) {
			return;
		}
		// [D.Gwyer, Custom code #1 - END]

		if ( ! $element ) {
			return;
		}

		$id_field = $this->db_fields['id'];
		$id       = $element->$id_field;

		// display this element
		$this->has_children = ! empty( $children_elements[ $id ] );
		if ( isset( $args[0] ) && is_array( $args[0] ) ) {
			$args[0]['has_children'] = $this->has_children; // Back-compat.
		}

		$cb_args = array_merge( array( &$output, $element, $depth ), $args );
		call_user_func_array( array( $this, 'start_el' ), $cb_args );

		// descend only when the depth is right and there are childrens for this element
		if ( ( $max_depth == 0 || $max_depth > $depth + 1 ) && isset( $children_elements[ $id ] ) ) {

			foreach ( $children_elements[ $id ] as $child ) {

				if ( ! isset( $newlevel ) ) {
					$newlevel = true;
					// start the child delimiter
					$cb_args = array_merge( array( &$output, $depth ), $args );
					call_user_func_array( array( $this, 'start_lvl' ), $cb_args );
				}
				$this->display_element( $child, $children_elements, $max_depth, $depth + 1, $args, $output );
			}
			unset( $children_elements[ $id ] );
		}

		if ( isset( $newlevel ) && $newlevel ) {
			// end the child delimiter
			$cb_args = array_merge( array( &$output, $depth ), $args );
			call_user_func_array( array( $this, 'end_lvl' ), $cb_args );
		}

		// end this element
		$cb_args = array_merge( array( &$output, $element, $depth ), $args );
		call_user_func_array( array( $this, 'end_el' ), $cb_args );
	}
}
