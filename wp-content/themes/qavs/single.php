<?php
/**
 * The template for displaying all single posts
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/#single-post
 *
 * @package QAVS
 */

get_header();
?>

	<main id="primary" class="site-main" tabindex="-1">

		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/single', get_post_type() );
		endwhile; // End of the loop.
		?>

	</main><!-- #main -->

<?php
get_footer();
