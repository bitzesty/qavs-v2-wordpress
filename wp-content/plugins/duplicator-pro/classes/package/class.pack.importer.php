<?php

/**
 * Class to import archive
 *
 * Standard: PSR-2 (almost)
 * @link http://www.php-fig.org/psr/psr-2
 *
 * @package DUP_PRO
 * @subpackage classes/package
 * @copyright (c) 2017, Snapcreek LLC
 * @license https://opensource.org/licenses/GPL-3.0 GNU Public License
 *
 */

defined('ABSPATH') || defined('DUPXABSPATH') || exit;

use Duplicator\Libs\Snap\SnapIO;
use Duplicator\Libs\Snap\SnapJson;
use Duplicator\Addons\ProBase\License\License;
use Duplicator\Installer\Core\Params\PrmMng;

class DUP_PRO_Package_Importer
{
    const IMPORT_ENABLE_MIN_VERSION = '4.0.0';

    /**
     *
     * @var string
     */
    protected $archive = null;

    /**
     *
     * @var string
     */
    protected $ext = null;

    /**
     *
     * @var bool
     */
    protected $isValid = false;

    /**
     *
     * @var string
     */
    protected $notValidMessage = '';

    /**
     *
     * @var object
     */
    protected $info = null;

    /**
     * @var object
     */
    protected $scan = null;

    /**
     *
     * @var string
     */
    protected $nameHash = null;

    /**
     *
     * @var string
     */
    protected $hash = null;

    /**
     *
     * @param string $path // valid archive patch
     * @throws Exception if file ins't valid
     */
    public function __construct($path)
    {
        if (!is_file($path)) {
            throw new Exception('Archive path "' . $path . '" is invalid');
        }

        SnapIO::chmod($path, 'u+rw');
        if (!is_readable($path)) {
            throw new Exception('Can\'t read the archive "' . $path . '"');
        }

        if (!preg_match(DUPLICATOR_PRO_ARCHIVE_REGEX_PATTERN, basename($path))) {
            throw new Exception('Invalid archive name "' . $path . '"');
        }

        $this->archive  = $path;
        $this->ext      = pathinfo($this->archive, PATHINFO_EXTENSION);
        $this->hash     = self::getHashFromArchiveName($this->archive);
        $this->nameHash = self::getNameHashFromArchiveName($this->archive);

        $this->initInfoObjects();
    }

    /**
     * This function extract a single file from archive and put it in target folder.
     * If the file is in a subfolder it is put in a subfolder in the target folder
     * Es. file = subfolder/file.txt is extracted in /target/folder/subsolder/file.txt
     *
     * @param string $file         file relative path
     * @param string $targetFolder target folder
     *
     * @return string extracted file fullpath
     *
     * @throws Exception if the extraction fails
     */
    protected function extractSingleFile($file, $targetFolder)
    {

        $targetFile = SnapIO::trailingslashit($targetFolder) . $file;

        switch ($this->ext) {
            case 'zip':
                if (!class_exists('ZipArchive', false)) {
                    throw new Exception(DUP_PRO_U::__('ZipArchive PHP module is not installed/enabled. The current package cannot be opened.'));
                }

                $zip = new ZipArchive();
                if ($zip->open($this->archive) !== true) {
                    throw new Exception('Can\'t open ZipArcive ' . $this->archive);
                }
                if (($fileContent = $zip->getFromName($file)) === false) {
                    $zip->close();
                    throw new Exception('Can\'t get file ' . $file . ' from archive ' . $this->archive);
                }
                $zip->close();

                if (SnapIO::mkdirP(dirname($targetFile)) === false) {
                    throw new Exception('Can\'t create file content folder ' . dirname($targetFile));
                }

                if (file_put_contents($targetFile, $fileContent) === false) {
                    throw new Exception('Can\'t create file ' . $targetFile);
                }
                break;
            case 'daf':
                DupArchiveEngine::expandFiles($this->archive, array($file), $targetFolder);
                break;
            default:
                throw new Exception('Invalid archive extension "' . $this->ext . '"');
        }

        if (!file_exists($targetFile)) {
            throw new Exception('Can\'t extract file ' . $targetFile . ' from archive');
        }

        return $targetFile;
    }

