<?php
/**
 * The main template file
 *
 * This is the most generic template file in a WordPress theme
 * and one of the two required files for a theme (the other being style.css).
 * It is used to display a page when nothing more specific matches a query.
 * E.g., it puts together the home page when no home.php file exists.
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package QAVS
 */

get_header();
?>

	<main id="primary" class="site-main">
    <div class="section">
      <div class="container">
        <h1 class="page-title">
          News
        </h1>
        <div class="news-social-links">
          <p>
            Follow us on:
            <a href="" class="news__social-link" aria-label="Twitter">
              <svg width="19" height="15" fill="none" xmlns="http://www.w3.org/2000/svg"><path d="M6.605 14.979c1.31 0 2.541-.206 3.692-.618 1.15-.413 2.133-.965 2.949-1.657a11.108 11.108 0 002.109-2.389c.59-.9 1.03-1.84 1.32-2.82a10.34 10.34 0 00.423-3.42 7.706 7.706 0 001.852-1.896 7.4 7.4 0 01-2.126.568 3.563 3.563 0 001.623-2.02 7.271 7.271 0 01-2.343.885C15.372.84 14.473.455 13.405.455c-1.021 0-1.892.357-2.612 1.072a3.519 3.519 0 00-1.08 2.593c0 .272.03.552.091.84A10.347 10.347 0 015.56 3.83a10.452 10.452 0 01-3.367-2.706 3.571 3.571 0 00-.503 1.85c0 .628.15 1.21.446 1.747.297.537.698.972 1.2 1.305a3.683 3.683 0 01-1.668-.465v.045c0 .885.28 1.663.84 2.332a3.637 3.637 0 002.12 1.265c-.32.083-.644.125-.971.125-.214 0-.446-.019-.698-.057.237.734.67 1.337 1.303 1.81a3.617 3.617 0 002.15.732c-1.342 1.044-2.87 1.566-4.584 1.566a7.95 7.95 0 01-.892-.045c1.715 1.097 3.605 1.645 5.67 1.645z" fill="#fff"/></svg>
              Twitter
            </a>
            <a href="" class="news__social-link" aria-label="Facebook">
              <svg width="10" height="17" fill="none" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.571.754v2.597H8.012c-.569 0-.953.118-1.151.354-.199.236-.298.59-.298 1.062v1.858h2.909l-.387 2.911H6.563V17H3.524V9.536H.993v-2.91h2.531V4.481c0-1.22.345-2.166 1.033-2.838C5.245.972 6.162.636 7.307.636c.973 0 1.728.04 2.264.118z" fill="#fff"/></svg>
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
    <div class="container">
      <div class="featured-news-article">
        <div class="featured-news-article__details">
          <h3>Lorem ipsum dolor sit amet</h3>
          <p class="featured-news-article__meta">11 April 2021</p>
          <p class="featured-news-article__excerpt">
            Lorem ipsum dolor, sit amet consectetur adipisicing elit. Accusamus ea provident dolores fugiat reprehenderit, iusto pariatur sit facere, ullam iste ipsa consectetur laborum nam cum repellat eum nostrum natus blanditiis!
          </p>
          <a href="" class="featured-news-article__cta">Read article</a>
        </div>
        <img src="https://via.placeholder.com/400" alt="" class="featured-news-article__image">
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
