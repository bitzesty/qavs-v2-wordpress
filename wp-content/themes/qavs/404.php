<?php
/**
 * The template for displaying 404 pages (not found)
 *
 * @link https://codex.wordpress.org/Creating_an_Error_404_Page
 *
 * @package QAVS
 */

get_header();
?>

	<main id="primary" class="site-main">

		<section class="section error-404 not-found">
			<div class="container">
				<header class="page-header">
					<h1 class="page-title"><?php esc_html_e( 'Page not found', 'qavs' ); ?></h1>
				</header><!-- .page-header -->

				<div class="page-content">
					<p>
						If you entered a web address, check it is correct.
					</p>

					<p>
						You can <a href="/">browse from the homepage</a> or use the search box above to find the information you need.
					</p>
				</div><!-- .page-content -->
			</div>
		</section><!-- .error-404 -->

	</main><!-- #main -->

<?php
get_footer();
