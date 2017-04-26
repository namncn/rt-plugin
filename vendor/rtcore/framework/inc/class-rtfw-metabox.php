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
 * RTFW_Metabox
 */
class RTFW_Metabox {

	/**
	 * The metabox ID
	 *
	 * @var string
	 */
	protected $id;

	/**
	 * Metabox options
	 *
	 * @var array
	 */
	protected $options;

	/**
	 * //
	 *
	 * @var array
	 */
	protected $sections;

	/**
	 * //
	 *
	 * @var array
	 */
	protected $section_count = 0;

	/**
	 * Metabox instance
	 *
	 * @param string $id      Metabox ID.
	 * @param array  $options Metabox settings.
	 */
	public static function make( $id, $options = array() ) {
		return new static( $id, $options );
	}

	/**
	 * New metabox
	 *
	 * @param string $id      Metabox ID.
	 * @param array  $options Metabox settings.
	 */
	public function __construct( $id, $options = array() ) {
		$this->id = sanitize_key( $id );

		$this->options = wp_parse_args( $options, array(
			'id' => $this->id,
			'title' => '',
			'screen' => 'post',
			'context' => 'advanced',
			'priority' => 'default',
		) );

		if ( isset( $options['fields'] ) ) {
			$this->add_fields( $options['fields'] );
		}
	}

	/**
	 * //
	 *
	 * @return string
	 */
	public function get_id() {
		return $this->id;
	}

	/**
	 * Set metabox title
	 *
	 * @param string $title The title.
	 */
	public function set_title( $title ) {
		$this->options['title'] = $title;
	}

	/**
	 * Alias of title method.
	 *
	 * @param string $title The title.
	 */
	public function set_name( $title ) {
		return $this->set_title( $title );
	}

	/**
	 * //
	 *
	 * @param string $screen //.
	 */
	public function set_screen( $screen ) {
		$this->options['screen'] = $screen;
	}

	/**
	 * //
	 *
	 * @param string $priority //.
	 */
	public function set_priority( $priority ) {
		$this->options['priority'] = $priority;
	}

	/**
	 * //
	 *
	 * @param string $context //.
	 */
	public function set_context( $context ) {
		$this->options['context'] = $context;
	}

	/**
	 * //
	 *
	 * @param string $id   //.
	 * @param array  $args //.
	 */
	public function add_field( $id, $args = array() ) {
		if ( is_array( $id ) && isset( $id['id'] ) ) {
			$args = $id;
			$id = $id['id'];
		}

		$args['id'] = sanitize_key( $id );
		$section_key = 0; // DEVTODO: Add custom section id by field.

		if ( ! isset( $this->sections[ $section_key ] ) ) {
			$this->sections[ $section_key ] = array(
				'name' => $this->id . '_tab_0',
				'title' => esc_html__( 'Generals', 'awethemes' ),
			);
		}

		$this->sections[ $section_key ]['fields'][] = $args;
	}

	/**
	 * //
	 *
	 * @param array $fields //.
	 */
	public function add_fields( array $fields ) {
		foreach ( $fields as $field ) {
			$this->add_field( $field );
		}
	}

	/**
	 * //
	 *
	 * @param array $section //.
	 * @param array $fields  //.
	 */
	public function add_section( $section, $fields ) {
		$args = (array) $section;

		if ( 0 === count( $this->sections ) ) {
			$section_id = 1;
		} else {
			$section_id = count( $this->sections );
		}

		$id = $this->id . '_tab_' . $section_id;
		$args['fields'] = (array) $fields;

		if ( empty( $args['id'] ) ) {
			$args['name'] = $id;
		}

		if ( ! isset( $args['title'] ) || is_string( $section ) ) {
			$args['title'] = $section;
		}

		$this->sections[ $section_id ] = $args;
	}

	/**
	 * Inital metabox
	 */
	public function get_options() {
		$opts = $this->options;

		$opts['id'] = $this->id;
		$opts['post_type'] = $this->options['screen'];

		if ( count( $this->sections ) === 1 ) {
			unset( $this->sections[0]['title'] );
		}

		ksort( $this->sections );
		$opts['sections'] = array_values( (array) $this->sections );

		unset( $opts['fields'] );
		unset( $opts['screen'] );

		return $opts;
	}
}
