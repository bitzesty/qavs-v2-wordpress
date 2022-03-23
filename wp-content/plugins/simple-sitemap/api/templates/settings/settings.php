<?php

namespace WPGO_Plugins\Plugin_Framework;

/*
 *    Plugin utility functions
 */

class Settings_Templates_FW {


	protected $module_roots;

	/* Class constructor. */
	public function __construct( $module_roots ) {
		$this->module_roots = $module_roots;
	}

	public function try_our_other_plugins( $current_plugin = '' ) {
		// use correct path for images
		$image_path = $this->module_roots['uri'];

		$plugins = array(
			'flexible-faqs'     => array(
				'label' => 'Flexible FAQs',
				'link'  => 'https://flexiblefaqs.com',
				'thumb' => $image_path . '/api/assets/images/flexible-faqs-thumb.jpg',
			),
			'svg-flags'         => array(
				'label' => 'SVG Flags',
				'link'  => 'https://wpgoplugins.com/plugins/svg-flags/',
				'thumb' => $image_path . '/api/assets/images/svg-flags-thumb.png',
			),
			'simple-sitemap'    => array(
				'label' => 'Simple Sitemap',
				'link'  => 'https://wpgoplugins.com/plugins/simple-sitemap/',
				'thumb' => $image_path . '/api/assets/images/simple-sitemap-thumb.png',
			),
			'content-censor'    => array(
				'label' => 'Content Censor',
				'link'  => 'https://wpgoplugins.com/plugins/content-censor/',
				'thumb' => $image_path . '/api/assets/images/content-censor-thumb.png',
			),
			'seo-media-manager' => array(
				'label' => 'SEO Media Manager',
				'link'  => 'https://wpgoplugins.com/plugins/seo-media-manager/',
				'thumb' => $image_path . '/api/assets/images/seo-media-manager-thumb.png',
			),
		);

		// remove the current plugin if specified
		if ( ! empty( $current_plugin ) ) {
			if ( array_key_exists( $current_plugin, $plugins ) ) {
				unset( $plugins[ $current_plugin ] );
			}
		}

		$html_open  = '<tr id="try-other-plugins" valign="top"><th scope="row">Try our other top plugins!</th><td>';
		$html_close = '</td></tr>';
		$html_main  = '';

		foreach ( $plugins as $plugin => $data ) {
			$html_main .= '<table class="other-plugins-tbl">
        <tr><td><a class="plugin-image-link" href="' . $data['link'] . '" target="_blank"><img src="' . $data['thumb'] . '" title="Click for more details"></a></td></tr>
        <tr><td class="plugin-text-link"><div><h3><a style="color:#444;" href="' . $data['link'] . '" target="_blank">' . $data['label'] . '</a></h3></div></td></tr></table>';
		}

		return $html_open . $html_main . $html_close;

	}

	public function subscribe_to_newsletter( $newsletter_url ) {
		return '<tr valign="top">
      <th scope="row">Read all about it!</th>
      <td>
        <p>Subscribe to our newsletter for news and updates about the latest development work. Be the first to find out about future projects and exclusive promotions.</p>
        <div><a class="plugin-btn" target="_blank" href="' . $newsletter_url . '">Sign Me Up!</a></div>
      </td>
    </tr>';
	}

	public function keep_in_touch() {
		// use correct path for images
		$image_path = $this->module_roots['uri'];

		return '<tr valign="top">
      <th scope="row">Keep in touch...</th>
      <td>
        <div><p style="margin-bottom:10px;">Come and say hello. I\'d love to hear from you!</p>
          <span><a class="social-link" href="http://www.twitter.com/dgwyer" title="Follow me on Twitter" target="_blank"><img src="' . $image_path . '/api/assets/images/twitter.png" /></a></span>
          <span><a class="social-link" href="https://www.facebook.com/wpgoplugins/" title="Our Facebook page" target="_blank"><img src="' . $image_path . '/api/assets/images/facebook.png" /></a></span>
          <span><a class="social-link" href="https://www.youtube.com/channel/UCWzjTLWoyMgtIfpDgJavrTg" title="View our YouTube channel" target="_blank"><img src="' . $image_path . '/api/assets/images/yt.png" /></a></span>
          <span><a style="text-decoration:none;" title="Need help with ANY aspect of WordPress? We\'re here to help!" href="https://wpgoplugins.com/need-help-with-wordpress/" target="_blank"><span style="margin-left:-2px;color:#d41515;font-size:39px;line-height:32px;width:39px;height:39px;" class="dashicons dashicons-sos"></span></a></span>
        </div>
      </td>
    </tr>';
	}

	public function report_issues( $contact_form_url ) {
		return '<tr valign="top">
      <th scope="row">Report any issues</th>
      <td>
        <div style="margin-bottom:50px;"><p>Please <a href="' . $contact_form_url . '">report</a> any plugin issues, or suggest additional features. We read every single message. <span style="font-weight:bold;">All feedback is welcome!</span></p></div>
      </td>
    </tr>';
	}

} /* End class definition */
