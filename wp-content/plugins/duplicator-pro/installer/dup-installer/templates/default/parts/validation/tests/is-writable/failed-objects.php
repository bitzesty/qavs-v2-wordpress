<?php

/**
 *
 * @package templates/default
 *
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

if (empty($failedObjects)) {
    return;
}
?>
<p>
    <b>Overwrite fails for these folders (change permissions or remove then restart):</b>
</p>
<div class="validation-iswritable-failes-objects">
    <pre><?php
    foreach ($failedObjects as $failedPath) {
        echo '- ' . DUPX_U::esc_html($failedPath) . "\n";
    }
    ?></pre>
</div>
