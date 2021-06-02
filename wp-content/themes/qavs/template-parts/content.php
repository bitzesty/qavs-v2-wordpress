<?php
/**
 * Template part for displaying posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package QAVS
 */

?>

<article id="post-<?php the_ID(); ?>" class='news-article' >
  <?php echo qavs_post_thumbnail(get_the_ID()); ?>
  <div class="news-article__details">
    <?php the_title( '<h2 class="news-article__title" id="title-' . get_the_ID() . '">', '</a></h2>' ); ?>
    <div class="news-article__meta">
      <?php echo qavs_posted_on(get_the_ID()); ?>
    </div>
    <a href="<?php echo esc_url( get_permalink() ); ?>" rel="bookmark" aria-label="Read article: <?php echo get_the_title(); ?>" title="Click to read article" class="news-article__cta arrow-link">
      Read article
    </a>
  </div>
</article>
