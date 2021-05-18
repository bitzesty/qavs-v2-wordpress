<?php


function qavs_block__featured_news( $block_attributes, $content ) {

  $featured_news = wp_get_recent_posts( array(
    'numberposts' => 3,
    'post_status' => 'publish',
    'meta_key' => 'featured_news',
    'meta_value' => '1'
  ) );

  $html = "";

  if(empty($featured_news)) {
    return "";
  }

  $html .= "<div class='featured-news-wrapper'><h2>Featured news</h2>";
  $html .= '<a href="' . get_permalink( get_option( 'page_for_posts' ) ) . '" class="button button--inverse">View all news</a><div class="news-articles">';

  foreach($featured_news as $news) {
    $html .= '<article id="post-' . $news["ID"]. '" class="news-article">';
    $html .= qavs_post_thumbnail($news["ID"]);
    $html .= '<div class="news-article__details">';
    $html .= '<h2 class="news-article__title" id="title-' . $news["ID"] . '">' . get_the_title($news["ID"]) . '</h2>';
    $html .= '<div class="news-article__meta">';
    $html .= qavs_posted_on($news["ID"]);
    $html .= '</div>';
    $html .= '<a href="' . esc_url( get_permalink($news["ID"]) ) . '" rel="bookmark" aria-labelledby="title-' . $news["ID"] . '" title="Click to read article" class="news-article__cta arrow-link">Read article</a></div></article>';
  }
  
  $html .= '</div></div>';

  return $html;
}

function qavs_block__featured_awardees( $block_attributes, $content ) {
  if (!isset($block_attributes["categoryID"])) {
    return "";
  }

  $featured_awardees = wp_get_recent_posts( array(
    'numberposts' => 3,
    'post_status' => 'publish',
    'tax_query' => [
      'taxonomy' => 'category',
      'field' => 'term_id',
      'terms' => $block_attributes["categoryID"],
      'operator' => 'IN'
    ]
  ) );

  if ($_GET['debug']) {
    var_dump($featured_awardees);
    var_dump($block_attributes["categoryID"]);
    exit;
  }

  $html = "";

  if(empty($featured_awardees)) {
    return "";
  }

  $html .= "<div class='featured-awardees-wrapper'><h2>Featured awardees</h2>";
  $html .= '<a href="' . get_category_link($block_attributes["categoryID"]) . '" class="button button--inverse">View all awardees</a>';
  $html .= "<p class='featured-awardees-text'>Take a look at some of the fantastic volunteer groups who have received a Queen’s Award for Voluntary Service in our showcase below.</p><div class='featured-awardees'>";

  foreach($featured_awardees as $awardee) {
    $html .= '<article id="post-' . $awardee["ID"]. '" class="featured-awardee">';
    $html .= qavs_post_thumbnail($awardee["ID"]);
    $html .= '<div class="featured-awardee__details">';
    $html .= '<h2 class="featured-awardee__title" id="title-' . $awardee["ID"] . '">' . get_the_title($awardee["ID"]) . '</h2>';
    $html .= '<div class="featured-awardee__meta">';
    $html .= qavs_posted_on($awardee["ID"]);
    $html .= '</div>';
    $html .= '<a href="' . esc_url( get_permalink($awardee["ID"]) ) . '" rel="bookmark" aria-labelledby="title-' . $awardee["ID"] . '" title="Click to read article" class="featured-awardee__cta">View article</a></div></article>';
  }
  
  $html .= '</div></div>';

  return $html;
}

function qavs_block__promoted_article( $block_attributes, $content ) {

  $featured_news = wp_get_recent_posts( array(
    'numberposts' => 1,
    'post_status' => 'publish',
    'meta_key' => 'featured_home_page',
    'meta_value' => '1'
  ) );

  $html = "";

  if(empty($featured_news)) {
    return "";
  }

  foreach($featured_news as $news) {
    $html .= '<article id="post-' . $news["ID"]. '" class="promoted-article">';
    $html .= qavs_post_thumbnail($news["ID"]);
    $html .= '<div class="promoted-article__details">';
    $html .= '<h2 class="promoted-article__title" id="title-' . $news["ID"] . '">' . get_the_title($news["ID"]) . '</h2>';
    $html .= '<div class="promoted-article__meta">';
    $html .= qavs_posted_on($news["ID"]);
    $html .= '</div>';
    $html .= '<a href="' . esc_url( get_permalink($news["ID"]) ) . '" rel="bookmark" aria-labelledby="title-' . $news["ID"] . '" title="Click to read article" class="promoted-article__cta arrow-link">Read article</a></div></article>';
  }

  return $html;
}

function qavs_block__parental_tabs( $block_attributes, $content ) {

  $parent_id = wp_get_post_parent_id(get_the_ID());
  $pages = get_pages();
  $children = get_page_children( $parent_id, $pages );

  $html = "<ul class='page-tabs'>";
    foreach ($children as $child ) {
      $class = get_the_ID() == $child->ID ? "active" : "";
      $html .= "<li class='" . $class . "'>";
      $html .= "<a href='" . get_permalink($child->ID) . "'>" . get_the_title($child->ID) . "</a>";
      $html .= "</li>";
    }
  $html .= "</ul>";
  
  return $html;
}

function qavs_block__parental_pagination( $block_attributes, $content ) {

  $parent_id = wp_get_post_parent_id(get_the_ID());
  $pages = get_pages();
  $children = get_page_children( $parent_id, $pages );
  $index = null;
  $found = false;

  foreach ($children as $idx => $child ) {

    if ($child->ID == get_the_ID()) {
      $index = $idx;
      $found = true;
    }
  }

  if (!$found || $index + 1 >= count($children)) {
    return "";
  }

  $next = $children[$index + 1];
  $permalink = get_permalink($next->ID);
  $title = get_the_title($next->ID);

  return '<div class="parental-navigation"><a href="' . $permalink . '" rel="next"><span class="nav-subtitle">Next</span> <span class="nav-title">' . $title . '</span></a></div>';
}
 
function qavs_dynamic_blocks() { 
  register_block_type( 'qavs/featured-news', array(
    'api_version' => 2,
    'render_callback' => 'qavs_block__featured_news'
  ) );

  register_block_type( 'qavs/featured-awardees', array(
    'api_version' => 2,
    'render_callback' => 'qavs_block__featured_awardees'
  ) );

  register_block_type( 'qavs/promoted-article', array(
    'api_version' => 2,
    'render_callback' => 'qavs_block__promoted_article'
  ) );

  register_block_type( 'qavs/parental-tabs', array(
    'api_version' => 2,
    'render_callback' => 'qavs_block__parental_tabs'
  ) );

  register_block_type( 'qavs/parental-pagination', array(
    'api_version' => 2,
    'render_callback' => 'qavs_block__parental_pagination'
  ) );
}
add_action( 'init', 'qavs_dynamic_blocks' );