    /**
     * This function extract archive info package and read it, After initializing the information deletes the file.
     *
     * @return void
     */
    protected function initInfoObjects()
    {
        try {
            $this->info    = $this->getObjectFromJson('dup-installer/dup-archive__' . $this->hash . '.txt');
            $this->scan    = $this->getObjectFromJson('dup-installer/dup-scan__' . $this->hash . '.json');
            $this->isValid = true;
        } catch (Exception $ex) {
            DUP_PRO_Log::trace("Couldn't initialize the info object: " . $ex->getMessage());
            $this->notValidMessage = $ex->getMessage();
            $this->isValid         = false;
        }
    }

    /**
     * @param string $relativePathInArchive Relative path to the file contains the json in the archive
     * @return object The decoded json object
     * @throws Exception
     */
    protected function getObjectFromJson($relativePathInArchive)
    {
        $tempArchiveJson = $this->extractSingleFile($relativePathInArchive, DUPLICATOR_PRO_SSDIR_PATH_TMP_IMPORT);

        if (($jsonContent = file_get_contents($tempArchiveJson)) === false) {
            throw new Exception('Can\'t read tmp json file ' . $relativePathInArchive);
        }
        unlink($tempArchiveJson);

        if (($result = json_decode($jsonContent)) === false) {
            throw new Exception('Can\'t decode scan json ' . $relativePathInArchive);
        }

        return $result;
    }

    /**
     * return admin installer page ling with right query string
     *
     * @return string
     */
    public function getInstallerPageLink()
    {
        if (is_multisite()) {
            $url = network_admin_url('admin.php');
        } else {
            $url = admin_url('admin.php');
        }

        $queryStr = http_build_query(array(
            'page'    => DUP_PRO_Constants::$IMPORT_INSTALLER_PAGE,
            'package' => $this->archive
        ));
        return $url . '?' . $queryStr;
    }

    /**
     * Return true if path have a import sub path
     *
     * @param string $path archive path
     *
     * @return boolean
     */
    public static function isImportPath($path)
    {
        return (preg_match('/[\/]' . preg_quote(DUPLICATOR_PRO_SSDIR_NAME, '/') . '[\/]' . preg_quote(DUPLICATOR_PRO_IMPORTS_DIR_NAME, '/') . '[\/]/', $path) === 1);
    }

    /**
     *
     * @param bool $removeArchive if true remove all or exclude archives
     * @return bool
     */
    public static function cleanImportFolder($removeArchive = false)
    {
        SnapIO::regexGlobCallback(DUPLICATOR_PRO_PATH_IMPORTS, array('\\Duplicator\\Libs\\Snap\\SnapIO', 'rrmdir'), array(
            'regexFile'   => ($removeArchive ? false : DUPLICATOR_PRO_ARCHIVE_REGEX_PATTERN),
            'regexFolder' => false,
            'invert'      => true,
        ));
        return true;
    }

    /**
     * This function prepares the installer execution by extracting the installer-backup.php file and creating the overwrite parameter file
     *
     * @return string // installer.php link with right params.
     * @throws Exception
     */
    public function prepareToInstall()
    {
        $failMessage = '';
        self::cleanImportFolder();
        if (!$this->isImportable($failMessage)) {
            throw new Exception($failMessage);
        }

        $this->createOverwriteParams();
        $installerLink = $this->extractInstallerBackup();

        return $installerLink;
    }

    /**
     * Return installer folder path
     *
     * @return string
     */
    protected function getInstallerFolderPath()
    {
        return DUPLICATOR_PRO_PATH_IMPORTS;
    }

    /**
     * Return installer filder url
     *
     * @return string
     */
    protected function getInstallerFolderUrl()
    {
        return DUPLICATOR_PRO_URL_IMPORTS;
    }

    /**
     * Return installer name
     *
     * @return string
     */
    protected function getInstallerName()
    {
        $pathInfo = pathinfo($this->info->installer_backup_name);
        if (!isset($pathInfo['extension']) || $pathInfo['extension'] !== 'php') {
            return $pathInfo['filename'] . '.php';
        }
        return $this->info->installer_backup_name;
    }

