<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Plugin options class.
 */
class Settings
{
    /**
     * Common root paths/directories.
     *
     * @var $module_roots
     */
    protected  $module_roots ;
    /**
     * Main class constructor.
     *
     * @param Array $module_roots Root plugin path/dir.
     * @param Array $settings_fw Plugin framework settings.
     */
    public function __construct(
        $module_roots,
        $plugin_data,
        $custom_plugin_data,
        $utility,
        $settings_fw,
        $new_features_arr
    )
    {
        $this->module_roots = $module_roots;
        $this->custom_plugin_data = $custom_plugin_data;
        $this->hook_prefix = $this->custom_plugin_data->plugin_settings_prefix;
        $this->freemius_upgrade_url = $this->custom_plugin_data->freemius_upgrade_url;
        $this->utility = $utility;
        $this->settings_fw = $settings_fw;
        $this->plugin_data = $plugin_data;
        $this->new_features_arr = $new_features_arr;
        $this->pro_attribute = ( $this->custom_plugin_data->is_premium ? '' : '<span class="pro" title="Upgrade now for immediate access to this feature"><a href="' . $this->freemius_upgrade_url . '">PRO</a></span>' );
        $this->settings_slug = $this->custom_plugin_data->settings_pages['settings']['slug'];
        $this->new_features_slug = $this->custom_plugin_data->settings_pages['new-features']['slug'];
        $this->welcome_slug = $this->custom_plugin_data->settings_pages['welcome']['slug'];
        add_action( 'admin_init', array( &$this, 'register_plugin_settings' ) );
        add_action( 'admin_menu', array( &$this, 'add_options_page' ) );
        add_filter( 'simple_sitemap_defaults', array( &$this, 'add_defaults' ) );
        add_filter( 'custom_menu_order', array( &$this, 'filter_menu_order' ) );
        // enable custom menu ordering.
        add_action( 'admin_notices', function () {
            // Check to see if user clicked on the reset options button.
            
            if ( isset( $_POST['reset_options'] ) ) {
                // Display update notice here.
                ?>
				<div class="notice notice-success is-dismissible">
					<p>Plugin settings reset to defaults.</p>
				</div>
				<?php 
                // Reset plugin defaults.
                update_option( 'simple_sitemap_options', self::get_default_plugin_options() );
            }
        
        } );
    }
    
    /**
     * Register plugin options with Settings API.
     */
    public function register_plugin_settings()
    {
        /* Register plugin options settings for all tabs. */
        register_setting( 'simple_sitemap_options_group', 'simple_sitemap_options', array( $this, 'sanitize_plugin_options' ) );
    }
    
    /**
     * Register plugin options page, and enqueue scripts/styles.
     */
    public function add_options_page()
    {
        // @todo calc this in constants.php just once and pass it in.
        $opt_pfx = $this->custom_plugin_data->db_option_prefix;
        $new_features_number = \WPGO_Plugins\Plugin_Framework\Upgrade_FW::calc_new_features( $opt_pfx, $this->new_features_arr, $this->plugin_data );
        $title = ( 0 === $new_features_number ? __( 'Simple Sitemap', 'simple-sitemap' ) : 'Sitemap <span class="update-plugins count-' . $new_features_number . '"><span class="plugin-count">' . $new_features_number . '</span></span>' );
        add_menu_page(
            __( 'Simple Sitemap Settings Page', 'simple-sitemap' ),
            $title,
            'manage_options',
            $this->settings_slug,
            array( &$this, 'render' ),
            'dashicons-pressthis',
            82
        );
        // Make the settings page have a 'Settings' menu title rather than 'Simple Sitemap'.
        add_submenu_page(
            'simple-sitemap-menu',
            'Simple Sitemap Settings Page',
            'Settings',
            'manage_options',
            $this->settings_slug
        );
    }
    
    /**
     * Define default option settings.
     *
     * @param Array $defaults Current plugin defaults.
     * @return Array $defaults Updated plugin defaults.
     */
    public function add_defaults( $defaults )
    {
        $defaults['txtar_sitemap_script'] = '';
        $defaults['chk_parent_page_link'] = '0';
        $defaults['txt_exclude_parent_pages'] = '';
        $defaults['default_on_checkboxes']['chk_parent_page_link'] = '0';
        return $defaults;
    }
    
