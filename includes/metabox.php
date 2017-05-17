<?php
/**
 * Register metabox.
 */

function rtcore_register_partner_metabox( RTFW $rtfw ) {
	$screen = apply_filters( 'rt_partner_metabox_screen', array( 'partner' ) );

	$args = array(
		'title'   => esc_html__( 'Thông tin đối tác', 'raothue' ),
		'screen'  => $screen,
		'fields'  => array(
			array(
				'id'      => 'link',
				'type'    => 'text',
				'title'   => esc_html__( 'Link đến website của đối tác?', 'raothue' ),
				'default' => '#',
			),
		),
		'context' => 'advanced',
	);

	$rtfw->register_metabox( new RTFW_Metabox( 'partner', $args ) );
}
add_action( 'rtfw_init', 'rtcore_register_partner_metabox' );
