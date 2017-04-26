<?php
/**
 * RTFramework_Taxonomy
 *
 * @package RTCORE
 */

/**
 * RTFramework_Taxonomy Class
 */
class RTFramework_Taxonomy {

	/**
	 * //
	 *
	 * @var array
	 */
	public $options = array();

	/**
	 * //
	 *
	 * @var $this
	 */
	protected static $instance;

	/**
	 * //
	 *
	 * @param  array $options Options.
	 * @return instance
	 */
	public static function instance( $options = array() ) {
		if ( is_null( static::$instance ) ) {
			static::$instance = new static( $options );
		}

		return static::$instance;
	}

	/**
	 * //
	 *
	 * @param array $options Options.
	 */
	public function __construct( array $options ) {
		$this->options = apply_filters( 'rt_taxonomy_options', $options );

		foreach ( $this->options as $option ) {
			$this->setup( $option );
		}
	}

	/**
	 * //
	 *
	 * @param array $option //.
	 */
	protected function setup( $option ) {
		$taxonomies = isset( $option['taxonomy'] ) ? (array) $option['taxonomy'] : array();

		foreach ( $taxonomies as $taxonomy ) {
			add_action( $taxonomy .'_add_form_fields', array( $this, 'add_form_fields' ) );
			add_action( $taxonomy .'_edit_form_fields', array( $this, 'edit_form_fields' ), 99 );

			add_action( 'edit_' . $taxonomy, array( $this, 'save_category_fields' ), 10, 2 );
			add_action( 'created_' . $taxonomy, array( $this, 'save_category_fields' ), 10, 2 );
		}
	}

	/**
	 * //
	 */
	public function add_form_fields() {
		foreach ( $this->options as $option ) {
			$id = $option['id'];

			$title = '';
			$fields = '';

			if ( isset( $option['field'] ) ) {
				$option['field']['name'] = $id;
				$value = isset( $option['field']['default'] ) ? $option['field']['default'] : '';

				$fields = cs_add_element( $option['field'], $value );

			} elseif ( isset( $option['fields'] ) ) {
				if ( isset( $option['title'] ) ) {
					$title .= sprintf( '<h2 class="form-group-heading">%1$s</h2>', $option['title'] );
				}

				foreach ( $option['fields'] as $field ) {
					$value = isset( $field['default'] ) ? $field['default'] : '';

					$fields .= cs_add_element( $field, $value, $id );
				}
			}

			printf( '<div class="form-field term-%1$s cs-content">%2$s %3$s</div>', $id, $title, $fields ); // WPCS: XSS OK.
		}
	}

	/**
	 * //
	 *
	 * @param WP_Term $term Term object.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/taxonomy_edit_form_fields
	 */
	public function edit_form_fields( $term ) {
		foreach ( $this->options as $option ) {
			$id = $option['id'];
			$values = get_term_meta( $term->term_id, $option['id'], true );

			if ( isset( $option['field'] ) ) {
				$option['field']['name'] = $id;
				$option['field']['_render'] = 'edit-tags';

				$default = isset( $option['field']['default'] ) ? $option['field']['default'] : '';
				$values = empty( $values ) ? $default : $values;

				echo cs_add_element( $option['field'], $values ); // WPCS: XSS OK.

			} elseif ( isset( $option['fields'] ) ) {
				$title = isset( $option['title'] ) ? $option['title'] : '';

				echo '<tr class="form-field cs-content">';
				echo '<th scope="row"><label>' . $title . '</label></th>'; // WPCS: XSS OK.

				echo '<td>';
				foreach ( $option['fields'] as $field ) {
					$default = isset( $field['default'] ) ? $field['default'] : '';
					$value = isset( $values[ $field['id'] ] ) ? $values[ $field['id'] ] : $default;

					echo cs_add_element( $field, $value, $id ); // WPCS: XSS OK.
				}
				echo '</td>';
				echo '</tr>';
			}
		}
	}

	/**
	 * The save_category_fields method.
	 *
	 * @param mixed $term_id Term ID being saved.
	 * @param mixed $tt_id   TT_ID.
	 *
	 * @link https://developer.wordpress.org/reference/hooks/edit_taxonomy
	 */
	public function save_category_fields( $term_id, $tt_id = null ) {
		foreach ( $this->options as $option ) {
			if ( empty( $option['id'] ) || ! isset( $_POST[ $option['id'] ] ) ) {
				continue;
			}

			$data = '';
			$raw_data = $_POST[ $option['id'] ];

			if ( is_array( $raw_data ) ) {
				$raw_data = array_map( 'cs_sanitize_clean', wp_unslash( $raw_data ) );
			} else {
				$raw_data = cs_sanitize_clean( wp_unslash( $raw_data ) );
			}

			if ( isset( $option['field'] ) && ! empty( $raw_data ) ) {

				$data = rtfw_sanitize_field( $option['field'], $raw_data );

			} elseif ( isset( $option['fields'] ) ) {
				$data = array();
				$raw_data = (array) $raw_data;

				foreach ( $option['fields'] as $field ) {
					$field_value = isset( $raw_data[ $field['id'] ] ) ? rtfw_sanitize_field( $field, $raw_data[ $field['id'] ] ) : '';
					$data[ $field['id'] ] = $field_value;
				}
			}

			if ( empty( $data ) ) {
				delete_term_meta( $term_id, $option['id'] );
			} else {
				update_term_meta( $term_id, $option['id'], $data );
			}
		}
	}
}
