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
 * RT Recent Posts Widget.
 *
 * Show recent posts.
 *
 * @link https://github.com/namncn/ncn-recent-posts
 *
 * @author   NamNCN
 * @category Widgets
 * @package  RTCORE/Widgets
 * @version  1.0.0
 * @extends  RT_Widget
 */
class RT_Recent_Posts_Widget extends RT_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'rt-recent-posts-widget';
		$this->widget_description = esc_html__( "Hiển thị danh sách bài viết mới nhất.", 'raothue' );
		$this->widget_id          = 'rt-recent-posts-widget';
		$this->widget_name        = esc_html__( 'RT: Bài viết mới nhất', 'raothue' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => esc_html__( 'Bài viết mới nhất', 'raothue' ),
				'label' => esc_html__( 'Tiêu đề:', 'raothue' ),
			),
			'number' => array(
				'type'   => 'text',
				'std'    => 5,
				'label'  => esc_html__( 'Tổng số bài viết muốn hiển thị:', 'raothue' ),
				'desc'   => esc_html__( 'Điền "-1" để hiển thị tất cả', 'raothue' ),
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
				'std'   => 3,
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
			'meta' => array(
				'type'  => 'checkbox',
				'label' => esc_html__( 'Bật/tắt Ngày tháng năm đăng bài', 'raothue' ),
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

		// extract( $instance ); Don't extract variable $args, $instance cuz its not work when selective refresh.

		$defaults = array(
			'number'        => 6,
			'on_off_slider' => false,
			'items'         => 3,
			'scroll'        => 3,
			'speed'         => 5000,
			'autoplaySpeed' => 5000,
			'autoplay'      => true,
			'arrows'        => true,
		);

		$instance = wp_parse_args( $instance, $defaults );

		$post_args = array(
			'posts_per_page'       => $instance['number'],
			'ignore_sticky_posts'  => 1,
		);

		$post_query = new WP_Query( $post_args );

		$this->widget_start( $args, $instance );

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$rand = wp_rand( 10, 1000 );

		if ( $post_query->have_posts() ) : ?>

			<div class="rt__list_posts-<?php echo $rand; ?>">

			<?php while ( $post_query->have_posts() ) : $post_query->the_post(); ?>

				<div class="list_item clearfix">

					<?php if ( has_post_thumbnail() ) : ?>

					<div class="list_item-thumbnail thumbnail-left">

						<a href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail( 'thumbnail' ); ?>
						</a>

					</div><!-- .list_item-thumbnail -->

					<?php endif; ?>

					<div class="list_item-details">

						<?php the_title( '<a href="' . get_the_permalink() . '" class="list_item--title mt-0">', '</a>' ); ?>

						<?php if ( $instance['meta'] ) : ?>

						<div class="list_item--post-meta">

							<?php the_time( 'd/m/Y' ); ?>

						</div>

						<?php endif; ?>

					</div><!-- .list_item-details -->

				</div><!-- .list_item -->

			<?php endwhile; ?>

			</div><!-- .list__items -->

			<?php if ( $instance['on_off_slider'] ) : ?>

			<script type="text/javascript">
				jQuery(document).ready(function($) {
					"use strict";
					$('.rt__list_posts-<?php echo $rand; ?>').slick({
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
