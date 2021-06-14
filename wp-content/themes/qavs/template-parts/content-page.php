<?php
/**
 * Template part for displaying page content in page.php
 *
 * @link https://developer.wordpress.org/themes/basics/template-hierarchy/
 *
 * @package QAVS
 */

$pageLanguage = carbon_get_post_meta(get_the_ID(), "page_language");
?>

<article id="post-<?php the_ID(); ?>" <?php post_class(); ?> lang="<?php echo $pageLanguage == 'cy' ? 'cy' : 'en'; ?>">
	<div class="entry-content">
		<?php
		the_content();
		?>
	</div><!-- .entry-content -->
</article><!-- #post-<?php the_ID(); ?> -->
