<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Plugin constants.
 */
class Constants {

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
		$this->define_constants();
	}

	/**
	 * Define plugin constants.
	 */
	public function define_constants() {

		// **********************
		// START - EDIT CONSTANTS
		// **********************

		$this->plugin_data            = get_plugin_data( $this->module_roots['file'] );
		$this->freemius_slug          = ss_fs()->get_slug();
		$this->main_menu_label        = 'Simple Sitemap';
		$this->plugin_slug            = 'simple-sitemap-menu';
		$this->plugin_cpt_slug        = 'simple-sitemap'; // use this as plugin (menu) slug if using CPT as parent menu.
		$this->menu_type              = 'top'; // top|top-cpt|sub.
		$this->cpt_slug               = ''; // same one used in register_post_type().
		$this->css_prefix             = 'simple-sitemap';
		$this->filter_prefix          = 'simple_sitemap';
		$this->db_option_prefix       = 'simple_sitemap';
		$this->enqueue_prefix         = 'simple-sitemap';
		$this->plugin_settings_prefix = 'simple_sitemap';
		$this->donation_link          = 'https://www.paypal.com/donate?hosted_button_id=FBAG4ZHA4TTUC';
		$this->duplicate_post_label   = '';
		$coupon                       = '&coupon=30PCOFF';

		// ********************
		// END - EDIT CONSTANTS
		// ********************

		$root = $this->module_roots['dir'];

		// CREATE FAQ THEME DEFAULTS.

		// Setup template names and urls for JSON data.
		// $attributes_urls = array(
		// 	'text'      => $root . 'assets/misc/default-text-expand-attributes.json',
		// 	'box'       => $root . 'assets/misc/default-box-expand-attributes.json',
		// 	'accordion' => $root . 'assets/misc/default-accordion-expand-attributes.json',
		// );

		// Loop through each JSON template file and extract default FAQ attributes for each theme.
		// foreach ( $attributes_urls as $key => $value ) {

		// 	$raw_json_contents     = file_get_contents( $value );
		// 	$decoded_json_contents = json_decode( $raw_json_contents, true );

		// 	if ( flexible_faqs_fs()->can_use_premium_code__premium_only() ) {
		// 		// Merge free theme defaults with pro theme defaults.
		// 		$decoded_json_contents = Priority_Extend_Utility::filter_pro_att( $decoded_json_contents, $key, $root );
		// 	}
		// 	$prop        = 'raw_' . $key . '_expand_attribute_defaults';
		// 	$this->$prop = $decoded_json_contents;
		// }

		// FORMAT ATTRIBUTE DEFAULTS FOR SHORTCODE AND BLOCKS.

		//$block_templates = array( 'text', 'box', 'accordion' );

		// Build block/shortcode attributes.
		// foreach ( $block_templates as $template ) {

		// 	$raw_prop       = 'raw_' . $template . '_expand_attribute_defaults';
		// 	$block_prop     = 'block_' . $template . '_expand_attribute_defaults';
		// 	$shortcode_prop = 'shortcode_' . $template . '_expand_attribute_defaults';

		// 	$this->$block_prop = array();

		// 	// Cycle through each FAQ theme defined in JSON files.
		// 	foreach ( $this->$raw_prop as $faq_theme_name => $faq_theme_atts ) {

		// 		$tmp_attribute_defaults = array();

		// 		// Cycle through each parsed FAQ theme attribute defined in JSON files.
		// 		foreach ( $faq_theme_atts['faq_attributes'] as $faq_attribute => $value ) {

		// 			// For each FAQ theme format the defaults to be used in the shortcode.
		// 			$tmp_attribute_defaults[ $faq_attribute ] = $value['default'];

		// 			// Only need the initial defaults from the 'default' theme for blocks so we can register the block via PHP.
		// 			// The other themes will be created via JS.
		// 			if ( 'default' === $faq_theme_name ) {
		// 				// Adding brackets here manually as when deploying the PHP processor inserts them incorrectly when accessing dynamic array properties.
		// 				$this->{$block_prop}[ $faq_attribute ] = $value;
		// 			}
		// 		}
		// 		// Flatten the faq_attributes array to $attr => $value.
		// 		// Adding brackets here manually as when deploying, the PHP processor inserts them incorrectly when accessing dynamic array properties.
		// 		$this->{$raw_prop}[ $faq_theme_name ]['faq_attributes'] = $tmp_attribute_defaults;
		// 	}
		// 	$this->$shortcode_prop = $this->$raw_prop;
		// }

		// LOAD SAMPLE FAQS.

		// $sf_url = $root . 'assets/misc/sample-faqs.json';
		// if ( flexible_faqs_fs()->can_use_premium_code__premium_only() ) {
		// 	// Load pro sample FAQs.
		// 	$sf_url = $root . 'classes/modules/misc/pro-sample-faqs.json';
		// }

		// $raw_json_contents     = file_get_contents( $sf_url );
		// $decoded_json_contents = json_decode( $raw_json_contents, true );
		// $this->sample_faqs     = $decoded_json_contents;

		// ========================================================================================

		// Always set to true for blocks.
		// $this->block_text_expand_attribute_defaults['gutenberg_bl']      = array(
		// 	'type'    => 'boolean',
		// 	'default' => true,
		// );
		// $this->block_box_expand_attribute_defaults['gutenberg_bl']       = array(
		// 	'type'    => 'boolean',
		// 	'default' => true,
		// );
		// $this->block_accordion_expand_attribute_defaults['gutenberg_bl'] = array(
		// 	'type'    => 'boolean',
		// 	'default' => true,
		// );

		// Store plugin premium status in variable.
		$this->is_premium = ss_fs()->can_use_premium_code();

		if ( 'sub' === $this->menu_type ) {
			$this->parent_slug        = 'options-general.php';
			$this->settings_page_hook = 'settings_page_' . $this->plugin_slug; // when main settings page is a submenu under the 'Settings' menu.
		} elseif ( 'top' === $this->menu_type ) {
			$this->parent_slug            = $this->plugin_slug; // when main settings page is a top-level menu.
			$this->settings_page_hook_top = 'toplevel_page_' . $this->plugin_slug;
			// Important: WordPress calculates the first part of this string (i.e. before '_page_') by basing it on the second argument passed to add_menu_page().
			// Unfortunately we can't generalize the argument in add_menu_page() into a variable because the language translation functions don't allow vars. @TODO actually we should be able to do this with translation string functions that allow vars.
			$this->settings_page_hook_sub = $this->db_option_prefix . '_page_' . $this->plugin_slug;
		} elseif ( 'top-cpt' === $this->menu_type ) {
			$this->parent_slug            = 'edit.php?post_type=' . $this->cpt_slug; // when main settings page is a top-level menu.
			$this->settings_page_hook_top = 'edit.php';
			// Important: WordPress calculates the first part of this string (i.e. before '_page_') by basing it on the second argument passed to add_menu_page().
			// Unfortunately we can't generalize the argument in add_menu_page() into a variable because the language translation functions don't allow vars.
			$this->settings_page_hook_sub = $this->cpt_slug . '_page_' . $this->plugin_slug;
		} else {
			wp_die( 'WPGO PLUGINS ERROR [' . $this->main_menu_label . ']: $this->menu_type must be one of: top|top-cpt|sub.' );
		}

		// Define settings pages used in the plugin.
		$this->settings_pages = array(
			'settings'     => array(
				'slug'      => $this->plugin_slug,
				'label'     => 'Settings',
				'css_class' => 'home',
			),
			'new-features' => array(
				'slug'      => $this->plugin_slug . '-new-features',
				'label'     => 'New Features',
				'css_class' => 'new-features',
			),
			'welcome'      => array(
				'slug'      => $this->plugin_slug . '-welcome',
				'label'     => 'Welcome to ' . $this->main_menu_label . '!',
				'css_class' => 'welcome',
			),
		);

		// Define menu prefix and upgrade URL.
		$this->url_prefix = '';
		if ( 'sub' === $this->menu_type ) {
			$this->url_prefix = 'options-general.php';
		} elseif ( 'top' === $this->menu_type ) {
			$this->url_prefix = 'admin.php';
		} elseif ( 'top-cpt' === $this->menu_type ) {
			$this->url_prefix = 'edit.php?post_type=' . $this->cpt_slug;
		}
		// If using a CPT as top-level parent then the post type is already the first query string so subsequent values are separated by '&' and not '?'.
		$query_string_prefix = 'top-cpt' === $this->menu_type ? '&' : '?';

		// For Freemius pages if CPT is used for top-level menu then use slightly different slug.
		$freemius_slug                       = 'top-cpt' === $this->menu_type ? $this->plugin_cpt_slug : $this->settings_pages['settings']['slug'];
		$this->url_prefix                   .= $query_string_prefix;
		$this->main_settings_url             = admin_url() . $this->url_prefix . 'page=' . $this->settings_pages['settings']['slug'];
		$this->welcome_url                   = admin_url() . $this->url_prefix . 'page=' . $this->settings_pages['welcome']['slug'];
		$this->new_features_url              = admin_url() . $this->url_prefix . 'page=' . $this->settings_pages['new-features']['slug'];
		$this->freemius_upgrade_url          = admin_url() . $this->url_prefix . 'page=' . $freemius_slug . '-pricing';
		$this->freemius_discount_upgrade_url = admin_url() . $this->url_prefix . 'page=' . $freemius_slug . '-pricing&checkout=true&plan_id=6617&plan_name=pro&billing_cycle=annual&pricing_id=6018&currency=usd' . $coupon;
		$this->contact_us_url                = admin_url() . $this->url_prefix . 'page=' . $freemius_slug . '-contact';
		$this->admin_url                     = admin_url();

		// Don't allow tabs to be used when the plugin uses a top-level menu.
		if ( SITEMAP_FREEMIUS_NAVIGATION === 'tabs' && ( 'top' === $this->menu_type || 'top-cpt' === $this->menu_type ) ) {
			wp_die( 'WPGO PLUGINS ERROR [' . $this->main_menu_label . ']: Freemius doesn\'t support using tabs with a top-level main settings page. Please change navigation to \'menu\' or use a submenu for the main settings page.' );
		}

		// Construct list of expand icons.

		// $icon_methods = get_class_methods( new Svg_Icons() );

		// if ( flexible_faqs_fs()->can_use_premium_code__premium_only() ) {
		// 	// Include pro only icons.
		// 	$pro_icon_methods = get_class_methods( new Svg_Pro_Icons() );
		// 	$icon_methods     = array_merge( $icon_methods, $pro_icon_methods );
		// }

		// 		$icon_methods_format = array();
		// foreach ( $icon_methods as $key => $icon ) {
		// 	if ( substr( $icon, 0, 1 ) === '_' ) {
		// 		continue;
		// 	}
		// 		$tmp_val_str = ucfirst( substr( $icon, 0, strlen( $icon ) - 1 ) );
		// 		$tmp_val_num = substr( $icon, -1 );
		// 		array_push(
		// 			$icon_methods_format,
		// 			array(
		// 				'label' => $tmp_val_str . ' ' . $tmp_val_num,
		// 				'value' => $icon,
		// 			)
		// 		);
		// }
		// sort( $icon_methods_format );

		// $this->expand_icons = $icon_methods_format;
	}

} /* End class definition */
