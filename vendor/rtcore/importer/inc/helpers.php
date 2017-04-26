<?php

global $rt_importers;

if ( ! function_exists( 'rt_importer_init' ) ) :
	/**
	 * Initial RT-Importer
	 *
	 * @todo Improve this function!!!
	 */
	function rt_importer_init() {
		$class = array( new RT_Importer_Manager, 'dispatch' );
		$description = esc_html__( 'Import demo content from rtcore by one click.', 'raothue' );

		register_importer( 'rt-importer', esc_html__( 'RT: Importer', 'raothue' ), $description, $class );
	}
endif;
add_action( 'admin_init', 'rt_importer_init', 999 );

if ( ! function_exists( 'rt_importer_register' ) ) :
	/**
	 * //
	 *
	 * @param  string $id   //.
	 * @param  array  $args //.
	 */
	function rt_importer_register( $id, array $args ) {
		global $rt_importers;

		$id = sanitize_key( $id );

		$args = wp_parse_args( $args, array(
			'name'        => '',
			'preview'     => '',
			'screenshot'  => '',
			'archive'     => '',
		) );

		$rt_importers[ $id ] = $args;
	}
endif;

if ( ! function_exists( 'rt_importers' ) ) :
	/**
	 * Get registered rt-importer
	 *
	 * @return array
	 */
	function rt_importers() {
		global $rt_importers;

		return is_null( $rt_importers ) ? array() : $rt_importers;
	}
endif;
add_filter( 'rt_importer_metadata', 'rt_importers', 20 );
