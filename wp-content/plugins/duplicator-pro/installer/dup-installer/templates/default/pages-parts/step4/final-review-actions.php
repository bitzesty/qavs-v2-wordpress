<?php

/**
 *
 * @package templates/default
 *
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Libs\Snap\SnapURL;

$paramsManager  = PrmMng::getInstance();
$nManager       = DUPX_NOTICE_MANAGER::getInstance();
?>
<ul class="final-review-actions" >
    <li>
        <a href="javascript:void(0)" onclick="$('#s4-install-report').toggle(400)">Review Migration Report</a>
    </li>
    <li>
        Review this site's <a href="<?php echo DUPX_U::esc_url($paramsManager->getValue(PrmMng::PARAM_URL_NEW)); ?>" target="_blank">front-end</a> or
        re-run the installer and <span class="link-style" data-go-step-one-url="<?php echo SnapURL::urlEncodeAll(DUPX_CSRF::getVal('installerOrigCall')); ?>" >go back to step 1</span>.
    </li>
    <li>
        <?php
        $wpconfigNotice = $nManager->getFinalReporNoticeById('wp-config-changes');
        $htaccessNorice = $nManager->getFinalReporNoticeById('htaccess-changes');
        ?>
        Please validate <?php echo $wpconfigNotice->longMsg; ?> and <?php echo $htaccessNorice->longMsg; ?>.</li>
    <li>
        For additional help and questions visit the <a href='http://snapcreek.com/support/docs/faqs/' target='_blank'>online FAQs</a>.
    </li>
</ul>
<?php if (DUPX_InstallerState::isClassicInstall()) { ?>
    <p class="final-review-drag-drop-advertisement">
        <b>Next time try "<a target='_blank' href='https://snapcreek.com/blog/how-migrate-wordpress-site-drag-drop-duplicator-pro'>Drag and Drop</a>" for a rapid install! </b>
    </p>
    <?php
}