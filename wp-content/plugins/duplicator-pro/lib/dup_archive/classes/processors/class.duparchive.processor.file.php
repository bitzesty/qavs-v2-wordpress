<?php

/**
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Libs\Snap\Snap32BitSizeLimitException;
use Duplicator\Libs\Snap\SnapIO;

require_once(dirname(__FILE__).'/../headers/class.duparchive.header.file.php');
require_once(dirname(__FILE__).'/../headers/class.duparchive.header.glob.php');

if (!class_exists('DupArchiveFileProcessor')) {

    class DupArchiveFileProcessor
    {

        protected static $newFilePathCallback = null;

        public static function setNewFilePathCallback($callback)
        {
            if (!is_callable($callback)) {
                self::$newFilePathCallback = null;
                return false;
            }

            self::$newFilePathCallback = $callback;
            return true;
        }

        protected static function getNewFilePath($basePath, $relativePath)
        {
            if (is_null(self::$newFilePathCallback)) {
                return $basePath.'/'.$relativePath;
            } else {
                return call_user_func_array(self::$newFilePathCallback, array($relativePath));
            }
        }

        public static function writeFilePortionToArchive($createState, $archiveHandle, $sourceFilepath, $relativeFilePath)
        {
            /* @var $createState DupArchiveCreateState */

            DupArchiveUtil::tlog("writeFileToArchive for {$sourceFilepath}");

            // profile ok
            // switching to straight call for speed
            $sourceHandle = @fopen($sourceFilepath, 'rb');

            // end profile ok

            if (!is_resource($sourceHandle)) {
                $createState->archiveOffset     = SnapIO::ftell($archiveHandle);
                $createState->currentFileIndex++;
                $createState->currentFileOffset = 0;
                $createState->skippedFileCount++;
                $createState->addFailure(DupArchiveFailureTypes::File, $sourceFilepath, "Couldn't open $sourceFilepath", false);

                return;
            }

            if ($createState->currentFileOffset > 0) {
                SnapIO::fseek($sourceHandle, $createState->currentFileOffset);
            } else {
                $fileHeader = DupArchiveFileHeader::createFromFile($sourceFilepath, $relativeFilePath);
                $fileHeader->writeToArchive($archiveHandle);
            }

            // profile ok
            $sourceFileSize = filesize($sourceFilepath);

            $moreFileDataToProcess = true;

            while ((!$createState->timedOut()) && $moreFileDataToProcess) {

                if ($createState->throttleDelayInUs !== 0) {
                    usleep($createState->throttleDelayInUs);
                }

                // profile ok
                $moreFileDataToProcess = self::appendGlobToArchive($createState, $archiveHandle, $sourceHandle, $sourceFilepath, $sourceFileSize);
                // end profile ok
                // profile ok
                if ($moreFileDataToProcess) {
                    $createState->currentFileOffset += $createState->globSize;
                    $createState->archiveOffset     = SnapIO::ftell($archiveHandle); //??
                } else {
                    $createState->archiveOffset     = SnapIO::ftell($archiveHandle);
                    $createState->currentFileIndex++;
                    $createState->currentFileOffset = 0;
                }

                // Only writing state after full group of files have been written - less reliable but more efficient
                // $createState->save();
            }

            // profile ok
            SnapIO::fclose($sourceHandle);
            // end profile ok
        }

        /**
         * Assumption is that this is called at the beginning of a glob header since file header already writtern
         *
         * @param $expandState DupArchiveExpandState
         * @param $archiveHandle
         * @return bool true on success
         * @throws Snap32BitSizeLimitException
         */
        public static function writeToFile($expandState, $archiveHandle)
        {
            $destFilepath = self::getNewFilePath($expandState->basePath, $expandState->currentFileHeader->relativePath);
            $parentDir    = dirname($destFilepath);

            $moreGlobstoProcess = true;

            SnapIO::dirWriteCheckOrMkdir($parentDir, 'u+rwx', true);

            if ($expandState->currentFileHeader->fileSize > 0) {

                if ($expandState->currentFileOffset > 0) {
                    $destFileHandle = SnapIO::fopen($destFilepath, 'r+b');
                    SnapIO::fseek($destFileHandle, $expandState->currentFileOffset);
                } else {
                    $destFileHandle = SnapIO::fopen($destFilepath, 'w+b');
                }

                while (!$expandState->timedOut()) {
                    $moreGlobstoProcess = $expandState->currentFileOffset < $expandState->currentFileHeader->fileSize;

                    if ($moreGlobstoProcess) {
                        if ($expandState->throttleDelayInUs !== 0) {
                            usleep($expandState->throttleDelayInUs);
                        }

                        self::appendGlobToFile($expandState, $archiveHandle, $destFileHandle, $destFilepath);

                        $expandState->currentFileOffset = ftell($destFileHandle);
                        $expandState->archiveOffset     = SnapIO::ftell($archiveHandle);

                        $moreGlobstoProcess = $expandState->currentFileOffset < $expandState->currentFileHeader->fileSize;

                        if (!$moreGlobstoProcess) {

                            break;
                        }
                    } else {
                        // rsr todo record fclose error
                        @fclose($destFileHandle);
                        $destFileHandle = null;

                        if ($expandState->validationType == DupArchiveValidationTypes::Full) {
                            self::validateExpandedFile($expandState);
                        }
                        break;
                    }
                }

                DupArchiveUtil::tlog('Out of glob loop');

                if ($destFileHandle != null) {
                    // rsr todo record file close error
                    @fclose($destFileHandle);
                    $destFileHandle = null;
                }

                if (!$moreGlobstoProcess && $expandState->validateOnly && ($expandState->validationType == DupArchiveValidationTypes::Full)) {
                    if (!is_writable($destFilepath)) {
                        SnapIO::chmod($destFilepath, 'u+rw');
                    }
                    if (@unlink($destFilepath) === false) {
                        //      $expandState->addFailure(DupArchiveFailureTypes::File, $destFilepath, "Couldn't delete {$destFilepath} during validation", false);
                        // TODO: Have to know how to handle this - want to report it but don’t want to mess up validation - some non critical errors could be important to validation
                    }
                }
            } else {
                // 0 length file so just touch it
                $moreGlobstoProcess = false;

                if (file_exists($destFilepath)) {
                    @unlink($destFilepath);
                }

                if (touch($destFilepath) === false) {
                    throw new Exception("Couldn't create {$destFilepath}");
                }
            }

            if (!$moreGlobstoProcess) {
                self::setFileMode($expandState, $destFilepath);

                DupArchiveUtil::tlog('No more globs to process');

                if ((!$expandState->validateOnly) && (isset($expandState->fileRenames[$expandState->currentFileHeader->relativePath]))) {
                    $newRelativePath = $expandState->fileRenames[$expandState->currentFileHeader->relativePath];
                    $newFilepath     = self::getNewFilePath($expandState->basePath, $newRelativePath);

                    $perform_rename = true;

                    if (@file_exists($newFilepath)) {
                        if (@unlink($newFilepath) === false) {

                            $perform_rename = false;

                            $error_message = "Couldn't delete {$newFilepath} when trying to rename {$destFilepath}";

                            $expandState->addFailure(DupArchiveFailureTypes::File, $expandState->currentFileHeader->relativePath, $error_message, true);
                            DupArchiveUtil::tlog($error_message);
                        }
                    }

                    if ($perform_rename && @rename($destFilepath, $newFilepath) === false) {

                        $error_message = "Couldn't rename {$destFilepath} to {$newFilepath}";

                        $expandState->addFailure(DupArchiveFailureTypes::File, $expandState->currentFileHeader->relativePath, $error_message, true);
                        DupArchiveUtil::tlog($error_message);
                    }
                }

                $expandState->fileWriteCount++;
                $expandState->resetForFile();
            }

            return !$moreGlobstoProcess;
        }

        /**
         * 
         * @param DupArchiveExpandState $expandState
         * @param DupArchiveDirectoryHeader $directoryHeader
         * @return boolean
         */
        public static function createDirectory($expandState, $directoryHeader)
        {
            /* @var $expandState DupArchiveExpandState */
            $destDirPath = self::getNewFilePath($expandState->basePath, $directoryHeader->relativePath);

            $mode = $directoryHeader->permissions;

            if ($expandState->directoryModeOverride != -1) {
                $mode = $expandState->directoryModeOverride;
            }

            if (!SnapIO::dirWriteCheckOrMkdir($destDirPath, $mode, true)) {
                $error_message = "Unable to create directory $destDirPath";
                $expandState->addFailure(DupArchiveFailureTypes::Directory, $directoryHeader->relativePath, $error_message, false);
                DupArchiveUtil::tlog($error_message);
                return false;
            } else {
                return true;
            }
        }

        public static function setFileMode($expandState, $filePath)
        {
            if ($expandState->fileModeOverride === -1) {
                return;
            }
            SnapIO::chmod($filePath, $expandState->fileModeOverride);
        }

        public static function standardValidateFileEntry(&$expandState, $archiveHandle)
        {
            /* @var $expandState DupArchiveExpandState */

            $moreGlobstoProcess = $expandState->currentFileOffset < $expandState->currentFileHeader->fileSize;

            if (!$moreGlobstoProcess) {

                // Not a 'real' write but indicates that we actually did fully process a file in the archive
                $expandState->fileWriteCount++;
            } else {

                while ((!$expandState->timedOut()) && $moreGlobstoProcess) {

                    // Read in the glob header but leave the pointer at the payload
                    // profile ok
                    $globHeader = DupArchiveGlobHeader::readFromArchive($archiveHandle, false);

                    // profile ok
                    $globContents = fread($archiveHandle, $globHeader->storedSize);

                    if ($globContents === false) {
                        throw new Exception("Error reading glob from $destFilePath");
                    }

                    $hash = hash('crc32b', $globContents);

                    if ($hash != $globHeader->hash) {
                        $expandState->addFailure(DupArchiveFailureTypes::File, $expandState->currentFileHeader->relativePath, 'Hash mismatch on DupArchive file entry', true);
                        DupArchiveUtil::tlog("Glob hash mismatch during standard check of {$expandState->currentFileHeader->relativePath}");
                    } else {
                        //    DupArchiveUtil::tlog("Glob MD5 passes");
                    }

                    $expandState->currentFileOffset += $globHeader->originalSize;

                    // profile ok
                    $expandState->archiveOffset = SnapIO::ftell($archiveHandle);


                    $moreGlobstoProcess = $expandState->currentFileOffset < $expandState->currentFileHeader->fileSize;

                    if (!$moreGlobstoProcess) {


                        $expandState->fileWriteCount++;

                        // profile ok
                        $expandState->resetForFile();
                    }
                }
            }

            return !$moreGlobstoProcess;
        }

        private static function validateExpandedFile(&$expandState)
        {
            /* @var $expandState DupArchiveExpandState */
            $destFilepath = self::getNewFilePath($expandState->basePath, $expandState->currentFileHeader->relativePath);

            if ($expandState->currentFileHeader->hash !== '00000000000000000000000000000000') {

                $hash = hash_file('crc32b', $destFilepath);

                if ($hash !== $expandState->currentFileHeader->hash) {
                    $expandState->addFailure(DupArchiveFailureTypes::File, $destFilepath, "MD5 mismatch for {$destFilepath}", false);
                } else {
                    DupArchiveUtil::tlog('MD5 Match for '.$destFilepath);
                }
            } else {
                DupArchiveUtil::tlog('MD5 non match is 0\'s');
            }
        }

        private static function appendGlobToArchive($createState, $archiveHandle, $sourceFilehandle, $sourceFilepath, $fileSize)
        {
            DupArchiveUtil::tlog("Appending file glob to archive for file {$sourceFilepath} at file offset {$createState->currentFileOffset}");

            if ($fileSize > 0) {
                $fileSize -= $createState->currentFileOffset;

                // profile ok
                $globContents = @fread($sourceFilehandle, $createState->globSize);
                // end profile ok

                if ($globContents === false) {
                    throw new Exception("Error reading $sourceFilepath");
                }

                // profile ok
                $originalSize = strlen($globContents);
                // end profile ok

                if ($createState->isCompressed) {
                    // profile ok
                    $globContents = gzdeflate($globContents, 2);    // 2 chosen as best compromise between speed and size
                    $storeSize    = strlen($globContents);
                    // end profile ok
                } else {
                    $storeSize = $originalSize;
                }


                $globHeader = new DupArchiveGlobHeader();

                $globHeader->originalSize = $originalSize;
                $globHeader->storedSize   = $storeSize;
                $globHeader->hash         = hash('crc32b', $globContents);

                // profile ok
                $globHeader->writeToArchive($archiveHandle);
                // end profile ok
                // profile ok
                if (@fwrite($archiveHandle, $globContents) === false) {
                    // Considered fatal since we should always be able to write to the archive - plus the header has already been written (could back this out later though)
                    throw new Exception("Error writing $sourceFilepath to archive. Ensure site still hasn't run out of space.", DupArchiveExceptionCodes::Fatal);
                }
                // end profile ok

                $fileSizeRemaining = $fileSize - $createState->globSize;

                $moreFileRemaining = $fileSizeRemaining > 0;

                return $moreFileRemaining;
            } else {
                // 0 Length file
                return false;
            }
        }

        // Assumption is that archive handle points to a glob header on this call
        private static function appendGlobToFile($expandState, $archiveHandle, $destFileHandle, $destFilePath)
        {
            /* @var $expandState DupArchiveExpandState */
            DupArchiveUtil::tlog('Appending file glob to file '.$destFilePath.' at file offset '.$expandState->currentFileOffset);

            // Read in the glob header but leave the pointer at the payload
            $globHeader = DupArchiveGlobHeader::readFromArchive($archiveHandle, false);

            $globContents = @fread($archiveHandle, $globHeader->storedSize);

            if ($globContents === false) {
                throw new Exception("Error reading glob from $destFilePath");
            }

            if ($expandState->isCompressed) {
                $globContents = gzinflate($globContents);
            }

            if (@fwrite($destFileHandle, $globContents) === false) {
                throw new Exception("Error writing glob to $destFilePath");
            } else {
                DupArchiveUtil::tlog('Successfully wrote glob');
            }
        }
    }
}