<?php

/**
 *
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package daws
 *
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Utils\Log\Log;
use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapUtil;

require_once(dirname(__FILE__) . '/class.daws.constants.php');
require_once(DAWSConstants::$DUPARCHIVE_CLASSES_DIR . '/class.duparchive.loggerbase.php');
require_once(DAWSConstants::$DUPARCHIVE_CLASSES_DIR . '/class.duparchive.engine.php');
require_once(DAWSConstants::$DUPARCHIVE_CLASSES_DIR . '/class.duparchive.mini.expander.php');
require_once(DAWSConstants::$DUPARCHIVE_STATES_DIR . '/class.duparchive.state.simplecreate.php');
require_once(DAWSConstants::$DAWS_ROOT . '/class.daws.state.expand.php');

DupArchiveUtil::$TRACE_ON = false;

class DAWS_Logger extends DupArchiveLoggerBase
{

    public static function init()
    {
        set_error_handler(array(__CLASS__, "terminate_missing_variables"), E_ERROR);
    }

    public function log($s, $flush = false, $callingFunctionOverride = null)
    {
        Log::info($s, $flush, $callingFunctionOverride);
    }

    public static function generateCallTrace()
    {
        $e      = new Exception();
        $trace  = explode("\n", $e->getTraceAsString());
        // reverse array to make steps line up chronologically
        $trace  = array_reverse($trace);
        array_shift($trace); // remove {main}
        array_pop($trace); // remove call to this method
        $length = count($trace);
        $result = array();

        for ($i = 0; $i < $length; $i++) {
            $result[] = ($i + 1) . ')' . substr($trace[$i], strpos($trace[$i], ' ')); // replace '#someNum' with '$i)', set the right ordering
        }

        return "\t" . implode("\n\t", $result);
    }

    public static function terminate_missing_variables($errno, $errstr, $errfile, $errline)
    {
        Log::info("ERROR $errno, $errstr, {$errfile}:{$errline}");
        /**
         * INTERCEPT ON processRequest AND RETURN JSON STATUS
         */
        throw new Exception("ERROR:{$errfile}:{$errline} | " . $errstr, $errno);
    }
}

class DAWS
{
    private $lock_handle     = null;
    private $failureCallback = null;

    function __construct()
    {
        DAWS_Logger::init();
        date_default_timezone_set('UTC'); // Some machines don’t have this set so just do it here.
        DupArchiveEngine::init(new DAWS_Logger());
    }

    public function setFailureCallBack($callback)
    {
        if (is_callable($callback)) {
            $this->failureCallback = $callback;
        }
    }

