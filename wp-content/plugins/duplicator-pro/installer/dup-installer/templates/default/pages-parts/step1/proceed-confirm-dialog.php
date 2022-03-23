<?php

/**
 *
 * @package templates/default
 *
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Core\Params\PrmMng;

$paramsManager = PrmMng::getInstance();
?>
<div id="db-install-dialog-confirm" title="Install Confirmation" style="display:none">
    <p>
        <b>Run installer with these settings?</b>
    </p>
    <p>
        <b>Install type:</b> <?php echo DUPX_InstallerState::installTypeToString(); ?>
        <?php if (DUPX_InstallerState::isOverwriteSiteOnMultisite()) { ?>
            <br>
            <span class="maroon">
                <i class="fas fa-exclamation-triangle"></i> Subsite overwrite mode enabled
            </span>
        <?php } ?>
    </p>
    <b>Site settings:</b><br>
    <table class="margin-bottom-1 margin-left-1" >
        <tr>
            <td><b>New URL:</b></td>
            <td><i id="dlg-url-new"><?php echo DUPX_U::esc_html($paramsManager->getValue(PrmMng::PARAM_URL_NEW)); ?></i></td>
        </tr>
        <tr>
            <td><b>New Path:</b></td>
            <td><i id="dlg-path-new"><?php echo DUPX_U::esc_html($paramsManager->getValue(PrmMng::PARAM_PATH_NEW)); ?></i></td>
        </tr>
    </table> 

    <b>Database Settings:</b><br/>
    <table class="margin-bottom-1 margin-left-1" >
        <tr>
            <td><b>Server:</b></td>
            <td><i id="dlg-dbhost"><?php echo DUPX_U::esc_html($paramsManager->getValue(PrmMng::PARAM_DB_HOST)); ?></i></td>
        </tr>
        <tr>
            <td><b>Name:</b></td>
            <td><i id="dlg-dbname"><?php echo DUPX_U::esc_html($paramsManager->getValue(PrmMng::PARAM_DB_NAME)); ?></i></td>
        </tr>
        <tr>
            <td><b>User:</b></td>
            <td><i id="dlg-dbuser"><?php echo DUPX_U::esc_html($paramsManager->getValue(PrmMng::PARAM_DB_USER)); ?></i></td>
        </tr>
    </table>

    <small>
        <i class="fa fa-exclamation-triangle"></i> WARNING: Be sure these database parameters are correct! Entering the wrong information WILL overwrite an existing database.
        Make sure to have backups of all your data before proceeding.
    </small>
</div>