    /**
     * extract installer-backup.php file in import folder
     *
     * @return string // return installer import URL
     * @throws Exception
     */
    protected function extractInstallerBackup()
    {
        $this->extractSingleFile($this->info->installer_backup_name, $this->getInstallerFolderPath());
        if ($this->info->installer_backup_name !== $this->getInstallerName()) {
            SnapIO::rename($this->getInstallerFolderPath() . '/' . $this->info->installer_backup_name, $this->getInstallerFolderPath() . '/' . $this->getInstallerName());
        }
        return $this->getInstallLink();
    }

    /**
     * Return installer link
     *
     * @return string
     */
    public function getInstallLink()
    {
        $queryStr = http_build_query(array(
            'archive'    => $this->archive,
            'dup_folder' => 'dup-installer-' . $this->info->packInfo->secondaryHash
        ));
        return $this->getInstallerFolderUrl() . '/' . $this->getInstallerName() . '?' . $queryStr;
    }

    /**
     * Return overwrite param for import
     *
     * @return array
     */
    protected function getOverwriteParams()
    {
        global $wpdb;
        global $wp_version;

        if (DUP_PRO_Package_Recover::getRecoverPackageId() !== false) {
            $recoverPackage     = DUP_PRO_Package_Recover::getRecoverPackage();
            $recoverLink        = $recoverPackage->getInstallLink();
            $packageIsOutToDate = $recoverPackage->isOutToDate();
            $packageLife        = $recoverPackage->getPackageLife();
        } else {
            $recoverLink        = '';
            $packageIsOutToDate = true;
            $packageLife        = 0;
        }

        $currentUser = wp_get_current_user();
        $updDirs     = wp_upload_dir();
        $params      = array(
            /* PrmMng::PARAM_DEBUG_PARAMS        => array(
              'value' => true
              ), */
            PrmMng::PARAM_TEMPLATE                    => array(
                'value' => 'import-base',
            ),
            PrmMng::PARAM_VALIDATION_ACTION_ON_START  => array(
                'value' => 'auto',
            ),
            PrmMng::PARAM_RECOVERY_LINK               => array(
                'value' => $recoverLink,
            ),
            PrmMng::PARAM_FROM_SITE_IMPORT_INFO       => array(
                'value' => array(
                    'import_page'             => DUP_PRO_CTRL_import::getImportPageLink(),
                    'recovery_page'           => DUP_PRO_CTRL_recovery::getRecoverPageLink(),
                    'recovery_is_out_to_date' => $packageIsOutToDate,
                    'recovery_package_life'   => $packageLife,
                    /** @deprecated not used on 4.0.3, remove on 4.0.5 * */
                    'color-scheme'            => DUP_PRO_UI_Screen::getCurrentColorScheme(),
                    'color-primary-button'    => DUP_PRO_UI_Screen::getPrimaryButtonColorByScheme()
                )
            ),
            PrmMng::PARAM_DB_DISPLAY_OVERWIRE_WARNING => array(
                'value' => false,
            ),
            PrmMng::PARAM_CPNL_CAN_SELECTED           => array(
                'value' => false,
            ),
            PrmMng::PARAM_DB_VIEW_MODE                => array(
                'value' => 'basic',
            ),
            PrmMng::PARAM_URL_NEW                     => array(
                'value'      => DUP_PRO_Archive::getOriginalUrls('home'),
                'formStatus' => 'st_infoonly'
            ),
            PrmMng::PARAM_PATH_NEW                    => array(
                'value'      => DUP_PRO_Archive::getOriginalPaths('home'),
                'formStatus' => 'st_infoonly'
            ),
            PrmMng::PARAM_ARCHIVE_ACTION              => array(),
            PrmMng::PARAM_ARCHIVE_ENGINE              => array(),
            PrmMng::PARAM_DB_ACTION                   => array(
                'value' => 'empty'
            ),
            PrmMng::PARAM_DB_HOST                     => array(
                'value'      => DB_HOST,
                'formStatus' => 'st_infoonly'
            ),
            PrmMng::PARAM_DB_NAME                     => array(
                'value'      => DB_NAME,
                'formStatus' => 'st_infoonly'
            ),
            PrmMng::PARAM_DB_USER                     => array(
                'value'      => DB_USER,
                'formStatus' => 'st_infoonly'
            ),
            PrmMng::PARAM_DB_PASS                     => array(
                'value'      => DB_PASSWORD,
                'formStatus' => 'st_infoonly'
            ),
            PrmMng::PARAM_DB_CHARSET                  => array(
                'value' => DB_CHARSET
            ),
            PrmMng::PARAM_DB_COLLATE                  => array(
                'value' => DB_COLLATE
            ),
            PrmMng::PARAM_OVERWRITE_SITE_DATA         => array(
                'value' => array(
                    'dupVersion'      => DUPLICATOR_PRO_VERSION,
                    'wpVersion'       => $wp_version,
                    'dupLicense'      => License::getType(),
                    'loggedUser'      => array(
                        'id'         => $currentUser->ID,
                        'user_login' => $currentUser->user_login
                    ),
                    'dbhost'          => DB_HOST,
                    'dbname'          => DB_NAME,
                    'dbuser'          => DB_USER,
                    'dbpass'          => DB_PASSWORD,
                    'table_prefix'    => $wpdb->base_prefix,
                    'restUrl'         => function_exists('get_rest_url') ? get_rest_url() : '',
                    'restNonce'       => wp_create_nonce('wp_rest'),
                    'muImportEnabled' => \DUP_PRO_Global_Entity::get_instance()->betaMUimport,
                    'isMultisite'     => is_multisite(),
                    'subdomain'       => (defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL),
                    'subsites'        => DUP_PRO_MU::getSubsites(),
                    'adminUsers'      => DUP_PRO_WP_U::getAdminUserLists(),
                    'paths'           => DUP_PRO_Archive::getOriginalPaths(),
                    'urls'            => DUP_PRO_Archive::getOriginalUrls()
                )
            )
        );

        // if is manage hosting overwrite url and paths
        if (DUP_PRO_Custom_Host_Manager::getInstance()->isManaged()) {
            $urlPathParams = array(
                PrmMng::PARAM_SITE_URL           => array(
                    'value'      => site_url(),
                    'formStatus' => 'st_infoonly'
                ),
                PrmMng::PARAM_PATH_WP_CORE_NEW   => array(
                    'value'      => DUP_PRO_Archive::getOriginalPaths('abs'),
                    'formStatus' => 'st_infoonly'
                ),
                PrmMng::PARAM_URL_CONTENT_NEW    => array(
                    'value'      => content_url(),
                    'formStatus' => 'st_infoonly'
                ),
                PrmMng::PARAM_PATH_CONTENT_NEW   => array(
                    'value'      => DUP_PRO_Archive::getOriginalPaths('wpcontent'),
                    'formStatus' => 'st_infoonly'
                ),
                PrmMng::PARAM_URL_UPLOADS_NEW    => array(
                    'value'      => $updDirs['baseurl'],
                    'formStatus' => 'st_infoonly'
                ),
                PrmMng::PARAM_PATH_UPLOADS_NEW   => array(
                    'value'      => DUP_PRO_Archive::getOriginalPaths('uploads'),
                    'formStatus' => 'st_infoonly'
                ),
                PrmMng::PARAM_URL_PLUGINS_NEW    => array(
                    'value'      => plugins_url(),
                    'formStatus' => 'st_infoonly'
                ),
                PrmMng::PARAM_PATH_PLUGINS_NEW   => array(
                    'value'      => DUP_PRO_Archive::getOriginalPaths('plugins'),
                    'formStatus' => 'st_infoonly'
                ),
                PrmMng::PARAM_URL_MUPLUGINS_NEW  => array(
                    'value'      => WPMU_PLUGIN_URL,
                    'formStatus' => 'st_infoonly'
                ),
                PrmMng::PARAM_PATH_MUPLUGINS_NEW => array(
                    'value'      => DUP_PRO_Archive::getOriginalPaths('muplugins'),
                    'formStatus' => 'st_infoonly'
                )
            );

            $params = array_merge($params, $urlPathParams);
        }
        return $params;
    }

