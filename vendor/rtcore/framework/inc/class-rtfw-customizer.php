<?php
/**
 * RTFramework
 * This file is a part of RTFW.
 *
 * @package RTFW
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RTFW_Customizer
 */
class RTFW_Customizer {

	public function add_control( $id, $args ) {

	}

	/**
	 * Add the panel using the Customizer API
	 *
	 * @param string $id   The panel ID.
	 * @param array  $args The panel arguments.
	 */
	public function add_panel( $id, $args ) {
		global $wp_customize;

		$wp_customize->add_panel( $id, $args );
	}

	/**
	 * Add the panel using the Customizer API
	 *
	 * @param string $id   The panel ID.
	 * @param array  $args The panel arguments.
	 */
	public function add_section( $id, $args ) {
		global $wp_customize;

		$wp_customize->add_section( $id, $args );
	}
}
