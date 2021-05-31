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
	wp_enqueue_style( 'qavs-style', get_stylesheet_uri(), array(), filemtime(get_stylesheet_directory() . '/style.css') );
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

class QavsWebsite {
  public static function groupTypeMapping() {
    return [
      "armed_forces"=>"Armed Forces",
      "arts_and_media"=>"Arts and Media",
      "asylum_seekers_and_refugees"=>"Asylum Seekers and Refugees",
      "children_and_young_people"=>"Children and Young People",
      "community_hubs_and_services"=>"Community Hubs and Services",
      "cultural"=>"Cultural",
      "disability"=>"Disability",
      "education_and_employment"=>"Education and Employment",
      "emergency_response"=>"Emergency Response",
      "environment"=>"Environment",
      "events"=>"Events",
      "family_support"=>"Family Support",
      "food_support"=>"Food Support",
      "health_and_care"=>"Health and Care",
      "heritage"=>"Heritage",
      "homelessness"=>"Homelessness",
      "mental_health_and_wellbeing"=>"Mental Health and Wellbeing",
      "loneliness_and_befriending"=>"Loneliness and Befriending",
      "older_people"=>"Older People",
      "social_justice"=>"Social Justice",
      "sport_and_outdoor_activities"=>"Sport and Outdoor Activities",
      "other"=>"Other"
    ];
  }
  
