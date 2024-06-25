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

    register_nav_menus(
			array(
				'menu-welsh' => esc_html__( 'Welsh menu', 'qavs' ),
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
	wp_enqueue_style( 'qavs-paypal-video-styles', get_template_directory_uri() . '/css/px-video.css', array(), filemtime(get_stylesheet_directory() . '/css/px-video.css') );
	wp_style_add_data( 'qavs-style', 'rtl', 'replace' );

	wp_enqueue_script( 'qavs-paypal-strings', get_template_directory_uri() . '/js/strings.js', array(), filemtime(get_stylesheet_directory() . '/js/strings.js'), true );
	wp_enqueue_script( 'qavs-paypal-video', get_template_directory_uri() . '/js/px-video.js', array(), filemtime(get_stylesheet_directory() . '/js/px-video.js'), true );
	wp_enqueue_script( 'qavs-navigation', get_template_directory_uri() . '/js/navigation.js', array(), filemtime(get_stylesheet_directory() . '/js/navigation.js'), true );
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
      "cambridgeshire"=>"Cambridgeshire",
      "cheshire"=>"Cheshire",
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
      "herefordshire"=>"Herefordshire",
      "hertfordshire"=>"Hertfordshire",
      "isle_of_man"=>"Isle of Man",
      "kent"=>"Kent",
      "lancashire"=>"Lancashire",
      "leicestershire"=>"Leicestershire",
      "lincolnshire"=>"Lincolnshire",
      "merseyside"=>"Merseyside",
      "norfolk"=>"Norfolk",
      "northamptonshire"=>"Northamptonshire",
      "northumberland"=>"Northumberland",
      "north_yorkshire"=>"North Yorkshire",
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
      "the_isle_of_wight"=>"the Isle of Wight",
      "the_west_midlands"=>"the West Midlands",
      "tyne_and_wear"=>"Tyne and Wear",
      "warwickshire"=>"Warwickshire",
      "west_sussex"=>"West Sussex",
      "west_yorkshire"=>"West Yorkshire",
      "wiltshire"=>"Wiltshire",
      "worcestershire"=>"Worcestershire",
      "guernsey"=>"Guernsey",
      "jersey"=>"Jersey",
      "county_antrim"=>"County Antrim",
      "county_armagh"=>"County Armagh",
      "county_down"=>"County Down",
      "county_fermanagh"=>"County Fermanagh",
      "county_londonderry"=>"County Londonderry",
      "county_tyrone"=>"County Tyrone",
      "the_county_borough_of_belfast"=>"The County Borough of Belfast",
      "the_county_borough_of_londonderry"=>"The County Borough of Londonderry",
      "aberdeenshire"=>"Aberdeenshire",
      "angus"=>"Angus",
      "argyll_and_bute"=>"Argyll and Bute",
      "ayrshire_and_arran"=>"Ayrshire and Arran",
      "banffshire"=>"Banffshire",
      "berwickshire"=>"Berwickshire",
      "caithness"=>"Caithness",
      "clackmannanshire"=>"Clackmannanshire",
      "dumfries"=>"Dumfries",
      "dunbartonshire"=>"Dunbartonshire",
      "east_lothian"=>"East Lothian",
      "fife"=>"Fife",
      "inverness"=>"Inverness",
      "kincardineshire"=>"Kincardineshire",
      "lanarkshire"=>"Lanarkshire",
      "midlothian"=>"Midlothian",
      "Moray"=>"Moray",
      "Nairn"=>"Nairn",
      "Orkney"=>"Orkney",
      "perth_and_kinross"=>"Perth and Kinross",
      "renfrewshire"=>"Renfrewshire",
      "ross_and_cromarty"=>"Ross and Cromarty",
      "roxburgh_etterick_and_lauderdale"=>"Roxburgh Etterick and Lauderdale",
      "shetland"=>"Shetland",
      "stirling_and_falkirk"=>"Stirling and Falkirk",
      "sutherland"=>"Sutherland",
      "the_city_of_aberdeen"=>"the City of Aberdeen",
      "the_city_of_dundee"=>"the City of Dundee",
      "the_city_of_edinburgh"=>"the City of Edinburgh",
      "the_city_of_glasgow"=>"the City of Glasgow",
      "the_stewartry_of_kirkcudbright"=>"the Stewartry of Kirkcudbright",
      "the_western_issles"=>"the Western Isles",
      "tweeddale"=>"Tweeddale",
      "west_lothian"=>"West Lothian",
      "wigtown"=>"Wigtown",
      "clwyd"=>"Clwyd",
      "dyfed"=>"Dyfed",
      "gwent"=>"Gwent",
      "gwynedd"=>"Gwynedd",
      "mid_glamorgan"=>"Mid Glamorgan",
      "powys"=>"Powys",
      "south_glamorgan"=>"South Glamorgan",
      "west_glamorgan"=>"West Glamorgan"
    ];
  }

  public static function getAwardeeFilters() {
    $filters = [
      'group_name' => '',
      'year_awarded' => null,
      'type_of_group' => null,
      'ceremonial_county' => null
    ];

    if (isset($_GET['awardee_filters'])) {
      foreach(array_keys($filters) as $key) {
        if (isset($_GET['awardee_filters'][$key]) && !empty($_GET['awardee_filters'][$key])) {
          $filters[$key] = $_GET['awardee_filters'][$key];
        }
      }
    }

    return $filters;
  }

  public static function getMetaQuery() {
    $meta_query = [];
    $filters = QavsWebsite::getAwardeeFilters();

    if ($filters['year_awarded']) {
      $meta_query[] = [
        'key' => 'awardee_award_year',
        'value' => $filters['year_awarded']
      ];
    } else {
      $years = QavsWebsite::getAwardeeYears();
      $meta_query[] = [
        'key' => 'awardee_award_year',
        'value' => $years[0]
      ];
    }

    if ($filters['type_of_group']) {
      $meta_query[] = [
        'relation' => 'OR',
        [
          'key' => 'awardee_group_type_1',
          'value' => $filters['type_of_group']
        ],
        [
          'key' => 'awardee_group_type_2',
          'value' => $filters['type_of_group']
        ]
      ];
    }

    if ($filters['ceremonial_county']) {
      $meta_query[] = [
        'key' => 'awardee_ceremonial_county',
        'value' => $filters['ceremonial_county']
      ];
    }

    return $meta_query;
  }

  public static function getAwardeeYears() {
    $_years = qavs_get_all_meta_values('_awardee_award_year');
    $years = array_map(function($year) {
      return intval($year);
    }, $_years);
    rsort($years);

    return $years;
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


function qavs_get_all_meta_values($key) {
  global $wpdb;
  $result = $wpdb->get_col( 
    $wpdb->prepare( "
      SELECT DISTINCT pm.meta_value FROM {$wpdb->postmeta} pm
      LEFT JOIN {$wpdb->posts} p ON p.ID = pm.post_id
      WHERE pm.meta_key = '%s' 
      AND p.post_status = 'publish'
      ORDER BY pm.meta_value", 
      $key
    ) 
  );

  return $result;
}

function qavs_pagination_information($query) {
  $page = $query->query_vars["paged"];
  $start = ($page - 1) * $query->query_vars["posts_per_page"];
  $end = $start + min($start + $query->query_vars["posts_per_page"], $query->post_count);

  return sprintf("Showing %s - %s out of %s awardees", $start + 1, $end, $query->found_posts);
}

add_action( 'after_setup_theme', 'qavs_load' );
function qavs_load() {
    require_once( 'vendor/autoload.php' );

    \Carbon_Fields\Carbon_Fields::boot();

    Block::make( __( 'Past Awardees Filters' ) )
      ->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
        $years = QavsWebsite::getAwardeeYears();
        $filters = QavsWebsite::getAwardeeFilters();
        ?>
        <section class="past-awardees-filter" aria-label="Past awardees filter">
          <h3 id="past-awardees-filter-title">Search awardees</h3>
          <form action="" method='get' aria-labelledby="past-awardees-filter-title">
          <div class="top-row">
              <div class="form-group">
                <label for="group_name">Group name</label>
                <input id="group_name" name="awardee_filters[group_name]" type="text" value="<?php echo htmlspecialchars($filters['group_name']); ?>" />
              </div>
            </div>
            <div class="bottom-row">
              <div class="form-group">
                <label for="year_awarded">Year awarded</label>
                <select name="awardee_filters[year_awarded]" id="year_awarded">
                  <option value="">Show all</option>
                  <?php foreach($years as $year): ?>
                    <option value="<?php echo $year; ?>" <?php echo $filters["year_awarded"] == $year ? 'selected="selected"' : ''; ?>><?php echo $year; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label for="type_of_group">Type of group</label>
                <select name="awardee_filters[type_of_group]" id="type_of_group">
                  <option value="">Show all</option>
                  <?php foreach(QavsWebsite::groupTypeMapping() as $key => $value): ?>
                    <option value="<?php echo $key; ?>" <?php echo $filters["type_of_group"] == $key ? 'selected="selected"' : ''; ?>><?php echo $value; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
              <div class="form-group">
                <label for="ceremonial_county">Ceremonial county</label>
                <select name="awardee_filters[ceremonial_county]" id="ceremonial_county">
                  <option value="">Show all</option>
                  <?php foreach(QavsWebsite::lieutenanciesMapping() as $key => $value): ?>
                    <option value="<?php echo $key; ?>" <?php echo $filters["ceremonial_county"] == $key ? 'selected="selected"' : ''; ?>><?php echo $value; ?></option>
                  <?php endforeach; ?>
                </select>
              </div>
            </div>
            <div class="search_buttons">
              <button type="submit" class="filter-button">Search</button>
              <a href="/awardees">Clear</a>
            </div>
          </form>
        </section>
        <?php
      } );

    Block::make( __( 'Past Awardees' ) )
      ->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
        $paged = ( get_query_var( 'paged' ) ) ? get_query_var( 'paged' ) : 1;
        $options = [
          'post_type' => 'awardee',
          'posts_per_page' => 25,
          'paged' => $paged,
          'meta_key' => '_awardee_has_news_article',
          'orderby' => [ 'meta_value_num' => 'DESC', 'title' => 'ASC' ]
        ];

        if (isset($_GET['awardee_filters'])) {
          $filters = QavsWebsite::getAwardeeFilters();
          if ($filters['group_name']) {
            $options['s'] = $filters['group_name'];
          }
        }

        $meta_query = QavsWebsite::getMetaQuery();
        if (!empty($meta_query)) {
          $options['meta_query'] = $meta_query;
        }
        
        $query = new WP_Query($options);
        ?>

        <?php if($query->have_posts()): ?>
          <div class="past-awardees-pagination-info" role="status">
            <?php echo qavs_pagination_information($query); ?>
          </div>

          <ul class="past-awardees" aria-label="Past awardees list">
            <?php while($query->have_posts()): $query->the_post(); ?>
              <li class="past-awardee" tabindex="0">
                <h3 class="past-awardee__title"><?php the_title(); ?></h3>
                <div class="past-awardee__details">
                  <div class="past-awardee__detail">
                    <strong>Year awarded: </strong> <?php echo carbon_get_post_meta( get_the_ID(), 'awardee_award_year' ); ?>
                  </div>
                  <div class="past-awardee__detail">
                    <strong>Type of group: </strong> <?php echo QavsWebsite::groupTypeMapping()[carbon_get_post_meta( get_the_ID(), 'awardee_group_type_1' )]; ?>
                  </div>
                  <div class="past-awardee__detail">
                    <strong>Ceremonial county: </strong> <?php echo QavsWebsite::lieutenanciesMapping()[carbon_get_post_meta( get_the_ID(), 'awardee_ceremonial_county' )]; ?>
                  </div>
                  <div class="past-awardee__detail">
                    <a href='<?php echo carbon_get_post_meta( get_the_ID(), 'awardee_website' ); ?>' rel='noopener nofollow' target='_blank'>
                      <?php echo carbon_get_post_meta( get_the_ID(), 'awardee_website' ); ?>
                    </a>
                  </div>
                </div>
                <div class="past-awardee__citation">
                  <?php echo carbon_get_post_meta( get_the_ID(), 'awardee_short_citation' ); ?>
                </div>
                <?php $article = carbon_get_post_meta( get_the_ID(), 'awardee_news_article' ); ?>
                
                <?php if ($article): ?>
                  <a href="<?php echo get_permalink($article); ?>" aria-label="Read article about awardee: <?php echo get_the_title($article); ?>" title="Click to read article" class="arrow-link">
                    Read article about awardee
                  </a>
                <?php endif; ?>
              </li>
            <?php endwhile; wp_reset_postdata(); ?>
          </ul>
          <?php

            $pagination_links = paginate_links( array(
              'base' => str_replace( 999999999, '%#%', esc_url( get_pagenum_link( 999999999 ) ) ),
              'format' => '?paged=%#%',
              'current' => max( 1, get_query_var('paged') ),
              'total' => $query->max_num_pages,
              'mid_size' => 4,
              'prev_text'    => __('Previous'),
              'next_text'    => __('Next'),
              'type'         => 'array'
            ) );
              
            if (!empty($pagination_links)):
              echo '<nav class="past-awardees-pagination" aria-label="Past awardee">';
              echo "<ul class='pagination'>";
                foreach ($pagination_links as $link) {
                  echo "<li>" . $link . "</li>";
                }
              echo "</ul>";
              echo '</nav>';
            endif;
          ?>
        <?php else: ?>
          <h2>0 awardees found</h2>

          <p>Please try changing your search criteria</p>
        <?php endif; ?>

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

        <video poster="<?php echo $fields["cover_image"]; ?>" muted playsinline id="hero-animation" class="home-animation-video" aria-hidden="true">
          <source src="<?php echo $fields["mp4_file"]; ?>" type="video/mp4" />
          <?php if (isset($fields["webm_file"]) && !empty($fields["webm_file"])): ?>
            <source src="<?php echo $fields["webm_file"]; ?>" type="video/webm" />
          <?php endif; ?>
          <?php if (isset($fields["ogv_file"]) && !empty($fields["ogv_file"])): ?>
            <source src="<?php echo $fields["ogv_file"]; ?>" type="video/ogg" />
          <?php endif; ?>
          <div>
            <a href="<?php echo $fields["mp4_file"]; ?>">
              <img src="<?php echo $fields["cover_image"]; ?>" alt="download video" />
            </a>
          </div>
        </video>
        <?php
      } );

    Block::make( __( 'Hero video controls' ) )
      ->add_fields( array(
        Field::make( 'text', 'play_text', __( 'Play text' ) ),
        Field::make( 'text', 'pause_text', __( 'Pause text' ) )
      ) )
      ->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
        ?>

        <button class="control-home-animation paused hidden" aria-controls="hero-animation" aria-hidden="true">
          <span class="playing">
            <svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 35 36" aria-hidden="true" focusable="false" class="pause-icon"><path fill-rule="evenodd" clip-rule="evenodd" d="M17.5.255C7.848.255 0 8.179 0 17.925c0 9.747 7.848 17.672 17.5 17.672 9.652 0 17.5-7.925 17.5-17.671C35 8.179 27.152.255 17.5.255zm0 2.162c8.495 0 15.359 6.93 15.359 15.509 0 8.578-6.864 15.509-15.359 15.509-8.495 0-15.359-6.931-15.359-15.51 0-8.577 6.864-15.508 15.359-15.508z" fill="#136C85"/><path fill="#136C85" d="M11 10h4.688v15H11zM19.438 10h4.688v15h-4.688z"/></svg>
            <?php echo $fields['pause_text']; ?>
          </span>
          <span class="paused">
            <svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 27 27" aria-hidden="true" focusable="false" class="play-icon"><path fill-rule="evenodd" clip-rule="evenodd" d="M13.117 0C5.882 0 0 5.882 0 13.117c0 7.235 5.882 13.118 13.117 13.118 7.235 0 13.118-5.883 13.118-13.118S20.352 0 13.117 0zm0 1.605A11.5 11.5 0 0124.63 13.117 11.5 11.5 0 0113.117 24.63 11.5 11.5 0 011.605 13.117 11.5 11.5 0 0113.117 1.605zm-2.924 4.062v14.901l9.347-7.45-9.347-7.451z" fill="#136C85"/></svg>
            Play logo animation video
          </span>
        </button>

        <?php
      });

      Block::make( __( 'Transcript' ) )
        ->add_fields( array(
          Field::make( 'text', 'title', __( 'Title' ) ),
          Field::make( 'text', 'aria_label', __( 'ARIA label' ) ),
          Field::make( 'rich_text', 'text', __( 'Text' ) )
        ) )
        ->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
          ?>

          <details class="transcript">
            <summary class="transcript__summary">
              <span class="transcript__summary-text" aria-label="<?php echo $fields['aria_label']; ?>">
                <?php echo $fields['title']; ?>
              </span>
              <svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" aria-hidden="true" focusable="false"><circle cx="16" cy="16" r="15" fill="transparent" stroke="#136C85"/><path d="M16 18.864l5.71-5.71M15.727 18.864l-5.71-5.71" stroke="#136C85" stroke-width="2" stroke-linecap="square"/></svg>
            </summary>
            <div class="transcript__text">
              <?php echo $fields['text']; ?>
            </div>
          </details>

          <?php
        });

    Block::make( __( 'Accessible video' ) )
      ->add_fields( array(
        Field::make( 'text', 'video_title', __( 'Video title' ) ),
        Field::make( 'file', 'mp4_file', __( 'MP4 file' ) )->set_value_type( 'url' )->set_type( 'video' ),
        Field::make( 'file', 'webm_file', __( 'Webm file' ) )->set_value_type( 'url' )->set_type( 'video' ),
        Field::make( 'file', 'ogv_file', __( 'OGV file' ) )->set_value_type( 'url' )->set_type( 'video' ),
        Field::make( 'file', 'captions_file', __( 'Captions file (VTT extension)' ) )->set_value_type( 'url' ),
        Field::make( 'image', 'cover_image', __( 'Cover image' ) )->set_value_type( 'url' ),
      ) )
      ->set_icon( 'video' )
      ->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
        $classes = "";
        if (isset($attributes['className'])) {
          $classes = $attributes['className'];
        }
        ?>


        <div class="px-video-container <?php echo $classes; ?>" id="video-<?php echo generateRandomString(); ?>" data-title="<?php echo $fields['video_title']; ?>">
          <div class="px-video-img-captions-container">
            <div class="px-video-captions hide" aria-hidden="true"></div>
            <video poster="<?php echo $fields["cover_image"]; ?>" controls>
              <source src="<?php echo $fields["mp4_file"]; ?>" type="video/mp4" />
              <?php if (isset($fields["webm_file"]) && !empty($fields["webm_file"])): ?>
                <source src="<?php echo $fields["webm_file"]; ?>" type="video/webm" />
              <?php endif; ?>
              <?php if (isset($fields["ogv_file"]) && !empty($fields["ogv_file"])): ?>
                <source src="<?php echo $fields["ogv_file"]; ?>" type="video/ogg" />
              <?php endif; ?>
              <track kind="captions" label="English captions" src="<?php echo $fields["captions_file"]; ?>" srclang="en" default />
              <div>
                <a href="<?php echo $fields["mp4_file"]; ?>">
                  <img src="<?php echo $fields["cover_image"]; ?>" alt="download video" />
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

    Block::make( __( 'Privacy aware youtube' ) )
      ->add_fields( array(
        Field::make( 'text', 'video_url', __( 'Video URL' ) )
      ) )
      ->set_icon( 'video' )
      ->set_render_callback( function ( $fields, $attributes, $inner_blocks ) {
        $url = $fields["video_url"];
        $parsed_url = parse_url($url);
        $videoID = null;

        if (strpos($parsed_url['path'], "/embed") !== false) {
          $path = explode('/', $parsed_url['path']);
          $videoID = array_pop($path);
        } else {
          $params = explode('&', $parsed_url['query']);
          foreach ($params as $param) {
            $parts = explode('=', $param);

            if ($parts[0] == 'v') {
              $videoID = $parts[1];
            }
          }
        }
        ?>

        <div class="embed-container">
          <iframe src="https://www.youtube-nocookie.com/embed/<?php echo $videoID; ?>?wmode=transparent&rel=0" title="YouTube video player" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen="" width="560" height="315" frameborder="0"></iframe>
        </div>

        <?php
      } );

    Container::make( 'post_meta', __( 'Awardee details', 'qavs' ) )
      ->where( 'post_type', '=', 'awardee' )
      ->add_fields( array(
        Field::make( 'text', 'awardee_award_year', 'Award Year' ),
        Field::make( 'select', 'awardee_ceremonial_county', 'Ceremonial County (Lieutenancy)' )->set_options('QavsWebsite::lieutenanciesMapping'),
        Field::make( 'textarea', 'awardee_short_citation', 'Short Citation' )->set_rows( 4 ),
        Field::make( 'select', 'awardee_group_type_1', 'Type of Group 1' )->set_options('QavsWebsite::groupTypeMapping'),
        Field::make( 'select', 'awardee_group_type_2', 'Type of Group 2' )->set_options('QavsWebsite::groupTypeMapping'),
        Field::make( 'text', 'awardee_website', 'Website' ),
        Field::make( 'radio', 'awardee_has_news_article', 'Has news article?')->set_options([
          '0' => 'No',
          '1' => 'Yes'
        ]),
        Field::make( 'select', 'awardee_news_article', __( 'News article' ) )->add_options('qavs_list_featured_awardees_articles')
      ) );
    
    Container::make( 'post_meta', __( 'Page language', 'qavs' ) )
      ->where( 'post_type', '=', 'page' )
      ->set_context('side')
      ->set_priority('high')
      ->add_fields( array(
        Field::make( 'radio', 'page_language', 'Page language' )->set_options([
          'en' => 'English',
          'cy' => 'Welsh'
        ])
      ) );
}

