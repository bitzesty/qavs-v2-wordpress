<?php
/**
 *
 * @package Duplicator
 * @copyright (c) 2021, Snapcreek LLC
 *
 */
defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Libs\Snap\SnapIO;

require_once(dirname(__FILE__).'/../util/class.duparchive.util.php');
require_once(dirname(__FILE__).'/class.duparchive.header.u.php');
require_once(dirname(__FILE__).'/../../define.php');

if(!class_exists('DupArchiveHeader')) {
//require_once(dirname(__FILE__).'/class.HeaderBase.php');
// Format: #A#{version:5}#{isCompressed}!
class DupArchiveHeader// extends HeaderBase
{
    public $version;
    public $isCompressed;

    //   public $directoryCount;
    // public $fileCount;

    // Format Version History
    // 1 = Initial alpha format
    // 2 = Pseudo xml based format
    //const LatestVersion = 2;
    const MaxHeaderSize = 60;

    private function __construct()
    {
        // Prevent instantiation
    }

  //  public static function create($isCompressed, $directoryCount, $fileCount, $version = self::LatestVersion)
    public static function create($isCompressed)
    {
        $instance = new DupArchiveHeader();

   //     $instance->directoryCount = $directoryCount;
        //  $instance->fileCount      = $fileCount;
        $instance->version        = DUPARCHIVE_VERSION;
        $instance->isCompressed   = $isCompressed;

        return $instance;
    }

    public static function readFromArchive($archiveHandle)
    {
        $instance = new DupArchiveHeader();

        $startElement = fgets($archiveHandle, 4);

        if ($startElement != '<A>') {
            throw new Exception("Invalid archive header marker found {$startElement}");
        }

        $instance->version           = DupArchiveHeaderU::readStandardHeaderField($archiveHandle, 'V');
        $instance->isCompressed      = DupArchiveHeaderU::readStandardHeaderField($archiveHandle, 'C') == 'true' ? true : false;

        // Skip the </A>
        fgets($archiveHandle, 5);

        return $instance;
    }

    public function writeToArchive($archiveHandle)
    {
        $isCompressedString = DupArchiveUtil::boolToString($this->isCompressed);

        //SnapIO::fwrite($archiveHandle, "<A><V>{$this->version}</V><C>{$isCompressedString}</C></A>");
		SnapIO::fwrite($archiveHandle, '<A><V>'.$this->version.'</V><C>'.$isCompressedString.'</C></A>');
    }
}
}