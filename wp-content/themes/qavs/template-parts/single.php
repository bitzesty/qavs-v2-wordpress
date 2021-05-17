<div class="news-single-page">
  <div class="container">
    <a href="<?php echo get_permalink( get_option( 'page_for_posts' ) ); ?>" class="back-link">
      Back to all news
    </a>

    <div class="article">
      <h1 class="article__title"><?php the_title(); ?></h1>
      <div class="article__meta">
        <?php qavs_posted_on(); ?>
      </div>
      <?php if(has_post_thumbnail()): ?>
        <?php the_post_thumbnail('full', ['class' => 'article__image']); ?>
      <?php endif; ?>
      <div class="article__body">
        <?php the_content(); ?>
      </div>
    </div>

    <?php

    the_post_navigation(
      array(
        'prev_text' => '<span class="nav-subtitle">' . esc_html__( 'Previous article', 'qavs' ) . '</span> <span class="nav-title">%title</span>',
        'next_text' => '<span class="nav-subtitle">' . esc_html__( 'Next article', 'qavs' ) . '</span> <span class="nav-title">%title</span>',
      )
    );
    
    ?>
  </div>
</div>