    /**
     * This function creates the parameter overwriting file
     *
     * @return boolean // return true on success
     *
     * @throws Exception if fail
     */
    protected function createOverwriteParams()
    {
        $overwriteFile = $this->getInstallerFolderPath() . '/' . DUPLICATOR_PRO_LOCAL_OVERWRITE_PARAMS . '_' . $this->hash . '.json';

        $params = $this->getOverwriteParams();

        if (file_put_contents($overwriteFile, SnapJson::jsonEncodePPrint($params)) === false) {
            throw new Exception('Can\'t create overwrite param file');
        }

        return true;
    }

    /**
     * this function check if package is importable
     *
     * @param string $failMessage message if isn't importable
     *
     * @return boolean
     */
    public function isImportable(&$failMessage = null)
    {
        if (!$this->isValid) {
            $failMessage = DUP_PRO_U::__('The package information can\'t be read.') . "<br>\n";
            $failMessage .= sprintf(DUP_PRO_U::__('Error: %s'), $this->notValidMessage);

            if (!class_exists('ZipArchive', false)) {
                $failMessage .= sprintf(
                    " %s <a href='https://snapcreek.com/duplicator/docs/faqs-tech/#faq-trouble-060-q' target='_blank'>%s</a>",
                    DUP_PRO_U::__('For more information see'),
                    DUP_PRO_U::__("[this FAQ item]")
                );
            }
            return false;
        }

        if (version_compare($this->getDupVersion(), self::IMPORT_ENABLE_MIN_VERSION, '<')) {
            $failMessage = sprintf(DUP_PRO_U::__('Package is incompatible or too old. Only packages created with Duplicator Pro v%s or higher can be imported.<br/> ' .
                    'If you want to install this package then please use the "classic installer.php" overwrite method ' .
                    '<a target="_blank" href="https://snapcreek.com/duplicator/docs/quick-start/#quick-040-q">explained here</a>.'), self::IMPORT_ENABLE_MIN_VERSION);
            return false;
        }

        if ($this->info->exportOnlyDB) {
            $failMessage = DUP_PRO_U::__('Database only packages are currently not supported.');
            return false;
        }

        //The scan logic is going to be refactored, so only use info from the scan.json, if it's too complex to use the
        // archive config info
        if ($this->scan->ARC->Status->HasFilteredCoreFolders) {
            $failMessage = DUP_PRO_U::__('The package is missing WordPress core folder(s)! It must include wp-admin, wp-content, wp-includes, uploads, plugins, and themes folders.');
            return false;
        }

        if ($this->info->mu_mode === 0) {
            if ($this->info->dbInfo->tablesBaseCount != $this->info->dbInfo->tablesFinalCount) {
                $failMessage = DUP_PRO_U::__('The package is missing some of the site tables.');
                return false;
            }
        } else {
            if (!DUP_PRO_Import_U::hasImportableSite($this->info->subsites)) {
                $failMessage = DUP_PRO_U::__('This package of a multisite installation does not contain an importable subsite.');
                return false;
            }
        }

        if (!$this->packageHasRequiredInstallerFiles()) {
            $failMessage = DUP_PRO_U::__('The package lacks some of the installer files.');
            return false;
        }

        $failMessage = '';
        return true;
    }


