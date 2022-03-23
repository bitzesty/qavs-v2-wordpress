<?php

namespace WPGO_Plugins\Simple_Sitemap;

/**
 * Enqueue plugin scripts.
 */
class Enqueue_Scripts
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
     * @param Array  $module_roots Root plugin path/dir.
     * @param Object $utilities_fw An object of API utilities class.
     */
    public function __construct(
        $module_roots,
        $utilities_fw,
        $new_features_arr,
        $plugin_data,
        $custom_plugin_data
    )
    {
        $this->module_roots = $module_roots;
        $this->utilities_fw = $utilities_fw;
        $this->new_features_arr = $new_features_arr;
        $this->plugin_data = $plugin_data;
        $this->custom_plugin_data = $custom_plugin_data;
        $this->plugin_version = get_plugin_data( $module_roots['file'] )['Version'];
        $this->enq_pfx = 'simple-sitemap';
        $this->plugin_settings_prefix = 'simple_sitemap';
        // Scripts for plugin settings page.
        add_action( 'admin_enqueue_scripts', array( &$this, 'enqueue_admin_settings_scripts' ) );
        add_action(
            'admin_enqueue_scripts',
            array( &$this, 'enqueue_admin_scripts' ),
            9,
            2
        );
        // Enqueue frontend/editor scripts.
        add_action( 'enqueue_block_assets', array( &$this, 'enqueue_assets' ) );
        add_action( 'enqueue_block_editor_assets', array( &$this, 'enqueue_block_editor_scripts' ) );
        // $this->js_deps = [ 'wp-element', 'wp-i18n', 'wp-hooks', 'wp-components', 'wp-blocks', 'wp-editor', 'wp-compose' ];
        $this->js_deps = array(
            'wp-plugins',
            'wp-element',
            'wp-edit-post',
            'wp-i18n',
            'wp-api-request',
            'wp-data',
            'wp-hooks',
            'wp-plugins',
            'wp-components',
            'wp-blocks',
            'wp-editor',
            'wp-compose'
        );
    }
    
    /**
     * Scripts for all admin pages. This is necessary as we need to modify the main admin menu from JS.
     *
     * @param String $hook Passed as parameter.
     */
    public function enqueue_admin_scripts( $hook )
    {
        // $all_admin_pages_js_url = plugins_url($all_admin_pages_js_rel, $this->module_roots['file']);
        // $all_admin_pages_js_ver = filemtime($this->module_roots['dir'] . $all_admin_pages_js_rel);
        $admin_settings_js = $this->utilities_fw->get_enqueue_version( '/lib/assets/js/update-menu.js', $this->custom_plugin_data->plugin_data['Version'] );
        //$opt_pfx           = $this->custom_plugin_data->db_option_prefix;
        //$new_features_number = \WPGO_Plugins\Plugin_Framework\Upgrade_FW::calc_new_features( $opt_pfx, $this->new_features_arr, $this->plugin_data );
        // Register and localize the script with new data.
        // wp_register_script( $this->enq_pfx . '-update-menu-js', $admin_settings_js['uri'], array( 'wpgo-all-admin-pages-fw-js' ), $admin_settings_js['ver'], true );
        wp_register_script(
            $this->enq_pfx . '-update-menu-js',
            $admin_settings_js['uri'],
            array(),
            $admin_settings_js['ver'],
            true
        );
        $data = array(
            'admin_url'       => admin_url(),
            'nav_status'      => SITEMAP_FREEMIUS_NAVIGATION,
            'hook'            => $hook,
            'menu_type'       => $this->custom_plugin_data->menu_type,
            'main_menu_label' => $this->custom_plugin_data->main_menu_label,
            'plugin_prefix'   => $this->enq_pfx,
        );
        // Keep the handle generic so only one instance is enqueued (if multiple WPGO plugins are installed).
        // wp_enqueue_script('wpgo-all-admin-pages-fw-js', $all_admin_pages_js_url, array(), $all_admin_pages_js_ver, true);
        wp_localize_script( $this->enq_pfx . '-update-menu-js', $this->custom_plugin_data->plugin_settings_prefix . '_admin_menu_data', $data );
        wp_enqueue_script( $this->enq_pfx . '-update-menu-js' );
    }
    
    // sitemap-5_page      _simple-sitemap-menu-welcome
    // simple-sitemap_page _simple-sitemap-menu-welcome
    /**
     * Enqueue front end and editor JavaScript and CSS assets.
     */
    public function enqueue_assets()
    {
        $simple_sitemap_css = $this->utilities_fw->get_enqueue_version( '/lib/assets/css/simple-sitemap.css', $this->plugin_version );
        wp_enqueue_style(
            'simple-sitemap-css',
            $simple_sitemap_css['uri'],
            array(),
            $simple_sitemap_css['ver']
        );
    }
    
    /**
     * Scripts for plugin settings page only.
     *
     * @param String $hook Page hook name.
     * @return Void
     */
    public function enqueue_admin_settings_scripts( $hook )
    {
        
        if ( 'toplevel_page_simple-sitemap-menu' === $hook ) {
            $ss_settings_css = $this->utilities_fw->get_enqueue_version( '/lib/assets/css/admin-settings.css', $this->plugin_version );
            $ss_settings_js = $this->utilities_fw->get_enqueue_version( '/lib/assets/js/simple-sitemap-admin.js', $this->plugin_version );
            wp_enqueue_style(
                'simple-sitemap-settings-welcome-css',
                $ss_settings_css['uri'],
                array(),
                $ss_settings_css['ver']
            );
            wp_enqueue_script(
                'simple-sitemap-settings-welcome-js',
                $ss_settings_js['uri'],
                array(),
                $ss_settings_js['ver']
            );
        }
        
        // Having to do it this way as for the welcome page the hook has the numbered icon number included (when rendered).
        
        if ( strpos( $hook, '_page_simple-sitemap-menu-welcome' ) !== false ) {
            //if ( 'simple-sitemap_page_simple-sitemap-menu-welcome' === $hook ) {
            $ss_settings_css = $this->utilities_fw->get_enqueue_version( '/lib/assets/css/admin-settings.css', $this->plugin_version );
            $ss_settings_js = $this->utilities_fw->get_enqueue_version( '/lib/assets/js/simple-sitemap-admin.js', $this->plugin_version );
            wp_enqueue_style(
                'simple-sitemap-settings-css',
                $ss_settings_css['uri'],
                array(),
                $ss_settings_css['ver']
            );
            //wp_enqueue_script( 'simple-sitemap-settings-js', $ss_settings_js['uri'], array(), $ss_settings_js['ver'] );
        }
    
    }
    
    /**
     * Add scripts for block editor only.
     **/
    public function enqueue_block_editor_scripts()
    {
        $block_editor_js = $this->utilities_fw->get_enqueue_version( '/lib/block_assets/js/blocks.editor.js', $this->plugin_version );
        $deps = $this->js_deps;
        // Block editor script.
        wp_register_script(
            $this->enq_pfx . '-block-editor-js',
            $block_editor_js['uri'],
            $deps,
            $block_editor_js['ver'],
            true
        );
        $data = array(
            'is_premium'           => ss_fs()->is_premium(),
            'can_use_premium_code' => ss_fs()->can_use_premium_code(),
        );
        wp_localize_script( $this->enq_pfx . '-block-editor-js', $this->plugin_settings_prefix . '_editor_data', $data );
        wp_enqueue_script( $this->enq_pfx . '-block-editor-js' );
        $block_editor_css = $this->utilities_fw->get_enqueue_version( '/lib/assets/css/simple-sitemap-block-editor.css', $this->plugin_version );
        // Block editor styles.
        wp_enqueue_style(
            'simple-sitemap-block-editor-css',
            $block_editor_css['uri'],
            array(),
            $block_editor_css['ver']
        );
    }

}
/* End class definition */