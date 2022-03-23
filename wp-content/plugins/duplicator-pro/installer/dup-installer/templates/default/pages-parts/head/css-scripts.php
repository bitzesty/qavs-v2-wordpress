<?php

/**
 *
 * @package templates/default
 *
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<link rel='stylesheet' href='assets/normalize.css' type='text/css' media='all' />
<link rel='stylesheet' href='assets/font-awesome/css/all.min.css' type='text/css' media='all' />
<link rel='stylesheet' href='assets/fonts/dots/dots-font.css' type='text/css' media='all' />
<link rel='stylesheet' href='assets/js/password-strength/password.css' type='text/css' media='all' />
<?php
require(DUPX_INIT . '/assets/inc.libs.css.php');
require(DUPX_INIT . '/assets/inc.css.php');
?>
<script src="<?php echo DUPX_INIT_URL; ?>/assets/inc.libs.js?v=<?php echo DUPX_ArchiveConfig::getInstance()->version_dup; ?>"></script>
<?php
require(DUPX_INIT . '/assets/inc.js.php');
dupxTplRender('scripts/dupx-functions');
?>
<script type="text/javascript" src="assets/js/password-strength/password.js"></script>