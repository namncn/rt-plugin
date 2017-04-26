<?php
/**
 * Plugin Name: RT Core
 * Plugin URI: https://bitbucket.org/namtruongncn/rt-plugin
 * Author: Nam NCN
 * Author URI: http://namncn.com/gioi-thieu/
 * Version: 1.0.0
 * Description: This plugin is mandatory install for RT Theme. It will bring power to your website, if you do not install this plugin, your website is just like a simple blog.
 * Requires at least: 4.5
 *
 * Text Domain: raothue
 * Domain Path: /languages/
 *
 * @package RT_CORE
 * @author NamNCN
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit(); // Exit if accessed directly.
}

if ( defined( 'RT_CORE_VERSION' ) ) {
	return;
}

define( 'RT_CORE_VERSION', '1.0.0' );
define( 'RT_CORE_FILE', __FILE__ );
define( 'RT_CORE_PATH', plugin_dir_path( RT_CORE_FILE ) );
define( 'RT_CORE_URL', plugin_dir_url( RT_CORE_FILE ) );

require_once RT_CORE_PATH . 'class-rt-core.php';

if ( ! function_exists( 'rtcore' ) ) {
	/**
	 * Get plugin instance.
	 *
	 * @return RT_CORE
	 */
	function rtcore() {
		return RT_CORE::get_instance();
	}
}
$GLOBALS['rtcore'] = rtcore();
