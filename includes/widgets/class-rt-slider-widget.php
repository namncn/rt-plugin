<?php
/**
 * Widget class.
 *
 * @package Raothue
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RT Slider Posts Widget.
 *
 * Show slider posts.
 *
 * @author   NamNCN
 * @category Widgets
 * @package  RTCORE/Widgets
 * @version  1.0.0
 * @extends  RT_Widget
 */
class RT_Slider_Widget extends RT_Widget {
	/**
	 * Sliders Object.
	 */
	public function sliders() {
		return get_posts( array(
			'post_type'      => 'ml-slider',
			'post_status'    => 'publish',
			'orderby'        => 'date',
			'order'          => 'ASC',
			'posts_per_page' => -1,
		) );
	}

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'rt-slider-widget';
		$this->widget_description = esc_html__( "Hiển thị slider.", 'raothue' );
		$this->widget_id          = 'rt-slider-widget';
		$this->widget_name        = esc_html__( 'RT: Slider', 'raothue' );

		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => esc_html__( 'Slider', 'raothue' ),
				'label' => esc_html__( 'Tiêu đề:', 'raothue' ),
			),
			'id' => array(
				'type'   => 'select',
				'std'    => $this->slider_id_default(),
				'options' => $this->slider_ids(),
				'label'  => esc_html__( 'Chọn Slider muốn hiển thị:', 'raothue' ),
			),
		);

		parent::__construct();
	}

	/**
	 * Output widget.
	 *
	 * @see WP_Widget
	 *
	 * @param array $args
	 * @param array $instance
	 */
	public function widget( $args, $instance ) {

		$defaults = array(
			'id'  => $this->slider_id_default(),
		);

		// var_dump( $id_default[0] );

		$instance = wp_parse_args( $instance, $defaults );

		$this->widget_start( $args, $instance );

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		echo do_shortcode( "[metaslider id={$instance['id']}]" );

		$this->widget_end( $args );
	}

	/**
	 * Slider
	 */
	public function slider_ids() {
		$ids = array();
		foreach ( $this->sliders() as $slider ) {
			$ids[ $slider->ID ] = $slider->post_title;
		}

		return $ids;
	}

	/**
	 * Slider
	 */
	public function slider_id_default() {
		$id_default = array();
		foreach ( $this->sliders() as $slider ) {
			$id_default[] = $slider->ID;
		}

		return $id_default[0];
	}
}
