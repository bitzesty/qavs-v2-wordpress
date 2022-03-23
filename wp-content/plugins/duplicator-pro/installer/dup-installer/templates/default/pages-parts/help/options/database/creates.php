<?php

defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<tr>
    <td class="col-opt">Create Queries</td>
    <td>
        Run all CREATE queries at once.  Should be checked when source database tables have foreign key relationships.  When you choosing this option there might be a chance of a timeout error. Uncheck this option to split CREATE queries in chunks.  Checked by default.
    </td>
</tr>