<?php
/**
 * RTFramework
 * This file is a part of RTFW.
 *
 * @package RTFW
 */

if ( ! function_exists( 'cs_add_element' ) ) :
	/**
	 * //
	 *
	 * @param  array  $field  //.
	 * @param  string $value  //.
	 * @param  string $unique //.
	 * @return string
	 */
	function cs_add_element( $field = array(), $value = '', $unique = '' ) {
		$output     = '';
		$depend     = '';
		$sub        = ( isset( $field['sub'] ) ) ? 'sub-': '';
		$unique     = ( isset( $unique ) ) ? $unique : '';
		$languages  = cs_language_defaults();
		$class      = 'CSFramework_Option_' . $field['type'];
		$wrap_class = ( isset( $field['wrap_class'] ) ) ? ' ' . $field['wrap_class'] : '';
		$hidden     = ( isset( $field['show_only_language'] ) && ( $field['show_only_language'] != $languages['current'] ) ) ? ' hidden' : '';
		$is_pseudo  = ( isset( $field['pseudo'] ) ) ? ' cs-pseudo-field' : '';

		if ( isset( $field['dependency'] ) ) {
			$hidden  = ' hidden';
			$depend .= ' data-'. $sub .'controller="'. $field['dependency'][0] .'"';
			$depend .= ' data-'. $sub .'condition="'. $field['dependency'][1] .'"';
			$depend .= ' data-'. $sub ."value='". $field['dependency'][2] ."'";
		}

		if ( isset( $field['_render'] ) && 'edit-tags' === $field['_render'] ) {
			$output .= '<tr class="form-field cs-element cs-field-'. $field['type'] . $is_pseudo . $wrap_class . $hidden .'"'. $depend .'>';

			$title = '';
			if ( isset( $field['title'] ) ) {
				$title = $field['title'];
			}

			$output .= '<th scope="row" class="cs-title"><label>' . $title . '</label></th>';
			$output .= '<td>';
		} else {
			$output .= '<div class="cs-element cs-field-'. $field['type'] . $is_pseudo . $wrap_class . $hidden .'"'. $depend .'>';

			if ( isset( $field['title'] ) ) {
				$output .= '<div class="cs-title"><h4>' . $field['title'] . '</h4></div>';
			}
		}

		$output .= ( isset( $field['title'] ) ) ? '<div class="cs-fieldset">' : '';

		$value   = ( ! isset( $value ) && isset( $field['default'] ) ) ? $field['default'] : $value;
		$value   = ( isset( $field['value'] ) ) ? $field['value'] : $value;

		if ( class_exists( $class ) ) {
			ob_start();
			$element = new $class( $field, $value, $unique );
			$element->output();
			$output .= ob_get_clean();
		}

		$output .= ( isset( $field['title'] ) ) ? '</div>' : '';
		$output .= ( isset( $field['desc'] ) ) ? '<p class="cs-text-desc">'. $field['desc'] .'</p>' : '';

		if ( isset( $field['_render'] ) && 'edit-tags' === $field['_render'] ) {
			$output .= '</td></tr>';
		} else {
			$output .= '<div class="clear"></div>';
			$output .= '</div>';
		}

		return $output;
	}
endif;

if ( ! function_exists( 'rtfw_sanitize_field' ) ) :
	/**
	 * //
	 *
	 * @param  array  $field //.
	 * @param  string $value //.
	 * @return array
	 */
	function rtfw_sanitize_field( $field, $value ) {
		$sanitize = empty( $field['sanitize'] ) ? $field['type'] : $field['sanitize'];
		$sanitize_name = 'cs_sanitize_'. $sanitize;

		if ( has_filter( $sanitize_name ) ) {
			$value = apply_filters( $sanitize_name, $value, $field );
		} elseif ( is_callable( $sanitize ) ) {
			$value = call_user_func_array( $sanitize, array( $value ) );
		}

		return $value;
	}
endif;
