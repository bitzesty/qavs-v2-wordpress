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
        <div class="article__sharing">
          <p id="share-article-title"><strong>Share this article</strong></p>
          <nav aria-labelledby="share-article-title" class="sharing-buttons">
            <a rel="noopener nofollow" target="_blank" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo qavs_sharing_url(); ?>&title=<?php echo qavs_sharing_title(); ?>&description=<?php echo qavs_sharing_text(); ?>" aria-label="Share this news article on LinkedIn">
              <svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 42" aria-hidden="true" focusable="false"><ellipse cx="20" cy="20.79" rx="19" ry="19.75" fill="transparent" stroke="#136C85"/><path fill-rule="evenodd" clip-rule="evenodd" d="M12.348 12.917c.715 0 1.291-.22 1.73-.66.437-.439.652-.986.644-1.64-.009-.664-.226-1.214-.651-1.649-.425-.435-.991-.652-1.697-.652-.707 0-1.279.217-1.717.652-.438.435-.657.985-.657 1.649 0 .654.213 1.201.638 1.64.425.44.987.66 1.685.66h.025zm8.627 15.149v-7.44c0-.458.047-.82.14-1.09.179-.457.45-.84.81-1.15.362-.31.811-.464 1.347-.464.732 0 1.27.267 1.614.8.345.534.517 1.271.517 2.213v7.13h4.199v-7.64c0-1.965-.443-3.456-1.328-4.474-.884-1.018-2.054-1.527-3.509-1.527-.536 0-1.023.07-1.461.208-.438.14-.808.334-1.11.585a5.563 5.563 0 00-.721.7c-.17.205-.336.441-.498.71v-1.894h-4.199l.013.646c.007.363.011 1.361.013 2.996v1.852c-.002 2.095-.01 4.707-.026 7.839h4.199zm-6.521-13.333v13.333h-4.211V14.733h4.211z" fill="#136C85"/></svg>LinkedIn
            </a>
            <a rel="noopener nofollow" target="_blank" href="https://twitter.com/intent/tweet?text=<?php echo qavs_sharing_text(); ?>&url=<?php echo qavs_sharing_url(); ?>" aria-label="Share this news article on Twitter">
              <svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 42" aria-hidden="true" focusable="false"><ellipse cx="20" cy="20.79" rx="19" ry="19.75" fill="transparent" stroke="#136C85"/><path d="M15.924 29.887c1.6 0 3.104-.262 4.509-.786 1.405-.524 2.605-1.225 3.601-2.105a13.961 13.961 0 002.576-3.034 13.732 13.732 0 002.143-7.324c0-.269-.005-.47-.014-.605A9.62 9.62 0 0031 13.625a8.762 8.762 0 01-2.596.721 4.498 4.498 0 001.982-2.566 8.672 8.672 0 01-2.862 1.125c-.893-.98-1.991-1.47-3.294-1.47-1.247 0-2.31.453-3.19 1.361-.88.909-1.32 2.007-1.32 3.294 0 .346.038.702.113 1.067a12.254 12.254 0 01-5.186-1.434 12.872 12.872 0 01-4.111-3.438 4.678 4.678 0 00-.615 2.35c0 .797.182 1.537.545 2.22a4.6 4.6 0 001.466 1.657 4.366 4.366 0 01-2.039-.59v.057c0 1.125.342 2.112 1.026 2.963.684.85 1.548 1.386 2.59 1.607-.391.106-.787.159-1.187.159-.26 0-.544-.024-.851-.072a4.553 4.553 0 001.591 2.299c.773.6 1.647.91 2.625.93-1.638 1.326-3.504 1.99-5.598 1.99-.4 0-.763-.02-1.089-.058 2.094 1.393 4.402 2.09 6.924 2.09z" fill="#136C85"/></svg>Twitter
            </a>
            <a rel="noopener nofollow" target="_blank" href="https://www.linkedin.com/shareArticle?mini=true&title=<?php echo qavs_sharing_title(); ?>&source=<?php echo urlencode(qavs_website_name()); ?>&url=<?php echo qavs_sharing_url(); ?>&summary=<?php echo qavs_sharing_text(); ?>"  aria-label="Share this news article on Facebook">
              <svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 42" aria-hidden="true" focusable="false"><ellipse cx="20" cy="20.79" rx="19" ry="19.75" fill="transparent" stroke="#136C85"/><path fill-rule="evenodd" clip-rule="evenodd" d="M24.977 9.993v3.141h-1.813c-.662 0-1.108.143-1.34.428-.23.286-.346.714-.346 1.286v2.248h3.384l-.45 3.523h-2.934v9.03h-3.533v-9.03H15v-3.523h2.945v-2.594c0-1.475.4-2.62 1.2-3.432.801-.813 1.868-1.22 3.2-1.22 1.131 0 2.009.048 2.632.143z" fill="#136C85"/></svg>Facebook
            </a>
            <a href="mailto:?subject=I wanted you to see this news article from <?php echo qavs_website_name(); ?>&body=Check out this news article '<?php echo qavs_sharing_title(); ?>' at <?php echo qavs_sharing_url(); ?>"  aria-label="Share this news article on Facebook">
              <svg fill="none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 42" aria-hidden="true" focusable="false"><ellipse cx="20" cy="20.79" rx="19" ry="19.75" fill="transparent" stroke="#136C85"/><path d="M19.432 21.804c.196 0 .408-.035.637-.105.23-.07.474-.175.733-.316.26-.14.48-.267.663-.38.183-.113.412-.26.688-.444.276-.183.457-.302.542-.356.781-.499 2.77-1.765 5.964-3.8a5.827 5.827 0 001.555-1.437c.416-.561.625-1.15.625-1.765 0-.514-.202-.955-.606-1.321a2.062 2.062 0 00-1.434-.55H10.04c-.654 0-1.157.203-1.51.608-.353.406-.529.912-.529 1.52 0 .491.234 1.023.701 1.596.467.572.964 1.022 1.491 1.35.289.187 1.16.742 2.613 1.665 1.453.924 2.566 1.635 3.339 2.134.085.054.265.173.542.356.276.183.505.331.688.444.182.113.403.24.663.38.259.14.503.246.732.316.23.07.442.105.638.105h.025z" fill="#136C85"/><path d="M28.8 27.68c.56 0 1.04-.184 1.44-.55.399-.366.599-.807.599-1.32v-9.282c-.366.374-.79.713-1.275 1.017-2.787 1.73-4.902 3.074-6.347 4.033-.484.327-.877.582-1.179.765-.301.183-.703.37-1.204.561-.501.191-.969.287-1.402.287h-.025c-.434 0-.901-.096-1.402-.287a7.539 7.539 0 01-1.205-.56 30.194 30.194 0 01-1.179-.766c-1.147-.772-3.258-2.116-6.334-4.033A6.63 6.63 0 018 16.528v9.281c0 .514.2.955.599 1.321.4.366.88.55 1.44.55H28.8z" fill="#136C85"/></svg>Email
            </a>
          </nav>
        </div>
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
