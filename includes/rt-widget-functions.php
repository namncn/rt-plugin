<?php
/**
 * RT-Theme Widget Functions
 *
 * Widget related functions and widget registration.
 *
 * @author 		NamNCN
 * @category 	Core
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Register Widgets.
 *
 * @since 1.0.0
 */
function rt_register_widgets() {
	register_widget( 'RT_Fbpage_Widget' );
	register_widget( 'RT_Facebook_Comment_Widget' );
	register_widget( 'RT_Recent_Posts_Widget' );
	register_widget( 'RT_Post_By_Category_Widget' );
	register_widget( 'RT_Popular_Posts_Widget' );
	register_widget( 'RT_Related_Posts_Widget' );
	register_widget( 'RT_Posts_Slider_Widget' );
	register_widget( 'RT_Textarea_Widget' );

	if ( class_exists( 'MetaSliderPlugin' ) ) {
		unregister_widget( 'MetaSlider_Widget' );
		register_widget( 'RT_Slider_Widget' );
	}

	if ( function_exists( 'WC' ) ) {
		register_widget( 'RT_Best_Seller_Products_Widget' );
		register_widget( 'RT_Recent_Products_Widget' );
		register_widget( 'RT_Products_By_Category_Widget' );
	}

}
add_action( 'widgets_init', 'rt_register_widgets' );
