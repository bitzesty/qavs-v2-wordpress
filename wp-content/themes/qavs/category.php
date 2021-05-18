<?php
get_header();
?>

	<main id="primary" class="site-main">
    <div class="section">
      <div class="container">
        <h1 class="page-title">
          <?php echo single_cat_title('', false); ?>
        </h1>
        <div class="news-social-links">
          <p>
            Follow us on:
            <a href="" class="news__social-link" aria-label="Twitter">
              <svg width="40" height="40" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><g fill="none" fill-rule="evenodd"><g transform="translate(1 1)"><circle stroke="#136C85" fill="#FFF" cx="19" cy="19" r="19"/><path d="M14.924 27.752c1.6 0 3.103-.252 4.509-.756 1.405-.504 2.605-1.179 3.601-2.025a13.575 13.575 0 002.576-2.92 12.967 12.967 0 001.612-3.445 12.649 12.649 0 00.517-4.182A9.415 9.415 0 0030 12.108a9.03 9.03 0 01-2.596.693c.977-.582 1.637-1.405 1.982-2.468a8.876 8.876 0 01-2.862 1.082C25.631 10.472 24.533 10 23.23 10c-1.247 0-2.31.437-3.19 1.31-.88.874-1.32 1.93-1.32 3.17 0 .332.038.675.112 1.026a12.628 12.628 0 01-5.185-1.38 12.766 12.766 0 01-4.111-3.308 4.367 4.367 0 00-.615 2.26c0 .768.182 1.48.545 2.137a4.485 4.485 0 001.465 1.595 4.495 4.495 0 01-2.038-.57v.056a4.31 4.31 0 001.026 2.85 4.441 4.441 0 002.59 1.547c-.391.102-.786.152-1.187.152-.26 0-.544-.023-.851-.069a4.383 4.383 0 001.591 2.212 4.415 4.415 0 002.625.895c-1.638 1.276-3.504 1.914-5.598 1.914-.4 0-.763-.019-1.089-.056 2.094 1.34 4.402 2.011 6.924 2.011z" fill="#136C85" fill-rule="nonzero"/></g></g></svg>
              Twitter
            </a>
            <a href="" class="news__social-link" aria-label="Facebook">
              <svg width="40" height="40" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 40 40"><g transform="translate(1 1)" fill="none" fill-rule="evenodd"><circle stroke="#136C85" fill="#FFF" cx="19" cy="19" r="19"/><path d="M23.977 8.614v3.022h-1.813c-.662 0-1.108.137-1.34.412-.23.274-.346.686-.346 1.236v2.163h3.384l-.45 3.389h-2.934v8.688h-3.533v-8.688H14v-3.389h2.945v-2.495c0-1.42.4-2.52 1.2-3.303.801-.782 1.868-1.173 3.2-1.173 1.131 0 2.009.046 2.632.138z" fill="#136C85"/></g></svg>
              Facebook
            </a>
          </p>
        </div>

        <div class="news-categories">
        <p>View by topic</p>
          <ul>
            <li>
              <a href="/news" class="<?php echo !is_category() ? 'active' : ''; ?>">All topics</a>
            </li>
            <?php foreach(qavs_get_categories() as $category): ?>
              <li>
                <a href="<?php echo get_category_link($category['id']); ?>" class="<?php echo $category['active'] ? 'active' : ''; ?>">
                  <?php echo $category['name']; ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      </div>
    </div>

    <?php if ( have_posts() ) : ?>
      <div class="container">
        <div class='news-articles'>
          <?php while (have_posts()) { the_post(); get_template_part( 'template-parts/content', get_post_type() ); } ?>
        </div>
      
        <?php the_posts_navigation(); ?>
      </div>
		<?php else :
			get_template_part( 'template-parts/content', 'none' );
		endif;
		?>
	</main><!-- #main -->

<?php
get_sidebar();
get_footer();