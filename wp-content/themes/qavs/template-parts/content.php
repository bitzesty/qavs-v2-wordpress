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
