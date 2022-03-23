<?php

/**
 * Validation object
 *
 * Standard: PSR-2
 * @link http://www.php-fig.org/psr/psr-2 Full Documentation
 *
 * @package SC\DUPX\U
 *
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Installer\Core\Params\PrmMng;
use Duplicator\Libs\Snap\SnapIO;

class DUPX_Validation_test_iswritable extends DUPX_Validation_abstract_item
{
    /**
     *
     * @var string
     */
    protected $faildDirPerms = array();

    protected function runTest()
    {
        $this->faildDirPerms = $this->checkWritePermissions();
        if (empty($this->faildDirPerms)) {
            return self::LV_PASS;
        } else {
            if (DUPX_InstallerState::isRecoveryMode() || DUPX_Custom_Host_Manager::getInstance()->isManaged()) {
                return self::LV_SOFT_WARNING;
            } else {
                return self::LV_HARD_WARNING;
            }
        }
    }

    protected function checkWritePermissions()
    {
        $failResult    = array();
        $dirFiles      = DUPX_Package::getDirsListPath();
        $archiveConfig = DUPX_ArchiveConfig::getInstance();

        if (($handle = fopen($dirFiles, "r")) === false) {
            throw new Exception('Can\'t open dirs file list');
        }

        while (($line = fgets($handle)) !== false) {
            if (($info = json_decode($line)) === null) {
                throw new Exception('Invalid json line in dirs file: ' . $line);
            }
            $destPath = $archiveConfig->destFileFromArchiveName($info->p);
            if (file_exists($destPath) && !SnapIO::dirAddFullPermsAndCheckResult($destPath)) {
                $failResult[] = $destPath;
            }
        }

        fclose($handle);
        return $failResult;
    }

    public function getTitle()
    {
        return 'Permissions';
    }

    protected function hwarnContent()
    {
        $result = dupxTplRender('parts/validation/tests/is-writable/info', array(), false);
        $result .= dupxTplRender('parts/validation/tests/is-writable/failed-objects', array(
            'failedObjects' => $this->faildDirPerms
            ), false);

        return $result;
    }

    protected function swarnContent()
    {
        return $this->hwarnContent();
    }

    protected function passContent()
    {
        return dupxTplRender('parts/validation/tests/is-writable/info', array(), false);
    }
}
