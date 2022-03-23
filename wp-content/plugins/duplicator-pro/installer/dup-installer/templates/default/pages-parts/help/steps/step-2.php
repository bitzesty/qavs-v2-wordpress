<?php
defined('ABSPATH') || defined('DUPXABSPATH') || exit;
?>
<!-- ============================================
STEP 2
============================================== -->
<?php
$sectionId   = 'section-step-2';
$expandClass = $sectionId == $open_section ? 'open' : 'close';
?>
<section id="<?php echo $sectionId; ?>" class="expandable <?php echo $expandClass; ?>" >
    <h2 class="header expand-header">Step <span class="step">2</span> of 4: Install Database</h2>
    <div class="content" >
        <div id="dup-help-step1" class="help-page">            
            <!-- OPTIONS-->
            <h3>General</h3>
            <table class="help-opt">
                <?php
                dupxTplRender('pages-parts/help/widgets/option-heading');
                dupxTplRender('pages-parts/help/options/database/general-database-heading');
                dupxTplRender('pages-parts/help/options/database/charset');
                dupxTplRender('pages-parts/help/options/database/collation');
                dupxTplRender('pages-parts/help/options/database/spacing');
                dupxTplRender('pages-parts/help/options/database/mysql-mode');
                dupxTplRender('pages-parts/help/options/database/views');
                dupxTplRender('pages-parts/help/options/database/stored-procedures');
                dupxTplRender('pages-parts/help/options/database/creates');
                ?>
            </table>
            <br/><br/>

            <h3>Tables</h3>
                <p>
                In this tab, Origional table names and Install tables names with number of rows and size are displayed. For each table, There are two options:
                </p>
                <table class="help-opt">
                <?php
                dupxTplRender('pages-parts/help/widgets/option-heading');
                dupxTplRender('pages-parts/help/options/tables/extract');
                dupxTplRender('pages-parts/help/options/tables/replace');
                ?>
                </table>
        </div>
    </div>
</section>
