<?php
/**
 * Load files.
 */

require_once RT_CORE_PATH . 'vendor/rtcore/framework/init.php';
require_once RT_CORE_PATH . 'vendor/rtcore/importer/rt-importer.php';
require_once RT_CORE_PATH . 'vendor/rtcore/sidebar-manager/create-sidebar.php';
require_once RT_CORE_PATH . 'vendor/webdevstudios/cpt-core/CPT_Core.php';
if ( function_exists( 'WC' ) ) {
	require_once RT_CORE_PATH . 'vendor/rt-woocommerce-quick-view/init.php';
}
