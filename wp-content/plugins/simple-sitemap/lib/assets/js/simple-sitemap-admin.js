// eslint-disable-next-line no-undef
jQuery(document).ready(function ($) {
	// Reset plugin settings link
	$('#simple-sitemap-reset > a').on('click', function () {
		const res = window.confirm(
			'Are you sure? All plugin options will be reset to their default settings!'
		);
		if (res === true) {
			$('#simple-sitemap-reset-form').submit();
		}
	});

	// setup event listeners for expandable sections
	['blocks', 'shortcodes', 'attributes'].forEach(function (
		section
	) {
		const btn = $('#' + section + '-btn');
		const wrap = $('#' + section + '-wrap');

		btn.on('click', function () {
			const isHidden = wrap.is(':hidden');
			wrap.slideToggle(300, function () {
				if (isHidden) {
					btn.html(
						'Collapse <span style="vertical-align:sub;width:16px;height:16px;font-size:16px;" class="dashicons dashicons-arrow-up-alt2"></span>'
					);
				} else {
					btn.html(
						'Expand <span style="vertical-align:sub;width:16px;height:16px;font-size:16px;" class="dashicons dashicons-arrow-down-alt2"></span>'
					);
				}
			});
		});
	});
});
