<?php
/**
 * Rating notice class.
 *
 * Rating notice is almost same as email notice. Only the notice
 * content is different.
 *
 * @since      2.0
 * @author     Incsub (Joel James)
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * @copyright  Copyright (c) 2022, Incsub
 * @package    WPMUDEV\Notices\Notices
 */

namespace WPMUDEV\Notices\Notices;

// If this file is called directly, abort.
defined( 'WPINC' ) || die;

if ( ! class_exists( __NAMESPACE__ . '\\Rating' ) ) {
	/**
	 * Class Rating
	 *
	 * @since   2.0
	 * @package WPMUDEV\Notices
	 */
	class Rating extends Email {

		/**
		 * Current notice type.
		 *
		 * @var string $type
		 *
		 * @since 2.0
		 */
		protected $type = 'rate';

		/**
		 * Show after 2 days
		 *
		 * @var string $type
		 *
		 * @since 2.0
		 */
		protected $time = WEEK_IN_SECONDS; // After 1 week.

		/**
		 * Render a notice type content.
		 *
		 * @param string $plugin Plugin ID.
		 *
		 * @since 2.0
		 *
		 * @return void
		 */
		public function render( $plugin ) {
			$this->enqueue_assets( $plugin );

			$user = wp_get_current_user();

			/* translators: %1$s - user name, %2$s - plugin name, %2$s - new line <br> */
			$msg = __( "Hey %1\$s, you've been using %2\$s for a while now, and we hope you're happy with it.", 'wdev_frash' ) . '%3$s' . __( "We've spent countless hours developing this free plugin for you, and we would really appreciate it if you dropped us a quick rating!", 'wdev_frash' );
			$msg = apply_filters( 'wdev_rating_message_' . $plugin, $msg );

			?>
			<div class="notice frash-notice frash-notice-<?php echo esc_attr( $this->type ); ?>" style="display:none">
				<?php $this->render_hidden_fields( $plugin ); ?>

				<div class="frash-notice-logo"><span></span></div>
				<div class="frash-notice-message">
					<?php
					printf(
						esc_html( $msg ),
						'<strong>' . esc_html( $user->display_name ) . '</strong>',
						'<strong>' . esc_html( $this->get_option( 'title', __( 'Plugin', 'wdev_frash' ) ) ) . '</strong>',
						'<br>'
					);
					?>
				</div>
				<div class="frash-notice-cta">
					<button class="frash-notice-act button-primary" data-msg="<?php esc_attr_e( 'Thanks :)', 'wdev_frash' ); ?>">
						<?php
						printf( /* translators: %s - plugin name */
							esc_html__( 'Rate %s', 'wdev_frash' ),
							esc_html( $this->get_option( 'title', __( 'Plugin', 'wdev_frash' ) ) )
						);
						?>
					</button>
					<button class="frash-notice-dismiss" data-msg="<?php esc_attr_e( 'Saving', 'wdev_frash' ); ?>">
						<?php esc_html_e( 'No thanks', 'wdev_frash' ); ?>
					</button>
				</div>
			</div>
			<?php
		}

		/**
		 * Check if current notice is allowed for the plugin.
		 *
		 * @param string $plugin Plugin ID.
		 *
		 * @since 2.0
		 *
		 * @return bool
		 */
		public function can_show( $plugin ) {
			// Mailchimp list id is required.
			$wp_slug = $this->get_option( 'wp_slug' );

			// Show only on dashboard.
			return 'dashboard' === $this->screen_id() && ! empty( $wp_slug );
		}
	}
}
