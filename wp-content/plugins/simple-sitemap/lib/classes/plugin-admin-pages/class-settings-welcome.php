<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Plugin 'Welcome' settings page
 */
class Settings_Welcome {

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
	 * @param Array $plugin_data Plugin data.
	 * @param Array $custom_plugin_data Custom plugin data.
	 * @param Array $utility Utility data.
	 */
	public function __construct( $module_roots, $plugin_data, $custom_plugin_data, $utility ) {
		$this->module_roots                  = $module_roots;
		$this->plugin_data                   = $plugin_data;
		$this->custom_plugin_data            = $custom_plugin_data;
		$this->freemius_upgrade_url          = $this->custom_plugin_data->freemius_upgrade_url;
		$this->freemius_discount_upgrade_url = $this->custom_plugin_data->freemius_discount_upgrade_url;
		$this->new_features_url              = $this->custom_plugin_data->new_features_url;
		$this->admin_url                     = $this->custom_plugin_data->admin_url;
		$this->utility                       = $utility;

		// $this->pro_attribute = '<span class="pro" title="Shortcode attribute available in ' . $this->custom_plugin_data->main_menu_label . ' Pro"><a href="' . $this->freemius_upgrade_url . '">PRO</a></span>';
		// $this->settings_slug = $this->custom_plugin_data->settings_pages['settings']['slug'];
		// $this->new_features_slug = $this->custom_plugin_data->settings_pages['new-features']['slug'];
		$this->welcome_slug = $this->custom_plugin_data->settings_pages['welcome']['slug'];
		$this->plugin_data  = $plugin_data;

		add_action( 'admin_menu', array( &$this, 'add_options_page' ) );
	}

	/**
	 * Add menu page.
	 */
	public function add_options_page() {
		$parent_slug = null;

		if ( SITEMAP_FREEMIUS_NAVIGATION === 'tabs' ) {
			// Only show submenu page when tabs enabled if welcome tab is active.
			if ( isset( $_GET['page'] ) && $_GET['page'] === $this->welcome_slug ) {
				$parent_slug = $this->custom_plugin_data->parent_slug;
			}
		} else {
			// Always use this if navigation is set to 'menu'.
			$parent_slug = $this->custom_plugin_data->parent_slug;
		}

		if ( 'top' === $this->custom_plugin_data->menu_type || 'top-cpt' === $this->custom_plugin_data->menu_type ) {
			$label = 'About';
		} elseif ( 'sub' === $this->custom_plugin_data->menu_type ) {
			$label = '<span class="fs-submenu-item fs-sub wpgo-plugins">About</span>';
		}

		$hook = add_submenu_page(
			'simple-sitemap-menu', //$parent_slug,
			__( 'Welcome to ' . $this->custom_plugin_data->main_menu_label, 'simple-sitemap' ),
			$label,
			'manage_options',
			'simple-sitemap-menu-welcome', // $this->welcome_slug,
			array( &$this, 'render_sub_menu_form' )
		);

	}