    /**
     * Sanitize plugin options.
     *
     * @param Array $input Current input content.
     * @return Array $input Sanitized input content.
     */
    public function sanitize_plugin_options( $input )
    {
        // Strip html from textboxes.
        $input['txtar_sitemap_script'] = wp_filter_nohtml_kses( $input['txtar_sitemap_script'] );
        $input['txt_exclude_parent_pages'] = wp_filter_nohtml_kses( $input['txt_exclude_parent_pages'] );
        // Sanitize plugin options via this filter hook. Allows you to sanitize options via another class.
        // return Hooks::wpgo_sanitize_plugin_options( $input );
        return $input;
    }
    
    /**
     * Display plugin options page.
     */
    public function render()
    {
        $freemius_upgrade_url = admin_url() . 'admin.php?page=simple-sitemap-menu-pricing';
        $pro_attribute = '';
        $pro_attribute = '&nbsp;<span class="pro" title="Click to get immediate access to this feature"><a href="' . $freemius_upgrade_url . '">PRO</a></span>';
        ?>
		<div class="wrap welcome main no-tabs">

		<?php 
        // Display setting update messages.
        settings_errors();
        ?>
			<div class="wpgo-settings-inner">

				<div class="wpgo-settings-page-header">
					<h1 class="heading">
							<img src="<?php 
        echo  $this->module_roots['uri'] . '/lib/assets/images/simple-sitemap.svg' ;
        ?>">
						<?php 
        _e( 'Simple Sitemap', 'simple-sitemap' );
        ?>
					</h1>
					<div class="wpgo-header-btns">
						<a class="plugin-btn" href="<?php 
        echo  $this->custom_plugin_data->welcome_url ;
        ?>#getting-started">Start Here<span style="width:15px;height:15px;" class="dashicons dashicons-arrow-right-alt2"></span></a>
						<a style="background:#f5a356;border:2px #d9914e solid;" class="plugin-btn" href="https://demo.wpgothemes.com/flexr/simple-sitemap-pro-demo/" target="_blank">Live Demo</a></span>
						<a style="background:#933c60;border:2px #6d314a solid;" class="plugin-btn" href="https://wpgoplugins.com/document/simple-sitemap-pro-documentation/" target="_blank">Plugin Docs</a>
					</div>
				</div>

				<div class="wpgo-header-description">
					<p class="description-txt">
						To see what's new at a glance and how to use the plugin we recommend visiting the <a href="<?php 
        echo  $this->custom_plugin_data->welcome_url ;
        ?>">About</a> plugin page. Or, why not take a look at the Simple Sitemap <a href="https://demo.wpgothemes.com/flexr/simple-sitemap-pro-demo/" target="_blank">Live Demo</a> to see plenty of sitemap examples in action.
					</p>
				</div>

			<h2 style="margin:35px 0 0 0;">Sitemap Blocks</h2>

			<p>Using blocks is the is the new preferred way to add content to posts and pages. The main benefit is that the editor view is exactly the same as the frontend. This means no more having to switch between the editor and frontend to check how everything looks.</p>

			<p>Expand the section directly below to see all editor blocks included with the Simple Sitemap plugin.</p>

			<div class="wpgo-expand-box" style="margin-top:20px;">
				<h4 style="margin-top:5px;display:inline-block;margin-bottom:10px;">Available Blocks</h4><button id="blocks-btn" class="button">Expand <span style="vertical-align:sub;width:16px;height:16px;font-size:16px;" class="dashicons dashicons-arrow-down-alt2"></span></button>

				<div style="padding-top:0;" id="blocks-wrap">

					<p>I'm pleased to announce that Simple Sitemap plugin now includes support for <a href="https://wordpress.org/gutenberg/" target="_blank">blocks</a>! This means you can now easily add a sitemap visually directly inside the WordPress editor. No more swapping back and forth between the editor and front end to preview the sitemap.</p>

					<p>There are two sitemap blocks available. One to insert a standard sitemap, and the other to display a list of posts grouped by taxonomy. These two blocks can be used as a direct replacement for the following shortcodes.</p>

					<ul>
						<li><code>[simple-sitemap]</code></li>
						<li><code>[simple-sitemap-group]</code></li>
					</ul>

					<p>All shortcode attributes are now supported inside the editor via a specially built user interface.</p>

					<div style="margin-top:20px;"><img style="max-width:550px;" src="<?php 
        echo  $this->module_roots['pdir'] ;
        ?>shared/images/simple-sitemap-block.png" /></div>

					<div>
						<h4>Usage Instructions:</h4>
						<ol>
							<li>Inside the new editor click the plus icon to insert a new block.</li>
							<li>In the popup window search for 'sitemap' or scroll down until you see the Simple Sitemap blocks section, and expand it.</li>
							<li>Click on the particular sitemap block icon you want to insert.</li>
							<li>Once the block has been added to the editor you can access block settings in the inspector panel to the right.</li>
							<li>Make sure to save your changes and then view the sitemap on the front end!</li>
						</ol>
					</div>
				</div>
			</div>

			<h2 style="margin:35px 0 0 0;">Sitemap Shortcodes</h2>

			<p>Shortcodes have been around for a long time in WordPress and, before blocks were available, they were the only really accessible way to add complex or dynamic content to the editor. We've included sitemap shortcodes for those who prefer them to blocks, or if you don't have any choice. e.g. If using a 3rd party page builder, or the block editor has been disabled.</p>

			<p style="font-size:14px;">Expand the section directly below to see all Simple Sitemap shortcodes currently available, along with a full list of supported shortcode attributes.</p>

			<div class="wpgo-expand-box">
				<h4 style="margin-top:5px;display:inline-block;margin-bottom:10px;">Available Shortcodes & Supported Attributes</h4><button id="attributes-btn" class="button">Expand <span style="vertical-align:sub;width:16px;height:16px;font-size:16px;" class="dashicons dashicons-arrow-down-alt2"></span></button>
				<div id="attributes-wrap" style="padding-bottom:10px;">
					<div style="padding-top:0;">
						<p>Click on a shortcode below to view the full list of supported attributes, along with a description and the default value used if the shortcode attribute is omitted.</p>
						
						<p><strong>Note:</strong> We now recommend using sitemap blocks instead of shortcodes where possible for a better user experience.</p>

						<p style="margin:25px 0 0 0;"><code style="font-size:15px;"><a class="code-link" href="#simple-sitemap-shortcode">[simple-sitemap]</a></code> <?php 
        printf( __( 'Displays a list of posts for one or more post types.', 'simple-sitemap' ) );
        ?></p>

						<p style="margin:15px 0 0 0;"><code style="font-size:15px;"><a class="code-link" href="#simple-sitemap-group-shortcode">[simple-sitemap-group]</a></code> <?php 
        printf( __( 'Displays a list of posts grouped by category, OR tags.', 'simple-sitemap' ) );
        ?></p>

						<p style="margin:15px 0 0 0;"><code style="font-size:15px;"><a class="code-link" href="#simple-sitemap-tax-shortcode">[simple-sitemap-tax]</a></code><?php 
        echo  $pro_attribute ;
        ?> <?php 
        printf( __( 'Displays a list of taxonomy terms for any registered taxonomy (e.g. categories).', 'simple-sitemap' ) );
        ?></p>

						<p style="margin:15px 0 0 0;"><code style="font-size:15px;"><a class="code-link" href="#simple-sitemap-menu-shortcode">[simple-sitemap-menu]</a></code><?php 
        echo  $pro_attribute ;
        ?> <?php 
        printf( __( 'Displays a sitemap based on a nav menu.', 'simple-sitemap' ) );
        ?></p>

						<p style="margin:15px 0 30px 0;"><code style="font-size:15px;"><a class="code-link" href="#simple-sitemap-child-shortcode">[simple-sitemap-child]</a></code><?php 
        echo  $pro_attribute ;
        ?> <?php 
        printf( __( 'Displays a list of child pages for a specific parent page.', 'simple-sitemap' ) );
        ?></p>

						<?php 
        ?>
						<hr><br>
					</div>

					<p id="simple-sitemap-shortcode" style="margin:10px 0 20px 0;"><code style="font-size:15px;">[simple-sitemap ... ]</code></p>
					<ul class="shortcode-attributes">
						<li><code>render=""</code> - Set to "tab" to display posts in a tabbed layout!</li>
						<li><code>page_depth="0"</code> - For the 'page' post type allow the indentation depth to be specified.</li>
						<li><code>orderby="title"</code> - Value to sort posts by (title, date, author etc.). See the full list <a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">here</a>.</li>
						<li><code>order="asc"</code> - List posts for each post type in ascending, or descending order.</li>
						<li><code>show_excerpt="false"</code> - Optionally show post excerpt (if defined) under each sitemap item.</li>
						<li><code>show_label="true"</code> - Optionally show post type label above the sitemap list of posts.</li>
						<li><code>links="true"</code> - Show sitemap items as links or plain text.</li>
						<li><code>title_tag=""</code> - Tag used to wrap each sitemap item in a specified tag.</li>
						<li><code>post_type_tag="h3"</code> - Tag used to display the post type label.</li>
						<li><code>excerpt_tag="div"</code> - Tag used to wrap the post excerpt text.</li>
						<li><code>container_tag="ul"</code> - List type tag, ordered, or unordered.</li>
						<li><code>types="page"</code> - List posts or pages (or both) in the order entered. e.g. <code>types="post, page"</code></li>
						<li><code>include=""</code><?php 
        echo  $pro_attribute ;
        ?> - Comma separated list of post IDs to include in the sitemap only. Other posts will be ignored.</li>
						<li><code>exclude=""</code><?php 
        echo  $pro_attribute ;
        ?> - Comma separated list of post IDs to exclude from the sitemap.</li>
						<li><code>image="false"</code><?php 
        echo  $pro_attribute ;
        ?> - Optionally show the post featured image (if defined) next to each sitemap item.</li>
						<li><code>image_size="22"</code><?php 
        echo  $pro_attribute ;
        ?> - Size of the post featured image (if displayed).</li>
						<li><code>list_icon="true"</code><?php 
        echo  $pro_attribute ;
        ?> - Optionally display HTML bullet icons.</li>
						<li><code>separator="false"</code><?php 
        echo  $pro_attribute ;
        ?> - Optionally render separator lines between sitemap items.</li>
						<li><code>horizontal="false"</code><?php 
        echo  $pro_attribute ;
        ?> - Set to "true" to display sitemap items in a flat horizontal list. Great for adding a sitemap to the footer!</li>
						<li><code>horizontal_separator=", "</code><?php 
        echo  $pro_attribute ;
        ?> - The character(s) used to separate sitemap items. Use with the 'horizontal' attribute.</li>
						<li><code>nofollow="false"</code><?php 
        echo  $pro_attribute ;
        ?> - Set to "true" to make sitemap links <a href="https://en.wikipedia.org/wiki/Nofollow" target="_blank">nofollow</a>.</li>
						<li><code>num_posts="-1"</code><?php 
        echo  $pro_attribute ;
        ?> - Limit the number of posts outputted in the sitemap.</li>
						<li><code>visibility="true"</code><?php 
        echo  $pro_attribute ;
        ?> - Control whether private posts/pages are displayed in the sitemap.</li>
						<li><code>page_excerpt_length="25"</code><?php 
        echo  $pro_attribute ;
        ?> - Trim page excerpt length to specific number of words.</li>
						<li><code>sitemap_item_line_height=""</code><?php 
        echo  $pro_attribute ;
        ?> - CSS <code>line-height</code> for individual sitemap items.</li>
						<li><code>sitemap_container_margin=""</code><?php 
        echo  $pro_attribute ;
        ?> - CSS <code>margin</code> for the sitemap container.</li>
						<li><code>responsive_breakpoint="500px"</code><?php 
        echo  $pro_attribute ;
        ?> - CSS responsive breakpoint value.</li>
						<li><code>max_width=""</code><?php 
        echo  $pro_attribute ;
        ?> - Controls the CSS <code>max-width</code> of the sitemap container.</li>
						<li><code>post_type_label_padding="10px 20px"</code><?php 
        echo  $pro_attribute ;
        ?> - CSS <code>padding</code> for the post type label (if displayed).</li>
						<li><code>post_type_label_font_size=""</code><?php 
        echo  $pro_attribute ;
        ?> - CSS <code>font-size</code> for the post type label (if displayed).</li>
						<li><code>tab_header_bg="#de5737"</code><?php 
        echo  $pro_attribute ;
        ?> - CSS <code>background-color</code> for the active sitemap tab.</li>
						<li><code>tab_color="#ffffff"</code><?php 
        echo  $pro_attribute ;
        ?> - CSS <code>color</code> for the sitemap tab text.</li>
					</ul>

					<p id="simple-sitemap-group-shortcode" style="margin:35px 0 20px 0;"><code style="font-size:15px;">[simple-sitemap-group ... ]</code></p>

					<ul class="shortcode-attributes">
						<li><code>tax="category"</code> - List posts grouped by categories OR tags ('post_tag').</li>
						<li><code>title_tag=""</code> - Tag used to wrap each sitemap item in a specified tag.</li>
						<li><code>show_excerpt="false"</code> - Optionally show post excerpt (if defined) under each sitemap item.</li>
						<li><code>excerpt_tag="div"</code> - Tag used to wrap the post excerpt text.</li>
						<li><code>links="true"</code> - Show sitemap items as links or plain text.</li>
						<li><code>orderby="title"</code> - Value to sort posts by (title, date, author etc.). See the full list <a href="https://codex.wordpress.org/Class_Reference/WP_Query#Order_.26_Orderby_Parameters" target="_blank">here</a>.</li>
						<li><code>order="asc"</code> - List posts for each post type in ascending, or descending order.</li>
						<li><code>post_type_tag="h3"</code> - Tag used to display the post type label.</li>
						<li><code>show_label="true"</code> - Optionally show post type label above the sitemap list of posts.</li>
						<li><code>page_depth="0"</code> - For the 'page' post type allow the indentation depth to be specified.</li>
						<li><code>container_tag="ul"</code> - List type tag, ordered, or unordered.</li>
						<li><code>num_terms="0"</code> - Limit the number of taxonomy terms displayed.</li>
						<li><code>type="post"</code><?php 
        echo  $pro_attribute ;
        ?> - List posts grouped by taxonomy from ANY post type.</li>
						<li><code>term_orderby="name"</code><?php 
        echo  $pro_attribute ;
        ?> - Order post taxonomy term labels by title etc.</li>
						<li><code>term_order="asc"</code><?php 
        echo  $pro_attribute ;
        ?> - List taxonomy term labels in ascending, or descending order.</li>
						<li><code>separator="false"</code><?php 
        echo  $pro_attribute ;
        ?> <em>(coming soon)</em> - Optionally render separator lines between sitemap items.</li>
						<li><code>image="false"</code><?php 
        echo  $pro_attribute ;
        ?> - Optionally show the post featured image (if defined) next to each sitemap item.</li>
						<li><code>list_icon="true"</code><?php 
        echo  $pro_attribute ;
        ?> - Optionally display HTML bullet icons.</li>
						<li><code>include_terms=""</code><?php 
        echo  $pro_attribute ;
        ?> - Comma separated list of taxonomy terms to include.</li>
						<li><code>exclude_terms=""</code><?php 
        echo  $pro_attribute ;
        ?> - Comma separated list of taxonomy terms to exclude.</li>
						<li><code>visibility="true"</code><?php 
        echo  $pro_attribute ;
        ?> - Control whether private posts/pages are displayed in the sitemap.</li>
						<li><code>num_posts="-1"</code><?php 
        echo  $pro_attribute ;
        ?> - Limit the number of posts outputted in the sitemap.</li>
						<li><code>nofollow="false"</code><?php 
        echo  $pro_attribute ;
        ?> - Set to "true" to make sitemap links <a href="https://en.wikipedia.org/wiki/Nofollow" target="_blank">nofollow</a>.</li>
						<li><code>taxonomy_links="false"</code><?php 
        echo  $pro_attribute ;
        ?> Show sitemap taxonomy items as links or plain text.</li>
						<li><code>term_tag="h3"</code><?php 
        echo  $pro_attribute ;
        ?> HTML tag for the term title.</li>
						<li><code>render_class=""</code><?php 
        echo  $pro_attribute ;
        ?> Add custom CSS class(es) to the sitemap container element.</li>
						<li><code>post_type_label_font_size=""</code><?php 
        echo  $pro_attribute ;
        ?> - CSS <code>font-size</code> for the post type label (if displayed).</li>
						<li><code>sitemap_item_line_height=""</code><?php 
        echo  $pro_attribute ;
        ?> - CSS <code>line-height</code> for individual sitemap items.</li>
						<li><code>sitemap_container_margin=""</code><?php 
        echo  $pro_attribute ;
        ?> - CSS <code>margin</code> for the sitemap container.</li>
						<li><code>exclude=""</code><?php 
        echo  $pro_attribute ;
        ?> <em>(coming soon)</em> - Comma separated list of post IDs to exclude from the sitemap.</li>
						<li><code>include=""</code><?php 
        echo  $pro_attribute ;
        ?> <em>(coming soon)</em> - Comma separated list of post IDs to include in the sitemap.</li>
					</ul>

					<p id="simple-sitemap-tax-shortcode" style="margin:35px 0 20px 0;"><code style="font-size:15px;">[simple-sitemap-tax ... ]</code></p>

					<ul class="shortcode-attributes">
						<li><code>taxonomy="category"</code><?php 
        echo  $pro_attribute ;
        ?> - Taxonomy type.</li>
						<li><code>include=""</code><?php 
        echo  $pro_attribute ;
        ?> - Comma separated list of taxonomy IDs to include in the sitemap only. Other taxonomies will be ignored.</li>
						<li><code>exclude=""</code><?php 
        echo  $pro_attribute ;
        ?> - Comma separated list of taxonomy IDs to exclude from the sitemap.</li>
						<li><code>depth="0"</code><?php 
        echo  $pro_attribute ;
        ?> - Controls indentation depth.</li>
						<li><code>child_of="0"</code><?php 
        echo  $pro_attribute ;
        ?> - Only show children of a specific category.</li>
						<li><code>title_li=""</code><?php 
        echo  $pro_attribute ;
        ?> - Text used for the list title element. Pass an empty string to disable.</li>
						<li><code>nofollow="false"</code><?php 
        echo  $pro_attribute ;
        ?> - Set to "true" to make sitemap links <a href="https://en.wikipedia.org/wiki/Nofollow" target="_blank">nofollow</a>.</li>
						<li><code>show_count="false"</code><?php 
        echo  $pro_attribute ;
        ?> - Display post counts when set to true.</li>
						<li><code>orderby="name"</code><?php 
        echo  $pro_attribute ;
        ?> - Value to sort taxonomies by (name, id etc.).</li>
						<li><code>order="asc"</code><?php 
        echo  $pro_attribute ;
        ?> - List taxonomies in ascending, or descending order.</li>
						<li><code>hide_empty="0"</code><?php 
        echo  $pro_attribute ;
        ?> - Hide empty taxonomies (accepts '0' or '1').</li>
					</ul>

					<p id="simple-sitemap-menu-shortcode" style="margin:35px 0 20px 0;"><code style="font-size:15px;">[simple-sitemap-menu ... ]</code></p>

					<ul class="shortcode-attributes">
						<li><code>menu=""</code><?php 
        echo  $pro_attribute ;
        ?> - Menu to be displayed. specify the menu name, menu ID, slug, or object. e.g. <code>menu='Main Menu'</code>.</li>
						<li><code>container="false"</code><?php 
        echo  $pro_attribute ;
        ?> - Whether to wrap the ul, and what to wrap it with. e.g. <code>"div"</code>. Also accepts <code>"false"</code>.</li>
						<li><code>menu_class="simple-sitemap-nav-menu"</code><?php 
        echo  $pro_attribute ;
        ?> - CSS class to use for the ul element which forms the menu.</li>
						<li><code>horizontal_separator=", "</code><?php 
        echo  $pro_attribute ;
        ?> - Separator character used when displaying menu items as a horizontal list.</li>
						<li><code>list_icon="true"</code><?php 
        echo  $pro_attribute ;
        ?> - Display list icon for each menu sitemap item.</li>
						<li><code>container_class=""</code><?php 
        echo  $pro_attribute ;
        ?> - Class applied to the menu container element.</li>
						<li><code>label=""</code><?php 
        echo  $pro_attribute ;
        ?> - Text label displayed above menu items.</li>
						<li><code>exclude_menu_ids=""</code><?php 
        echo  $pro_attribute ;
        ?> - Comma separated list of menu IDs to exclude from the sitemap..</li>
						<li><code>include_menu_ids=""</code><?php 
        echo  $pro_attribute ;
        ?> - Comma separated list of menu IDs to include in the sitemap.</li>
					</ul>

					<p id="simple-sitemap-child-shortcode" style="margin:35px 0 20px 0;"><code style="font-size:15px;">[simple-sitemap-child ... ]</code></p>

					<ul class="shortcode-attributes">
						<li><code>child_of="0"</code><?php 
        echo  $pro_attribute ;
        ?> - Display only the sub-pages of a single page by ID. Default 0 (all pages).</li>
						<li><code>title_li=""</code><?php 
        echo  $pro_attribute ;
        ?> - Text used for the list title element. Pass an empty string to disable.</li>
						<li><code>nofollow="false"</code><?php 
        echo  $pro_attribute ;
        ?> - Set to "true" to make sitemap links <a href="https://en.wikipedia.org/wiki/Nofollow" target="_blank">nofollow</a>.</li>
						<li><code>post_type="page"</code><?php 
        echo  $pro_attribute ;
        ?> - Post type to query for.</li>
						<li><code>show_excerpt="false"</code> - Optionally show post excerpt (if defined) under each sitemap item.</li>
						<li><code>page_excerpt_length="25"</code><?php 
        echo  $pro_attribute ;
        ?> - Trim page excerpt length to specific number of words.</li>
					</ul>
				</div>
			</div>

			<h2 style="margin:35px 0 0 0;">Sitemap Settings</h2>

			<div class="wpgo-expand-box" style="margin-top:20px;">
				<p>Use this section to manage plugin options.</p>

				<div style="padding-top:10px;" class="settings-wrap">

					<!-- Start Main Form -->
					<form id="plugin-options-form" method="post" action="options.php">
						<?php 
        $options = self::get_plugin_options();
        settings_fields( 'simple_sitemap_options_group' );
        ?>

						<div class="simple-sitemap-pro-tab">
							<table class="form-table">

								<tr valign="top">
									<td colspan="2" style="padding:0;">
										<div>

											<label><input name="simple_sitemap_options[chk_parent_page_link]" type="checkbox" value="1" 
											<?php 
        if ( isset( $options['chk_parent_page_link'] ) ) {
            checked( '1', $options['chk_parent_page_link'] );
        }
        ?>
											> Remove parent page links?</label><br><br>

											<input type="text" class="exclude regular-text code" name="simple_sitemap_options[txt_exclude_parent_pages]" value="<?php 
        echo  $options['txt_exclude_parent_pages'] ;
        ?>">

											<p class="description">Enter comma separated list of parent page IDs to remove specific links. Leave blank to remove ALL parent page links.</p>
											</div>
									</td>
								</tr>

								<tr valign="top" style="display:none;">
									<th scope="row">Advanced Configuration</th>
									<td>
										<textarea name="simple_sitemap_options[txtar_sitemap_script]" rows="7" cols="50" type='textarea'><?php 
        echo  $options['txtar_sitemap_script'] ;
        ?></textarea>
										<p class="description">Add script into the box above to output an advanced sitemap.</p>
									</td>
								</tr>
							</table>
						</div>

						<div class="support-tab">
							<?php 
        do_settings_sections( 'simple-sitemap-menu' );
        ?>
						</div>

						<?php 
        submit_button();
        ?>

					</form>
					<!-- main form closing tag -->

					<form action="<?php 
        echo  self::current_url() ;
        // current page url
        ?>" method="post" id="simple-sitemap-reset-form" style="display:inline;">
						<div style="padding-bottom:10px;"><span id="simple-sitemap-reset"><a href="#">Reset plugin options</a><input type="hidden" name="reset_options" value="true"></span></div>
					</form>
				</div>
			</div>

			<div style="margin-top:25px;"></div>

			<h2 style="margin:35px 0 0 0;">Stay In Touch!</h2>

			<table class="form-table">

				<?php 
        do_action( $this->hook_prefix . '_settings_row_section_1', 'https://www.paypal.com/donate?hosted_button_id=FBAG4ZHA4TTUC' );
        ?>

				<?php 
        echo  $this->settings_fw->try_our_other_plugins( basename( $this->module_roots['dir'] ) ) ;
        ?>

				<?php 
        echo  $this->settings_fw->subscribe_to_newsletter( 'http://eepurl.com/bXZmmD' ) ;
        ?>

				<?php 
        echo  $this->settings_fw->keep_in_touch() ;
        ?>

				<?php 
        echo  $this->settings_fw->report_issues( admin_url() . 'admin.php?page=simple-sitemap-menu-contact' ) ;
        ?>

			</table>
			</div>
		</div>
		<?php 
    }
    
