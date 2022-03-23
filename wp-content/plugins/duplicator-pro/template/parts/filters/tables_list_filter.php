<?php

/**
 * Duplicator package row in table packages list
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

defined("ABSPATH") or die("");

/**
 * Variables
 * @var \Duplicator\Core\Controllers\ControllersManager $ctrlMng
 * @var \Duplicator\Core\Views\TplMng  $tplMng
 * @var array $tplData
 */

$dbFilterOn = isset($tplData['dbFilterOn']) ? $tplData['dbFilterOn'] :  false;
$tSelected  = isset($tplData['tablesSlected']) ? $tplData['tablesSlected'] :  array();
$dbTableCount = 1;
?>

<div class="dup-form-item">
    <span class="title">  <?php DUP_PRO_U::esc_html_e("Table Filters") ?>:</span>
    <span class="input">
        <input type="checkbox" id="dbfilter-on" name="dbfilter-on" <?php checked($dbFilterOn); ?> onclick="DupPro.Pack.ToggleDBFilters()" />
        <label for="dbfilter-on"><?php DUP_PRO_U::esc_html_e("Enable Database Table Filters") ?>&nbsp;</label>
        <i class="fas fa-question-circle fa-sm"
        data-tooltip-title="<?php DUP_PRO_U::esc_attr_e("Table Filters"); ?>"
        data-tooltip="<?php DUP_PRO_U::esc_attr_e('Table filters allow you to ignore certain tables from a database.  When creating a package only include the data you '
        . 'want and need.  This helps to speed up the build process and keep your backups simple and clean.  Tables that are checked will be excluded.  Tables with an * in red '
        . 'are core WordPress tables and should typically not be excluded.'); ?>"> <br/>
        </i>
    </span>
</div>

<div id="dup-db-filter-items" >
    <div class="dup-db-filter-buttons" >
        <span id="dbnone" class="link-style dup-db-filter-none"><i class="far fa-minus-square fa-lg"  title="<?php DUP_PRO_U::esc_html_e('Uncheck All Checkboxes'); ?>"></i></span> &nbsp;
        <span id="dball" class="link-style dup-db-filter-all"><i class="far fa-check-square fa-lg" title="<?php DUP_PRO_U::esc_html_e('Check All Checkboxes'); ?>"></i> </span>
    </div>
    <div id="dup-db-tables-exclude" >
        <input type="hidden" id="dup-db-tables-lists" name="dbtables-list" value="" >
        <?php
        foreach (DUP_PRO_DB::getTablesList() as $table) {
            if (DUP_PRO_U::isWPCoreTable($table)) {
                $tableBlogId = DUP_PRO_U::getWPBlogIdTable($table);

                $core_css    = 'core-table';
                $core_note   = '*';

                if ($tableBlogId > 0) {
                    $core_css .= ' subcore-table-' . ($tableBlogId % 2);
                }
            } else {
                $core_css    = '';
                $core_note   = '';
            }
            $dbTableCount++;
            $checked = (in_array($table, $tSelected) ? ' checked ' : '');
            ?>
            <label class="<?php echo $core_css; ?>">
                <span class="dup-pseudo-checkbox <?php echo $checked;?>" data-value="<?php echo esc_attr($table); ?>"></span>
                &nbsp;<span><?php echo $table . $core_note; ?></span>
            </label>
        <?php } ?>
    </div>    
    <div class="dup-tabs-opts-help">
        <?php
            echo wp_kses(DUP_PRO_U::__("Checked tables will be <u>excluded</u> from the database script. "), array('u' => array()));
            DUP_PRO_U::esc_html_e("Excluding certain tables can cause your site or plugins to not work correctly after install!");
            echo '<br>';
            echo '<i class="core-table-info"> ';
            DUP_PRO_U::esc_html_e("Use caution when excluding tables! It is highly recommended to not exclude WordPress core tables in red with an *, unless you know the impact.");
            echo '</i>';
        ?>
    </div>
</div>

<div id="dup-db-filter-items-no-filters">
    <?php
    printf(
        "<br/><br/><br/><br/><br/> %s <br/> %s [%s] %s",
        DUP_PRO_U::esc_html__("- Table Filters Disabled -"),
        DUP_PRO_U::esc_html__("All "),
        $dbTableCount,
        DUP_PRO_U::esc_html__(" tables will be included in this build.")
    );
    ?>
</div>


<script>
jQuery(function($) 
{
    /* METHOD: Toggle Database table filter red icon */
    DupPro.Pack.ToggleDBFilters = function () 
    {
      
        var filterItems = $('#dup-db-filter-items');

        if ($("#dbfilter-on").is(':checked')) {
            filterItems.removeClass('disabled');
            $('#dup-archive-filter-db').show();
            $('#dup-db-filter-items-no-filters').hide();
        } else {
            filterItems.addClass('disabled');
            $('#dup-archive-filter-db').hide();
            $('#dup-db-filter-items-no-filters').show();
        }
    };

    DupPro.Pack.FillExcludeTablesList = function () {
        let values = $("#dup-db-tables-exclude .dup-pseudo-checkbox.checked")
            .map(function() {
                return this.getAttribute('data-value');
            })
            .get()
            .join();

        $('#dup-db-tables-lists').val(values);
    };
});

jQuery(document).ready(function($) 
{
    let tablesToExclude = $("#dup-db-tables-exclude");

    $('.dup-db-filter-none').click(function () {
        tablesToExclude.find(".dup-pseudo-checkbox.checked").removeClass("checked");
    });

    $('.dup-db-filter-all').click(function () {
        tablesToExclude.find(".dup-pseudo-checkbox:not(.checked)").addClass("checked");
    });

    DupPro.Pack.ToggleDBFilters();
});
</script>