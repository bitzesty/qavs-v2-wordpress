<?php

defined("ABSPATH") or die("");

use Duplicator\Libs\Snap\SnapIO;

class DUP_PRO_MU_Generations
{
    const NotMultisite  = 0;
    const PreThreeFive  = 1;
    const ThreeFivePlus = 2;
}

class DUP_PRO_MU
{

    public static function networkMenuPageUrl($menu_slug, $echo = true)
    {
        global $_parent_pages;

        if (isset($_parent_pages[$menu_slug])) {
            $parent_slug = $_parent_pages[$menu_slug];
            if ($parent_slug && !isset($_parent_pages[$parent_slug])) {
                $url = network_admin_url(add_query_arg('page', $menu_slug, $parent_slug));
            } else {
                $url = network_admin_url('admin.php?page=' . $menu_slug);
            }
        } else {
            $url = '';
        }

        $url = esc_url($url);

        if ($echo) {
            echo esc_url($url);
        }

        return $url;
    }

    /**
     * return multisite mode
     * 0 = single site
     * 1 = multisite subdomain
     * 2 = multisite subdirectory
     *
     * @return int
     */
    public static function getMode()
    {

        if (is_multisite()) {
            if (defined('SUBDOMAIN_INSTALL') && SUBDOMAIN_INSTALL) {
                return 1;
            } else {
                return 2;
            }
        } else {
            return 0;
        }
    }

    /**
     * This function is wrong because it assumes that if the folder sites exist, blogs.dir cannot exist.
     * This is not true because if the network is old but a new site is created after the WordPress update both blogs.dir and sites folders exist.
     *
     * @deprecated since version 3.8.4
     *
     * @return int
     */
    public static function getGeneration()
    {
        if (self::getMode() == 0) {
            return DUP_PRO_MU_Generations::NotMultisite;
        } else {
            $sitesDir = WP_CONTENT_DIR . '/uploads/sites';

            if (file_exists($sitesDir)) {
                return DUP_PRO_MU_Generations::ThreeFivePlus;
            } else {
                return DUP_PRO_MU_Generations::PreThreeFive;
            }
        }
    }

    /**
     *
     * @param array $filteredSites
     * @param array $filteredTables
     * @param array $filteredPaths
     *
     * @return array
     */
    public static function getSubsites($filteredSites = array(), $filteredTables = array(), $filteredPaths = array())
    {
        if (!is_multisite()) {
            return array();
        }

        $site_array    = array();
        $filteredSites = is_array($filteredSites) ? $filteredSites : array();

        DUP_PRO_LOG::trace("NETWORK SITES");

        if (function_exists('get_sites')) {
            $sites = get_sites(array('number' => 2000));
        } else {
            $sites = wp_get_sites(array('limit' => 2000));
        }

        foreach ($sites as $site) {
            $site = (object) $site;
            if (in_array($site->blog_id, $filteredSites)) {
                continue;
            }
            $site_info = self::getSubsiteInfo($site, $filteredTables, $filteredPaths);
            array_push($site_array, $site_info);
            DUP_PRO_LOG::trace("Multisite subsite detected. ID={$site_info->id} Domain={$site_info->domain} Path={$site_info->path} Blogname={$site_info->blogname}");
        }

        return $site_array;
    }

    /**
     *
     * @param int $subsiteId
     * @return boolean|object
     */
    public static function getSubsiteInfoById($subsiteId)
    {
        if (!is_multisite()) {
            return false;
        }

        if (function_exists('get_sites')) {
            $sites = get_sites(array('number' => 2000));
        } else {
            $sites = wp_get_sites(array('limit' => 2000));
        }

        foreach ($sites as $site) {
            $site = (object) $site;
            if ($site->blog_id == $subsiteId) {
                return self::getSubsiteInfo($site);
            }
        }

        return false;
    }