function qavs_list_featured_awardees_articles() {
  $featured_awardees = get_posts( array(
    'posts_per_page' => -1,
    'post_status' => 'publish',
    'tax_query' => [
      [
        'taxonomy' => 'category',
        'field' => 'term_id',
        'terms' => get_cat_ID('Featured awardees'),
        'operator' => 'IN'
      ]
    ]
  ) );

  $dictionary = ["Select a news article"];
  
  foreach ($featured_awardees as $awardee) {
    $dictionary[$awardee->ID] = $awardee->post_title;
  }

  return $dictionary;
}

function generateRandomString($length = 10) {
  return substr(str_shuffle(str_repeat($x='0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ', ceil($length/strlen($x)) )),1,$length);
}

add_filter( 'allowed_block_types', 'qavs_allowed_block_types', 10, 2 );
 
function qavs_allowed_block_types( $allowed_blocks, $post ) {
 
	$allowed_blocks = array(
		'core/image',
		'core/paragraph',
		'core/heading',
		'core/list',
    'core/html',
    'core/quote',
    'core/table',
    'core/spacer',
    'core/text-columns',
    'core/columns',
    'core/column',
    'core/button',
    'core/buttons',
    'core/separator',
    'carbon-fields/transcript',
    'carbon-fields/accessible-video',
    'carbon-fields/privacy-aware-youtube'
	);
 
	if( $post->post_type === 'page' ) {
		$allowed_blocks[] = 'qavs/section';
		$allowed_blocks[] = 'qavs/promoted-article';
		$allowed_blocks[] = 'qavs/featured-news';
		$allowed_blocks[] = 'qavs/featured-awardees';
		$allowed_blocks[] = 'qavs/notice';
		$allowed_blocks[] = 'qavs/parental-tabs';
		$allowed_blocks[] = 'qavs/parental-navigation';
    $allowed_blocks[] = 'qavs/parental-pagination';
		$allowed_blocks[] = 'qavs/commitee-member';
		$allowed_blocks[] = 'qavs/resource';
		$allowed_blocks[] = 'atomic-blocks/ab-cta';
		$allowed_blocks[] = 'pb/accordion-item';
		$allowed_blocks[] = 'carbon-fields/hero-video-controls';
		$allowed_blocks[] = 'carbon-fields/hero-video';
		$allowed_blocks[] = 'carbon-fields/past-awardees';
		$allowed_blocks[] = 'carbon-fields/past-awardees-filters';
	}
 
	return $allowed_blocks;
 
}
