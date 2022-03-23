<?php

/**
 *
 * @package templates/default
 *
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<!DOCTYPE html>
<html>
    <?php dupxTplRender('pages-parts/boot-error/header'); ?>
    <body id="page-security-error" >
        <div>
            <h1>DUPLICATOR PRO: SECURITY ISSUE</h1>
            An invalid request was made.<br>
            Message: <b><?php echo htmlspecialchars($message); ?></b><br>
            <br>
            In order to protect this request from unauthorized access <b>please restart this install process.</b>
        </div>
    </body>
</html>