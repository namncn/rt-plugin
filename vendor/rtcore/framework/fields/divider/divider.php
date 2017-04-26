<?php
/**
 * CS-Framework Oembed.
 *
 * @package cs-ncnteam
 */

// Cannot access pages directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Field: Oembed
 */
class CSFramework_Option_divider extends CSFramework_Options {

	/**
	 * Output the field.
	 */
	public function output() {
		echo '<hr>';
	}
}
