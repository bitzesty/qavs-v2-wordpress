<?php

/**
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Libs\Snap\SnapIO;

require_once(dirname(__FILE__).'/../headers/class.duparchive.header.directory.php');

if(!class_exists('DupArchiveDirectoryProcessor')) {
class DupArchiveDirectoryProcessor
{
	public static function writeDirectoryToArchive($createState, $archiveHandle, $sourceDirectoryPath, $relativeDirectoryPath)
	{
		/* @var $createState DupArchiveCreateState */

		$directoryHeader = new DupArchiveDirectoryHeader();

		$directoryHeader->permissions        = substr(sprintf('%o', fileperms($sourceDirectoryPath)), -4);
		$directoryHeader->mtime              = SnapIO::filemtime($sourceDirectoryPath);
		$directoryHeader->relativePath       = $relativeDirectoryPath;
		$directoryHeader->relativePathLength = strlen($directoryHeader->relativePath);

		$directoryHeader->writeToArchive($archiveHandle);

		// Just increment this here - the actual state save is on the outside after timeout or completion of all directories
		$createState->currentDirectoryIndex++;

	}
}
}