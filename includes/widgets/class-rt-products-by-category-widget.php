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
class RT_Products_By_Category_Widget extends RT_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'rt_products_by_category_widget woocommerce';
		$this->widget_description = esc_html__( "Hiển thị sản phẩm mới nhất hoặc theo chuyên mục.", 'raothue' );
		$this->widget_id          = 'rt_products_by_category_widget';
		$this->widget_name        = esc_html__( 'RT: Danh sách sản phẩm', 'raothue' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'label' => esc_html__( 'Tiêu đề', 'raothue' ),
				'std'   => esc_html__( 'Danh sách sản phẩm', 'raothue' ),
			),
			'number' => array(
				'type'   => 'text',
				'std'    => 6,
				'label'  => esc_html__( 'Số sản phẩm muốn hiển thị:', 'raothue' ),
				'desc'   => esc_html__( 'Điền -1 để hiển thị tất cả sản phẩm', 'raothue' ),
			),
			'cat'  => array(
				'type'     => 'taxonomy_select',
				'std'      => '-1',
				'label'    => esc_html__( 'Chọn chuyên mục muốn hiển thị sản phẩm:', 'raothue' ),
				'desc'     => esc_html__( 'Chọn chuyên mục muốn hiển thị các sản phẩm, nếu không chọn, sẽ hiển thị các sản phẩm mới nhất.', 'raothue' ),
				'options'  => array(
					'show_option_none' => esc_html__( 'Lựa chọn', 'raothue' ),
					'taxonomy'         => 'product_cat',
				),
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
			'number'         => 6,
			'cat'            => '-1',
		);

		$instance = wp_parse_args( $instance, $defaults );

		$post_args = array(
			'post_type'            => 'product',
			'posts_per_page'       => $instance['number'],
			'ignore_sticky_posts'  => true,
		);

		if ( '-1' != $instance['cat'] ) {
			$post_args['tax_query'] = array(
				array(
					'taxonomy' => 'product_cat',
					'feild'    => 'term_id',
					'terms'    => array( $instance['cat'] ),
				)
			);
		}

		$post_query = new WP_Query( $post_args );

		$this->widget_start( $args, $instance );

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {

			echo $args['before_title'] . $title . $args['after_title'];

		}

		if ( $post_query->have_posts() ) : ?>

			<ul class="rt__grid_products row">

			<?php while ( $post_query->have_posts() ) : $post_query->the_post(); ?>

				<?php wc_get_template_part( 'content', 'product' ); ?>

			<?php endwhile; ?>

			</ul><!-- .list__items -->

			<?php wp_reset_postdata();
		endif;

		$this->widget_end( $args );
	}
}
