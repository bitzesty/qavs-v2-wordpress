<?php

/**
 *
 * @package templates/default
 *
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

/* Variables */
/* @var $testResult int */
/* @var $dbuser string */
/* @var $dbname string */
/* @var $perms array */
/* @var $errorMessages string[] */

$statusClass = $testResult == DUPX_Validation_test_db_user_perms::LV_PASS ? 'green' : 'red';
?>
<div class="sub-title">STATUS</div>
<p class="<?php echo $statusClass; ?>">
    <?php
    switch ($testResult) {
        case DUPX_Validation_test_db_user_perms::LV_PASS:
            ?>
            The user <b>[<?php echo htmlentities($dbuser); ?>]</b> the correct privileges on the database <b>[<?php echo htmlentities($dbname); ?>]</b>.
            <?php
            break;
        case DUPX_Validation_test_db_user_perms::LV_FAIL:
            ?>        
            The user <b>[<?php echo htmlentities($dbuser); ?>]</b> is missing privileges on the database <b>[<?php echo htmlentities($dbname); ?>]</b>
            <?php
            break;
        case DUPX_Validation_test_db_user_perms::LV_HARD_WARNING:
            ?>        
            The user <b>[<?php echo htmlentities($dbuser); ?>]</b> is missing privileges on the database <b>[<?php echo htmlentities($dbname); ?>]</b><br>
            You can continue with the installation but some features may not be restored correctly.
            <?php
            break;
    }
    ?>
</p>
<?php if (!empty($errorMessages)) { ?>
    <p>
        Error detail: <br>
        <?php foreach ($errorMessages as $errorMessage) { ?>
            <span class="maroon" ><?php echo htmlentities($errorMessage); ?></span><br>
        <?php } ?>
    </p>
<?php } ?>

<div class="sub-title">DETAILS</div>
<p>
    This test checks the privileges a user has when working with tables.  Below is a list of all the privileges that the user can currently view.  In order
    to successfully use Duplicator Pro all of the privileges are required.
</p>

<div class="sub-title">TABLE PRIVILEDGES ON [<?php echo htmlentities($dbname); ?>]</div>
<ul class="tbl-list">
    <li>
        <b>Create:</b> <span class="status-badge <?php echo DUPX_Validation_abstract_item::resultLevelToBadgeClass($perms['create']); ?>">
    </li>
    <li>
        <b>Select:</b> <span class="status-badge <?php echo DUPX_Validation_abstract_item::resultLevelToBadgeClass($perms['select']); ?>">
    </li>
    <li>
        <b>Insert:</b> <span class="status-badge <?php echo DUPX_Validation_abstract_item::resultLevelToBadgeClass($perms['insert']); ?>"> 
    </li>
    <li>
        <b>Update:</b> <span class="status-badge <?php echo DUPX_Validation_abstract_item::resultLevelToBadgeClass($perms['update']); ?>">
    </li>
    <li>
        <b>Delete:</b> <span class="status-badge <?php echo DUPX_Validation_abstract_item::resultLevelToBadgeClass($perms['delete']); ?>">
    </li>
    <li>
        <b>Drop:</b> <span class="status-badge <?php echo DUPX_Validation_abstract_item::resultLevelToBadgeClass($perms['drop']); ?>">
    </li>
    <?php if ($perms['view'] < DUPX_Validation_abstract_item::LV_SKIP) { ?>
        <li>
            <b>Create Views:</b> <span class="status-badge <?php echo DUPX_Validation_abstract_item::resultLevelToBadgeClass($perms['view']); ?>">
        </li>
        <?php
    }
    if ($perms['proc'] < DUPX_Validation_abstract_item::LV_SKIP) {
        ?>
        <li>
            <b>Create & Alter Procedures:</b> <span class="status-badge <?php echo DUPX_Validation_abstract_item::resultLevelToBadgeClass($perms['proc']); ?>">
        </li>
    <?php }
    if ($perms['func'] < DUPX_Validation_abstract_item::LV_SKIP) {
        ?>
        <li>
            <b>Create & Alter Functions:</b> <span class="status-badge <?php echo DUPX_Validation_abstract_item::resultLevelToBadgeClass($perms['func']); ?>">
        </li>
    <?php }
    if ($perms['trigger'] < DUPX_Validation_abstract_item::LV_SKIP) { ?>
        <li>
            <b>Trigger:</b> <span class="status-badge <?php echo DUPX_Validation_abstract_item::resultLevelToBadgeClass($perms['trigger']); ?>">
        </li>
    <?php }?>
</ul>

<div class="sub-title">TROUBLESHOOT</div>
<ul>
    <li>Validate that the database user is correct per your hosts documentation</li>
    <li>
        Check to make sure the 'User' has been granted the correct privileges
        <ul class='vids'>
            <li><i class="fa fa-video-camera"></i>  <a href='https://www.youtube.com/watch?v=UU9WCC_-8aI' target='_video'>How to grant user privileges in cPanel</a></li>
            <li><i class="fa fa-video-camera"></i> <a href="https://www.youtube.com/watch?v=FfX-B-h3vo0" target="_video">How to grant user privileges in phpMyAdmin</a></li>
        </ul>
    </li>
    <li><i class="far fa-file-code"></i> <a href='<?php echo DUPX_U::esc_attr(DUPX_Constants::FAQ_URL); ?>#faq-installer-100-q' target='_help'>I'm running into issues with the Database what can I do?</a></li>
</ul>