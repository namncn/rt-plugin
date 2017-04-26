<?php

$settings = array(

	'settings'  => array(

		'general-options' => array(
			'title' => __( 'Cài đặt cơ bản', 'yith-woocommerce-quick-view' ),
			'type' => 'title',
			'desc' => '',
			'id' => 'yith-wcqv-general-options'
		),

		'enable-quick-view' => array(
			'id'        => 'yith-wcqv-enable',
			'name'      => __( 'Bật chức năng xem nhanh', 'yith-woocommerce-quick-view' ),
			'type'      => 'checkbox',
			'default'   => 'yes'
		),

		'enable-quick-view-mobile'  => array(
			'id'        => 'yith-wcqv-enable-mobile',
			'name'      => __( 'Bật chức năng xem nhanh trên điện thoại', 'yith-woocommerce-quick-view' ),
			'type'      => 'checkbox',
			'default'   => 'yes'
		),

		'quick-view-label'  => array(
			'id'        => 'yith-wcqv-button-label',
			'name'      => __( 'Thay đổi chữ Xem nhanh', 'yith-woocommerce-quick-view' ),
			'type'      => 'text',
			'default'   => __( 'Xem nhanh', 'yith-woocommerce-quick-view' ),
		),

		'enable-lightbox' => array(
			'id'        => 'yith-wcqv-enable-lightbox',
			'name'      => __( 'Bật Lightbox', 'yith-woocommerce-quick-view' ),
			'desc'      => __( 'Bật lightbox. Hình ảnh sản phẩm sẽ được xem trên lightbox.', 'yith-woocommerce-quick-view' ),
			'type'      => 'checkbox',
			'default'   => 'yes'
		),

		'general-options-end' => array(
			'type'      => 'sectionend',
			'id'        => 'yith-wcqv-general-options'
		),

		// 'style-options' => array(
		// 	'title' => __( 'Style Options', 'yith-woocommerce-quick-view' ),
		// 	'desc'  => '',
		// 	'type'  => 'title',
		// 	'id'    => 'yith-wcqv-style-options'
		// ),

		// 'background-color-modal' => array(
		// 	'name'    => __( 'Modal Window Background Color', 'yith-woocommerce-quick-view' ),
		// 	'type'    => 'color',
		// 	'desc'    => '',
		// 	'id'      => 'yith-wcqv-background-modal',
		// 	'default' => '#ffffff'
		// ),

		// 'close-button-color' => array(
		// 	'name'    => __( 'Closing Button Color', 'yith-woocommerce-quick-view' ),
		// 	'type'    => 'color',
		// 	'desc'    => '',
		// 	'id'      => 'yith-wcqv-close-color',
		// 	'default' => '#cdcdcd'
		// ),

		// 'close-button-color-hover' => array(
		// 	'name'    => __( 'Closing Button Hover Color', 'yith-woocommerce-quick-view' ),
		// 	'type'    => 'color',
		// 	'desc'    => '',
		// 	'id'      => 'yith-wcqv-close-color-hover',
		// 	'default' => '#ff0000'
		// ),

		'style-options-end' => array(
			'type'  => 'sectionend',
			'id'    => 'yith-wcqv-style-options'
		),


	)
);

return apply_filters( 'yith_wcqv_panel_settings_options', $settings );