<?php

/**
 * controller step 2 db install test
 *
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package CTRL
 *
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Utils\Log\Log;
use Duplicator\Installer\Core\Params\Descriptors\ParamDescUsers;
use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Libs\Snap\SnapJson;

require_once(DUPX_INIT . '/api/class.cpnl.ctrl.php');

class DUPX_DBInstall
{
    const ENGINE_NORMAL                     = 'normal';
    const ENGINE_CHUNK                      = 'chunk';
    const DBACTION_CREATE                   = 'create';
    const DBACTION_EMPTY                    = 'empty';
    const DBACTION_REMOVE_ONLY_TABLES       = 'removetables';
    const DBACTION_RENAME                   = 'rename';
    const DBACTION_MANUAL                   = 'manual';
    const DBACTION_ONLY_CONNECT             = 'onlyconnect';
    const TEMP_DB_PREFIX                    = 'dpro___tmp__';
    const TABLE_CREATION_END_MARKER         = "/* DUPLICATOR PRO TABLE CREATION END */\n";
    const QUERY_ERROR_LOG_LEN               = 200;
    const USER_DEFINER_REPLACE_PATTERN      = "/^(\s*(?:\/\*!\d+\s)?\s*(?:CREATE.+)?DEFINER\s*=)([^\*\s]+)(.*)$/m";
    const USER_DEFINER_REMOVE_PATTERN       = "/^(\s*(?:\/\*!\d+\s)?\s*(?:CREATE.+)?)(DEFINER\s*=\s*\S+)(.*)$/m";
    const USER_DEFINER_REMOVE_REPLACE       = '$1 $3';
    const SQL_SECURITY_INVOKER_PATTERN      = "/^(\s*CREATE.+(?:PROCEDURE|FUNCTION)[\s\S]*)(BEGIN)([\s\S]*)$/";
    const SQL_SECURITY_INVOKER_REPLACE      = "$1SQL SECURITY INVOKER\n$2$3";
    const SQL_CREATE_VIEW_PROC_FUNC_PATTERN = "/^\s*(?:\/\*!\d+\s)?\s*CREATE\s.*?(?:VIEW|PROCEDURE|FUNCTION).*$/ms";
    const BUILD_MODE_MYSQLDUMP              = 'MYSQLDUMP';
    const SKIP_TABLES_REGEX_CHUNK_SIZE      = 100;

    /**
     * @var mysqli|obj
     */
    private $dbh                 = null;
    public $post;
    public $dbaction             = self::DBACTION_EMPTY;
    public $sql_result_data;
    public $sql_result_data_length;
    public $dbvar_maxtime;
    public $dbvar_maxpacks;
    public $dbvar_sqlmode;
    public $dbvar_version;
    public $pos_in_sql;
    public $sql_file_path;
    public $table_count;
    public $table_rows;
    public $query_errs;
    public $drop_tbl_log;
    public $rename_tbl_log;
    public $dbquery_errs;
    public $dbquery_rows;
    public $dbtable_count;
    public $dbtable_rows;
    public $dbdelete_count;
    public $profile_start;
    public $profile_end;
    public $start_microtime;
    public $dbobj_views;
    public $dbobj_procs;
    public $dbobj_funcs;
    public $dbsplit_creates;
    public $dbchunk;
    public $supportedCollateList;
    public $supportedCharSetList;
    public $legacyCharsetRegexes = false;
    public $dbFileSize           = 0;
    public $setQueries           = array();

    /**
     *
     * @var DUPX_DBInstall
     */
    protected static $instance = null;

    /**
     *
     * @return self
     */
    public static function getInstance()
    {
        if (is_null(self::$instance)) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    private function __construct()
    {
        if (!DUPX_Validation_manager::isValidated()) {
            throw new Exception('Installer isn\'t validated');
        }
        $this->initData();
    }

    /**
     * inizialize extraction data
     */
    protected function initData()
    {
        // if data file exists load saved data
        if (file_exists(self::dbinstallDataFilePath())) {
            Log::info('LOAD DBINSTALL DATA FROM JSON', Log::LV_DETAILED);
            if ($this->loadData() == false) {
                throw new Exception('Can\'t load dbinstall data');
            }
        } else {
            Log::info('INIT DB INSTALL DATA', Log::LV_DETAILED);
            $this->constructData();
            $this->initLogDbInstall();
            $this->saveData();
        }
    }

    protected function constructData()
    {
        $paramsManager         = PrmMng::getInstance();
        $this->start_microtime = DUPX_U::getMicrotime();
        $this->sql_file_path   = DUPX_Package::getSqlFilePath();
        $this->dbFileSize      = DUPX_Package::getSqlFileSize();
        $this->dbvar_maxtime   = 300;
        $this->dbvar_maxpacks  = MB_IN_BYTES;
        $this->dbvar_sqlmode   = 'NOT_SET';
        $this->profile_start   = DUPX_U::getMicrotime();
        $this->dbquery_errs    = 0;
        $this->drop_tbl_log    = 0;
        $this->rename_tbl_log  = 0;
        $this->dbquery_rows    = 0;
        $this->dbdelete_count  = 0;

        $this->post = array(
            'view_mode'         => $paramsManager->getValue(PrmMng::PARAM_DB_VIEW_MODE),
            'dbaction'          => $paramsManager->getValue(PrmMng::PARAM_DB_ACTION),
            'dbhost'            => $paramsManager->getValue(PrmMng::PARAM_DB_HOST),
            'dbname'            => $paramsManager->getValue(PrmMng::PARAM_DB_NAME),
            'dbuser'            => $paramsManager->getValue(PrmMng::PARAM_DB_USER),
            'dbpass'            => $paramsManager->getValue(PrmMng::PARAM_DB_PASS),
            'dbport'            => parse_url($paramsManager->getValue(PrmMng::PARAM_DB_HOST), PHP_URL_PORT),
            'dbchunk'           => $paramsManager->getValue(PrmMng::PARAM_DB_CHUNK),
            'dbnbsp'            => $paramsManager->getValue(PrmMng::PARAM_DB_SPACING),
            'dbmysqlmode'       => $paramsManager->getValue(PrmMng::PARAM_DB_MYSQL_MODE),
            'dbmysqlmode_opts'  => $paramsManager->getValue(PrmMng::PARAM_DB_MYSQL_MODE_OPTS),
            'dbobj_views'       => $paramsManager->getValue(PrmMng::PARAM_DB_VIEW_CREATION),
            'dbobj_procs'       => $paramsManager->getValue(PrmMng::PARAM_DB_PROC_CREATION),
            'dbobj_funcs'       => $paramsManager->getValue(PrmMng::PARAM_DB_FUNC_CREATION),
            'dbsplit_creates'   => $paramsManager->getValue(PrmMng::PARAM_DB_SPLIT_CREATES),
            'dbcharset'         => $paramsManager->getValue(PrmMng::PARAM_DB_CHARSET),
            'dbcollate'         => $paramsManager->getValue(PrmMng::PARAM_DB_COLLATE),
            'cpnl-host'         => $paramsManager->getValue(PrmMng::PARAM_CPNL_HOST),
            'cpnl-user'         => $paramsManager->getValue(PrmMng::PARAM_CPNL_USER),
            'cpnl-pass'         => $paramsManager->getValue(PrmMng::PARAM_CPNL_PASS),
            'cpnl-dbuser-chk'   => $paramsManager->getValue(PrmMng::PARAM_CPNL_DB_USER_CHK),
            'pos'               => 0,
            'pass'              => false,
            'first_chunk'       => true,
            'dbchunk_retry'     => 0,
            'continue_chunking' => $paramsManager->getValue(PrmMng::PARAM_DB_CHUNK),
            'progress'          => 0,
            'delimiter'         => ';',
            'is_error'          => 0,
            'error_msg'         => ''
        );

        $this->dbaction         = $paramsManager->getValue(PrmMng::PARAM_DB_ACTION);
        $this->dbcharset        = $paramsManager->getValue(PrmMng::PARAM_DB_CHARSET);
        $this->dbcollate        = $paramsManager->getValue(PrmMng::PARAM_DB_COLLATE);
        $this->dbobj_views      = $paramsManager->getValue(PrmMng::PARAM_DB_VIEW_CREATION);
        $this->dbobj_procs      = $paramsManager->getValue(PrmMng::PARAM_DB_PROC_CREATION);
        $this->dbobj_funcs      = $paramsManager->getValue(PrmMng::PARAM_DB_FUNC_CREATION);
        $this->dbsplit_creates  = $paramsManager->getValue(PrmMng::PARAM_DB_SPLIT_CREATES);
        $this->dbchunk          = $paramsManager->getValue(PrmMng::PARAM_DB_CHUNK);
    }

    protected function initLogDbInstall()
    {
        $paramsManager = PrmMng::getInstance();
        $labelPadSize  = 20;
        Log::info("\n\n\n********************************************************************************");
        Log::info('* DUPLICATOR PRO INSTALL-LOG');
        Log::info('* STEP-2 START @ ' . @date('h:i:s'));
        Log::info('* NOTICE: Do NOT post to public sites or forums!!');
        Log::info("********************************************************************************");
        Log::info("USER INPUTS");
        Log::info(str_pad('DB ENGINE', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_ENGINE)));
        Log::info(str_pad('VIEW MODE', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_VIEW_MODE)));
        Log::info(str_pad('DB ACTION', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_ACTION)));
        Log::info(str_pad('DB HOST', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str('**OBSCURED**'));
        Log::info(str_pad('DB NAME', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str('**OBSCURED**'));
        Log::info(str_pad('DB PASS', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str('**OBSCURED**'));
        Log::info(str_pad('DB PORT', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str('**OBSCURED**'));
        Log::info(str_pad('KEEP USERS', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_KEEP_TARGET_SITE_USERS)));
        Log::info(str_pad('CONTENT OWNER', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_CONTENT_OWNER)));
        Log::info(str_pad('TABLE PREFIX', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_TABLE_PREFIX)));
        Log::info(str_pad('MYSQL MODE', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_MYSQL_MODE)));
        Log::info(str_pad('MYSQL MODE OPTS', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_MYSQL_MODE_OPTS)));
        Log::info(str_pad('NON-BREAKING SPACES', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_SPACING)));
        Log::info(str_pad('CHARSET', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_CHARSET)));
        Log::info(str_pad('COLLATE', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_COLLATE)));
        Log::info(str_pad('CUNKING', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_CHUNK)));
        Log::info(str_pad('VIEW CREATION', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_VIEW_CREATION)));
        Log::info(str_pad('STORED PROCEDURE', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_PROC_CREATION)));
        Log::info(str_pad('FUNCTIONS', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_FUNC_CREATION)));
        Log::info(str_pad('REMOVE DEFINER', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_REMOVE_DEFINER)));
        Log::info(str_pad('SPLIT CREATES', $labelPadSize, '_', STR_PAD_RIGHT) . ': ' . Log::v2str($paramsManager->getValue(PrmMng::PARAM_DB_SPLIT_CREATES)));

        $tables = DUPX_DB_Tables::getInstance()->getTables();
        Log::info("--------------------------------------");
        Log::info('TABLES');
        Log::info("--------------------------------------");
        foreach ($tables as $tablesObj) {
            Log::info('TABLE ' . str_pad(Log::v2str($tablesObj->getOriginalName()), 50, '_', STR_PAD_RIGHT)
                . '[ROWS:' . str_pad($tablesObj->getRows(), 8, " ", STR_PAD_LEFT) . ']'
                . ' [' . ($tablesObj->extract() ? 'EXTRACT' : 'NO EXTR') . '|' . ($tablesObj->replaceEngine() ? 'REPLACE' : 'NO REPL') . '] '
                . '[INST NAME: ' . $tablesObj->getNewName() . ']');
        }
        Log::info("********************************************************************************\n");
        Log::flush();
    }

    public function deploy()
    {
        $paramsManager = PrmMng::getInstance();
        $nManager      = DUPX_NOTICE_MANAGER::getInstance();
        Log::setThrowExceptionOnError(true);
        if ($this->firstOrNotChunking()) {
            if ($this->post['dbchunk_retry']) {
                if ($this->post['dbchunk_retry'] > 0) {
                    Log::info("## >> Last DB Chunk installation was failed, so retrying from start point. Retrying count: " . $this->post['dbchunk_retry']);
                }
            }

            $this->prepareCpanel();
            $this->prepareDB();

            //Fatal Memory errors from file_get_contents is not catchable.
            //Try to warn ahead of time with a check on buffer in memory difference
            $current_php_mem = DUPX_U::returnBytes($GLOBALS['PHP_MEMORY_LIMIT']);
            $current_php_mem = is_numeric($current_php_mem) ? $current_php_mem : null;

            if ($current_php_mem != null && $this->dbFileSize > $current_php_mem) {
                $readable_size = DUPX_U::readableByteSize($this->dbFileSize);
                $msg           = "\nWARNING: The database script is '{$readable_size}' in size.  The PHP memory allocation is set\n";
                $msg           .= "at '{$GLOBALS['PHP_MEMORY_LIMIT']}'.  There is a high possibility that the installer script will fail with\n";
                $msg           .= "a memory allocation error when trying to load the database.sql file.  It is\n";
                $msg           .= "recommended to increase the 'memory_limit' setting in the php.ini config file.\n";
                $msg           .= "see: " . DUPX_Constants::FAQ_URL . "#faq-trouble-056-q \n";
                Log::info($msg);
                unset($msg);
            }

            Log::info("--------------------------------------");
            Log::info("DATABASE RESULTS");
            Log::info("--------------------------------------");
        }

        switch ($paramsManager->getValue(PrmMng::PARAM_DB_ACTION)) {
            case self::DBACTION_MANUAL:
                Log::info("\n** SQL EXECUTION IS IN MANUAL MODE **");
                Log::info("- No SQL script has been executed -");
                $this->post['pass']              = 1;
                $this->post['continue_chunking'] = false;
                break;
            case DUPX_DBInstall::DBACTION_ONLY_CONNECT:
            case DUPX_DBInstall::DBACTION_CREATE:
            case DUPX_DBInstall::DBACTION_EMPTY:
            case DUPX_DBInstall::DBACTION_REMOVE_ONLY_TABLES:
            case DUPX_DBInstall::DBACTION_RENAME:
                $this->insertDatabase();

                if (!$this->post['continue_chunking']) {
                    $this->afterInstallDatabaseActions();
                }
                break;
            default:
                throw new Exception('Invalid db action');
        }
        $this->post['first_chunk'] = false;

        $this->saveData();
        $nManager->saveNotices();

        return $this->getResultData();
    }

    /**
     *
     * @throws Exception
     */
    protected function insertDatabase()
    {
        $paramsManager = PrmMng::getInstance();
        if ($paramsManager->getValue(PrmMng::PARAM_DB_CHUNK)) {
            if ($this->post['continue_chunking'] == true) {
                if ($this->writeInChunks() == false) {
                    throw new Exception('Error on db extraction');
                }
            } else {
                if ($this->post['pass'] == 1) {
                    $rowCountMisMatchTables = $this->getRowCountMisMatchTables();
                    if (!empty($rowCountMisMatchTables)) {
                        $nManager = DUPX_NOTICE_MANAGER::getInstance();
                        $errMsg   = 'Database Table row count verification was failed for table(s): ' . implode(', ', $rowCountMisMatchTables) . '.';
                        Log::info($errMsg);
                        $nManager->addNextStepNoticeMessage($errMsg, DUPX_NOTICE_ITEM::NOTICE);
                        /*
                          $nManager->addFinalReportNotice(array(
                          'shortMsg' => 'Database Table row count validation failed',
                          'level' => DUPX_NOTICE_ITEM::NOTICE,
                          'longMsg' => $errMsg,
                          'sections' => 'database'
                          ));
                         */
                    }
                } else {
                    throw new Exception('Error on db extraction');
                }
            }
        } else {
            $this->writeInDB();
            $rowCountMisMatchTables = $this->getRowCountMisMatchTables();
            $this->post['pass']     = 1;
            if (!empty($rowCountMisMatchTables)) {
                $nManager = DUPX_NOTICE_MANAGER::getInstance();
                $errMsg   = 'Database Table row count verification was failed for table(s): '
                    . implode(', ', $rowCountMisMatchTables) . '.';
                Log::info($errMsg);
                $nManager->addNextStepNoticeMessage($errMsg, DUPX_NOTICE_ITEM::NOTICE);
                /*
                  $nManager->addFinalReportNotice(array(
                  'shortMsg' => 'Database Table row count was validation failed',
                  'level' => DUPX_NOTICE_ITEM::NOTICE,
                  'longMsg' => $errMsg,
                  'sections' => 'database'
                  ));
                 */
            }
        }
    }

    protected function afterInstallDatabaseActions()
    {

        $this->moveUserTableOnCurrent();
        $this->runCleanupRoutines();

        $this->profile_end = DUPX_U::getMicrotime();
        $this->writeLog();

        //FINAL RESULTS
        $ajax1_sum = DUPX_U::elapsedTime($this->profile_end, $this->start_microtime);
        Log::info("\nINSERT DATA RUNTIME: " . DUPX_U::elapsedTime($this->profile_end, $this->profile_start));
        Log::info('STEP-2 COMPLETE @ ' . @date('h:i:s') . " - RUNTIME: {$ajax1_sum}");
        self::resetData();
    }

    protected function moveUserTableOnCurrent()
    {
        $paramsManager = PrmMng::getInstance();

        if (ParamDescUsers::getKeepUserId() == 0) {
            return;
        }

        Log::info("\nKEEP TARGET SITE USER ENABLED");

        $dbFunc        = DUPX_DB_Functions::getInstance();
        $overwriteData = $paramsManager->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);

        if ($overwriteData['table_prefix'] != $paramsManager->getValue(PrmMng::PARAM_DB_TABLE_PREFIX)) {
            $targetUserTable          = DUPX_DB_Functions::getUserTableName($overwriteData['table_prefix']);
            $targetUserMetaTable      = DUPX_DB_Functions::getUserMetaTableName($overwriteData['table_prefix']);
            $currentUserTableName     = DUPX_DB_Functions::getUserTableName();
            $currentUserMetaTableName = DUPX_DB_Functions::getUserMetaTableName();

            Log::info('RENAME USERS TABLE FROM ' . $targetUserTable . ' TO ' . $currentUserTableName);
            $dbFunc->renameTable($targetUserTable, $currentUserTableName, true);
            Log::info('RENAME USERS META TABLE FROM ' . $targetUserMetaTable . ' TO ' . $currentUserMetaTableName);
            $dbFunc->renameTable($targetUserMetaTable, $currentUserMetaTableName, true);

            DUPX_UpdateEngine::updateTablePrefix(
                $dbFunc->dbConnection(),
                $currentUserMetaTableName,
                'meta_key',
                $overwriteData['table_prefix'],
                $paramsManager->getValue(PrmMng::PARAM_DB_TABLE_PREFIX)
            );
        } else {
            Log::info('TABLE NAMES ARE THE SAME, SO SKIP USERS TABLES RENAME');
        }

        $this->updatPostsTableAuthorOnKeepUser();

        Log::info("SITE USERS UPDATED\n");
    }

    protected function updatPostsTableAuthorOnKeepUser()
    {
        if (($keepUser = ParamDescUsers::getKeepUserId()) == 0) {
            return;
        }

        Log::info('UPDATE POSTS AUTHOR ID ' . $keepUser);

        $prefix = DUPX_InstallerState::isAddSiteOnMultisite() ? DUPX_MU::getSubsiteOverwriteTablePrefix() : null;

        if (DUPX_DB_Functions::getInstance()->updatePostsAuthor($keepUser, $prefix) == false) {
            DUPX_NOTICE_MANAGER::getInstance()->addBothNextAndFinalReportNotice(array(
                'shortMsg' => 'Cant update posts author ',
                'level'    => DUPX_NOTICE_ITEM::CRITICAL,
                'sections' => 'database'
            ));
        }
    }

    protected function prepareCpanel()
    {
        if ($this->dbaction === self::DBACTION_MANUAL) {
            return;
        }

        if ($this->post['view_mode'] != 'cpnl') {
            return;
        }

        try {
            //===============================================
            //CPANEL LOGIC: From Postback
            //===============================================

            $cpnllog = "";
            $cpnllog .= "--------------------------------------\n";
            $cpnllog .= "CPANEL API\n";
            $cpnllog .= "--------------------------------------\n";

            $CPNL = new DUPX_cPanel_Controller();

            $cpnlToken = $CPNL->create_token($this->post['cpnl-host'], $this->post['cpnl-user'], $this->post['cpnl-pass']);
            $cpnlHost  = $CPNL->connect($cpnlToken);

            //CREATE DB USER: Attempt to create user should happen first in the case that the
            //user passwords requirements are not met.
            if ($this->post['cpnl-dbuser-chk']) {
                $result = $CPNL->create_db_user($cpnlToken, $this->post['dbuser'], $this->post['dbpass']);
                if ($result['status'] !== true) {
                    Log::info('CPANEL API ERROR: create_db_user ' . print_r($result['cpnl_api'], true), 2);
                    Log::error(sprintf(ERR_CPNL_API, $result['status']));
                } else {
                    $cpnllog .= "- A new database user was created\n";
                }
            }

            //CREATE NEW DB
            if ($this->post['dbaction'] == self::DBACTION_CREATE) {
                $result = $CPNL->create_db($cpnlToken, $this->post['dbname']);
                if ($result['status'] !== true) {
                    Log::info('CPANEL API ERROR: create_db ' . print_r($result['cpnl_api'], true), 2);
                    Log::error(sprintf(ERR_CPNL_API, $result['status']));
                } else {
                    $cpnllog .= "- A new database was created\n";
                }
            } else {
                $cpnllog .= "- Used to connect to existing database named [" . $this->post['dbname'] . "]\n";
            }

            //ASSIGN USER TO DB IF NOT ASSIGNED
            $result = $CPNL->is_user_in_db($cpnlToken, $this->post['dbname'], $this->post['dbuser']);
            if (!$result['status']) {
                $result = $CPNL->assign_db_user($cpnlToken, $this->post['dbname'], $this->post['dbuser']);
                if ($result['status'] !== true) {
                    Log::info('CPANEL API ERROR: assign_db_user ' . print_r($result['cpnl_api'], true), 2);
                    Log::error(sprintf(ERR_CPNL_API, $result['status']));
                } else {
                    $cpnllog .= "- Database user was assigned to database";
                }
            }

            Log::info($cpnllog);
        } catch (Exception $ex) {
            Log::error($ex);
        }
    }

    /**
     *
     * @return string
     */
    protected static function dbinstallDataFilePath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = DUPX_INIT . '/dup-installer-dbinstall__' . DUPX_Package::getPackageHash() . '.json';
        }
        return $path;
    }

    /**
     *
     * @staticvar string $path
     * @return string
     */
    protected static function seekTellFilePath()
    {
        static $path = null;
        if (is_null($path)) {
            $path = DUPX_INIT . "/dup-database-seek-tell-log__" . DUPX_ArchiveConfig::getInstance()->package_hash . ".txt";
        }
        return $path;
    }

    /**
     *
     * @return boolean
     */
    protected function saveData()
    {
        if (($json = SnapJson::jsonEncodePPrint($this)) === false) {
            throw new Exception('Can\'t encode json data');
        }

        if (file_put_contents(self::dbinstallDataFilePath(), $json) === false) {
            throw new Exception('Can\'t save dbinstall data file');
        }

        return true;
    }

    /**
     *
     * @return boolean
     */
    protected function loadData()
    {
        if (!file_exists(self::dbinstallDataFilePath())) {
            return false;
        }

        if (($json = file_get_contents(self::dbinstallDataFilePath())) === false) {
            throw new Exception('Can\'t load dbinstall data file');
        }

        if (($data = json_decode($json, true)) === null) {
            throw new Exception('Can\'t decode json progress data content: ' . Log::v2str($json));
        }

        foreach ($data as $key => $value) {
            $this->{$key} = $value;
        }

        return true;
    }

    /**
     *
     * @return boolean
     */
    public static function resetData()
    {
        $result = true;
        if (file_exists(self::dbinstallDataFilePath())) {
            if (unlink(self::dbinstallDataFilePath()) === false) {
                throw new Exception('Can\'t delete dbinstall data file');
            }
        }
        if (file_exists(self::seekTellFilePath())) {
            if (unlink(self::seekTellFilePath()) === false) {
                throw new Exception('Can\'t delete dbinstall chunk seek data file');
            }
        }
        return $result;
    }

    /**
     * execute a connection if db isn't connected
     *
     * @return resource
     */
    protected function dbConnect($reconnect = false)
    {
        if ($reconnect) {
            $this->dbClose();
        }

        $paramsManager = PrmMng::getInstance();

        if (is_null($this->dbh)) {
            switch ($this->post['dbaction']) {
                case self::DBACTION_EMPTY:
                case self::DBACTION_REMOVE_ONLY_TABLES:
                case self::DBACTION_RENAME:
                case self::DBACTION_ONLY_CONNECT:
                    //ESTABLISH CONNECTION
                    if (($this->dbh = DUPX_DB_Functions::getInstance()->dbConnection()) == false) {
                        $this->dbh = null;
                        Log::error(ERR_DBCONNECT . mysqli_connect_error());
                    }

                    // EXEC ALWAYS A DB SELECT is required when chunking is activated
                    if (DUPX_DB::selectDB($this->dbh, $paramsManager->getValue(PrmMng::PARAM_DB_NAME)) == false) {
                        Log::error(sprintf(ERR_DBCREATE, $this->post['dbname']));
                    }
                    break;
                case self::DBACTION_CREATE:
                    //ESTABLISH CONNECTION WITHOUT DATABASE NAME
                    $connParams = array(
                        'dbhost' => $paramsManager->getValue(PrmMng::PARAM_DB_HOST),
                        'dbname' => null,
                        'dbuser' => $paramsManager->getValue(PrmMng::PARAM_DB_USER),
                        'dbpass' => $paramsManager->getValue(PrmMng::PARAM_DB_PASS)
                    );

                    if (($this->dbh = DUPX_DB_Functions::getInstance()->dbConnection($connParams)) == false) {
                        $this->dbh = null;
                        Log::error(ERR_DBCONNECT . mysqli_connect_error());
                    }

                    // don't check for success because in the create new database option the database may not exist.
                    DUPX_DB::selectDB($this->dbh, $paramsManager->getValue(PrmMng::PARAM_DB_NAME));
                    break;
                case self::DBACTION_MANUAL:
                    Log::info('DB ACTION MANUAL');
                    break;
                default:
                    Log::error('Invalid dbaction: ' . Log::v2str($this->post['dbaction']));
                    break;
            }


            DUPX_DB::mysqli_query($this->dbh, "SET wait_timeout = " . mysqli_real_escape_string($this->dbh, $GLOBALS['DB_MAX_TIME']));
            DUPX_DB::mysqli_query($this->dbh, "SET GLOBAL max_allowed_packet = " . mysqli_real_escape_string($this->dbh, $GLOBALS['DB_MAX_PACKETS']), Log::LV_DEBUG);
            DUPX_DB::mysqli_query($this->dbh, "SET max_allowed_packet = " . mysqli_real_escape_string($this->dbh, $GLOBALS['DB_MAX_PACKETS']), Log::LV_DEBUG);

            $this->dbvar_maxtime  = DUPX_DB::getVariable($this->dbh, 'wait_timeout');
            $this->dbvar_maxpacks = DUPX_DB::getVariable($this->dbh, 'max_allowed_packet');
            $this->dbvar_sqlmode  = DUPX_DB::getVariable($this->dbh, 'sql_mode');
            $this->dbvar_version  = DUPX_DB::getVersion($this->dbh);

            $this->supportedCollateList = DUPX_DB::getSupportedCollateList($this->dbh);
            $this->supportedCharSetList = DUPX_DB::getSupportedCharSetList($this->dbh);

            $this->legacyCharsetRegexes = DUPX_DB_Functions::getInstance()->getLegacyCharsetAndCollationRegexes();
        }
        return $this->dbh;
    }

    protected function dbClose()
    {
        if (!is_null($this->dbh)) {
            mysqli_close($this->dbh);
            $this->dbh = null;
        }
    }

    protected function pingAndReconnect()
    {
        if (!mysqli_ping($this->dbh)) {
            $this->dbConnect(true);
        }
    }

    protected function prepareDB()
    {
        if ($this->dbaction === self::DBACTION_MANUAL) {
            return;
        }

        $this->dbConnect();
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        DUPX_DB::setCharset($this->dbh, $this->dbcharset, $this->dbcollate);
        $this->setSQLSessionMode();

        //Set defaults incase the variable could not be read
        $this->drop_tbl_log   = 0;
        $this->rename_tbl_log = 0;
        $sql_file_size1       = DUPX_U::readableByteSize(DUPX_Package::getSqlFileSize());

        Log::info("--------------------------------------");
        Log::info('DATABASE-ENVIRONMENT');
        Log::info("--------------------------------------");
        Log::info("MYSQL VERSION:\tThis Server: {$this->dbvar_version} -- Build Server: {$archiveConfig->version_db}");
        Log::info("FILE SIZE:\t" . basename(DUPX_Package::getSqlFilePath()) . " ({$sql_file_size1})");
        Log::info("TIMEOUT:\t{$this->dbvar_maxtime}");
        Log::info("MAXPACK:\t{$this->dbvar_maxpacks}");
        Log::info("SQLMODE-GLOBAL:\t{$this->dbvar_sqlmode}");
        Log::info("SQLMODE-SESSION:" . ($this->getSQLSessionMode()));

        if (version_compare($this->dbvar_version, $archiveConfig->version_db) < 0) {
            Log::info("\nNOTICE: This servers version [{$this->dbvar_version}] is less than the build version [{$archiveConfig->version_db}].  \n"
                . "If you find issues after testing your site please referr to this FAQ item.\n"
                . "https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-260-q");
        }

        switch ($this->dbaction) {
            case self::DBACTION_CREATE:
                $this->dbActionCreate();
                break;
            case self::DBACTION_EMPTY:
                $this->dbActionEmpty();
                break;
            case self::DBACTION_REMOVE_ONLY_TABLES:
                $this->dbActionRemoveOnlyTables();
                break;
            case self::DBACTION_RENAME:
                $this->dbActionRename();
                break;
            case self::DBACTION_MANUAL:
            case self::DBACTION_ONLY_CONNECT:
                break;
            default:
                Log::error('DB ACTION INVALID');
                break;
        }
    }

    protected function dbActionCreate()
    {
        if ($this->post['view_mode'] == 'basic') {
            DUPX_DB::mysqli_query($this->dbh, "CREATE DATABASE IF NOT EXISTS `" . mysqli_real_escape_string($this->dbh, $this->post['dbname']) . "`");
        }

        if (mysqli_select_db($this->dbh, mysqli_real_escape_string($this->dbh, $this->post['dbname'])) == false) {
            Log::error(sprintf(ERR_DBCONNECT_CREATE, $this->post['dbname']));
        }
    }

    protected function dbActionEmpty()
    {
        $paramsManager    = PrmMng::getInstance();
        $excludeDropTable = DUPX_DB_Functions::getExcludedTables();

        //Drop all tables, views and procs
        $this->dropTables($excludeDropTable);
        $this->dropViews();
        $this->dropProcs();
        $this->dropFuncs();
    }

    protected function dbActionRemoveOnlyTables()
    {
        $paramsManager    = PrmMng::getInstance();
        $excludeDropTable = DUPX_DB_Functions::getExcludedTables();

        $this->dropTables($excludeDropTable, DUPX_DB_Tables::getInstance()->getNewTablesNames());

        if (!DUPX_InstallerState::isAddSiteOnMultisite()) {
            $this->dropProcs();
            $this->dropFuncs();
            $this->dropViews();
        }
    }

    protected function dbActionRename()
    {
        Log::info('TABLE RENAME TO BACKUP');
        $paramsManager = PrmMng::getInstance();

        DUPX_DB_Functions::getInstance()->pregReplaceTableName('/^(.+)$/', $GLOBALS['DB_RENAME_PREFIX'] . '$1', array(
            'prefixFilter'         => DUPX_Constants::BACKUP_RENAME_PREFIX,
            'regexTablesDropFkeys' => '^' . $GLOBALS['DB_RENAME_PREFIX'] . '.+',
            'exclude'              => array(
                DUPX_DB_Functions::getUserTableName(self::TEMP_DB_PREFIX),
                DUPX_DB_Functions::getUserMetaTableName(self::TEMP_DB_PREFIX)
            )
        ));

        if (ParamDescUsers::getKeepUserId() > 0) {
            $overwriteData         = $paramsManager->getValue(PrmMng::PARAM_OVERWRITE_SITE_DATA);
            $originalUserTable     = DUPX_DB_Functions::getUserTableName($overwriteData['table_prefix']);
            $originalUserMetaTable = DUPX_DB_Functions::getUserMetaTableName($overwriteData['table_prefix']);
            $replacedUserTable     = $GLOBALS['DB_RENAME_PREFIX'] . $originalUserTable;
            $replaceUserMetaTable  = $GLOBALS['DB_RENAME_PREFIX'] . $originalUserMetaTable;
            DUPX_DB_Functions::getInstance()->copyTable($replacedUserTable, $originalUserTable, true);
            DUPX_DB_Functions::getInstance()->copyTable($replaceUserMetaTable, $originalUserMetaTable, true);
        }
    }

    protected function writeInChunks()
    {
        Log::info("--------------------------------------");
        Log::info("** DATABASE CHUNK install start");
        Log::info("--------------------------------------");
        $this->dbConnect();

        if (isset($this->post['dbchunk_retry']) && $this->post['dbchunk_retry'] > 0) {
            Log::info("DATABASE CHUNK RETRY COUNT: " . Log::v2str($this->post['dbchunk_retry']));
        }

        $delimiter = $this->post['delimiter'];

        $handle = fopen($this->sql_file_path, 'rb');
        if ($handle === false) {
            return false;
        }

        Log::info("DATABASE CHUNK SEEK POSITION: " . Log::v2str($this->post['pos']));

        if (-1 !== fseek($handle, $this->post['pos'])) {
            DUPX_DB::setCharset($this->dbh, $this->dbcharset, $this->dbcollate);

            $this->setSQLSessionMode();

            $this->thread_start_time = DUPX_U::getMicrotime();

            Log::info('DATABASE CHUNK START POS:' . Log::v2str($this->post['pos']), Log::LV_DETAILED);
            $this->pingAndReconnect();

            if (@mysqli_autocommit($this->dbh, false)) {
                Log::info('Auto Commit set to false successfully');
            } else {
                Log::info('Failed to set Auto Commit to false');
            }

            Log::info("DATABASE CHUNK: Iterating query loop", Log::LV_DEBUG);

            if (!$this->post['first_chunk'] && !empty($this->setQueries)) {
                Log::info("SET QUERIES FROM FIRST CHUNK", Log::LV_DETAILED);
                foreach ($this->setQueries as $setQuery) {
                    Log::info("\tSET QUERY " . Log::v2str($setQuery), Log::LV_DEBUG);
                    $this->writeQueryInDB($setQuery);
                }
            }

            $query                 = '';
            $skipChunkTimeoutCheck = $this->dbsplit_creates && $this->post['first_chunk'];
            $delimiterMatch        = null;

            while (($line = fgets($handle)) !== false) {
                if (preg_match('/^\s*DELIMITER\s+([^\s]+)\s*$/i', $line, $delimiterMatch) === 1) {
                    $query                   = '';
                    $this->post['delimiter'] = $delimiter = $delimiterMatch[1];

                    Log::info("SET DELIMITER " . $delimiter . " AND SKIP QUERY");
                    continue;
                }

                if ($this->post['first_chunk']) {
                    //Matches ordinary set queries e.g "SET @saved_cs_client = @@character_set_client;"
                    //and version dependent set queries e.g. "/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;"
                    if (preg_match('/^[\s\t]*(?:\/\*!\d+)?[\s\t]*SET[\s\t]*@.+;/', $line)) {
                        $setQuery = trim($line);
                        if (!in_array($setQuery, $this->setQueries)) {
                            Log::info("FIRST CHUNK SET QUERY " . Log::v2str($setQuery), Log::LV_DEBUG);
                            $this->setQueries[] = $setQuery;
                        }
                    }

                    if ($line === self::TABLE_CREATION_END_MARKER) {
                        Log::info("DATABASE CHUNK: CREATION TABLE MARKER FOUND");
                        $skipChunkTimeoutCheck = false;
                        continue;
                    }
                }

                $query .= $line;
                if (preg_match('/' . preg_quote($delimiter, '/') . '\s*$/S', $line)) {
                    // Temp: Uncomment this to randomly kill the php db process to simulate real world hosts and verify system recovers properly
                    /*
                      $rand_no = rand(0, 500);
                      if (0 == $this->post['dbchunk_retry'] && 1 == $rand_no) {
                      Log::info("intentionally killing db chunk installation process");
                      error_log('intentionally killing db chunk installation process');
                      exit(1);
                      }
                     */

                    $query = trim($query);
                    if (!$this->skipQuery($query)) {
                        $this->writeQueryInDB($query);
                    }
                    $query = '';

                    $elapsed_time = (microtime(true) - $this->thread_start_time);
                    if (Log::isLevel(Log::LV_DEBUG)) {
                        Log::info("DATABASE CHUNK: Elapsed time: " . Log::v2str($elapsed_time), Log::LV_HARD_DEBUG);
                        if ($elapsed_time > DUPX_Constants::CHUNK_DBINSTALL_TIMEOUT_TIME) {
                            Log::info("DATABASE CHUNK: Breaking query loop.", Log::LV_DEBUG);
                        } else {
                            Log::info("DATABASE CHUNK: Not Breaking query loop", Log::LV_HARD_DEBUG);
                        }
                    }

                    //Only stop first chunk if all CREATE queries have been run
                    if (!$skipChunkTimeoutCheck && $elapsed_time > DUPX_Constants::CHUNK_DBINSTALL_TIMEOUT_TIME) {
                        break;
                    }
                }
            }

            if (@mysqli_autocommit($this->dbh, true)) {
                Log::info('Auto Commit set to true successfully');
            } else {
                Log::info('Failed to set Auto Commit to true');
            }

            $query_offset = ftell($handle);

            $seek_tell_log_line = (
                file_exists(self::seekTellFilePath()) &&
                filesize(self::seekTellFilePath()) > 0
                ) ? ',' : '';

            $seek_tell_log_line .= $this->post['pos'] . '-' . $query_offset;
            file_put_contents(self::seekTellFilePath(), $seek_tell_log_line, FILE_APPEND);

            $this->post['progress'] = ceil($query_offset / $this->dbFileSize * 100);
            $this->post['pos']      = $query_offset;

            if (feof($handle)) {
                if ($this->seekIntegrityCheck()) {
                    Log::info('DATABASE CHUNK: DB install chunk process integrity check has been just passed successfully.', Log::LV_DETAILED);
                    $this->post['pass']              = 1;
                    $this->post['continue_chunking'] = false;
                } else {
                    Log::info('DB install chunk process integrity check has been just failed.');
                    $this->post['pass']      = 0;
                    $this->post['is_error']  = 1;
                    $this->post['error_msg'] = 'DB install chunk process integrity check has been just failed.';
                }
            } else {
                $this->post['pass']              = 0;
                $this->post['continue_chunking'] = true;
            }
        }
        Log::info("DATABASE CHUNK: End Query offset " . Log::v2str($query_offset), Log::LV_DETAILED);

        if ($this->post['pass']) {
            Log::info('DATABASE CHUNK: This is last chunk', Log::LV_DETAILED);
        }

        fclose($handle);

        Log::info("--------------------------------------");
        Log::info("** DATABASE CHUNK install end");
        Log::info("--------------------------------------");

        ob_flush();
        flush();
        return true;
    }

    protected function seekIntegrityCheck()
    {
        // ensure integrity
        $seek_tell_log          = file_get_contents(self::seekTellFilePath());
        $seek_tell_log_explodes = explode(',', $seek_tell_log);
        $last_start             = 0;
        $last_end               = 0;
        foreach ($seek_tell_log_explodes as $seek_tell_log_explode) {
            $temp_arr = explode('-', $seek_tell_log_explode);
            if (is_array($temp_arr) && 2 == count($temp_arr)) {
                $start = $temp_arr[0];
                $end   = $temp_arr[1];
                if ($start != $last_end) {
                    return false;
                }
                if ($last_start > $end) {
                    return false;
                }

                $last_start = $start;
                $last_end   = $end;
            } else {
                return false;
            }
        }

        if ($last_end != DUPX_Package::getSqlFileSize()) {
            return false;
        }
        return true;
    }

    /**
     * Check if query should be skipped
     *
     * @param string $query query to check
     *
     * @return bool return true if query should be skipped
     */
    public static function skipQuery($query)
    {
        static $skipRegex = null;

        if (is_null($skipRegex)) {
            $skipRegex  = array();
            $skipTables = DUPX_DB_Tables::getInstance()->getTablesToSkip();

            if (count($skipTables) > 0) {
                $skipTables = array_map(function ($table) {
                    return preg_quote($table, '/');
                }, $skipTables);

                for ($i = 0; $i < ceil(count($skipTables) / self::SKIP_TABLES_REGEX_CHUNK_SIZE); $i++) {
                    $subArray = array_slice($skipTables, $i * self::SKIP_TABLES_REGEX_CHUNK_SIZE, self::SKIP_TABLES_REGEX_CHUNK_SIZE);

                    if (count($subArray) == 0) {
                        break;
                    }

                    if (DUPX_ArchiveConfig::getInstance()->dbInfo->buildMode === self::BUILD_MODE_MYSQLDUMP) {
                        $skipRegex[] = '/^\s*(?:\/\*!\d+\s)?\s*(?:CREATE|INSERT|ALTER|LOCK)\s.*(?:TABLE|INTO).*[`\s](' . implode('|', $subArray) . ')[`\s]/im';
                    } else {
                        $skipRegex[] = '/^\s*(?:CREATE|INSERT)\s.*(?:TABLE|INTO).*[`\s](' . implode('|', $subArray) . ')[`\s]/im';
                    }
                }
            }

            switch (count($skipRegex)) {
                case 0:
                    $skipRegex = false;
                    Log::info('NO TABLE TO SKIP');
                    break;
                case 1:
                    $skipRegex = $skipRegex[0];
                    // no break
                default:
                    Log::info('TABLES TO SKIP FOUND');
                    Log::info('SKIP TABLE EXTRACTION REGEX ' . Log::v2str($skipRegex), Log::LV_DETAILED);
                    break;
            }
        }

        if ($skipRegex === false) {
            return false;
        } elseif (is_array($skipRegex)) {
            foreach ($skipRegex as $regex) {
                if (preg_match($regex, $query) === 1) {
                    return true;
                }
            }
            return false;
        } else {
            return (preg_match($skipRegex, $query) === 1);
        }
    }

    public function getRowCountMisMatchTables()
    {
        $nManager      = DUPX_NOTICE_MANAGER::getInstance();
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        $this->dbConnect();

        if (is_null($this->dbh)) {
            $errorMsg = "**ERROR** database DBH is null";
            $this->dbquery_errs++;
            $nManager->addBothNextAndFinalReportNotice(array(
                'shortMsg' => $errorMsg,
                'level'    => DUPX_NOTICE_ITEM::CRITICAL,
                'sections' => 'database'
                ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'query-dbh-null');
            Log::info($errorMsg);
            $nManager->saveNotices();
            return false;
        }

        $tablesList     = $archiveConfig->dbInfo->tablesList;
        $tablePrefix    = PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_TABLE_PREFIX);
        $skipTables     = array(
            $tablePrefix . "duplicator_packages",
            DUPX_DB_Functions::getOptionsTableName(),
            DUPX_DB_Functions::getPackagesTableName(),
            DUPX_DB_Functions::getEntitiesTableName(),
        );
        $misMatchTables = array();
        foreach ($tablesList as $table => $tableInfo) {
            if ($tableInfo->insertedRows === false) {
                // if it is false it means that no precise count is available to perform the validity test.
                continue;
            }
            $table = $archiveConfig->getTableWithNewPrefix($table);
            if (in_array($table, $skipTables)) {
                continue;
            }
            $sql    = "SELECT count(*) as cnt FROM `" . mysqli_real_escape_string($this->dbh, $table) . "`";
            $result = DUPX_DB::mysqli_query($this->dbh, $sql);
            if (false !== $result) {
                $row = mysqli_fetch_assoc($result);
                if ($tableInfo->insertedRows != ($row['cnt'])) {
                    $errMsg           = 'DATABASE: table ' . Log::v2str($table) . ' row count mismatch; expected ' . Log::v2str($tableInfo->insertedRows) . ' in database' . Log::v2str($row['cnt']);
                    Log::info($errMsg);
                    $nManager->addBothNextAndFinalReportNotice(array(
                        'shortMsg' => 'Database Table row count validation was failed',
                        'level'    => DUPX_NOTICE_ITEM::NOTICE,
                        'longMsg'  => $errMsg . "\n",
                        'sections' => 'database'
                        ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, 'row-count-mismatch');
                    $misMatchTables[] = $table;
                }
            }
        }
        return $misMatchTables;
    }

    protected function writeInDB()
    {
        // WRITE DATA
        $fcgi_buffer_pool  = 5000;
        $fcgi_buffer_count = 0;
        $counter           = 0;

        $this->dbConnect();

        $handle = fopen($this->sql_file_path, 'rb');
        if ($handle === false) {
            return false;
        }
        $paramsManager = PrmMng::getInstance();
        $nManager      = DUPX_NOTICE_MANAGER::getInstance();
        if (is_null($this->dbh)) {
            $errorMsg = "**ERROR** database DBH is null";
            $this->dbquery_errs++;
            $nManager->addNextStepNoticeMessage($errorMsg, DUPX_NOTICE_ITEM::CRITICAL, DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'query-dbh-null');
            $nManager->addFinalReportNotice(array(
                'shortMsg' => $errorMsg,
                'level'    => DUPX_NOTICE_ITEM::CRITICAL,
                'sections' => 'database'
                ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'query-dbh-null');
            Log::info($errorMsg);
            $nManager->saveNotices();
            return;
        }

        @mysqli_autocommit($this->dbh, false);

        $query     = '';
        $delimiter = ';';

        while (($line      = fgets($handle)) !== false) {
            if ('DELIMITER ;' == trim($query)) {
                $delimiter = ';';
                $query     = null;
                continue;
            }

            $query .= trim($line);

            if (!preg_match('/' . $delimiter . '\s*$/S', $query)) {
                continue;
            }

            $query_strlen = strlen($query);

            if ($this->dbvar_maxpacks < $query_strlen) {
                $errorMsg = "FAILED QUERY LIMIT [QLEN:" . $query_strlen . "|MAX:{$this->dbvar_maxpacks}]\n\t[SQL=" . substr($query, 0, self::QUERY_ERROR_LOG_LEN) . "...]\n\n";
                $this->dbquery_errs++;
                $nManager->addBothNextAndFinalReportNotice(array(
                    'shortMsg'    => 'Query size limit error (max limit ' . $this->dbvar_maxpacks . ')',
                    'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'     => $errorMsg,
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                    'sections'    => 'database',
                    'faqLink'     => array(
                        'url'   => 'https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-260-q',
                        'label' => 'FAQ Link'
                    )
                    ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, 'query-size-limit-msg');
                Log::info($errorMsg);
            } else {
                if (0 === strpos($query, "DELIMITER")) {
                    // Ending delimiter
                    // control never comes in this if condition, but written
                    if ('DELIMITER ;' == $query) {
                        $delimiter = ';';
                    } else { // starting delimiter
                        $delimiter = substr($query, 10);
                    }

                    Log::info("Skipping delimiter query");
                    $query = '';
                    continue;
                }

                $query = self::applyQueryFixes($query);

                $tempRes = DUPX_DB::mysqli_query($this->dbh, $query);
                if (!is_bool($tempRes)) {
                    @mysqli_free_result($tempRes);
                }
                $err = mysqli_error($this->dbh);
                //Check to make sure the connection is alive
                if (!empty($err)) {
                    $errMsg = "**ERROR** database error write '{$err}' - [sql=" . substr($query, 0, self::QUERY_ERROR_LOG_LEN) . "...]";
                    Log::info($errMsg);

                    $this->pingAndReconnect();

                    if (DUPX_U::contains($err, 'Unknown collation')) {
                        $nManager->addNextStepNotice(array(
                            'shortMsg'    => 'DATABASE ERROR: database error write',
                            'level'       => DUPX_NOTICE_ITEM::HARD_WARNING,
                            'longMsg'     => 'Unknown collation<br>RECOMMENDATION: Try resolutions found at https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q',
                            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                            'faqLink'     => array(
                                'url'   => 'https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q',
                                'label' => 'FAQ Link'
                            )
                            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'query-collation-write-msg');
                        $nManager->addFinalReportNotice(array(
                            'shortMsg'    => 'DATABASE ERROR: database error write',
                            'level'       => DUPX_NOTICE_ITEM::HARD_WARNING,
                            'longMsg'     => 'Unknown collation<br>RECOMMENDATION: Try resolutions found at https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q' . '<br>' . $errMsg,
                            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                            'sections'    => 'database',
                            'faqLink'     => array(
                                'url'   => 'https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q',
                                'label' => 'FAQ Link'
                            )
                        ));
                        Log::info('RECOMMENDATION: Try resolutions found at https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q');
                    } elseif (!$this->skipErrorNotice($err, $query)) {
                        $nManager->addNextStepNoticeMessage('DATABASE ERROR: database error write', DUPX_NOTICE_ITEM::SOFT_WARNING, DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'query-write-msg');
                        $nManager->addFinalReportNotice(array(
                            'shortMsg' => 'DATABASE ERROR: database error write',
                            'level'    => DUPX_NOTICE_ITEM::SOFT_WARNING,
                            'longMsg'  => $errMsg,
                            'sections' => 'database'
                        ));
                    }

                    $this->dbquery_errs++;

                    //Buffer data to browser to keep connection open
                } else {
                    if ($fcgi_buffer_count++ > $fcgi_buffer_pool) {
                        $fcgi_buffer_count = 0;
                    }
                    $this->dbquery_rows++;
                }
            }
            $query = '';
            $counter++;
        }
        @mysqli_commit($this->dbh);
        @mysqli_autocommit($this->dbh, true);

        $nManager->saveNotices();
    }

    /**
     * @param string $query
     * @return boolean // false on failure
     */
    protected function writeQueryInDB($query)
    {
        $this->dbConnect();
        $return = false;

        $query_strlen = strlen($query);

        $nManager      = DUPX_NOTICE_MANAGER::getInstance();
        $paramsManager = PrmMng::getInstance();

        @mysqli_autocommit($this->dbh, false);

        if ($this->dbvar_maxpacks < $query_strlen) {
            $errorMsg = "FAILED QUERY LIMIT [QLEN:" . $query_strlen . "|MAX:{$this->dbvar_maxpacks}]\n\t[SQL=" . substr($query, 0, self::QUERY_ERROR_LOG_LEN) . "...]\n\n";
            $this->dbquery_errs++;
            $nManager->addBothNextAndFinalReportNotice(array(
                'shortMsg'    => 'Query size limit error (max limit ' . $this->dbvar_maxpacks . ')',
                'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'     => $errorMsg,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE,
                'sections'    => 'database',
                'faqLink'     => array(
                    'url'   => 'https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-260-q',
                    'label' => 'FAQ Link'
                )
                ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, 'query-size-limit-msg');
            Log::info($errorMsg);
            $return   = false;
        } elseif ($query_strlen > 0) {
            $query = self::applyQueryFixes($query);

            //Check to make sure the connection is alive
            if (($query_res = DUPX_DB::mysqli_query($this->dbh, $query)) === false) {
                $err    = mysqli_error($this->dbh);
                $errMsg = "DATABASE ERROR: '{$err}'\n\t[SQL=" . substr($query, 0, self::QUERY_ERROR_LOG_LEN) . "...]\n\n";

                if (DUPX_U::contains($err, 'Unknown collation')) {
                    $nManager->addNextStepNotice(array(
                        'shortMsg'    => 'DATABASE ERROR: ' . $err,
                        'level'       => DUPX_NOTICE_ITEM::HARD_WARNING,
                        'longMsg'     => 'Unknown collation<br>RECOMMENDATION: Try resolutions found at https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q',
                        'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                        'faqLink'     => array(
                            'url'   => 'https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q',
                            'label' => 'FAQ Link'
                        )
                        ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'query-collation-write-msg');
                    $nManager->addFinalReportNotice(array(
                        'shortMsg'    => 'DATABASE ERROR: ' . $err,
                        'level'       => DUPX_NOTICE_ITEM::HARD_WARNING,
                        'longMsg'     => 'Unknown collation<br>RECOMMENDATION: Try resolutions found at https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q' . '<br>' . $errMsg,
                        'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                        'sections'    => 'database',
                        'faqLink'     => array(
                            'url'   => 'https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q',
                            'label' => 'FAQ Link'
                        )
                    ));
                    Log::info('RECOMMENDATION: Try resolutions found at https://snapcreek.com/duplicator/docs/faqs-tech/#faq-installer-110-q');
                } elseif (!$this->skipErrorNotice($err, $query)) {
                    $nManager->addNextStepNotice(array(
                        'shortMsg'    => 'DATABASE ERROR: database error write',
                        'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                        'longMsg'     => $errMsg,
                        'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_PRE
                        ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, 'query-write-msg');
                    $nManager->addFinalReportNotice(array(
                        'shortMsg' => 'DATABASE ERROR: ' . $err,
                        'level'    => DUPX_NOTICE_ITEM::SOFT_WARNING,
                        'longMsg'  => $errMsg,
                        'sections' => 'database'
                    ));
                }

                $this->pingAndReconnect();
                $this->dbquery_errs++;

                //Buffer data to browser to keep connection open
                $return = false;
            } else {
                if (!is_bool($query_res)) {
                    @mysqli_free_result($query_res);
                }
                $this->dbquery_rows++;
                $return = true;
            }
        }
        @mysqli_commit($this->dbh);
        @mysqli_autocommit($this->dbh, true);
        return $return;
    }

    public function runCleanupRoutines()
    {
        if (DUPX_InstallerState::isRestoreBackup()) {
            Log::info("CLEANUP ROUTINE SKIP [BACKUP MODE ENABLE]");
            $this->updateCurrentPackageStatus();
            return;
        } if (DUPX_InstallerState::isAddSiteOnMultisite()) {
            Log::info("CLEANUP ROUTINE ADD ON MULTISITE START");
        } else {
            Log::info("CLEANUP ROUTINE START");
            $this->emptyDuplicatorPackages();
            $this->cleanupExtra();
        }

        $this->cleanupOptions();

        Log::info("CLEANUP ROUTINE COMPLETE");
    }

    private function emptyDuplicatorPackages()
    {
        Log::info("CLEAN PACKAGES");
        $this->dbConnect();
        $packagesTable = mysqli_real_escape_string($this->dbh, DUPX_DB_Functions::getPackagesTableName());

        $deleteCount = DUPX_DB::chunksDelete($this->dbh, $packagesTable, '1 = 1');

        $this->dbdelete_count += abs($deleteCount);
    }

    private function cleanupOptions()
    {
        $this->dbConnect();
        $archiveConfig = DUPX_ArchiveConfig::getInstance();
        if (DUPX_InstallerState::isAddSiteOnMultisite()) {
            $optionsTable = mysqli_real_escape_string($this->dbh, DUPX_DB_Functions::getOptionsTableName(DUPX_MU::getSubsiteOverwriteTablePrefix()));
        } else {
            $optionsTable = mysqli_real_escape_string($this->dbh, DUPX_DB_Functions::getOptionsTableName());
        }

        $deleteOptionConds   = array();
        $deleteOptionConds[] = '`option_name` LIKE "\_transient%"';
        $deleteOptionConds[] = '`option_name` LIKE "\_site\_transient%"';

        $opts_delete = array();
        foreach ((array) json_decode($archiveConfig->opts_delete) as $value) {
            $opts_delete[] = '"' . mysqli_real_escape_string($this->dbh, $value) . '"';
        }
        if (count($opts_delete) > 0) {
            $deleteOptionConds[] = '`option_name` IN (' . implode(',', $opts_delete) . ')';
        }

        Log::info("CLEAN OPTIONS [" . $optionsTable . "]" . Log::v2str($deleteOptionConds));
        $this->dbdelete_count += DUPX_DB::chunksDelete($this->dbh, $optionsTable, implode(' OR ', $deleteOptionConds));
    }

    private function cleanupExtra()
    {
        Log::info("CLEAN EXTRA");
        $this->dbConnect();
        if (!$this->dbobj_views) {
            $this->dropViews();
            Log::info("/t - VIEWS DROPPED");
        } else {
            Log::info("/t - SKIP DROP VIEWS");
        }

        if (!$this->dbobj_procs) {
            $this->dropProcs();
            Log::info("/t - PROCS DROPPED");
        } else {
            Log::info("/t - SKIP DROP PROCS");
        }

        if (!$this->dbobj_funcs) {
            $this->dropFuncs();
            Log::info("/t - FUNCS DROPPED");
        } else {
            Log::info("/t - SKIP DROP FUNCS");
        }
    }

    private function updateCurrentPackageStatus()
    {
        $packageId = DUPX_ArchiveConfig::getInstance()->packInfo->packageId;
        Log::info("CLEANUP CURRENT PACKAGE STATUS ID " . $packageId);

        $packagesTable = mysqli_real_escape_string($this->dbh, DUPX_DB_Functions::getPackagesTableName());
        $optionsTable  = mysqli_real_escape_string($this->dbh, DUPX_DB_Functions::getOptionsTableName());
        DUPX_DB::mysqli_query($this->dbh, 'DELETE FROM `' . $packagesTable . '` WHERE `id` = ' . $packageId);
        DUPX_DB::mysqli_query($this->dbh, "DELETE FROM `" . $optionsTable . "` WHERE `option_name` = 'duplicator_pro_package_active'");
    }

    private function getSQLSessionMode()
    {
        $this->dbConnect();
        $result = DUPX_DB::mysqli_query($this->dbh, "SELECT @@SESSION.sql_mode;");
        $row    = mysqli_fetch_row($result);
        $result->close();
        return is_array($row) ? $row[0] : '';
    }

    /**
     * SQL MODE OVERVIEW:
     * sql_mode can cause db create issues on some systems because the mode affects how data is inserted.
     * Right now defaulting to  NO_AUTO_VALUE_ON_ZERO (https://dev.mysql.com/doc/refman/5.5/en/sql-mode.html#sqlmode_no_auto_value_on_zero)
     * has been the saftest option because the act of seting the sql_mode will nullify the MySQL Engine defaults which can be very problematic
     * if the default is something such as STRICT_TRANS_TABLES,STRICT_ALL_TABLES,NO_ZERO_DATE.  So the default behavior will be to always
     * use NO_AUTO_VALUE_ON_ZERO.  If the user insits on using the true system defaults they can use the Custom option.  Note these values can
     * be overriden by values set in the database.sql script such as:
     * !40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO'
     *
     * @throws Exception
     */
    private function setSQLSessionMode()
    {
        $this->dbConnect();
        switch ($this->post['dbmysqlmode']) {
            case 'DEFAULT':
                $query = "SET SESSION sql_mode = 'NO_AUTO_VALUE_ON_ZERO'";
                break;
            case 'DISABLE':
                $query = "SET SESSION sql_mode = ''";
                break;
            case 'CUSTOM':
                $query = "SET SESSION sql_mode = '" . mysqli_real_escape_string($this->dbh, $this->post['dbmysqlmode_opts']) . "'";
                break;
            default:
                throw new Exception('Unknown dbmysqlmode option ' . $this->post['dbmysqlmode']);
        }

        if (!$result = DUPX_DB::mysqli_query($this->dbh, $query)) {
            $sql_error = mysqli_error($this->dbh);
            $long      = "WARNING: A custom sql_mode setting issue has been detected:\n{$sql_error}.<br>";
            $long      .= "The installation continue with the default MySQL Mode of the database.<br><br>";
            $long      .= "For more details visit: <a href=\"https://dev.mysql.com/doc/refman/8.0/en/sql-mode.html\" target=\"_blank\">sql-mode documentation</a>";
            DUPX_NOTICE_MANAGER::getInstance()->addBothNextAndFinalReportNotice(array(
                'shortMsg'    => 'SET SQL MODE ERROR',
                'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'     => $long,
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                'sections'    => 'database'
                ), DUPX_NOTICE_MANAGER::ADD_UNIQUE, 'drop-mysql-mode-set');
        }
    }

    /**
     *
     * @param array      $exclude tables to exclude
     * @param bool|array $tables  // if true drop all tables or table in list
     * @return type
     */
    private function dropTables($exclude = array(), $tables = true)
    {
        $found_tables = array();

        $sql    = "SHOW FULL TABLES WHERE Table_Type != 'VIEW'";
        if (($result = DUPX_DB::mysqli_query($this->dbh, $sql)) === false) {
            Log::error('QUERY ' . Log::v2str($sql) . 'ERROR: ' . mysqli_error($this->dbh));
        }
        while ($row = mysqli_fetch_row($result)) {
            if (in_array($row[0], $exclude)) {
                continue;
            }

            if (is_bool($tables) && $tables == false) {
                continue;
            }

            if (is_array($tables) && !in_array($row[0], $tables)) {
                continue;
            }

            $found_tables[] = $row[0];
        }

        if (!count($found_tables)) {
            return;
        }

        DUPX_DB::mysqli_query($this->dbh, "SET FOREIGN_KEY_CHECKS = 0;");
        foreach ($found_tables as $table_name) {
            Log::info('DROP TABLE ' . $table_name, Log::LV_DEBUG);
            $sql    = "DROP TABLE `" . mysqli_real_escape_string($this->dbh, $this->post['dbname']) . "`.`" . mysqli_real_escape_string($this->dbh, $table_name) . "`";
            if (!$result = DUPX_DB::mysqli_query($this->dbh, $sql)) {
                Log::error(sprintf(ERR_DROP_TABLE_TRYCLEAN, $table_name, $this->post['dbname'], mysqli_error($this->dbh)));
            }
        }
        DUPX_DB::mysqli_query($this->dbh, "SET FOREIGN_KEY_CHECKS = 1;");

        $this->drop_tbl_log = count($found_tables);
    }

    private function dropProcs()
    {
        $sql      = "SHOW PROCEDURE STATUS WHERE db='{$this->post['dbname']}'";
        $nManager = DUPX_NOTICE_MANAGER::getInstance();

        if (!($result = DUPX_DB::mysqli_query($this->dbh, $sql))) {
            $nManager->addFinalReportNotice(array(
                'shortMsg'    => 'PROCEDURE CLEAN ERROR: ' . mysqli_error($this->dbh),
                'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'     => sprintf('Unable to get list of PROCEDURES from database "%s".', $this->post['dbname']),
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                'sections'    => 'database',
            ));

            Log::info("PROCEDURE CLEAN ERROR: Could not get list of PROCEDURES to drop them.");
            return;
        }

        if ($result->num_rows === 0) {
            return;
        }

        while ($row = mysqli_fetch_row($result)) {
            $proc_name = $row[1];
            $sql       = "DROP PROCEDURE IF EXISTS `" . mysqli_real_escape_string($this->dbh, $this->post['dbname']) . "`.`" . mysqli_real_escape_string($this->dbh, $proc_name) . "`";
            if (!DUPX_DB::mysqli_query($this->dbh, $sql)) {
                $err = mysqli_error($this->dbh);
                $nManager->addNextStepNotice(array(
                    'shortMsg'    => 'PROCEDURE CLEAN ERROR',
                    'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'     => sprintf('Unable to remove PROCEDURE "%s" from database "%s".<br/>', $proc_name, $this->post['dbname']),
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                    ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, 'drop-proc-fail-msg');

                $nManager->addFinalReportNotice(array(
                    'shortMsg'    => 'PROCEDURE CLEAN ERROR: ' . $err,
                    'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'     => sprintf('Unable to remove PROCEDURE "%s" from database "%s".', $proc_name, $this->post['dbname']),
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                    'sections'    => 'database',
                ));

                Log::info("PROCEDURE CLEAN ERROR: '{$err}'\n\t[SQL=" . substr($sql, 0, self::QUERY_ERROR_LOG_LEN) . "...]\n\n");
            }
        }

        $nManager->addNextStepNotice(array(
            'shortMsg'    => 'PROCEDURE CLEAN ERROR',
            'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
            'longMsg'     => sprintf(ERR_DROP_PROCEDURE_TRYCLEAN, mysqli_error($this->dbh)),
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_PREPEND_IF_EXISTS, 'drop-proc-fail-msg');
    }

    private function dropFuncs()
    {
        $sql      = "SHOW FUNCTION STATUS WHERE db='{$this->post['dbname']}'";
        $nManager = DUPX_NOTICE_MANAGER::getInstance();

        if (!($result = DUPX_DB::mysqli_query($this->dbh, $sql))) {
            $nManager->addFinalReportNotice(array(
                'shortMsg'    => 'FUNCTION CLEAN ERROR: ' . mysqli_error($this->dbh),
                'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'     => sprintf('Unable to get list of FUNCTIONS from database "%s".', $this->post['dbname']),
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                'sections'    => 'database',
            ));

            Log::info("FUNCTION CLEAN ERROR: Could not get list of FUNCTIONS to drop them.");
            return;
        }

        if ($result->num_rows === 0) {
            return;
        }

        while ($row = mysqli_fetch_row($result)) {
            $func_name = $row[1];
            $sql       = "DROP FUNCTION IF EXISTS `" . mysqli_real_escape_string($this->dbh, $this->post['dbname']) . "`.`" . mysqli_real_escape_string($this->dbh, $func_name) . "`";
            if (!DUPX_DB::mysqli_query($this->dbh, $sql)) {
                $err = mysqli_error($this->dbh);
                $nManager->addNextStepNotice(array(
                    'shortMsg'    => 'FUNCTION CLEAN ERROR',
                    'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'     => sprintf('Unable to remove FUNCTION "%s" from database "%s".<br/>', $func_name, $this->post['dbname']),
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                    ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, 'drop-func-fail-msg');

                $nManager->addFinalReportNotice(array(
                    'shortMsg'    => 'FUNCTION CLEAN ERROR: ' . $err,
                    'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'     => sprintf('Unable to remove FUNCTION "%s" from database "%s".', $func_name, $this->post['dbname']),
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                    'sections'    => 'database',
                ));

                Log::info("FUNCTION CLEAN ERROR: '{$err}'\n\t[SQL=" . substr($sql, 0, self::QUERY_ERROR_LOG_LEN) . "...]\n\n");
            }
        }

        $nManager->addNextStepNotice(array(
            'shortMsg'    => 'FUNCTION CLEAN ERROR',
            'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
            'longMsg'     => sprintf(ERR_DROP_FUNCTION_TRYCLEAN, mysqli_error($this->dbh)),
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_PREPEND_IF_EXISTS, 'drop-func-fail-msg');
    }

    private function dropViews()
    {
        $sql      = "SHOW FULL TABLES WHERE Table_Type = 'VIEW'";
        $nManager = DUPX_NOTICE_MANAGER::getInstance();

        if (!($result = DUPX_DB::mysqli_query($this->dbh, $sql))) {
            $nManager->addFinalReportNotice(array(
                'shortMsg'    => 'VIEW CLEAN ERROR: ' . mysqli_error($this->dbh),
                'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                'longMsg'     => sprintf('Unable to get list of VIEWS from database "%s"', $this->post['dbname']),
                'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                'sections'    => 'database',
            ));

            Log::info("VIEW CLEAN ERROR: Could not get list of VIEWS to drop them.");
            return;
        }

        if ($result->num_rows === 0) {
            return;
        }

        while ($row = mysqli_fetch_row($result)) {
            $view_name = $row[0];
            $sql       = "DROP VIEW `" . mysqli_real_escape_string($this->dbh, $this->post['dbname']) . "`.`" . mysqli_real_escape_string($this->dbh, $view_name) . "`";
            if (!DUPX_DB::mysqli_query($this->dbh, $sql)) {
                $err = mysqli_error($this->dbh);

                $nManager->addNextStepNotice(array(
                    'shortMsg'    => 'VIEW CLEAN ERROR',
                    'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'     => sprintf('Unable to remove VIEW "%s" from database "%s".<br/>', $view_name, $this->post['dbname']),
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                    ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_APPEND, 'drop-view-fail-msg');

                $nManager->addFinalReportNotice(array(
                    'shortMsg'    => 'VIEW CLEAN ERROR: ' . $err,
                    'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
                    'longMsg'     => sprintf('Unable to remove VIEW "%s" from database "%s"', $view_name, $this->post['dbname']),
                    'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
                    'sections'    => 'database',
                ));

                Log::info("VIEW CLEAN ERROR: '{$err}'\n\t[SQL=" . substr($sql, 0, self::QUERY_ERROR_LOG_LEN) . "...]\n\n");
            }
        }

        $nManager->addNextStepNotice(array(
            'shortMsg'    => 'VIEW CLEAN ERROR',
            'level'       => DUPX_NOTICE_ITEM::SOFT_WARNING,
            'longMsg'     => sprintf(ERR_DROP_VIEW_TRYCLEAN, mysqli_error($this->dbh)),
            'longMsgMode' => DUPX_NOTICE_ITEM::MSG_MODE_HTML,
            ), DUPX_NOTICE_MANAGER::ADD_UNIQUE_PREPEND_IF_EXISTS, 'drop-view-fail-msg');
    }

    public function writeLog()
    {
        $this->dbConnect();
        $nManager      = DUPX_NOTICE_MANAGER::getInstance();
        $paramsManager = PrmMng::getInstance();

        Log::info("ERRORS FOUND:\t{$this->dbquery_errs}");
        Log::info("DROPPED TABLES:\t{$this->drop_tbl_log}");
        Log::info("RENAMED TABLES:\t{$this->rename_tbl_log}");
        Log::info("QUERIES RAN:\t{$this->dbquery_rows}\n");

        $this->dbtable_rows  = 1;
        $this->dbtable_count = 0;

        Log::info("TABLES ROWS IN DATABASE AFTER EXTRACTION\n");
        if (($result = DUPX_DB::mysqli_query($this->dbh, "SHOW TABLES")) != false) {
            while ($row = mysqli_fetch_array($result, MYSQLI_NUM)) {
                $table_rows         = DUPX_DB::countTableRows($this->dbh, $row[0]);
                $this->dbtable_rows += $table_rows;
                Log::info('TABLE ' . str_pad(Log::v2str($row[0]), 50, '_', STR_PAD_RIGHT) . '[ROWS:' . str_pad($table_rows, 6, " ", STR_PAD_LEFT) . ']');
                $this->dbtable_count++;
            }
            @mysqli_free_result($result);
        }

        Log::info("\n" . 'DATABASE CACHE/TRANSITIENT [ROWS:' . str_pad($this->dbdelete_count, 6, " ", STR_PAD_LEFT) . ']');

        if ($this->dbtable_count == 0) {
            $tablePrefix = $paramsManager->getValue(PrmMng::PARAM_DB_TABLE_PREFIX);
            $longMsg     = "You may have to manually run the installer-data.sql to validate data input. " .
                "Also check to make sure your installer file is correct and the table prefix '" . $tablePrefix . " is correct for this particular version of WordPress.";
            $nManager->addBothNextAndFinalReportNotice(array(
                'shortMsg' => 'No table in database',
                'level'    => DUPX_NOTICE_ITEM::NOTICE,
                'longMsg'  => $longMsg,
                'sections' => 'database'
            ));
            Log::info("NOTICE: " . $longMsg . "\n");
        }



        $finalReport                              = $paramsManager->getValue(PrmMng::PARAM_FINAL_REPORT_DATA);
        $finalReport['extraction']['table_count'] = $this->dbtable_count;
        $finalReport['extraction']['table_rows']  = $this->dbtable_rows;
        $finalReport['extraction']['query_errs']  = $this->dbquery_errs;
        $paramsManager->setValue(PrmMng::PARAM_FINAL_REPORT_DATA, $finalReport);

        $paramsManager->save();
        $nManager->saveNotices();
    }

    public function getResultData()
    {
        $result                      = array();
        $result['pass']              = $this->post['pass'];
        $result['continue_chunking'] = $this->post['continue_chunking'];
        if ($result['continue_chunking'] == 0 && $result['pass']) {
            $result['perc']        = '100%';
            $result['queryOffset'] = 'Bytes processed ' . number_format($this->dbFileSize) . ' of ' . number_format($this->dbFileSize);
        } else {
            $result['perc']        = round(($this->post['pos'] * 100 / $this->dbFileSize), 2) . '%';
            $result['queryOffset'] = 'Bytes processed ' . number_format($this->post['pos']) . ' of ' . number_format($this->dbFileSize);
        }
        $result['is_error']    = $this->post['is_error'];
        $result['error_msg']   = $this->post['error_msg'];
        $result['table_count'] = $this->dbtable_count;
        $result['table_rows']  = $this->dbtable_rows;
        $result['query_errs']  = $this->dbquery_errs;

        return $result;
    }

    /**
     * @param $err   string Error message
     * @param $query string the SQL query
     * @return bool if true will skip front-end notice of error message
     * @throws Exception
     */
    private function skipErrorNotice($err, $query)
    {
        if (preg_match(self::SQL_CREATE_VIEW_PROC_FUNC_PATTERN, $query) && DUPX_U::contains($err, "already exists") && DUPX_InstallerState::isAddSiteOnMultisite()) {
            return true;
        }

        return false;
    }

    /**
     * return search and replace rules
     *
     * @return array
     */
    private static function rulesProcAndViews()
    {
        $result = array(
            'search'  => array(),
            'replace' => array()
        );

        if (DUPX_InstallerState::isRestoreBackup()) {
            return $result;
        }

        if (PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_REMOVE_DEFINER)) {
            $result['search'][]  = self::USER_DEFINER_REMOVE_PATTERN;
            $result['replace'][] = self::USER_DEFINER_REMOVE_REPLACE;
        } else {
            $result['search'][] = self::USER_DEFINER_REPLACE_PATTERN;

            $dbHost = PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_HOST);
            $dbUser = PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_USER);

            $definerHost = (($dbHost == "localhost" || $dbHost == "127.0.0.1") ? $dbHost : '%');
            $result['replace'][] = '$1' . addcslashes("`" . $dbUser . "`@`" . $definerHost . "`", '\\$') . '$3';
        }

        $result['search'][] = self::SQL_SECURITY_INVOKER_PATTERN;
        $result['replace'][] = self::SQL_SECURITY_INVOKER_REPLACE;

        return $result;
    }

    /**
     * @param $query
     * @return string The query with appropriate substitutions done
     * @throws Exception
     */
    private static function applyQueryFixes($query)
    {
        static $replaceRules = null;
        if (is_null($replaceRules)) {
            $replaceRules = array(
                'search'  => array(),
                'replace' => array()
            );

            $replaceRules = self::mergeSearchReplaceRules($replaceRules, self::rulesNbspFix());
            $replaceRules = self::mergeSearchReplaceRules($replaceRules, self::rulesTableNames());
            $replaceRules = self::mergeSearchReplaceRules($replaceRules, self::rulesProcAndViews());
            if (($legacyCharsetRegexes = DUPX_DB_Functions::getInstance()->getLegacyCharsetAndCollationRegexes()) !== false) {
                $replaceRules = self::mergeSearchReplaceRules($replaceRules, $legacyCharsetRegexes);
            }

            if (count($replaceRules['search']) === 0) {
                $replaceRules = false;
            }

            Log::info('QUERY FIXES ' . Log::v2str($replaceRules));
        }

        if ($replaceRules !== false) {
            return preg_replace($replaceRules['search'], $replaceRules['replace'], $query);
        } else {
            return $query;
        }
    }

    /**
     * return reash and repalce table prefix rules
     *
     * @return array
     */
    private static function rulesTableNames()
    {
        $result = array(
            'search'  => array(),
            'replace' => array()
        );

        $mapping = DUPX_DB_Tables::getInstance()->getRenameTablesMapping();

        foreach ($mapping as $oldPrefix => $newMapping) {
            $quoteOldPrefix = preg_quote($oldPrefix, '/');

            foreach ($newMapping as $newPrefix => $commons) {
                Log::info('CANGHE TABLES PREFIX ' . $oldPrefix . ' TO ' . $newPrefix . 'FOR ' . Log::v2str($commons));
                $quoteNewPrefix = addcslashes($newPrefix, '\\$');
                $quoteCommons   = array_map(
                    function ($val) {
                        return preg_quote($val, '/');
                    },
                    $commons
                );
                $result['search'][]  = '/' . $quoteOldPrefix . '(' . implode('|', $quoteCommons) . ')/m';
                $result['replace'][] = $quoteNewPrefix . '$1';
                $result['search'][]  = '/(CONSTRAINT[\s\t]+[`\'"]?.+)(?-i)' . $quoteOldPrefix . '(?i)(.+[`\'"]?[\s\t]+FOREIGN[\s\t]+KEY)/mi';
                $result['replace'][] = '$1' . $quoteNewPrefix . '$2';
            }
        }

        if (count($result['search']) > 0) {
            foreach ($result['search'] as $index => $regex) {
                Log::info('TABLE REGEX  SEARCH:' . $regex);
                Log::info('TABLE REGEX REPLACE:' . $result['replace'][$index]);
            }
        }

        return $result;
    }

    /**
     * return rearch an replace rules
     *
     * @return array return search and replacce list
     *
     */
    private static function rulesNbspFix()
    {
        $result = array(
            'search'  => array(),
            'replace' => array()
        );

        if (!PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_SPACING)) {
            $result['search'][]  = '/\xC2\xA0/';
            $result['replace'][] = ' ';
        }
        return $result;
    }

    /**
     * checj and return search and replace rules
     *
     * @param array $a search and replace rules
     * @param array $b search and replace rules
     *
     * @return array
     */
    private static function mergeSearchReplaceRules($a, $b)
    {
        if (
            !isset($a['search']) ||
            !isset($b['search']) ||
            !isset($a['replace']) ||
            !isset($b['replace']) ||
            count($a['search']) !== count($a['replace']) ||
            count($b['search']) !== count($b['replace'])
        ) {
            Log::info('Invalid search and replace rules');
            Log::info('A: ' . Log::v2str($a));
            Log::info('B: ' . Log::v2str($b));
            throw new Exception('Invalid search and replace rules');
        }

        return array_merge_recursive($a, $b);
    }

    public function firstOrNotChunking()
    {
        return $this->post['first_chunk'] || !PrmMng::getInstance()->getValue(PrmMng::PARAM_DB_CHUNK);
    }

    public function __destruct()
    {
        $this->dbClose();
    }
}