    /**
     * Check if passed values are ok for import
     *
     * @param bool   $dbOnly               package db only value
     * @param bool   $archiveFilterOn      is archive filter enabled
     * @param string $archiveFilterDirs    string of filter dirs
     * @param bool   $databaseFilterOn     is tables filter enabled
     * @param string $databaseFilterTables string of filter tables
     * @param array  $filteredData         reference value where set filter data
     *
     * @return boolean
     */
    protected static function isValuesImportable(
        $dbOnly,
        $archiveFilterOn,
        $archiveFilterDirs,
        $databaseFilterOn,
        $databaseFilterTables,
        &$filteredData = array()
    ) {
        $result = true;

        $filteredData = array(
            'dbonly'       => false,
            'filterDirs'   => array(),
            'filterTables' => array()
        );

        if (filter_var($dbOnly, FILTER_VALIDATE_BOOLEAN) === true) {
            $filteredData['dbonly'] = true;
            $result                 = false;
        }

        if (filter_var($archiveFilterOn, FILTER_VALIDATE_BOOLEAN) === true) {
            $filterDirs                 = explode(';', $archiveFilterDirs);
            if (strlen($archiveFilterDirs) > 0 && ($filteredData['filterDirs'] = array_intersect($filterDirs, DUP_PRO_U::getWPCoreDirs()))) {
                $result = false;
            }
        }

        if (
            filter_var($databaseFilterOn, FILTER_VALIDATE_BOOLEAN) === true &&
            strlen($databaseFilterTables) > 0
        ) {
            $filteredData['filterTables'] = explode(',', $databaseFilterTables);
            $result                       = false;
        }

        return $result;
    }