	/**
	 * Display the sub menu page.
	 */
	public function render_sub_menu_form() {

		$tabs_list_html        = $this->utility->build_settings_tabs_html( $this->plugin_data );
		$tab_classes           = SITEMAP_FREEMIUS_NAVIGATION === 'tabs' ? ' fs-section fs-full-size-wrapper' : ' no-tabs';
		$is_premium            = $this->custom_plugin_data->is_premium;
				$plugin_lbl    = $this->custom_plugin_data->main_menu_label;
				$more_features = $is_premium ? 'View the full set of <a href="https://wpgoplugins.com/document/simple-sitemap-pro-documentation/" target="_blank">shortcode attributes</a> available.' : 'See the <a href="' . $this->custom_plugin_data->main_settings_url . '">main settings</a> page for a list of shortcode attributes available in the free version (expand the <strong>Shortcode Attributes & Default Values</strong> section).';
		?>
				<div class="wrap <?php echo $tab_classes; ?> about-wrap">
					<?php echo $tabs_list_html; ?>
					<div class="wpgo-settings-inner about-con">
						<div class="header">
							<div class="header-img">
								<img style="box-shadow:none;" src="<?php echo $this->module_roots['uri'] . '/lib/assets/images/simple-sitemap.svg'; ?>" />
							</div>
							<div class="sub-header">
								<div class="sub-header-title">
									<div>
										<h1 class="title">Welcome to <?php _e( $plugin_lbl . '!', 'simple-sitemap' ); ?></h1>
									</div>
									<div>
										<span class="dashicons dashicons-flag"></span>
										<span class="title-ver">v<?php echo $this->plugin_data['Version']; ?></span>
										<span style="display:none;" class="title-ver">v<?php echo $this->plugin_data['Version']; ?> | <a href="#">Changelog</a></span>
									</div>
								</div>
								<div class="title-desc">
									<p>This release see's a new positive direction for the Simple Sitemap plugin. Starting with this version you'll be redirected to this welcome page upon activation (and when updating). There's also a <a href="<?php echo $this->new_features_url; ?>">New Features & Updates</a> page which goes into more detail about new plugin features and other relevant updates.</p>
								</div>
							</div>
						</div>

						<?php if ( ! $is_premium ) : ?>
							<table class="premium-tbl">
								<tr>
									<td>
										<a href="<?php echo $this->freemius_discount_upgrade_url; ?>">
											<button class="button upgrade-txt-pc" title="Apply the exclusive discount offer right now">Upgrade to PRO - <span style="color:red;">30% OFF</span></button>
										</a>
									</td>
									<td>
										<div class="detail-cel">
											<strong>Exclusive discount offer for our FREE plugin users!</strong>
											Get immediate access to <a class="upgrade-link" href="<?php echo $this->freemius_upgrade_url; ?>" title="View the FULL list of pro plugin features" target="_blank">all Pro features</a> right now, at a significantly reduced price! <small>(first year only - renewals charged at normal rate)</small>
										</div>
									</td>
								</tr>
							</table>
						<?php endif; ?>

						<div class="plugin-desc-con">
							<div class="new-con plugin-des-item">
								<div class="new-fea-con-des">
									<h2 style="margin-top:25px;" class="sub-title">What's New in <?php echo $this->plugin_data['Version']; ?>?</h2>
									<p>To see all new major features for this plugin release, click the <span style="font-weight:bold;">'New Features'</span> button below for more details. You can also access the plugin documentation, and check out the the live sitemap demo examples via the other two buttons.</p>
									<div>
										<span><a class="plugin-btn" href="<?php echo $this->new_features_url; ?>">New Features<span style="margin:0 -2px 0 3px;width:18px;height:18px;" class="dashicons dashicons-arrow-right-alt2"></span></a></span>
										<span style="margin-left:5px;"><a style="background:#933c60;border:2px #6d314a solid;" class="plugin-btn" href="https://wpgoplugins.com/document/simple-sitemap-pro-documentation/" target="_blank">Plugin Docs</a></span>
										<span style="margin-left:5px;"><a style="background:#f5a356;border:2px #d9914e solid;" class="plugin-btn" href="https://demo.wpgothemes.com/flexr/simple-sitemap-pro-demo/" target="_blank">Sitemap Live Demo</a></span>
									</div>
							</div>

							<div class="getting-start-con plugin-des-item">
								<h2 class="sub-title">Getting Started Using <?php echo $plugin_lbl; ?></h2>
								<p>The Simple Sitemap plugin is one of the easiest way to add a comprehensive HTML sitemap to your website. To get started quickly, follow these steps and you'll be creating a sitemap in no time at all. For full instructions take a look at the plugin <a href="https://wpgoplugins.com/document/simple-sitemap-pro-documentation/" target="_blank">documentation</a>.</p>
								<div class="getting-start-gen-con">
									<div class="getting-start-gen">
										<h3 style="margin-top:0;display:flex;align-items:center;"><span><img style="margin:0 6px 2px 0;width:40px;" src="<?php echo $this->module_roots['uri'] . '/lib/assets/images/icons/1s.svg'; ?>" /></span><span> Create an HTML Sitemap Page</span></h3>
										<p>The first thing to do is to create a <a href="<?php echo $this->admin_url . '/post-new.php?post_type=page'; ?>" target="_blank">new page</a> that you want to display the sitemap on. Then, enter a page title. e.g. "Sitemap".</p>
									</div>
									<div class="getting-start-img">
										<img src="<?php echo $this->module_roots['uri'] . '/lib/assets/images/create-sitemap-page.png'; ?>" />
									</div>
								</div>
								<div class="getting-start-gen-con">
									<div class="getting-start-gen">
									<h3 style="margin-top:0;display:flex;align-items:center;"><span><img style="margin:0 6px 2px 0;width:40px;" src="<?php echo $this->module_roots['uri'] . '/lib/assets/images/icons/2s.svg'; ?>" /></span><span> Add a Sitemap Block or Shortcode</span></h3>
										<p>There are two ways to add a sitemap into a post or page:</p>
										<ul style="list-style:initial;padding-left:20px;">
											<li><a href="https://wordpress.org/support/article/adding-a-new-block/" target="_blank">Blocks</a></li>
											<li><a href="https://codex.wordpress.org/shortcode" target="_blank">Shortcodes</a></li>
										</ul>
										<p>If you want to add a sitemap block then you can see a list of available ones via the block inserter (along with all the other blocks). To find them quicker just start typing <em>'sitemap'</em> in the block inserter to see only sitemap blocks listed.</p>
										<p>Sitemap shortcodes are also included in the plugin as they can be used in any page builder too, which is very useful if you're not using the core block editor. Any block setting is also available as a shortcode attribute for convenience. See <a href="https://wpgoplugins.com/document/simple-sitemap-pro-documentation/#simple-sitemap-shortcodes" target="_blank">here</a> for a list of available sitemap shortcodes.</p>
									</div>
									<div class="getting-start-img">
										<img src="<?php echo $this->module_roots['uri'] . '/lib/assets/images/add-sitemap-block.png'; ?>" />
									</div>
								</div>
								<div class="getting-start-gen-con">
									<div class="getting-start-gen">
									<h3 style="margin-top:0;display:flex;align-items:center;"><span><img style="margin:0 6px 2px 0;width:40px;" src="<?php echo $this->module_roots['uri'] . '/lib/assets/images/icons/3s.svg'; ?>" /></span><span> Customize & Display</span></h3>
										<p>Once you've inserted a Sitemap block into the editor you can configure block options via the settings sidebar panel to the right.</p>
										<p>There are many options to choose from so take your time looking through each settings panel.</p>
										<p>If you're using the sitemap shortcodes then take a look <a href="https://wpgoplugins.com/document/simple-sitemap-pro-documentation/#simple-sitemap-shortcodes" target="_blank">here</a> for all the various shortcodes and attributes that are available.</p>
									</div>
									<div class="getting-start-img">
										<img src="<?php echo $this->module_roots['uri'] . '/lib/assets/images/customise-sitemap.png'; ?>" />
									</div>
								</div>
								<div class="getting-start-gen-con">
									<div class="getting-start-gen">
									<h3 style="margin-top:0;display:flex;align-items:center;"><span><img style="margin:0 6px 2px 0;width:40px;" src="<?php echo $this->module_roots['uri'] . '/lib/assets/images/icons/4s.svg'; ?>" /></span><span> Let's Get Started!</span></h3>
										<p>OK, time to get started. What would you like to do first? <span class="dashicons dashicons-smiley"></span></p>
										<div>
											<div><a class="plugin-btn get-started-btn" href="<?php echo $this->admin_url . '/post-new.php?post_type=page'; ?>" target="_blank"><span style="margin:0 -2px 0 3px;width:18px;height:18px;" class="dashicons dashicons-arrow-right-alt2"></span><span class="get-started-txt">Add a new Sitemap page</span></a></div>
											<div><a class="plugin-btn get-started-btn" href="https://wpgoplugins.com/document/simple-sitemap-pro-documentation/" target="_blank"><span style="margin:0 -2px 0 3px;width:18px;height:18px;" class="dashicons dashicons-arrow-right-alt2"></span><span class="get-started-txt">View plugin documentation</span></a></div>
											<div><a class="plugin-btn get-started-btn" href="https://demo.wpgothemes.com/flexr/simple-sitemap-pro-demo/" target="_blank"><span style="margin:0 -2px 0 3px;width:18px;height:18px;" class="dashicons dashicons-arrow-right-alt2"></span><span class="get-started-txt">See some examples</span></a></div>
											<div><a class="plugin-btn get-started-btn" href="<?php echo $this->custom_plugin_data->contact_us_url; ?>" target="_blank"><span style="margin:0 -2px 0 3px;width:18px;height:18px;" class="dashicons dashicons-arrow-right-alt2"></span><span class="get-started-txt">Ask us a question</span></a></div>
										</div>
									</div>
								</div>
							</div>
							<div class="coming-soon-con">
								<h2 class="sub-title">Coming Soon</h2>
								<p>We're not done yet! There's still lot's more feature we want to add to <?php echo $plugin_lbl; ?>. Some of the upcoming features include:</p>
								<ul class="coming-soon-ul">
									<li>More sitemap block settings.</li>
									<li>New sitemap blocks.</li>
									<li>Display number of posts in the sitemap.</li>
									<li>Add dates to sitemap items.</li>
									<li>More color options.</li>
									<li>Add font options.</li>
									<li><strong><em>Plus many more...</em></strong></li>
								</ul>
								<p>If you'd like to be notified of all plugin changes as soon as they are available then please <a href="http://eepurl.com/bXZmmD" target="_blank">signup to our newsletter</a>.</p>
								<p>Or, if you'd like to see a new feature added then why not drop us a line? We always like to hear how our plugins can be improved. Click the button below to send us a message and tell us what's on your mind.</a></p>
								<p><a href="<?php echo $this->custom_plugin_data->contact_us_url; ?>"><button class="button"><strong>Suggest a Feature</strong></button></a></p>
							</div>
						</div>
					</div>
				</div>
				<?php
	}

} /* End class definition */