    /**
     *
     * @param object $site
     */
    public static function getSubsiteInfo($siteObj, $filteredTables = array(), $filteredPaths = array())
    {
        $site = (object) $siteObj;

        $site_details               = get_blog_details($site->blog_id);
        $subsiteID                  = $site->blog_id;
        $site_info                  = new stdClass();
        $site_info->id              = $subsiteID;
        $site_info->domain          = $site_details->domain;
        $site_info->path            = $site_details->path;
        $site_info->blogname        = $site_details->blogname;
        $site_info->blog_prefix     = $GLOBALS['wpdb']->get_blog_prefix($subsiteID);
        $site_info->filtered_tables = array_values(array_intersect(self::getSubsiteTables($subsiteID), $filteredTables));
        $site_info->adminUsers      = DUP_PRO_WP_U::getAdminUserLists($site_info->id);
        $site_info->fullHomeUrl     = get_home_url($site->blog_id);
        $site_info->fullSiteUrl     = get_site_url($site->blog_id);

        switch_to_blog($site->blog_id);
        $uploadData                = wp_upload_dir();
        $uploadPath                = $uploadData['basedir'];
        $site_info->upload_path    = SnapIO::getRelativePath($uploadPath, DUP_PRO_Archive::getTargetRootPath(), true);
        $site_info->fullUploadPath = $uploadPath;
        $site_info->fullUploadUrl  = $uploadData['baseurl'];
        $site_info->filtered_paths = array_values(array_filter($filteredPaths, function ($path) use ($uploadPath, $subsiteID) {
            if (
                ($relativeUpload = SnapIO::getRelativePath($path, $uploadPath)) === false
                    || in_array($path, DUP_PRO_Archive::getDefaultGlobalDirFilter())
            ) {
                return false;
            }

            if ($subsiteID > 1) {
                return true;
            } else {
                // no check on blogs.dir because in wp-content/blogs.dir not in upload folder
                return !(strpos($relativeUpload, 'sites') === 0);
            }
        }));
        restore_current_blog();

        return $site_info;
    }

    /**
     * @param int $subsiteID
     * @return array List of tables belonging to subsite
     */
    protected static function getSubsiteTables($subsiteID)
    {
        $allTables             = $GLOBALS['wpdb']->get_col("SHOW FULL TABLES WHERE Table_Type != 'VIEW'");
        $basePrefix            = $GLOBALS['wpdb']->base_prefix;
        $subsitePrefix         = $GLOBALS['wpdb']->get_blog_prefix($subsiteID);
        $qBasePrefix           = preg_quote($basePrefix, "/");
        //Matches only tables with base prefix not followed by number and _
        $patternMainSiteTables = "^{$qBasePrefix}(?!\d+_)";

        $sharedTables        = array(
            $basePrefix . 'users',
            $basePrefix . 'usermeta'
        );
        $multisiteOnlyTables = array(
            $basePrefix . 'blogmeta',
            $basePrefix . 'blogs',
            $basePrefix . 'blog_versions',
            $basePrefix . 'registration_log',
            $basePrefix . 'signups',
            $basePrefix . 'site',
            $basePrefix . 'sitemeta'
        );
        $subsiteTables       = array();

        foreach ($allTables as $table) {
            if ($subsiteID != 1) {
                if (substr($table, 0, strlen($subsitePrefix)) === $subsitePrefix || in_array($table, $sharedTables)) {
                    $subsiteTables[] = $table;
                }
            } else {
                if (preg_match("/{$patternMainSiteTables}/", $table) && !in_array($table, $multisiteOnlyTables)) {
                    $subsiteTables[] = $table;
                }
            }
        }

        return $subsiteTables;
    }

    /**
     * Returns the main site ID for the network.
     *
     * Copied from the source of the get_main_site_id() except first line in https://developer.wordpress.org/reference/functions/get_main_site_id/
     * get_main_site_id() is introduced in WP 4.9.0. It is for backward compatibility
     *
     * @param int|null network id
     * @return int The ID of the main site.
     */
    public static function get_main_site_id($network_id = null)
    {
        // For > WP 4.9.0
        if (function_exists('get_main_site_id')) {
            return get_main_site_id($network_id);
        }

        if (!is_multisite()) {
            return get_current_blog_id();
        }

        $network = function_exists('get_network') ? get_network($network_id) : wp_get_network($network_id);
        if (!$network) {
            return 0;
        }

        return $network->site_id;
    }
}
