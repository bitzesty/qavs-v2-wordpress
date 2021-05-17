<?php

$featured_news = wp_get_recent_posts( array(
  'numberposts' => 3,
  'post_status' => 'publish',
  'meta_key' => 'featured_news',
  'meta_value' => '1'
) );
?>

<?php if(!empty($featured_news)): ?>
<h2>Featured news</h2>
<a href="<?php echo get_permalink( get_option( 'page_for_posts' ) ); ?>" class="button button--inverse">
  View all news
</a>

<div class="news-articles">
  <?php foreach($featured_news as $news): ?>
    <article id="post-<?php the_ID(); ?>" class='news-article' >
      <?php qavs_post_thumbnail(); ?>
      <div class="news-article__details">
        <?php the_title( '<h2 class="news-article__title" id="title-' . get_the_ID() . '">', '</a></h2>' ); ?>
        <div class="news-article__meta">
          <?php qavs_posted_on(); ?>
        </div>
        <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" aria-labelledby="title-<?php the_ID(); ?>" title="Click to read article" class="news-article__cta arrow-link">
          Read article
        </a>
      </div>
    </article>
  <?php endforeach; ?>
</div>
<?php endif; ?>