    /**
     * Get URL of current page.
     *
     * @return String Current URL.
     */
    public static function current_url()
    {
        return (( isset( $_SERVER['HTTPS'] ) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http' )) . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
    }
    
    /**
     * Get plugin option default settings.
     */
    public static function get_default_plugin_options()
    {
        $defaults = array();
        // Setup an array to store list of checkboxes that have a checkbox default set to 1.
        $defaults['default_on_checkboxes'] = array();
        // Add plugin specific default settings via this filter hook.
        return Hooks::simple_sitemap_defaults( $defaults );
    }
    
    /**
     * Get current plugin options.
     *
     * Merges plugin options with the defaults to ensure any gaps are filled.
     * i.e. when adding new options.
     */
    public static function get_plugin_options()
    {
        $options = get_option( 'simple_sitemap_options' );
        $defaults = self::get_default_plugin_options();
        // Store the OFF checkboxes array.
        $default_on_checkboxes_arr = $defaults['default_on_checkboxes'];
        // Remove the OFF checkboxes array from the main defaults array.
        unset( $defaults['default_on_checkboxes'] );
        if ( is_array( $options ) ) {
            // Merge OFF checkboxes into main options array to add entries for empty checkboxes.
            $options = array_merge( $default_on_checkboxes_arr, $options );
        }
        return wp_parse_args( $options, $defaults );
        // return wp_parse_args(
        // get_option( 'simple_sitemap_options' ),
        // self::get_default_plugin_options()
        // );
    }
    
    /**
     * Filters the order of menu.
     *
     * @param String $custom A custom request string.
     * @return Array List of items in the menu.
     * 
     * @todo Add this to the plugin framework.
     */
    public function filter_menu_order( $custom )
    {
        global  $submenu ;
        // selectively rearrange for 'tabs', and always for 'menu' .
        if ( SITEMAP_FREEMIUS_NAVIGATION === 'tabs' ) {
            // don't bother to rearrange unless the submenu page is displayed.
            if ( !isset( $_GET['page'] ) || $_GET['page'] !== $this->new_features_slug && $_GET['page'] !== $this->welcome_slug ) {
                return $custom;
            }
        }
        $parent_slug = $this->custom_plugin_data->parent_slug;
        $menu_type = $this->custom_plugin_data->menu_type;
        $pricingpage_index = 0;
        $parent_index = 0;
        $subpage_index1 = 0;
        $subpage_index2 = 0;
        $wp_org_support_forum_index = 0;
        // if global menu array is empty then don't try to re-index. This is typically empty when the Freemius
        // opt-in is displayed.
        if ( empty($submenu[$parent_slug]) ) {
            return $custom;
        }
        // store menu indexes of settings pages.
        foreach ( $submenu[$parent_slug] as $menu_index => $val ) {
            $menu_slug = $val[2];
            if ( $menu_slug === $this->settings_slug ) {
                $parent_index = $menu_index;
            }
            if ( $menu_slug === $this->new_features_slug ) {
                $subpage_index1 = $menu_index;
            }
            if ( $menu_slug === $this->welcome_slug ) {
                $subpage_index2 = $menu_index;
            }
            if ( $menu_slug === $this->custom_plugin_data->plugin_cpt_slug . '-wp-support-forum' ) {
                $wp_org_support_forum_index = $menu_index;
            }
            if ( $val[2] === $this->custom_plugin_data->freemius_slug . '-pricing' ) {
                $pricingpage_index = $menu_index;
            }
            // in the case of a CPT being used as the parent menu the Freemius generated pages will use
            // a different prefix to the normal one
            // if ($this->custom_plugin_data->menu_type === 'top-cpt') {
            // if ($menu_slug === $this->custom_plugin_data->plugin_cpt_slug . '-pricing') {
            // $pricingpage_index = $menu_index;
            // }
            // } else {
            // if ($menu_slug === $this->settings_slug . '-pricing') {
            // $pricingpage_index = $menu_index;
            // }
            // } // .
        }
        // Only re-index new features page if menu type is 'sub'.
        if ( 'sub' === $menu_type ) {
            // Only re-index if tabs are active and we're on new feature settings page OR tabs are not active.
            
            if ( SITEMAP_FREEMIUS_NAVIGATION === 'tabs' && $_GET['page'] === $this->new_features_slug || SITEMAP_FREEMIUS_NAVIGATION === 'menu' ) {
                // Find the next available index after the main settings page.
                $tmp_parent_index1 = $parent_index;
                while ( isset( $submenu[$parent_slug][$tmp_parent_index1] ) ) {
                    $tmp_parent_index1++;
                }
                // Move new features page to next position after main settings page.
                $submenu[$parent_slug][$tmp_parent_index1] = $submenu[$parent_slug][$subpage_index1];
                unset( $submenu[$parent_slug][$subpage_index1] );
                ksort( $submenu[$parent_slug] );
            }
        
        }
        // Only re-index welcome (about) page if tabs are active and we're on welcome settings page, OR tabs are not active.
        
        if ( SITEMAP_FREEMIUS_NAVIGATION === 'tabs' && $_GET['page'] === $this->welcome_slug || SITEMAP_FREEMIUS_NAVIGATION === 'menu' ) {
            // Find the next available index after the pricing page unless tabs are active in which case get next.
            // Available index after main settings page.
            
            if ( SITEMAP_FREEMIUS_NAVIGATION === 'tabs' ) {
                $tmp_parent_index2 = $parent_index;
            } else {
                $tmp_parent_index2 = $pricingpage_index;
            }
            
            // $pricingpage_index will be 0 when license has been activated
            // @todo perhaps better to always make the about page last menu item rather than dependent on pricing page (if it exists).
            if ( 0 === $pricingpage_index ) {
                $tmp_parent_index2 = $wp_org_support_forum_index;
            }
            while ( isset( $submenu[$parent_slug][$tmp_parent_index2] ) ) {
                $tmp_parent_index2++;
            }
            // Move welcome page to next position after pricing/support forum page.
            $submenu[$parent_slug][$tmp_parent_index2] = $submenu[$parent_slug][$subpage_index2];
            unset( $submenu[$parent_slug][$subpage_index2] );
            ksort( $submenu[$parent_slug] );
        }
        
        return $custom;
    }

}