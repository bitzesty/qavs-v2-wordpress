<?php
/*
 Plugin Name: Alt Text Tools
 Description: Exports a CSV file of all images that are actually used in your content, along with their corresponding alt tags.
 Version: 0.2.0
 Author: NerdPress
 Author URI: https://www.nerdpress.net
 License: GPLv2
*/

if ( ! defined( 'ABSPATH' ) )
    die( 'YOU SHALL NOT PASS' );

if ( ! defined( 'NP_ATT_VERSION' ) )
    define( 'NP_ATT_VERSION', '0.2.0' );

class NerdpressAltTextTools {
    /**
     * @var string. Plugins PRO Api Key
     *
     * This is reserved for the tiered features
     *
     * @since 0.0.1
     */
    private $apikey;

    /**
     * Class' initializer
     *
     * @since 0.0.1
     */
    public static function init() {
        $class = __CLASS__;
        new $class;
    }
    
    /**
     * Constructor
     *
     * @since 0.0.1
     */
    public function __construct() {
        if ( ! current_user_can( 'manage_options' ) )
            return;

        add_action( 'admin_menu', array( $this, 'settingsPage' ) );
        add_action( 'wp_ajax_getCsv', array( $this, 'getCsv' ) );
        
    }
   
    /**
     * Inject JS script(s) into the settings page
     *
     * @since 0.0.1
     */ 
    public function injectScripts() {
        wp_register_script( 'np_alt_tools_js', plugins_url( 'js/np_alt_tools.js', __FILE__ ), array( 'jquery' ),  NP_ATT_VERSION );
        wp_localize_script( 'np_alt_tools_js', 'np_alt_tools', array(
            'endpoint' => admin_url( 'admin-ajax.php' ),
            'nonce' => wp_create_nonce( 'np_alt_tools_secure_me' )
        ) );
        wp_enqueue_script( 'np_alt_tools_js' ); 
    }

    /**
     * Plugin settings page (where most of the action happens ;)
     * Use injectScripts to insert admin scripts only on the settings page
     *
     * @since 0.0.1
     */
    public function settingsPage() {
        $hookSuffix = add_management_page(
            'Alt Text Tools',
            'Alt Text Tools',
            'manage_options',
            'nerdpress-alt-text-tools',
            array( $this, 'settingsHtml' )
        );
        add_action( 'admin_print_scripts-' . $hookSuffix, array( $this, 'injectScripts' ) );
    }

    /**
     * Markup for the settings page
     *
     * @since 0.0.1
     */
    public function settingsHtml() {
        ?>
        <div class="wrap">
            <h1>NerdPress Alt Text Tools</h1>
            <div class="button" id="npatt-csv-action" style="margin-top:30px">Download Alt Tag CSV</div>
        </div>
        <?php
    }

    private static function quoteIfComma( $fields_array ) {
        $result = [];
        foreach( $fields_array as $field => $value ) {
            if ( strpos( $value, ',' ) !== FALSE )
                $result[] = '"' . $value . '"';
            else
                $result[] = $value;
        }
        return $result;             
    }

    private static function getImageNameFromUrl( $img_url ) {
        $path_parts = explode( '/', $img_url );
        $len = count( $path_parts );
        if ( empty( $path_parts[ $len - 1 ] ) )
            $image_name = $path_paths[ $len - 2 ];
        else
            $image_name = $path_parts[ $len - 1 ];

        // Strip the size of the image
        return preg_replace( '/-[0-9]+x[0-9]+/', '', $image_name );
    }

    /**
     * Assemble a csv file
     * To be called via AJAX
     *
     * @since 0.0.1
     */
    public static function getCsv() {
        check_ajax_referer( 'np_alt_tools_secure_me', 'np_alt_tools_nonce' );

        /*=============================
         * Get all the posts/pages etc.
         *=============================*/
        $opts = array(
            'public' => TRUE,
            '_builtin' => FALSE
        );
        $postTypes = array_merge( 
            array( 'post', 'page' ), 
            get_post_types( $opts ) 
        );

        $posts = [];
        foreach( $postTypes as $type ) { 
            $posts = array_merge( $posts, get_posts( array(
                'posts_per_page' => -1,
                'post_type' => $type 
            ) ) );
        }
        sort( $posts );

        /*============================
         * Find images, src, alt tags.
         *============================*/
        $imgElemRegex = '/<img[^>]*>/';
        $site_url = site_url();
        $finds = []; 
        foreach( $posts as $post ) {
            $post_link = get_permalink( $post );
            $post_title = get_the_title( $post );
            $post_edit_link = get_edit_post_link( $post );
            $found = preg_match_all( 
                $imgElemRegex, 
                apply_filters( 'the_content', $post->post_content ),
                $matches, 
                PREG_PATTERN_ORDER 
            );

            if ( ! $found ) continue;

            foreach( $matches as $match ) {
                foreach( $match as $submatch ) {
                    preg_match( '/src="([^"]*)"/', $submatch, $img_url );
                    if ( strpos( $img_url[ 1 ], $site_url ) !== FALSE )
                        $media_link = $site_url .'/wp-admin/upload.php?s=' . self::getImageNameFromUrl( $img_url[ 1 ] );
                    else
                        $media_link = ''; 

                    preg_match( '/alt="([^"]*)"/', $submatch, $alt_tag );
                    if ( ! $alt_tag )
                        $alt_tag = [ '', 'MISSING' ];
                    else if ( $alt_tag[1] == '' )
                        $alt_tag = [ '', 'EMPTY' ];

                    $finds[] = array(
                        $post->ID,
                        $post->post_type,
                        html_entity_decode( $post_title ),
                        html_entity_decode( $post_link ),
                        html_entity_decode( $img_url[1] ),
                        html_entity_decode( $alt_tag[1] ),
                        html_entity_decode( $post_edit_link ),
                        html_entity_decode( $media_link )
                    );
                }
            }
        }
      
        /*================================
        * Create CSV File and AJAX it back
        *=================================*/
        $rows = array( implode( ',', [ 
              'post id', 
              'post_type',
              'page title', 
              'page url', 
              'image url', 
              'image alt tag', 
              'edit link',
              'media library link' 
            ] 
        ) );

        foreach( $finds as $find ) {
            $rows[] = implode( ',', self::quoteIfComma( $find ) );
        }

        $csvString = implode( '\r\n', $rows );
        echo $csvString;
        die(); 
    }
}

add_action( 'plugins_loaded', array( 'NerdpressAltTextTools', 'init' ) );