    /**
     * Check if passed package is avaiable for import and set $filteredData
     *
     * @param DUP_PRO_Package $package      packaged
     * @param array           $filteredData reference value where set filter data
     *
     * @return boolean
     */
    public static function isLocalPackageImportable(DUP_PRO_Package $package, &$filteredData = array())
    {
        return self::isValuesImportable(
            $package->Archive->ExportOnlyDB,
            $package->Archive->FilterOn,
            $package->Archive->FilterDirs,
            $package->Database->FilterOn,
            $package->Database->FilterTables,
            $filteredData
        );
    }

    /**
     * Check if passed template is avaiable for import and set $filteredData
     *
     * @param DUP_PRO_Package_Template_Entity $template     template entity
     * @param array                           $filteredData reference value where set filter data
     *
     * @return boolean
     */
    public static function isTemplateImportable(DUP_PRO_Package_Template_Entity $template, &$filteredData = array())
    {
        return self::isValuesImportable(
            $template->archive_export_onlydb,
            $template->archive_filter_on,
            $template->archive_filter_dirs,
            $template->database_filter_on,
            $template->database_filter_tables,
            $filteredData
        );
    }

    /**
     * Return true if package har required installer files
     *
     * @return bool
     */
    private function packageHasRequiredInstallerFiles()
    {
        try {
            if (!$this->isValid) {
                throw new Exception("Can't do this check on an invalid package.");
            }

            $requiredFilePaths = array(
                $this->info->installer_backup_name,
                'dup-installer/main.installer.php',
            );

            foreach ($requiredFilePaths as $path) {
                $tempFile = $this->extractSingleFile($path, DUPLICATOR_PRO_SSDIR_PATH_TMP_IMPORT);

                if (file_get_contents($tempFile) === false) {
                    throw new Exception('Can\'t read installer file: ' . $path);
                }
                unlink($tempFile);
            }

            return true;
        } catch (Exception $ex) {
            DUP_PRO_Log::trace($ex->getMessage());
        }

        return false;
    }

    /**
     * true if package is valid
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->isValid;
    }

    /**
     * return archive full path
     *
     * @return string
     */
    public function getFullPath()
    {
        return $this->archive;
    }

    /**
     * return archive name
     *
     * @return string
     */
    public function getName()
    {
        return basename($this->archive);
    }

    /**
     *
     * @return string
     */
    public function getPackageId()
    {
        if (!$this->isValid) {
            return 0;
        }
        return $this->info->packInfo->packageId;
    }

    /**
     *
     * @return string
     */
    public function getPackageName()
    {
        if (!$this->isValid) {
            return '';
        }
        return $this->info->packInfo->packageName;
    }

    /**
     * return package creation date
     *
     * @return string
     */
    public function getCreated()
    {
        if (!$this->isValid) {
            return '';
        }
        return $this->info->created;
    }

    /**
     * return archive size
     *
     * @return int
     */
    public function getSize()
    {
        return filesize($this->archive);
    }

    /**
     * return package version
     *
     * @return string
     */
    public function getDupVersion()
    {
        if (!$this->isValid) {
            return '';
        }
        return $this->info->version_dup;
    }

    /**
     * return source site wordpress version
     *
     * @return string
     */
    public function getWPVersion()
    {
        if (!$this->isValid) {
            return '';
        }
        return $this->info->version_wp;
    }

    /**
     * return source site PHP version
     *
     * @return string
     */
    public function getPhpVersion()
    {
        if (!$this->isValid) {
            return '';
        }
        return $this->info->version_php;
    }

    /**
     * return source site home url
     *
     * @return string
     */
    public function getHomeUrl()
    {
        if (!$this->isValid) {
            return '';
        }
        return $this->info->wpInfo->configs->realValues->homeUrl;
    }

    /**
     * return source site home path
     *
     * @return string
     */
    public function getHomePath()
    {
        if (!$this->isValid) {
            return '';
        }
        return $this->info->wpInfo->configs->realValues->originalPaths->home;
    }

    /**
     * return source site abs path
     *
     * @return string
     */
    public function getAbsPath()
    {
        if (!$this->isValid) {
            return '';
        }
        return $this->info->wpInfo->configs->realValues->archivePaths->abs;
    }

    /**
     * return package num folders
     *
     * @return int
     */
    public function getNumFolders()
    {
        if (!$this->isValid) {
            return 0;
        }
        return $this->info->fileInfo->dirCount;
    }

