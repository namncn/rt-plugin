<?php

class RTFW_Field {

	/**
	 * //
	 *
	 * @param  string $type Method name to call.
	 * @param  array  $args Method arguments.
	 * @return string
	 */
	public static function __callStatic( $type, $args ) {
		if ( count( $args ) === 2 ) {
			list( $id, $options ) = $args;
		} else {
			$id = $args[0];
			$options = array();
		}

		$options['type'] = $type;

		return cs_add_element( $options );
	}
}
