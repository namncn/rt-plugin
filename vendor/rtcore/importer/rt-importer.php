<?php
/*
Plugin Name: RT-Importer
Description: Description
Plugin URI: http://#
Author: Author
Author URI: http://#
Version: 1.0.0
License: GPL2
Text Domain: raothue
Domain Path: languages
*/

if ( ! class_exists( 'WP_Importer' ) ) {
	defined( 'WP_LOAD_IMPORTERS' ) || define( 'WP_LOAD_IMPORTERS', true );
	require_once ABSPATH . '/wp-admin/includes/class-wp-importer.php';
}

if ( ! class_exists( 'WXR_Importer' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'inc/wp-importer/class-logger.php';
	require_once plugin_dir_path( __FILE__ ) . 'inc/wp-importer/class-wxr-importer.php';
}

/**
 * RT Importer.
 */
if ( ! class_exists( 'RT_Importer_Manager' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-rt-importer.php';
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-rt-printer-logger.php';
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-rt-importer-manager.php';
}

/**
 * Widget Importer & Exporter.
 */
if ( ! class_exists( 'Widget_Importer_Exporter' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'inc/widget-importer.php';
}

if ( ! class_exists( 'RT_Customizer_Import_Export' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'inc/class-rt-customizer-import-export.php';
}

if ( ! function_exists( 'rt_importers' ) ) {
	require_once plugin_dir_path( __FILE__ ) . 'inc/helpers.php';
}
