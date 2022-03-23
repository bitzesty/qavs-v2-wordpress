<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/* Variables */
/* @var $errorMessage string */
?>
<div class="wrap">
    <h1>
        <?php _e("Install package error", 'duplicator-pro'); ?>
    </h1>
    <p>
        <?php DUP_PRO_U::esc_html_e("Error on package prepare"); ?><br>
        <?php
        DUP_PRO_U::esc_html_e('Message: ');
        echo esc_html($errorMessage);
        ?>
    </p>
</div>