<?php
/**
 * App view for the settings page.
 *
 * The React application from js/src/app.js mounts at #root.
 *
 * @package Genesis\Blocks\Settings
 * @since   1.0.0
 * @author  StudioPress
 * @license GPL-2.0-or-later
 * @link    https://github.com/studiopress/genesis-blocks/
 */

?>

<div class="wrap genesis-blocks-settings-page">
	<div class="intro-wrap">
		<div class="intro">
			<img src="<?php echo esc_url( $this->context['url'] . 'lib/Settings/assets/images/genesis-planet-icon.svg' ); ?>" alt="<?php esc_html_e( 'Genesis Blocks', 'genesis-blocks' ); ?>" />
			<h1 class="grow"><?php echo esc_html( $this->page_title ); ?></h1>
			<a class="components-button review-button hide-mobile" href="//wordpress.org/support/plugin/genesis-blocks/reviews/" target="_blank" rel="noopener noreferrer">
				<span>&#9733;</span>
				<?php esc_html_e( 'Leave a review!', 'genesis-blocks' ); ?>
			</a>
		</div>
	</div>
	<div id="root"></div>
</div>
