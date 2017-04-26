<?php
/**
 * Plugin Name: RTFramework
 * Description: NCNTeam framework, thanks for Codestars Framework
 * Plugin URI: #
 * Author: NamNCN
 * Author URI: #
 * Version: 1.0.0
 * License: GPL2
 * Text Domain: raothue
 */

require_once dirname( __FILE__ ) . '/inc/helpers.php';

if ( ! defined( 'CS_VERSION' ) ) {
	require_once dirname( __FILE__ ) . '/cs-framework/cs-framework-path.php';
}

if ( ! class_exists( 'RTFW' ) ) {
	require_once dirname( __FILE__ ) . '/inc/rtfw.php';
}

if ( ! function_exists( 'rtfw' ) ) :
	/**
	 * NCNFramework Start
	 */
	$rtfw = new RTFW;

	/**
	 * //
	 *
	 * @param  string $make //.
	 * @return mixins
	 */
	function rtfw( $make = null ) {
		$rtfw = RTFW::get_instance();
		return is_null( $make ) ? $rtfw : $rtfw[ $make ];
	}
endif;
