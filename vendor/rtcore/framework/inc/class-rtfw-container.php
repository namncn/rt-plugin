<?php
/**
 * RTCORE Framework
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
 * RTFW_Container
 */
class RTFW_Container implements ArrayAccess {

	/**
	 * All of the attributes set on the container.
	 *
	 * @var array
	 */
	protected $attributes = array();

	/**
	 * Create a new fluent container instance.
	 *
	 * @param array|object $attributes Default attributes given.
	 */
	public function __construct( $attributes = array() ) {
		foreach ( $attributes as $key => $value ) {
			$this->attributes[ $key ] = $value;
		}
	}

	/**
	 * Get the attributes from the container.
	 *
	 * @return array
	 */
	public function attributes() {
		return $this->attributes;
	}

	/**
	 * Get an attribute from the container.
	 *
	 * @param  string $key     The key to retrieve.
	 * @param  mixed  $default Default value return.
	 * @return mixed
	 */
	public function get( $key, $default = null ) {
		if ( array_key_exists( $key, $this->attributes ) ) {
			return $this->attributes[ $key ];
		}

		return $default;
	}

	/**
	 * Get an attribute from the container.
	 *
	 * @param string $key   The offset to assign the value to.
	 * @param mixed  $value The value to set.
	 */
	public function set( $key, $value ) {
		$this->attributes[ $key ] = $value;
	}

	/**
	 * Alias of set() method.
	 *
	 * @param string $key   The offset to assign the value to.
	 * @param mixed  $value The value to set.
	 */
	public function register( $key, $value ) {
		$this->set( $key, $value );
	}

	/**
	 * Determine if the given offset exists.
	 *
	 * @param  string $offset An offset to check for.
	 * @return bool
	 */
	public function offsetExists( $offset ) {
		return isset( $this->{$offset} );
	}

	/**
	 * Get the value for a given offset.
	 *
	 * @param  string $offset The offset to retrieve.
	 * @return mixed
	 */
	public function offsetGet( $offset ) {
		return $this->{$offset};
	}

	/**
	 * Set the value at the given offset.
	 *
	 * @param string $offset The offset to assign the value to.
	 * @param mixed  $value  The value to set.
	 */
	public function offsetSet( $offset, $value ) {
		$this->{$offset} = $value;
	}

	/**
	 * Unset the value at the given offset.
	 *
	 * @param string $offset he offset to unset.
	 */
	public function offsetUnset( $offset ) {
		unset( $this->{$offset} );
	}

	/**
	 * Dynamically retrieve the value of an attribute.
	 *
	 * @param  string $key The offset to retrieve.
	 * @return mixed
	 */
	public function __get( $key ) {
		return $this->get( $key );
	}

	/**
	 * Dynamically set the value of an attribute.
	 *
	 * @param string $key   The offset to assign the value to.
	 * @param mixed  $value The value to set.
	 */
	public function __set( $key, $value ) {
		$this->set( $key, $value );
	}

	/**
	 * Dynamically check if an attribute is set.
	 *
	 * @param  string $key Whether an offset exists.
	 * @return bool
	 */
	public function __isset( $key ) {
		return isset( $this->attributes[ $key ] );
	}

	/**
	 * Dynamically unset an attribute.
	 *
	 * @param string $key Unset an offset.
	 */
	public function __unset( $key ) {
		unset( $this->attributes[ $key ] );
	}
}
