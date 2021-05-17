<?php
/**
 * Genesis Getting Started page.
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
			<h1 class="grow"><?php echo esc_html_e( 'Getting Started with Genesis Blocks', 'genesis-blocks' ); ?></h1>
			<a class="components-button review-button hide-mobile" href="//wordpress.org/support/plugin/genesis-blocks/reviews/" target="_blank" rel="noopener noreferrer">
				<span>&#9733;</span>
				<?php esc_html_e( 'Leave a review!', 'genesis-blocks' ); ?>
			</a>
		</div>
	</div>

	<div class="genesis-blocks-settings-sections">
		<div class="components-tab-panel__tab-content">
			<div class="genesis-gs-intro genesis-blocks-grid-2">
				<div class="genesis-gs-intro-text">
					<span class="genesis-gs-intro-tag"><?php echo esc_html_e( 'Getting Started', 'genesis-blocks' ); ?></span>
					<h2><?php echo esc_html_e( 'Welcome to the future of site building with WordPress and Genesis Blocks.', 'genesis-blocks' ); ?></h2>
					<p><?php echo esc_html_e( 'The Genesis Blocks collection is now ready to use in your posts and pages. Simply search for "genesis" in the block inserter to display the Genesis Blocks collection. Check out the boxes below for help docs, tips and tricks, and additional resources.', 'genesis-blocks' ); ?></p>
				</div>
				<div class="genesis-gs-intro-image">
					<img src="<?php echo esc_url( $this->context['url'] . 'lib/Settings/assets/images/genesis-intro.svg' ); ?>" alt="<?php esc_html_e( 'Genesis Blocks', 'genesis-blocks' ); ?>" />
				</div>
			</div>

			<div class="genesis-gs-resources genesis-blocks-grid-3">
				<div class="genesis-gs-resource">
					<img src="<?php echo esc_url( $this->context['url'] . 'lib/Settings/assets/images/help.svg' ); ?>" alt="<?php esc_html_e( 'Help and Documentation', 'genesis-blocks' ); ?>" />
					<h3><?php echo esc_html_e( 'Help and Documentation', 'genesis-blocks' ); ?></h3>
					<p><?php echo esc_html_e( 'The Genesis Blocks wiki has helpful documentation, tips and tricks, code snippets, and more to help you get started.', 'genesis-blocks' ); ?></p>
					<a href="https://developer.wpengine.com/genesis-pro/" class="components-button is-primary"><?php echo esc_html_e( 'Browse the Docs', 'genesis-blocks' ); ?></a>
				</div>
				<div class="genesis-gs-resource">
					<img src="<?php echo esc_url( $this->context['url'] . 'lib/Settings/assets/images/themes.svg' ); ?>" alt="<?php esc_html_e( 'Genesis Themes', 'genesis-blocks' ); ?>" />
					<h3><?php echo esc_html_e( 'Browse Genesis Themes', 'genesis-blocks' ); ?></h3>
					<p><?php echo esc_html_e( 'The Genesis theme collection has beautiful block-powered themes that help you quickly get started with the new block editor.', 'genesis-blocks' ); ?></p>
					<a href="https://studiopress.com/themes" class="components-button is-primary"><?php echo esc_html_e( 'Browse Themes', 'genesis-blocks' ); ?></a>
				</div>
				<div class="genesis-gs-resource">
					<img src="<?php echo esc_url( $this->context['url'] . 'lib/Settings/assets/images/review.svg' ); ?>" alt="<?php esc_html_e( 'Leave a review', 'genesis-blocks' ); ?>" />
					<h3><?php echo esc_html_e( 'Enjoying Genesis Blocks?', 'genesis-blocks' ); ?></h3>
					<p><?php echo esc_html_e( 'Leave a review on WordPress.org and help the rest of the community discover the potential of the block editor.', 'genesis-blocks' ); ?></p>
					<a href="http://wordpress.org/support/plugin/genesis-blocks/reviews/" class="components-button is-primary"><?php echo esc_html_e( 'Leave a Review', 'genesis-blocks' ); ?></a>
				</div>
			</div>
		</div>
	</div>
</div>
