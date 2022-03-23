<?php

/*
Plugin Name: Simple Sitemap
Plugin URI: http://wordpress.org/plugins/simple-sitemap/
Description: HTML sitemap to display content as a single linked list of posts, pages, or custom post types. You can even display posts in groups sorted by taxonomy!
Version: 3.5.5
Author: David Gwyer
Author URI: http://www.wpgoplugins.com
Text Domain: simple-sitemap
*/
/*
  Copyright 2019 David Gwyer (email : david@wpgoplugins.com)

	This program is free software; you can redistribute it and/or modify
	it under the terms of the GNU General Public License as published by
	the Free Software Foundation; either version 2 of the License, or
	(at your option) any later version.

	This program is distributed in the hope that it will be useful,
	but WITHOUT ANY WARRANTY; without even the implied warranty of
	MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
	GNU General Public License for more details.

	You should have received a copy of the GNU General Public License
	along with this program; if not, write to the Free Software
	Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/
namespace WPGO_Plugins\Simple_Sitemap;

if ( !defined( 'ABSPATH' ) ) {
    exit;
}
// For now hard code this until a better method can be found.

if ( !defined( 'SITEMAP_FREEMIUS_NAVIGATION' ) ) {
    define( 'SITEMAP_FREEMIUS_NAVIGATION', 'menu' );
    // Options: menu|tabs.
}


if ( function_exists( __NAMESPACE__ . '\\ss_fs' ) ) {
    ss_fs()->set_basename( false, __FILE__ );
} else {
    
    if ( !function_exists( __NAMESPACE__ . '\\ss_fs' ) ) {
        /**
         * Create a helper function for easy SDK access.
         *
         * @return Object Freemius object instance.
         */
        function ss_fs()
        {
            global  $ss_fs ;
            
            if ( !isset( $ss_fs ) ) {
                // Include Freemius SDK.
                require_once dirname( __FILE__ ) . '/freemius/start.php';
                $ss_fs = fs_dynamic_init( array(
                    'id'             => '4087',
                    'slug'           => 'simple-sitemap',
                    'premium_slug'   => 'simple-sitemap-pro',
                    'type'           => 'plugin',
                    'public_key'     => 'pk_d7776ef9a819e02b17ef810b17551',
                    'is_premium'     => false,
                    'navigation'     => SITEMAP_FREEMIUS_NAVIGATION,
                    'premium_suffix' => 'Pro',
                    'has_addons'     => false,
                    'has_paid_plans' => true,
                    'menu'           => array(
                    'slug'       => 'simple-sitemap-menu',
                    'first-path' => 'admin.php?page=simple-sitemap-menu-welcome',
                ),
                    'is_live'        => true,
                ) );
            }
            
            return $ss_fs;
        }
        
        // Init Freemius.
        ss_fs();
        // Signal that SDK was initiated.
        do_action( 'ss_fs_loaded' );
    }
    
    /**
     * Main class constructor.
     *
     * @param Array $module_roots Root plugin path/dir.
     */
    class Main
    {
        /**
         * Common root paths/directories.
         *
         * @var $module_roots
         */
        public static  $module_roots ;
        /**
         * Initialize class.
         */
        public static function init()
        {
            self::$module_roots = array(
                'dir'  => plugin_dir_path( __FILE__ ),
                'pdir' => plugin_dir_url( __FILE__ ),
                'uri'  => plugins_url( '', __FILE__ ),
                'file' => __FILE__,
            );
            $root = self::$module_roots['dir'];
            require_once $root . 'lib/classes/bootstrap.php';
            new BootStrap();
        }
    
    }
    Main::init();
}
