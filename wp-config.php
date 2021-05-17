<?php
# Database Configuration
define( 'DB_NAME', 'wp_qavsdev' );
define( 'DB_USER', 'qavsdev' );
define( 'DB_PASSWORD', '9vWAs_c0v_esYJSvmCUa' );
define( 'DB_HOST', '127.0.0.1' );
define( 'DB_HOST_SLAVE', '127.0.0.1' );
define('DB_CHARSET', 'utf8');
define('DB_COLLATE', 'utf8_unicode_ci');
$table_prefix = 'wp_';

# Security Salts, Keys, Etc
define('AUTH_KEY',         'o#o(u3dbPr>r&_mq>K2N^`HzBPE@_nni]G!6rUr5 K+OyYXm|9]EOQ3~Yq~+rDIL');
define('SECURE_AUTH_KEY',  'p*[$yquKv7?>!Q5s&Et.60uTF.00t1&HNAy!.elscXU3E#jfeT.Hycw)E=j>|#mk');
define('LOGGED_IN_KEY',    'yjbFiAO|Vv?3_diaLo^%:XvfTK/yNzh+hQ:n8vC{n0RY{U8CZF7-.MldbeW.4W|>');
define('NONCE_KEY',        'Op+n>DJ%Z&u =#n/_ 4Rxr)cs-O^2y<YA6v^?]3?ETwtI[Z-r[#f+-rEdcT3<O< ');
define('AUTH_SALT',        'uoF}|YsQX3zaH$F%]#Cc,B%*t{]fV`:?QA?_ShHRY>1MlZj8bnpFPv%0v_16#^+(');
define('SECURE_AUTH_SALT', 'Rz9m(6,5lx3&Yr|jqCANp%EeXiz-].a#s-[:$9kZ_a3D:S-<DarVZ9qh]KfAl}8t');
define('LOGGED_IN_SALT',   'l%C_OAifC(+ZQ8*.qa61Tbv<Rm)@((M3)n2K0Ah/M&GjA4xZ&;QhHdP44zs.4u;+');
define('NONCE_SALT',       'gCMepQ|H(j_w4Td SR?azFo%wEozEuwheq1jEo0BM$78mtK0Q].-zuNy)CrRy&Q%');


# Localized Language Stuff

define( 'WP_CACHE', TRUE );

define( 'WP_AUTO_UPDATE_CORE', false );

define( 'PWP_NAME', 'qavsdev' );

define( 'FS_METHOD', 'direct' );

define( 'FS_CHMOD_DIR', 0775 );

define( 'FS_CHMOD_FILE', 0664 );

umask(0002);

define( 'WPE_APIKEY', 'a7fcc0086a1dd2374ee00cc865152bf0deaebee7' );

define( 'WPE_CLUSTER_ID', '101312' );

define( 'WPE_CLUSTER_TYPE', 'pod' );

define( 'WPE_ISP', true );

define( 'WPE_BPOD', false );

define( 'WPE_RO_FILESYSTEM', false );

define( 'WPE_LARGEFS_BUCKET', 'largefs.wpengine' );

define( 'WPE_SFTP_PORT', 2222 );

define( 'WPE_LBMASTER_IP', '' );

define( 'WPE_CDN_DISABLE_ALLOWED', true );

define( 'DISALLOW_FILE_MODS', FALSE );

define( 'DISALLOW_FILE_EDIT', FALSE );

define( 'DISABLE_WP_CRON', false );

define( 'WPE_FORCE_SSL_LOGIN', false );

define( 'FORCE_SSL_LOGIN', false );

/*SSLSTART*/ if ( isset($_SERVER['HTTP_X_WPE_SSL']) && $_SERVER['HTTP_X_WPE_SSL'] ) $_SERVER['HTTPS'] = 'on'; /*SSLEND*/

define( 'WPE_EXTERNAL_URL', false );

define( 'WP_POST_REVISIONS', FALSE );

define( 'WPE_WHITELABEL', 'wpengine' );

define( 'WP_TURN_OFF_ADMIN_BAR', false );

define( 'WPE_BETA_TESTER', false );

$wpe_cdn_uris=array ( );

$wpe_no_cdn_uris=array ( );

$wpe_content_regexs=array ( );

$wpe_all_domains=array ( 0 => 'qavsdev.wpengine.com', 1 => 'qavsdev.wpenginepowered.com', );

$wpe_varnish_servers=array ( 0 => 'pod-101312', );

$wpe_special_ips=array ( 0 => '35.197.245.96', );

$wpe_netdna_domains=array ( );

$wpe_netdna_domains_secure=array ( );

$wpe_netdna_push_domains=array ( );

$wpe_domain_mappings=array ( );

$memcached_servers=array ( );
define('WPLANG','');

# WP Engine ID


# WP Engine Settings






# That's It. Pencils down
if ( !defined('ABSPATH') )
	define('ABSPATH', __DIR__ . '/');
require_once(ABSPATH . 'wp-settings.php');