  public static function lieutenanciesMapping() {
    return [
      "bedfordshire"=>"Bedfordshire",
      "berkshire"=>"Berkshire",
      "buckinghamshire"=>"Buckinghamshire",
      "cornwall"=>"Cornwall",
      "county_durham"=>"County Durham",
      "cumbria"=>"Cumbria",
      "derbyshire"=>"Derbyshire",
      "devon"=>"Devon",
      "dorset"=>"Dorset",
      "east_sussex"=>"East Sussex",
      "essex"=>"Essex",
      "gloucestershire"=>"Gloucestershire",
      "greater_london"=>"Greater London",
      "greater_manchester"=>"Greater Manchester",
      "hampshire"=>"Hampshire",
      "hertfordshire"=>"Hertfordshire",
      "kent"=>"Kent",
      "lancashire"=>"Lancashire",
      "leicestershire"=>"Leicestershire",
      "lincolnshire"=>"Lincolnshire",
      "merseyside"=>"Merseyside",
      "norfolk"=>"Norfolk",
      "north_yorkshire"=>"North Yorkshire",
      "northamptonshire"=>"Northamptonshire",
      "northumberland"=>"Northumberland",
      "nottinghamshire"=>"Nottinghamshire",
      "oxfordshire"=>"Oxfordshire",
      "rutland"=>"Rutland",
      "shropshire"=>"Shropshire",
      "somerset"=>"Somerset",
      "south_yorkshire"=>"South Yorkshire",
      "staffordshire"=>"Staffordshire",
      "suffolk"=>"Suffolk",
      "surrey"=>"Surrey",
      "the_city_and_county_of_bristol"=>"The City and County of Bristol",
      "the_east_riding_of_yorkshire"=>"The East Riding of Yorkshire",
      "the_isle_of_wight"=>"The Isle of Wight",
      "the_west_midlands"=>"The West Midlands",
      "tyne_and_wear"=>"Tyne and Wear",
      "warwickshire"=>"Warwickshire",
      "west_sussex"=>"West Sussex",
      "west_yorkshire"=>"West Yorkshire",
      "guernsey"=>"Guernsey",
      "county_antrim"=>"County Antrim",
      "county_armagh"=>"County Armagh",
      "county_down"=>"County Down",
      "county_londonderry"=>"County Londonderry",
      "county_tyrone"=>"County Tyrone",
      "the_county_borough_of_belfast"=>"The County Borough of Belfast",
      "aberdeenshire"=>"Aberdeenshire",
      "argyll_and_bute"=>"Argyll and Bute",
      "banffshire"=>"Banffshire",
      "inverness"=>"Inverness",
      "lanarkshire"=>"Lanarkshire",
      "the_city_of_aberdeen"=>"The City of Aberdeen",
      "the_city_of_edinburgh"=>"The City of Edinburgh",
      "the_city_of_glasgow"=>"The City of Glasgow",
      "perth_and_kinross"=>"Perth and Kinross",
      "renfrewshire"=>"Renfrewshire",
      "ross_and_cromarty"=>"Ross and Cromarty",
      "clwyd"=>"Clwyd",
      "dyfed"=>"Dyfed",
      "gwynedd"=>"Gwynedd",
      "mid_glamorgan"=>"Mid Glamorgan",
      "powys"=>"Powys",
      "south_glamorgan"=>"South Glamorgan",
      "west_glamorgan"=>"West Glamorgan"
    ];
  }
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

add_action( 'admin_enqueue_scripts', 'qavs_load_editor_styles' );
function qavs_load_editor_styles() {
  wp_register_style(
    'qavs-editor-css',
    get_stylesheet_directory_uri() . '/css/qavs-blocks.css'
  );
}

add_action( 'after_setup_theme', 'qavs_load' );
function qavs_load() {
    require_once( 'vendor/autoload.php' );

    \Carbon_Fields\Carbon_Fields::boot();

    Block::make( __( 'Past Awardees Filters' ) )
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

    Block::make( __( 'Hero video' ) )
      ->add_fields( array(
        Field::make( 'file', 'mp4_file', __( 'MP4 file' ) )->set_value_type( 'url' )->set_type( 'video' ),
        Field::make( 'file', 'webm_file', __( 'Webm file' ) )->set_value_type( 'url' )->set_type( 'video' ),
        Field::make( 'file', 'ogv_file', __( 'OGV file' ) )->set_value_type( 'url' )->set_type( 'video' ),
        Field::make( 'image', 'cover_image', __( 'Cover image' ) )->set_value_type( 'url' ),
      ) )
      ->set_icon( 'video' )
      ->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
        ?>

        <video width="640" height="360" poster="<?php echo $fields["cover_image"]; ?>" controls>
          <source src="<?php echo $field["mp4_file"]; ?>" type="video/mp4" />
          <?php if (isset($fields["webm_file"]) && !empty($fields["webm_file"])): ?>
            <source src="<?php echo $fields["webm_file"]; ?>" type="video/webm" />
          <?php endif; ?>
          <?php if (isset($fields["ogv_file"]) && !empty($fields["ogv_file"])): ?>
            <source src="<?php echo $fields["ogv_file"]; ?>" type="video/ogg" />
          <?php endif; ?>
          <div>
            <a href="<?php echo $field["mp4_file"]; ?>">
              <img src="<?php echo $fields["cover_image"]; ?>" width="640" height="360" alt="download video" />
            </a>
          </div>
        </video>
        <?php
      } );
    
    Block::make( __( 'Accessible video' ) )
      ->add_fields( array(
        Field::make( 'file', 'mp4_file', __( 'MP4 file' ) )->set_value_type( 'url' )->set_type( 'video' ),
        Field::make( 'file', 'webm_file', __( 'Webm file' ) )->set_value_type( 'url' )->set_type( 'video' ),
        Field::make( 'file', 'ogv_file', __( 'OGV file' ) )->set_value_type( 'url' )->set_type( 'video' ),
        Field::make( 'file', 'captions_file', __( 'Captions file (VTT extension)' ) )->set_value_type( 'url' ),
        Field::make( 'image', 'cover_image', __( 'Cover image' ) )->set_value_type( 'url' ),
      ) )
      ->set_icon( 'video' )
      ->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
        ?>

        <div class="px-video-container">
          <div class="px-video-img-captions-container">
            <div class="px-video-captions hide" aria-hidden="true"></div>
            <video width="640" height="360" poster="<?php echo $fields["cover_image"]; ?>" controls>
              <source src="<?php echo $field["mp4_file"]; ?>" type="video/mp4" />
              <?php if (isset($fields["webm_file"]) && !empty($fields["webm_file"])): ?>
                <source src="<?php echo $fields["webm_file"]; ?>" type="video/webm" />
              <?php endif; ?>
              <?php if (isset($fields["ogv_file"]) && !empty($fields["ogv_file"])): ?>
                <source src="<?php echo $fields["ogv_file"]; ?>" type="video/ogg" />
              <?php endif; ?>
              <track kind="captions" label="English captions" src="<?php echo $fields["captions_file"]; ?>" srclang="en" default />
              <div>
                <a href="<?php echo $field["mp4_file"]; ?>">
                  <img src="<?php echo $fields["cover_image"]; ?>" width="640" height="360" alt="download video" />
                </a>
              </div>
            </video>
          </div>
          <div class="px-video-controls"></div>
        </div>
    
        <?php
      } );
    
    Block::make( __( 'Download button' ) )
      ->add_fields( array(
        Field::make( 'text', 'button_text', __( 'Button text' ) ),
        Field::make( 'file', 'button_file', __( 'File' ) )->set_value_type( 'url' )
      ) )
      ->set_icon( 'download' )
      ->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
        ?>
    
        <div class="block-download-button">
          <a href="<?php echo esc_html( $fields['button_file'] ); ?>" download target="_blank" rel="noopener nofollow" class="download-button button button--inverse">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 21 21"><path fill="#8A0DA6" d="M10.292 16.912a.783.783 0 01-.56-.236l-4.75-4.83a.814.814 0 010-1.139.781.781 0 011.12 0l4.19 4.262 4.19-4.262a.781.781 0 011.12 0c.31.315.31.824 0 1.139l-4.75 4.83a.783.783 0 01-.56.236z"/><path fill="#8A0DA6" d="M10.292 16.107a.798.798 0 01-.792-.805V1.615c0-.444.354-.805.792-.805.438 0 .791.361.791.805v13.687a.798.798 0 01-.791.805zM19.792 20.133h-19A.798.798 0 010 19.328c0-.445.354-.805.792-.805h19c.438 0 .791.36.791.805a.798.798 0 01-.791.805z"/></svg>
            <?php echo esc_html( $fields['button_text'] ); ?>
          </a>
        </div>
    
        <?php
      } );

    Container::make( 'post_meta', __( 'Awardee details', 'qavs' ) )
      ->where( 'post_type', '=', 'awardee' )
      ->add_fields( array(
        Field::make( 'text', 'awardee_award_year', 'Award Year' ),
        Field::make( 'text', 'awardee_group_name', 'Group Name' ),
        Field::make( 'select', 'awardee_ceremonial_county', 'Ceremonial County (Lieutenancy)' )->set_options('QavsWebsite::lieutenanciesMapping'),
        Field::make( 'textarea', 'awardee_short_citation', 'Short Citation' )->set_rows( 4 ),
        Field::make( 'select', 'awardee_group_type_1', 'Type of Group 1' )->set_options('QavsWebsite::groupTypeMapping'),
        Field::make( 'select', 'awardee_group_type_2', 'Type of Group 2' )->set_options('QavsWebsite::groupTypeMapping'),
        Field::make( 'text', 'awardee_website', 'Website' ),
      ) );
}