    public function processRequest($params)
    {
        $retVal = new StdClass();

        $retVal->pass = false;

        $action = $params['action'];

        $initializeState = false;

        $archiveConfig = DUPX_ArchiveConfig::getInstance();
        if (!DupArchiveFileProcessor::setNewFilePathCallback(array($archiveConfig, 'destFileFromArchiveName'))) {
            Log::info('ERROR: CAN\'T SET THE PATH SE CALLBACK FUNCTION');
        } else {
            Log::info('PATH SE CALLBACK FUNCTION OK ', Log::LV_DEBUG);
        }

        $throttleDelayInMs = SnapUtil::getArrayValue($params, 'throttle_delay', false, 0);

        if ($action == 'start_expand') {
            Log::info('DAWN START EXPAND');

            $initializeState = true;

            DAWSExpandState::purgeStatefile();
            SnapIO::rm(DAWSConstants::$PROCESS_CANCEL_FILEPATH);
            $archiveFilepath          = SnapUtil::getArrayValue($params, 'archive_filepath');
            $restoreDirectory         = SnapUtil::getArrayValue($params, 'restore_directory');
            $workerTime               = SnapUtil::getArrayValue($params, 'worker_time', false, DAWSConstants::$DEFAULT_WORKER_TIME);
            $filteredDirectories      = SnapUtil::getArrayValue($params, 'filtered_directories', false, array());
            $excludedDirWithoutChilds = SnapUtil::getArrayValue($params, 'excludedDirWithoutChilds', false, array());
            $filteredFiles            = SnapUtil::getArrayValue($params, 'filtered_files', false, array());
            $fileRenames              = SnapUtil::getArrayValue($params, 'file_renames', false, array());
            $fileModeOverride         = SnapUtil::getArrayValue($params, 'file_mode_override', false, 0644);
            $directoryModeOverride    = SnapUtil::getArrayValue($params, 'dir_mode_override', false, 0755);

            $action = 'expand';
        } else {
            Log::info('DAWN CONTINUE EXPAND');
        }

        if ($action == 'expand') {
            $expandState = DAWSExpandState::getInstance($initializeState);

            $this->lock_handle = SnapIO::fopen(DAWSConstants::$PROCESS_LOCK_FILEPATH, 'c+');
            SnapIO::flock($this->lock_handle, LOCK_EX);

            if ($initializeState || $expandState->working) {
                if ($initializeState) {
                    $expandState->archivePath              = $archiveFilepath;
                    $expandState->working                  = true;
                    $expandState->timeSliceInSecs          = $workerTime;
                    $expandState->basePath                 = $restoreDirectory;
                    $expandState->filteredDirectories      = $filteredDirectories;
                    $expandState->excludedDirWithoutChilds = $excludedDirWithoutChilds;
                    $expandState->filteredFiles            = $filteredFiles;
                    $expandState->fileRenames              = $fileRenames;
                    $expandState->fileModeOverride         = $fileModeOverride;
                    $expandState->directoryModeOverride    = $directoryModeOverride;

                    $expandState->save();
                }
                $expandState->throttleDelayInUs = 1000 * $throttleDelayInMs;
                DupArchiveEngine::expandArchive($expandState);
            }

            if (!$expandState->working) {
                $deltaTime = time() - $expandState->startTimestamp;
                Log::info("DAWN EXPAND DONE, SECONDS: " . $deltaTime, Log::LV_DETAILED);

                if (count($expandState->failures) > 0) {
                    Log::info('DAWN EXPAND ERRORS DETECTED');

                    foreach ($expandState->failures as $failure) {
                        Log::info("{$failure->subject}:{$failure->description}");
                        if (is_callable($this->failureCallback)) {
                            call_user_func($this->failureCallback, $failure);
                        }
                    }
                }
            } else {
                Log::info("DAWN EXPAND CONTINUE", Log::LV_DETAILED);
            }


            SnapIO::flock($this->lock_handle, LOCK_UN);

            $retVal->pass   = true;
            $retVal->status = $this->getStatus($expandState);
        } elseif ($action == 'get_status') {
            /* @var $expandState DAWSExpandState */
            $expandState = DAWSExpandState::getInstance($initializeState);

            $retVal->pass   = true;
            $retVal->status = $this->getStatus($expandState);
        } elseif ($action == 'cancel') {
            if (!SnapIO::touch(DAWSConstants::$PROCESS_CANCEL_FILEPATH)) {
                throw new Exception("Couldn't update time on " . DAWSConstants::$PROCESS_CANCEL_FILEPATH);
            }
            $retVal->pass = true;
        } else {
            throw new Exception('Unknown command.');
        }
        session_write_close();

        return $retVal;
    }

    private function getStatus($state)
    {
        /* @var $state DupArchiveStateBase */

        $ret_val                 = new stdClass();
        $ret_val->archive_offset = $state->archiveOffset;
        $ret_val->archive_size   = @filesize($state->archivePath);
        $ret_val->failures       = $state->failures;
        $ret_val->file_index     = $state->fileWriteCount;
        $ret_val->is_done        = !$state->working;
        $ret_val->timestamp      = time();

        return $ret_val;
    }
}
