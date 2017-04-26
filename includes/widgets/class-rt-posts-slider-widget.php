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
 * RT Posts Slider Widget.
 *
 * Show posts slider.
 *
 * @author   NamNCN
 * @category Widgets
 * @package  RTCORE/Widgets
 * @version  1.0.0
 * @extends  RT_Widget
 */
class RT_Posts_Slider_Widget extends RT_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'rt-posts-slider-widget';
		$this->widget_description = esc_html__( "Hiển thị bài viết liên quan.", 'raothue' );
		$this->widget_id          = 'rt-posts-slider-widget';
		$this->widget_name        = esc_html__( 'RT: Cuộn bài viết', 'raothue' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => esc_html__( 'Cuộn bài viết', 'raothue' ),
				'label' => esc_html__( 'Tiêu đề:', 'raothue' ),
			),
			'number' => array(
				'type'   => 'text',
				'std'    => 6,
				'label'  => esc_html__( 'Số bài viết muốn hiển thị:', 'raothue' ),
				'desc'   => esc_html__( 'Điền "-1" để hiển thị tất cả', 'raothue' ),
			),
			'cat'  => array(
				'type'     => 'taxonomy_select',
				'std'      => '-1',
				'label'    => esc_html__( 'Chọn chuyên mục muốn hiển thị bài viết:', 'raothue' ),
				'desc'     => esc_html__( 'Chọn chuyên mục muốn hiển thị các bài viết, nếu không chọn, sẽ hiển thị các bài viết mới nhất.', 'raothue' ),
				'options'  => array(
					'show_option_none' => esc_html__( 'Lựa chọn', 'raothue' ),
					'taxonomy'         => 'category',
				),
			),
			'items' => array(
				'type'  => 'number',
				'std'   => 4,
				'min'   => 1,
				'max'   => 15,
				'label' => esc_html__( 'Chọn số cột muốn hiển thị', 'raothue' ),
			),
			'style' => array(
				'type'  => 'select',
				'std'   => 'horizontal',
				'options' => array(
					'vertical'    => esc_html__( 'Cuộn dọc' ),
					'horizontal'  => esc_html__( 'Cuộn ngang' ),
				),
				'label' => esc_html__( 'Chọn số cột muốn hiển thị', 'raothue' ),
			),
			'scroll' => array(
				'type'  => 'number',
				'std'   => 3,
				'min'   => 1,
				'max'   => 5,
				'label' => esc_html__( 'Chọn số slide một lần cuộn', 'raothue' ),
			),
			'speed' => array(
				'type'  => 'number',
				'std'   => 5000,
				'min'   => 1000,
				'max'   => 50000,
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
			'heading' => array(
				'type'  => 'select',
				'std'   => 'h4',
				'options' => array(
					'h2' => 'H2',
					'h3' => 'H3',
					'h4' => 'H4',
					'h5' => 'H5',
				),
				'label' => esc_html__( 'Hiển thị tiêu đề theo thẻ heading gì?', 'raothue' ),
			),
			'excerpt' => array(
				'type'  => 'checkbox',
				'label' => esc_html__( 'Bật/tắt mô tả ngắn', 'raothue' ),
				'std'   => true,
			),
			'meta' => array(
				'type'  => 'checkbox',
				'label' => esc_html__( 'Bật/tắt Ngày tháng năm đăng bài', 'raothue' ),
				'std'   => true,
			),
			'show_hide_title' => array(
				'type'  => 'checkbox',
				'label' => esc_html__( 'Bật/tắt Tiêu đề bài viết', 'raothue' ),
				'std'   => true,
			),
			'arrows' => array(
				'type'  => 'checkbox',
				'label' => esc_html__( 'Bật/tắt Mũi tên điều hướng', 'raothue' ),
				'std'   => true,
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
			'number'          => 6,
			'cat'             => '-1',
			'items'           => 3,
			'style'           => 'horizontal',
			'scroll'          => 3,
			'speed'           => 5000,
			'autoplaySpeed'   => 5000,
			'autoplay'        => true,
			'heading'         => 'h4',
			'excerpt'         => true,
			'meta'            => true,
			'show_hide_title' => true,
			'arrows'          => true,
		);

		$instance = wp_parse_args( $instance, $defaults );

		$post_args = array(
			'posts_per_page'       => $instance['number'],
			'ignore_sticky_posts'  => 1,
		);

		$post_query = new WP_Query( $post_args );

		$this->widget_start( $args, $instance );

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {

			echo $args['before_title'];

			if ( '-1' != $instance['cat'] ) {
				echo '<a href="' . get_category_link( $instance['cat'] ) . '">' . $title . '</a>';
			} else {
				echo $title;
			}

			echo $args['after_title'];

		}

		$rand = wp_rand( 10, 1000 );

		if ( $post_query->have_posts() ) : ?>

			<div class="rt__posts_sliders-<?php echo $rand; ?>">

			<?php while ( $post_query->have_posts() ) : $post_query->the_post(); ?>

				<div class="slider_item">

					<?php if ( has_post_thumbnail() ) : ?>

					<div class="slider_item-thumbnail">

						<a href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail( 'medium' ); ?>
						</a>

					</div><!-- .slider_item-thumbnail -->

					<?php endif; ?>

					<?php if ( $instance['show_hide_title'] || $instance['meta'] || $instance['excerpt'] ) : ?>

					<div class="slider_item-details">

						<?php if ( $instance['show_hide_title'] ) : ?>

						<?php the_title( '<' . esc_attr( $instance['heading'] ) . ' class="slider_item--title"><a href="' . get_the_permalink() . '">', '</a></' . esc_attr( $instance['heading'] ) . '>' ); ?>

						<?php endif; ?>

						<?php if ( $instance['meta'] ) : ?>

						<div class="slider_item--meta">
							<?php the_time( 'd/m/Y' ); ?>
						</div>

						<?php endif; ?>

						<?php if ( $instance['excerpt'] ) : ?>

						<div class="slider_item--excerpt">
							<?php the_excerpt(); ?>
						</div>

						<?php endif; ?>

					</div><!-- .slider_item-details -->

					<?php endif; ?>

				</div>

			<?php endwhile; ?>

			</div><!-- .rt__slider_posts -->

			<script type="text/javascript">
				jQuery(document).ready(function($) {
					"use strict";
					$('.rt__posts_sliders-<?php echo $rand; ?>').slick({
						speed: <?php echo $instance['speed']; ?>,
						vertical: <?php echo 'vertical' == $instance['style'] ? 'true' : 'false'; ?>,
						slidesToShow: <?php echo absint( $instance['items'] ); ?>,
						slidesToScroll: <?php echo absint( $instance['scroll'] ); ?>,
						verticalSwiping: <?php echo 'vertical' == $instance['style'] ? 'true' : 'false'; ?>,
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

			<?php wp_reset_postdata();

		endif;

		$this->widget_end( $args );
	}
}
