<?php
/**
 * Register post type.
 */

/**
* Registers a new post type called partner
* @uses $wp_post_types Inserts new post type object into the list
*
* @param string  Post type key, must not exceed 20 characters
* @param array|string  See optional args description above.
* @return object|WP_Error the registered post type object, or an error object
*/
function rtcore_register_partner() {

	$labels = array(
		'name'                => esc_html__( 'Đối tác', 'raothue' ),
		'singular_name'       => esc_html__( 'Đối tác', 'raothue' ),
		'add_new'             => _x( 'Thêm mới đối tác', 'raothue', 'raothue' ),
		'add_new_item'        => esc_html__( 'Thêm mới đối tác', 'raothue' ),
		'edit_item'           => esc_html__( 'Chỉnh sửa đối tác', 'raothue' ),
		'new_item'            => esc_html__( 'Thêm mới đối tác', 'raothue' ),
		'view_item'           => esc_html__( 'Xem đối tác', 'raothue' ),
		'search_items'        => esc_html__( 'Tìm kiếm đối tác', 'raothue' ),
		'not_found'           => esc_html__( 'Không tìm thấy đối tác nào cả', 'raothue' ),
		'not_found_in_trash'  => esc_html__( 'Không có đối tác trong bộ nhớ tạm', 'raothue' ),
		'parent_item_colon'   => esc_html__( 'Parent đối tác:', 'raothue' ),
		'menu_name'           => esc_html__( 'Đối tác', 'raothue' ),
	);

	$args = array(
		'labels'                   => $labels,
		'hierarchical'        => false,
		'description'         => 'description',
		'taxonomies'          => array(),
		'public'              => true,
		'show_ui'             => true,
		'show_in_menu'        => true,
		'show_in_admin_bar'   => true,
		'menu_position'       => 30,
		'menu_icon'           => 'dashicons-format-gallery',
		'show_in_nav_menus'   => true,
		'publicly_queryable'  => true,
		'exclude_from_search' => false,
		'has_archive'         => true,
		'query_var'           => true,
		'can_export'          => true,
		'rewrite'             => true,
		'capability_type'     => 'post',
		'supports'            => array(
			'title', 'thumbnail'
			)
	);

	register_post_type( 'partner', $args );
}

add_action( 'init', 'rtcore_register_partner' );
