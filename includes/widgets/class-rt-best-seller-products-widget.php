<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RT products By Category Widget.
 *
 * Show products by category.
 *
 * @author   NamNCN
 * @category Widgets
 * @package  RTCORE/Widgets
 * @version  1.0.0
 * @extends  RT_Widget
 */
class RT_Best_Seller_Products_Widget extends RT_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'rt_best_seller_products_widget woocommerce';
		$this->widget_description = esc_html__( "Hiển thị sản phẩm bán chạy.", 'raothue' );
		$this->widget_id          = 'rt_best_seller_products_widget';
		$this->widget_name        = esc_html__( 'RT: Sản phẩm bán chạy', 'raothue' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'label' => esc_html__( 'Tiêu đề', 'raothue' ),
				'std'   => esc_html__( 'Sản phẩm bán chạy', 'raothue' ),
			),
			'number' => array(
				'type'   => 'text',
				'std'    => 5,
				'label'  => esc_html__( 'Số sản phẩm muốn hiển thị:', 'raothue' ),
				'desc'   => esc_html__( 'Điền -1 để hiển thị tất cả sản phẩm', 'raothue' ),
			),
			'on_off_slider' => array(
				'type'  => 'checkbox',
				'std'   => false,
				'label' => esc_html__( 'Bật/tắt chế độ cuộn', 'raothue' ),
			),
			'items' => array(
				'type'  => 'number',
				'std'   => 4,
				'min'   => 1,
				'max'   => 15,
				'label' => esc_html__( 'Hiển thị bao nhiêu slide trong 1 lần?', 'raothue' ),
			),
			'scroll' => array(
				'type'  => 'number',
				'std'   => 1,
				'min'   => 1,
				'max'   => 5,
				'label' => esc_html__( 'Chọn số slide một lần cuộn', 'raothue' ),
			),
			'speed' => array(
				'type'  => 'number',
				'std'   => 5000,
				'min'   => 1000,
				'max'   => 15000,
				'label' => esc_html__( 'Chọn tốc độ cuộn', 'raothue' ),
			),
			'autoplaySpeed' => array(
				'type'  => 'number',
				'std'   => 5000,
				'min'   => 1000,
				'max'   => 50000,
				'label' => esc_html__( 'Chọn tốc độ tự động cuộn cuộn', 'raothue' ),
			),
			'autoplay' => array(
				'type'  => 'checkbox',
				'std'   => true,
				'label' => esc_html__( 'Bật/tắt chế độ tự động cuộn', 'raothue' ),
			),
			'arrows' => array(
				'type'  => 'checkbox',
				'label' => esc_html__( 'Bật/tắt Mũi tên điều hướng', 'raothue' ),
				'std'   => false,
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

		// extract( $instance ); Don't extract variable $args, $instance cuz its not work when selective refresh.
		$defaults = array(
			'number'        => 5,
			'on_off_slider' => false,
			'items'         => 3,
			'scroll'        => 1,
			'speed'         => 5000,
			'autoplaySpeed' => 5000,
			'autoplay'      => true,
			'arrows'        => false,
		);

		$instance = wp_parse_args( $instance, $defaults );

		$post_args = array(
			'post_type'           => 'product',
			'posts_per_page'      => $instance['number'],
			'ignore_sticky_posts' => true,
			'meta_key'            => 'total_sales',
			'orderby'             => 'meta_value_num',
		);

		$post_query = new WP_Query( $post_args );

		$this->widget_start( $args, $instance );

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {

			echo $args['before_title'] . $title . $args['after_title'];

		}

		$rand = wp_rand( 10, 1000 );

		if ( $post_query->have_posts() ) : ?>

			<ul id="rt__best_seller_products-<?php echo $rand; ?>" class="rt__best_seller_products products row">

			<?php while ( $post_query->have_posts() ) : $post_query->the_post(); ?>

				<?php wc_get_template_part( 'content', 'product' ); ?>

			<?php endwhile; ?>

			</ul><!-- .list__items -->

			<?php if ( $instance['on_off_slider'] ) : ?>

			<script type="text/javascript">
				jQuery(document).ready(function($) {
					"use strict";
					$('#rt__best_seller_products-<?php echo $rand; ?>').slick({
						speed: <?php echo $instance['speed']; ?>,
						vertical: true,
						slidesToShow: <?php echo absint( $instance['items'] ); ?>,
						slidesToScroll: <?php echo absint( $instance['scroll'] ); ?>,
						verticalSwiping: true,
						autoplay: <?php echo $instance['autoplay']; ?>,
						autoplaySpeed: <?php echo $instance['autoplaySpeed']; ?>,
						arrows: <?php echo true == $instance['arrows'] ? 'true' : 'false'; ?>,
						prevArrow: '<button type="button" class="slick-prev"></button>',
						nextArrow: '<button type="button" class="slick-next"></button>',
						responsive: [
						{
							breakpoint: 769,
							settings: {
								slidesToShow: 2,
								slidesToScroll: 1,
								arrows: <?php echo true == $instance['arrows'] ? 'true' : 'false'; ?>,
							}
						},
						{
							breakpoint: 321,
							settings: {
								slidesToShow: 1,
								slidesToScroll: 1,
								arrows: <?php echo true == $instance['arrows'] ? 'true' : 'false'; ?>,
							}
						},
						]
					});
				});
			</script>

			<?php endif; ?>

			<?php wp_reset_postdata();
		endif;

		$this->widget_end( $args );
	}
}
