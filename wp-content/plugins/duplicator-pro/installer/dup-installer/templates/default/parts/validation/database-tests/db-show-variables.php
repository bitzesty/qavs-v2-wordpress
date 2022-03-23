<?php

/**
 *
 * @package templates/default
 *
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/* Variables */
/* @var bool $pass */
?>
<div class="sub-title">STATUS</div>
<?php if ($pass) : ?>
<p class="green">
    Successfully read database variables.
</p>
<?php else : ?>
<p class="maroon">
    Error reading database variables.
</p>
<?php endif; ?>
<div class="sub-title">DETAILS</div>
<p>
    Query executed: <i>SHOW VARIABLES like 'version'</i><br/><br/>
    The "SHOW VARIABLES" query is required to acquire necessary information about the database and safely execute the
    installation process. There's not single setting that will make this query work for all hosting providers. Please
    try contacting your hosting provider and asking them to help make the query work if it failed.
</p>