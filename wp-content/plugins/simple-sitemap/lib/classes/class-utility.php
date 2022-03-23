<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Plugin utility functions.
 */
class Utility {

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
	 * @param Array $custom_plugin_data Plugin data.
	 */
	public function __construct( $module_roots, $custom_plugin_data ) {
		$this->module_roots       = $module_roots;
		$this->custom_plugin_data = $custom_plugin_data;
	}

	/**
	 * Build attributes for the element
	 *
	 * @param Array  $class_attribute Array of strings of classes names to assign this element.
	 * @param Array  $style_attribute Array of style attributes for inline styling of this element.
	 * @param String $title_attribute Title for this element.
	 * @return String Attributes for an HTML element.
	 */
	public static function build_el_attributes( $class_attribute, $style_attribute, $title_attribute ) {
		$el_attributes = '';
		if ( ! empty( $class_attribute ) ) {
			$el_attributes .= ' class="';
			foreach ( $class_attribute as $key => $value ) {
				$el_attributes .= $value;
			}
			$el_attributes .= '"';
		}
		if ( ! empty( $style_attribute ) ) {
			$el_attributes .= ' style="';
			foreach ( $style_attribute as $key => $value ) {
				$el_attributes .= $value;
			}
			$el_attributes .= '"';
		}
		if ( ! empty( $title_attribute ) ) {
			$el_attributes .= ' title="' . $title_attribute . '"';
		}

		return $el_attributes;
	}

	/**
	 * Build and return tab HTML. Numbered icon is added via JS.
	 *
	 * @param Object $plugin_data All the settings and configurations of this plugin.
	 * @return String formatted as HTML.
	 */
	public function build_settings_tabs_html( $plugin_data ) {

		if ( SITEMAP_FREEMIUS_NAVIGATION === 'menu' ) {
			return '';
		}

		$settings_page_main_url         = admin_url() . 'options-general.php?page=' . $this->custom_plugin_data->settings_pages['settings']['slug'];
		$settings_page_new_features_url = admin_url() . 'options-general.php?page=' . $this->custom_plugin_data->settings_pages['new-features']['slug'];
		$settings_page_welcome_url      = admin_url() . 'options-general.php?page=' . $this->custom_plugin_data->settings_pages['welcome']['slug'];

		$main_active         = ( isset( $_GET['page'] ) && ( $_GET['page'] === $this->custom_plugin_data->settings_pages['settings']['slug'] ) ) ? ' nav-tab-active' : '';
		$new_features_active = ( isset( $_GET['page'] ) && ( $_GET['page'] === $this->custom_plugin_data->settings_pages['new-features']['slug'] ) ) ? ' nav-tab-active' : '';
		$welcome_active      = ( isset( $_GET['page'] ) && ( $_GET['page'] === $this->custom_plugin_data->settings_pages['welcome']['slug'] ) ) ? ' nav-tab-active' : '';

		$tabs_list_html = '<h2 class="nav-tab-wrapper"><a href="' . $settings_page_main_url . '" class="nav-tab fs-tab' . $main_active . '">Settings</a><a href="' . $settings_page_new_features_url . '" class="nav-tab fs-tab' . $new_features_active . '">New Features</a><a href="' . $settings_page_welcome_url . '" class="nav-tab fs-tab' . $welcome_active . '">About</a></h2>';

		return $tabs_list_html;
	}

	/**
	 * Decode and return the JSON encoded string in the form of Object.
	 *
	 * @todo Add to framework plugin.
	 * 
	 * @param String $data JSON  encoded string.
	 * @return Array List of premium new available features.
	 */
	public static function filter_and_decode_json( $data ) {
		$new_features = json_decode( $data );

		if ( ss_fs()->can_use_premium_code() ) {
			// Remove all entries that are 'free-only'.
			foreach ( $new_features as $key => $new_feature ) {
				if ( 'free' === $new_feature->license ) {
					unset( $new_features[ $key ] );
				}
			}
			$new_features = array_values( $new_features ); // reindex array.
		}

		return $new_features;
	}

	/**
	 * Utilized for converting the custom styled block's border object to CSS string.
	 *
	 * @param Object  $json_obj JSON encoded object.
	 * @param Boolean $border_bottom Parameter for border bottom state.
	 * @param Boolean $border_bottom_only Parameter for border bottom state.
	 * @param Boolean $border_top_only Parameter for border top state.
	 * @param Array   $args Additional arguments passed as an array.
	 * @return String CSS property and value formatted as valid CSS statement.
	 */
	public static function build_css_from_border_object( $json_obj, $border_bottom = true, $border_bottom_only = false, $border_top_only = false, $args = array() ) {
		$css_obj = json_decode( $json_obj );

		// If for any reason the parsed JSON string doesn't evaluate to an object (it should) then return no CSS.
		if ( ! is_object( $css_obj ) ) {
			return '';
		}

		// If JSON border style is in shorthand and the styles haven't been customised then output the literal value the first value is always assumed to be the border width.
		if ( property_exists( $css_obj, 'literal' ) ) {
			$literal_arr = explode( ' ', $css_obj->literal );

			if ( true === $border_bottom_only ) {
				return true === $border_bottom ? 'border-bottom: ' . $literal_arr[0] . 'px ' . $literal_arr[1] . ' ' . $literal_arr[2] . ';' : '';
			}

			if ( true === $border_top_only ) {
				return 'border-top: ' . $literal_arr[0] . 'px ' . $literal_arr[1] . ' ' . $literal_arr[2] . ';';
			}

			return 'border: ' . $literal_arr[0] . 'px ' . $literal_arr[1] . ' ' . $literal_arr[2] . ';';
		} else {
			$border_top    = 'border-top: ' . $css_obj->top->width . 'px ' . $css_obj->top->color . ' ' . $css_obj->top->style . ';';
			$border_right  = 'border-right: ' . $css_obj->right->width . 'px ' . $css_obj->right->color . ' ' . $css_obj->right->style . ';';
			$border_bottom = true === $border_bottom ? 'border-bottom: ' . $css_obj->bottom->width . 'px ' . $css_obj->bottom->color . ' ' . $css_obj->bottom->style . ';' : '';
			$border_left   = 'border-left: ' . $css_obj->left->width . 'px ' . $css_obj->left->color . ' ' . $css_obj->left->style . ';';
		}

		if ( true === $border_bottom_only ) {
			return $border_bottom;
		}

		if ( true === $border_top_only ) {
			return $border_top;
		}

		if ( '1' === $css_obj->mode ) {
			return 'border: ' . $css_obj->top->width . 'px ' . $css_obj->top->color . ' ' . $css_obj->top->style;
		}
		if ( '4' === $css_obj->mode ) {

			return $border_top . ' ' . $border_right . ' ' . $border_bottom . ' ' . $border_left;
		}
	}

} /* End class definition */
