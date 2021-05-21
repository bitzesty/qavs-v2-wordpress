<?php
/**
 * QAVS functions and definitions
 *
 * @link https://developer.wordpress.org/themes/basics/theme-functions/
 *
 * @package QAVS
 */

use Carbon_Fields\Container;
use Carbon_Fields\Block;
use Carbon_Fields\Field;

if ( ! defined( '_S_VERSION' ) ) {
	// Replace the version number of the theme on each release.
	define( '_S_VERSION', '1.0.0' );
}



if ( ! function_exists( 'qavs_setup' ) ) :
	/**
	 * Sets up theme defaults and registers support for various WordPress features.
	 *
	 * Note that this function is hooked into the after_setup_theme hook, which
	 * runs before the init hook. The init hook is too late for some features, such
	 * as indicating support for post thumbnails.
	 */
	function qavs_setup() {
		/*
		 * Make theme available for translation.
		 * Translations can be filed in the /languages/ directory.
		 * If you're building a theme based on QAVS, use a find and replace
		 * to change 'qavs' to the name of your theme in all the template files.
		 */
		load_theme_textdomain( 'qavs', get_template_directory() . '/languages' );

		// Add default posts and comments RSS feed links to head.
		add_theme_support( 'automatic-feed-links' );

		/*
		 * Let WordPress manage the document title.
		 * By adding theme support, we declare that this theme does not use a
		 * hard-coded <title> tag in the document head, and expect WordPress to
		 * provide it for us.
		 */
		add_theme_support( 'title-tag' );

		/*
		 * Enable support for Post Thumbnails on posts and pages.
		 *
		 * @link https://developer.wordpress.org/themes/functionality/featured-images-post-thumbnails/
		 */
		add_theme_support( 'post-thumbnails' );

		// This theme uses wp_nav_menu() in one location.
		register_nav_menus(
			array(
				'menu-1' => esc_html__( 'Primary', 'qavs' ),
			)
		);

		/*
		 * Switch default core markup for search form, comment form, and comments
		 * to output valid HTML5.
		 */
		add_theme_support(
			'html5',
			array(
				'search-form',
				'comment-form',
				'comment-list',
				'gallery',
				'caption',
				'style',
				'script',
			)
		);

		// Add theme support for selective refresh for widgets.
		add_theme_support( 'customize-selective-refresh-widgets' );

		/**
		 * Add support for core custom logo.
		 *
		 * @link https://codex.wordpress.org/Theme_Logo
		 */
		add_theme_support(
			'custom-logo',
			array(
				'height'      => 250,
				'width'       => 250,
				'flex-width'  => true,
				'flex-height' => true,
			)
		);
	}
endif;
add_action( 'after_setup_theme', 'qavs_setup' );

/**
 * Set the content width in pixels, based on the theme's design and stylesheet.
 *
 * Priority 0 to make it available to lower priority callbacks.
 *
 * @global int $content_width
 */
function qavs_content_width() {
	$GLOBALS['content_width'] = apply_filters( 'qavs_content_width', 640 );
}
add_action( 'after_setup_theme', 'qavs_content_width', 0 );

/**
 * Enqueue scripts and styles.
 */
function qavs_scripts() {
	wp_enqueue_style( 'qavs-style', get_stylesheet_uri(), array(), _S_VERSION );
	wp_style_add_data( 'qavs-style', 'rtl', 'replace' );

	wp_enqueue_script( 'qavs-navigation', get_template_directory_uri() . '/js/navigation.js', array(), _S_VERSION, true );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'qavs_scripts' );

require get_template_directory() . '/inc/types.php';
require get_template_directory() . '/inc/blocks.php';

/**
 * Implement the Custom Header feature.
 */
require get_template_directory() . '/inc/custom-header.php';

/**
 * Custom template tags for this theme.
 */
require get_template_directory() . '/inc/template-tags.php';

/**
 * Functions which enhance the theme by hooking into WordPress.
 */
require get_template_directory() . '/inc/template-functions.php';

/**
 * Customizer additions.
 */
require get_template_directory() . '/inc/customizer.php';

/**
 * Load Jetpack compatibility file.
 */
if ( defined( 'JETPACK__VERSION' ) ) {
	require get_template_directory() . '/inc/jetpack.php';
}


function wpdocs_custom_excerpt_length( $length ) {
    return 20;
}
add_filter( 'excerpt_length', 'wpdocs_custom_excerpt_length', 999 );

add_action( 'carbon_fields_register_fields', 'qavs_attach_theme_options' );
function qavs_attach_theme_options() {
    Container::make( 'theme_options', __( 'Theme Options' ) )
        ->add_fields( array(
            Field::make( 'text', 'qavs_login', 'Login' ),
            Field::make( 'text', 'qavs_facebook', 'Facebook' ),
            Field::make( 'text', 'qavs_twitter', 'Twitter' ),
            Field::make( 'text', 'qavs_linkedin', 'LinkedIn' ),
        ) );
}

add_action( 'after_setup_theme', 'qavs_load' );
function qavs_load() {
    require_once( 'vendor/autoload.php' );
    \Carbon_Fields\Carbon_Fields::boot();
    Block::make( __( 'Past Awardees' ) )
      ->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
        ?>
    
        <div class="block">
          <div class="block__heading">
            <h1><?php echo esc_html( $fields['heading'] ); ?></h1>
          </div><!-- /.block__heading -->
    
          <div class="block__image">
            <?php echo wp_get_attachment_image( $fields['image'], 'full' ); ?>
          </div><!-- /.block__image -->
    
          <div class="block__content">
            <?php echo apply_filters( 'the_content', $fields['content'] ); ?>
          </div><!-- /.block__content -->
        </div><!-- /.block -->
    
        <?php
      } );
}

