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
  $html .= '<a href="' . get_permalink( get_option( 'page_for_posts' ) ) . '" class="button button--inverse desktop-only">View all news</a>';
  $html .= "<div class=\"news-articles news-articles--home\">";

  foreach($featured_news as $news) {
    $html .= '<article id="post-' . $news["ID"]. '" class="news-article">';
    $html .= qavs_post_thumbnail($news["ID"]);
    $html .= '<div class="news-article__details">';
    $html .= '<h3 class="news-article__title" id="title-' . $news["ID"] . '">' . get_the_title($news["ID"]) . '</h3>';
    $html .= '<div class="news-article__meta">';
    $html .= qavs_posted_on($news["ID"]);
    $html .= '</div>';
    $html .= '<a href="' . esc_url( get_permalink($news["ID"]) ) . '" rel="bookmark" aria-label="Read article: ' .  get_the_title($news["ID"]) . '" title="Click to read article" class="news-article__cta arrow-link">Read article</a></div></article>';
  }

  $html .= '</div>';

  $html .= '<a href="' . get_permalink( get_option( 'page_for_posts' ) ) . '" class="button button--inverse mobile-only">View all news</a>';

  $html .= '</div>';

  return $html;
}

function qavs_block__featured_awardees( $block_attributes, $content ) {
  if (!isset($block_attributes["categoryID"])) {
    return "";
  }

  $featured_awardees = get_posts( array(
    'posts_per_page' => 3,
    'post_status' => 'publish',
    'tax_query' => [
      [
        'taxonomy' => 'category',
        'field' => 'term_id',
        'terms' => $block_attributes["categoryID"],
        'operator' => 'IN'
      ]
    ]
  ) );

  $html = "";

  if(empty($featured_awardees)) {
    return "";
  }

  $html .= "<div class='featured-awardees-wrapper'><h2>Featured awardees</h2>";
  $html .= "<p class='featured-awardees-text'>Here are just a few of the groups that have received the award this year.</p><p>You can <a href='https://qavs.dcms.gov.uk/awardees/'>view a list of all awardees</a>. Also, we will be adding more inspiring stories over the next few months - you can <a href='https://qavs.dcms.gov.uk/category/featured-awardees/'>read other stories</a> in the website’s news section.</p><br /><div class='featured-awardees'>";

  foreach($featured_awardees as $awardee) {
    if (isset($_GET['debug'])) {
      var_dump(get_field("year_and_location", $awardee->ID));
    }
    $html .= '<article id="post-' . $awardee->ID. '" class="featured-awardee">';
    $html .= qavs_post_thumbnail($awardee->ID);
    $html .= '<div class="featured-awardee__details">';
    $html .= '<h3 class="featured-awardee__title" id="title-' . $awardee->ID . '">' . get_the_title($awardee->ID) . '</h3>';
    $html .= '<div class="featured-awardee__meta">';
    $html .= get_field("year_and_location", $awardee->ID);
    $html .= '</div>';
    $html .= '<div class="featured-awardee__escerpt">';
    $html .= get_the_excerpt($awardee->ID);
    $html .= '</div>';
    $html .= '<a href="' . esc_url( get_permalink($awardee->ID) ) . '" rel="bookmark" aria-label="View details of the awardee: ' .  get_the_title($awardee->ID) . '" title="Click to read article" class="featured-awardee__cta">View details</a></div></article>';
  }

  $html .= '</div>';
  $html .= '</div>';

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
    $html .= '<h3 class="promoted-article__title" id="title-' . $news["ID"] . '">' . get_the_title($news["ID"]) . '</h3>';
    $html .= '<div class="promoted-article__meta">';
    $html .= qavs_posted_on($news["ID"]);
    $html .= '</div>';
    $html .= '<a href="' . esc_url( get_permalink($news["ID"]) ) . '" rel="bookmark" aria-label="Read article: ' .  get_the_title($news["ID"]) . '" title="Click to read article" class="promoted-article__cta arrow-link">Read article</a></div></article>';
  }

  return $html;
}

function qavs_block__parental_tabs( $block_attributes, $content ) {

  $parent_id = wp_get_post_parent_id(get_the_ID());
  $pages = get_pages([ 'sort_column' => 'menu_order' ]);
  $children = get_page_children( $parent_id, $pages );

  $html = "<nav aria-label='Table of contents'><ul class='page-tabs'>";
    foreach ($children as $child ) {
      $class = get_the_ID() == $child->ID ? "active" : "";
      $html .= "<li class='" . $class . "'>";
      $html .= "<a href='" . get_permalink($child->ID) . "' " . (get_the_ID() == $child->ID ? 'aria-current="page"' : '') . ">" . get_the_title($child->ID) . "</a>";
      $html .= "</li>";
    }
  $html .= "</ul></nav>";

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

  return '<nav aria-label="In-page navigation" class="parental-navigation"><a href="' . $permalink . '" rel="next"><span class="nav-subtitle">Next</span> <span class="nav-title">' . $title . '</span></a></nav>';
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
