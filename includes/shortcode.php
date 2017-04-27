<?php
/**
 * Register shortcode.
 *
 * @package NCNTeam
 */
if ( ! defined( 'ABSPATH' ) ) { die; } // Cannot access pages directly.

function ncn_shortcode_config() {
	$options = array();

	// -----------------------------------------
	// Basic Shortcode Examples                -
	// -----------------------------------------
	$options[]     = array(
	  'title'      => 'Basic Shortcode Examples',
	  'shortcodes' => array(

	    // begin: shortcode
	    array(
	      'name'      => 'cs_shortcode_1',
	      'title'     => 'Basic Shortcode 1',
	      'fields'    => array(

	        // shortcode option field
	        array(
	          'id'    => 'icon',
	          'type'  => 'icon',
	          'title' => 'Icon',
	        ),

	        array(
	          'id'    => 'image',
	          'type'  => 'image',
	          'title' => 'Image',
	        ),

	        // shortcode option field
	        array(
	          'id'    => 'gallery',
	          'type'  => 'gallery',
	          'title' => 'Gallery',
	        ),

	        // shortcode option field
	        array(
	          'id'    => 'title',
	          'type'  => 'text',
	          'title' => 'Title',
	        ),


	        // shortcode option field
	        array(
	          'id'    => 'title',
	          'type'  => 'text',
	          'title' => 'Title',
	        ),

	        // shortcode content
	        array(
	          'id'    => 'content',
	          'type'  => 'textarea',
	          'title' => 'Content',
	          'help'  => 'Lorem Ipsum Dollar.',
	        )

	      ),
	    ),

	  ),
	);

	return $options;
}

add_filter( 'cs_shortcode_options', 'ncn_shortcode_config' );

/**
 * Register series shortcode.
 *
 * @param array $atts //
 */
function rt_series_shortcode( $atts ) {
	$args = shortcode_atts( array(
		'cat' => 1,
	), $atts );

	$post_args = array(
		'post_type'           => 'post',
		'posts_per_page'      => -1,
		'ignore_sticky_posts' => 1,
		'cat'                 => $args['cat'],
	);

	$post_query = new WP_Query( $post_args );
	$html =  '<ul class="series">';
	if ( $post_query->have_posts() ) :
		while ( $post_query->have_posts() ) : $post_query->the_post();
			$html .= '<li><a href="' . get_the_permalink() . '">' . get_the_title() . '</a></li>';
		endwhile;
		wp_reset_postdata();
	endif;
	$html .= '</ul>';

	return $html;
}
add_shortcode( 'series', 'rt_series_shortcode' );

