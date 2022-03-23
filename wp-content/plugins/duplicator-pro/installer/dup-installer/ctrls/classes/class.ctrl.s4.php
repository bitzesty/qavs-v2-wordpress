<?php

/**
 *
 * @package templates/default
 *
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Core\Params\PrmMng;

final class DUPX_Ctrl_S4
{

    const DISAPLY_NO_NOTICE_MESSAGE = false;

    public static function updateFinalReport()
    {
        self::finalReportGeneral();
        self::finalReportFiles();
        self::finalReportDatabase();
        self::finalReportSearchReplace();
        self::finalReportPlugins();

        DUPX_NOTICE_MANAGER::getInstance()->sortFinalReport();
    }

    public static function getNoticesCount()
    {
        $nManager = DUPX_NOTICE_MANAGER::getInstance();

        return array(
            'general'        => $nManager->countFinalReportNotices('general', DUPX_NOTICE_ITEM::NOTICE, '>='),
            'files'          => $nManager->countFinalReportNotices('files', DUPX_NOTICE_ITEM::NOTICE, '>='),
            'database'       => $nManager->countFinalReportNotices('database', DUPX_NOTICE_ITEM::NOTICE, '>'),
            'search_replace' => $nManager->countFinalReportNotices('search_replace', DUPX_NOTICE_ITEM::NOTICE, '>='),
            'plugins'        => $nManager->countFinalReportNotices('plugins', DUPX_NOTICE_ITEM::NOTICE, '>=')
        );
    }

    protected static function finalReportGeneral()
    {
        $nManager          = DUPX_NOTICE_MANAGER::getInstance();
        $numGeneralNotices = $nManager->countFinalReportNotices('general', DUPX_NOTICE_ITEM::NOTICE, '>=');
        if ($numGeneralNotices == 0) {
            if (self::DISAPLY_NO_NOTICE_MESSAGE) {
                $nManager->addFinalReportNotice(array(
                    'shortMsg' => 'No general notices',
                    'level'    => DUPX_NOTICE_ITEM::INFO,
                    'sections' => array('general'),
                    'priority' => 5
                ));
            }
        } else {
            $longMsg = <<<LONGMSG
The following is a list of notices that may need to be fixed in order to finalize your setup.
These values should only be investigated if you're running into issues with your site.
For more details see the <a href="https://codex.wordpress.org/Editing_wp-config.php" target="_blank">WordPress Codex</a>.
LONGMSG;

            $nManager->addFinalReportNotice(array(
                'shortMsg'    => 'Info',
                'level'       => DUPX_NOTICE_ITEM::INFO,
                'longMsg'     => $longMsg,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                'sections'    => array('general'),
                'priority'    => 5
            ));
        }
    }

    protected static function finalReportFiles()
    {
        $nManager = DUPX_NOTICE_MANAGER::getInstance();

        $numFilesNotices = $nManager->countFinalReportNotices('files', DUPX_NOTICE_ITEM::NOTICE, '>=');
        if (self::DISAPLY_NO_NOTICE_MESSAGE && $numFilesNotices == 0) {
            $nManager->addFinalReportNotice(array(
                'shortMsg' => 'No files extraction errors',
                'level'    => DUPX_NOTICE_ITEM::INFO,
                'longMsg'  => '',
                'sections' => 'files',
                'priority' => 5
            ));
        }
    }

    protected static function finalReportDatabase()
    {
        $paramsManager   = PrmMng::getInstance();
        $finalReportData = $paramsManager->getValue(PrmMng::PARAM_FINAL_REPORT_DATA);
        $nManager        = DUPX_NOTICE_MANAGER::getInstance();

        if ($finalReportData['extraction']['query_errs'] > 0) {
            $longMsg = 'Queries that error during the deploy step are logged to the ' . DUPX_View_Funcs::installerLogLink(false);
            $longMsg .= <<<LONGMSG
file and
and marked with an **ERROR** status.   If you experience a few errors (under 5), in many cases they can be ignored as long as your site is working correctly.
However if you see a large amount of errors or you experience an issue with your site then the error messages in the log file will need to be investigated.
<br/><br/>

<b>COMMON FIXES:</b>
<ul>
    <li>
        <b>Unknown collation:</b> See Online FAQ:
        <a href="https://snapcreek.com/duplicator/docs/faqs-tech/#faq-trouble-090-q" target="_blank">What is Compatibility mode & 'Unknown collation' errors?</a>
    </li>
    <li>
        <b>Query Limits:</b> Update MySQL server with the <a href="https://dev.mysql.com/doc/refman/5.5/en/packet-too-large.html" target="_blank">max_allowed_packet</a>
        setting for larger payloads.
    </li>
</ul>
LONGMSG;

            $nManager->addFinalReportNotice(array(
                'shortMsg'    => 'DB EXTRACTION - INSTALL NOTICES (' . $finalReportData['extraction']['query_errs'] . ')',
                'level'       => DUPX_NOTICE_ITEM::HARD_WARNING,
                'longMsg'     => $longMsg,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                'sections'    => array('database'),
                'priority'    => 5,
                'open'        => true
            ));
        }

        if ($finalReportData['replace']['errsql_sum'] > 0) {
            $longMsg = <<<LONGMSG
Update errors that show here are queries that could not be performed because the database server being used has issues running it.
Please validate the query, if it looks to be of concern please try to run the query manually.
In many cases if your site performs well without any issues you can ignore the error.
LONGMSG;

            $nManager->addFinalReportNotice(array(
                'shortMsg' => 'STEP 3 - UPDATE NOTICES (' . $finalReportData['replace']['errsql_sum'] . ')',
                'level'    => DUPX_NOTICE_ITEM::HARD_WARNING,
                'longMsg'  => $longMsg,
                'sections' => array('database'),
                'priority' => 5,
                'open'     => true
            ));
        }

        if ($finalReportData['replace']['errkey_sum'] > 0) {
            $longMsg = <<<LONGMSG
Notices should be ignored unless issues are found after you have tested an installed site.
This notice indicates that a primary key is required to run the update engine. Below is a list of tables and the rows that were not updated.
On some databases you can remove these notices by checking the box 'Enable Full Search' under options in step3 of the installer.
<br/><br/>
<small>
    <b>Advanced Searching:</b><br/>
    Use the following query to locate the table that was not updated: <br/>
    <i>SELECT @row := @row + 1 as row, t.* FROM some_table t, (SELECT @row := 0) r</i>
</small>
LONGMSG;

            $nManager->addFinalReportNotice(array(
                'shortMsg'    => 'TABLE KEY NOTICES  (' . $finalReportData['replace']['errkey_sum'] . ')',
                'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'     => $longMsg,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                'sections'    => array('database'),
                'priority'    => 5,
                'open'        => true
            ));
        }

        $numDbNotices = $nManager->countFinalReportNotices('database', DUPX_NOTICE_ITEM::NOTICE, '>');
        if ($numDbNotices == 0) {
            if (self::DISAPLY_NO_NOTICE_MESSAGE) {
                $nManager->addFinalReportNotice(array(
                    'shortMsg' => 'No errors in database',
                    'level'    => DUPX_NOTICE_ITEM::INFO,
                    'longMsg'  => '',
                    'sections' => 'database',
                    'priority' => 5
                    ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_UPDATE, 'query_err_counts');
            }
        } else if ($numDbNotices <= 10) {
            $nManager->addFinalReportNotice(array(
                'shortMsg' => 'Some errors in the database (' . $numDbNotices . ')',
                'level'    => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'  => '',
                'sections' => 'database',
                'priority' => 5
                ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_UPDATE, 'query_err_counts');
        } else if ($numDbNotices <= 100) {
            $nManager->addFinalReportNotice(array(
                'shortMsg' => 'Errors in the database (' . $numDbNotices . ')',
                'level'    => DUPX_NOTICE_ITEM::HARD_WARNING,
                'longMsg'  => '',
                'sections' => 'database',
                'priority' => 5
                ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_UPDATE, 'query_err_counts');
        } else {
            $nManager->addFinalReportNotice(array(
                'shortMsg' => 'Many errors in the database (' . $numDbNotices . ')',
                'level'    => DUPX_NOTICE_ITEM::CRITICAL,
                'longMsg'  => '',
                'sections' => 'database',
                'priority' => 5
                ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_UPDATE, 'query_err_counts');
        }
    }

    protected static function finalReportSearchReplace()
    {
        $paramsManager   = PrmMng::getInstance();
        $finalReportData = $paramsManager->getValue(PrmMng::PARAM_FINAL_REPORT_DATA);
        $nManager        = DUPX_NOTICE_MANAGER::getInstance();

        if ($finalReportData['replace']['errser_sum'] > 0) {
            $longMsg = <<<LONGMSG
Notices should be ignored unless issues are found after you have tested an installed site.
The SQL below will show data that may have not been updated during the serialization process.
Best practices for serialization notices is to just re-save the plugin/post/page in question.
LONGMSG;

            $nManager->addFinalReportNotice(array(
                'shortMsg' => 'SERIALIZATION NOTICES  (' . $finalReportData['replace']['errser_sum'] . ')',
                'level'    => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'  => $longMsg,
                'sections' => array('search_replace'),
                'priority' => 5,
                'open'     => true
            ));
        }

        $numSerNotices = $nManager->countFinalReportNotices('search_replace', DUPX_NOTICE_ITEM::NOTICE, '>=');
        if (self::DISAPLY_NO_NOTICE_MESSAGE && $numSerNotices == 0) {
            $nManager->addFinalReportNotice(array(
                'shortMsg' => 'No search and replace data errors',
                'level'    => DUPX_NOTICE_ITEM::INFO,
                'longMsg'  => '',
                'sections' => 'search_replace',
                'priority' => 5
            ));
        }
    }

    protected static function finalReportPlugins()
    {
        $nManager = DUPX_NOTICE_MANAGER::getInstance();

        $numPluginsNotices = $nManager->countFinalReportNotices('plugins', DUPX_NOTICE_ITEM::NOTICE, '>=');
        if (self::DISAPLY_NO_NOTICE_MESSAGE && $numPluginsNotices == 0) {
            $nManager->addFinalReportNotice(array(
                'shortMsg' => 'No plugins notices',
                'level'    => DUPX_NOTICE_ITEM::INFO,
                'longMsg'  => '',
                'sections' => 'plugins',
                'priority' => 5
            ));
        }
    }
}
