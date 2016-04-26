<?php
/**
 * Plugin Name: IP Post Restrict
 * Plugin URI:
 * Description: Restrict visibility of some posts to an IP range.
 * Version:     0.2
 * Author:      Eric Teubert, Christoph Wolff
 * License:     MIT
 * License URI: license.txt
 * Text Domain: wp-ip-post-restrict
 */

define('IPR_VERSION'    , '0.1');
define('IPR_PLUGIN_NAME', 'IP Post Restrict');
define('IPR_FILE'       , __FILE__);
define('IPR_PATH'       , realpath(plugin_dir_path(IPR_FILE)) . '/');
define('IPR_URL'        , plugin_dir_url(IPR_FILE));
define('IPR_PHP_REQUIREMENT', '5.4');

$correct_php_version = version_compare( phpversion(), IPR_PHP_REQUIREMENT, ">=" );

if ( ! $correct_php_version ) {
	echo IPR_PLUGIN_NAME . " requires <strong>PHP " . IPR_PHP_REQUIREMENT . "</strong> or higher.<br>";
	echo "You are running PHP " . phpversion();
	exit;
}

add_action('plugins_loaded', 'ipr_load');

function ipr_load() {
	require_once 'inc/helper.php';
	require_once 'inc/network.php';
	require_once 'inc/postmeta.php';
}