    /**
     * return package num files
     *
     * @return int
     */
    public function getNumFiles()
    {
        if (!$this->isValid) {
            return 0;
        }
        return $this->info->fileInfo->fileCount;
    }

    /**
     * return package database size
     *
     * @return int
     */
    public function getDbSize()
    {
        if (!$this->isValid) {
            return 0;
        }
        return $this->info->dbInfo->tablesSizeOnDisk;
    }

    /**
     * return package num tables
     *
     * @return int
     */
    public function getNumTables()
    {
        if (!$this->isValid) {
            return 0;
        }
        return $this->info->dbInfo->tablesFinalCount;
    }

    /**
     * return package num rows
     *
     * @return int
     */
    public function getNumRows()
    {
        if (!$this->isValid) {
            return 0;
        }
        return (int) $this->info->dbInfo->tablesRowCount;
    }

    /**
     * thing function generate html package details
     *
     * @param bool $echo
     *
     * @return string|void
     */
    public function getHtmlDetails($echo = true)
    {
        ob_start();
        $importObj = $this;
        require DUPLICATOR_PRO_PLUGIN_PATH . '/views/tools/import/import-package-details.php';
        if ($echo) {
            ob_end_flush();
        } else {
            return ob_get_clean();
        }
    }

    /**
     * get the list folder to check package to import
     *
     * @return string[]
     */
    protected static function getFoldersToCheck()
    {
        $result = array();
        if (is_readable(DUPLICATOR_PRO_PATH_IMPORTS) && is_dir(DUPLICATOR_PRO_PATH_IMPORTS)) {
            $result[] = DUPLICATOR_PRO_PATH_IMPORTS;
        }
        /**
         * it has been decided that it is better not to scan the home to look for importable packages, I keep the code for future use
         *
         * $home = duplicator_pro_get_home_path();
          if (is_readable($home) && is_dir($home)) {
          $result[] = $home;
          } */
        return $result;
    }

    /**
     * get list of all packages avaibale to import sorted by filetime
     *
     * @return string[]
     */
    public static function getArchiveList()
    {
        $result = array();
        foreach (self::getFoldersToCheck() as $folder) {
            $result = array_merge($result, SnapIO::regexGlob($folder, array(
                'regexFile'   => DUPLICATOR_PRO_ARCHIVE_REGEX_PATTERN,
                'regexFolder' => false
            )));
        }
        usort($result, array(__CLASS__, 'archiveListSort'));
        return $result;
    }

    /**
     *
     * @param string $a // path
     * @param string $b // path
     * @return int
     */
    public static function archiveListSort($a, $b)
    {
        $timeA = 0;
        $timeB = 0;

        if (file_exists($a)) {
            $timeA = filemtime($a);
        }


        if (file_exists($b)) {
            $timeB = filemtime($b);
        }

        if ($timeA === $timeB) {
            return 0;
        } elseif ($timeA > $timeB) {
            return -1;
        } else {
            return 1;
        }
    }

    /**
     * get import objects of all packages avaibles to import
     *
     * @return \DUP_PRO_Package_Importer[]
     */
    public static function getArchiveObjects()
    {
        $objects = array();
        foreach (DUP_PRO_Package_Importer::getArchiveList() as $archivePath) {
            try {
                $objects[] = new DUP_PRO_Package_Importer($archivePath);
            } catch (Exception $e) {
                DUP_PRO_Log::traceObject('Can\'t read package and continue', $e);
            }
        }

        return $objects;
    }

    /**
     * Get package hash from archive file name
     *
     * @param $path archive file name
     * @return package hash
     */
    public static function getHashFromArchiveName($path)
    {
        return preg_replace('/^.+_([a-z0-9]{7})[a-z0-9]+_[0-9]{6}([0-9]{8})_archive\.(?:zip|daf)$/', '$1-$2', basename($path));
    }

    /**
     * get package name hash from archive file name
     *
     * @param string $path
     * @return string
     */
    public static function getNameHashFromArchiveName($path)
    {
        return preg_replace('/^(.+_[a-z0-9]{7}[a-z0-9]+_[0-9]{6}[0-9]{8})_archive\.(?:zip|daf)$/', '$1', basename($path));
    }
}
