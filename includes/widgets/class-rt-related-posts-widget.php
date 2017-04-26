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
 * RT Related Posts Widget.
 *
 * Show related posts.
 *
 * @author   NamNCN
 * @category Widgets
 * @package  RTCORE/Widgets
 * @version  1.0.0
 * @extends  RT_Widget
 */
class RT_Related_Posts_Widget extends RT_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'rt-related-posts-widget';
		$this->widget_description = esc_html__( "Hiển thị bài viết liên quan.", 'raothue' );
		$this->widget_id          = 'rt-related-posts-widget';
		$this->widget_name        = esc_html__( 'RT: Bài viết liên quan', 'raothue' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => esc_html__( 'Bài viết liên quan', 'raothue' ),
				'label' => esc_html__( 'Tiêu đề:', 'raothue' ),
			),
			'number' => array(
				'type'   => 'text',
				'std'    => 6,
				'label'  => esc_html__( 'Số bài viết muốn hiển thị:', 'raothue' ),
				'desc'   => esc_html__( 'Điền "-1" để hiển thị tất cả', 'raothue' ),
			),
			'layout' => array(
				'type'  => 'select',
				'std'   => '6',
				'options' => array(
					'12' => esc_html__( '1 Cột' ),
					'6'  => esc_html__( '2 Cột' ),
					'4'  => esc_html__( '3 Cột' ),
					'3'  => esc_html__( '4 Cột' ),
				),
				'label' => esc_html__( 'Chọn số cột muốn hiển thị', 'raothue' ),
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
			'number'  => 6,
			'layout'  => '6',
			'heading' => 'h4',
			'excerpt' => true,
			'meta'    => true,
		);

		$instance = wp_parse_args( $instance, $defaults );

		$post_args = array(
			'posts_per_page'       => $instance['number'],
			'ignore_sticky_posts'  => 1,
			'category__in'         => wp_get_post_categories( get_the_ID() ),
			'post__not_in'         => array( get_the_ID() ),
		);

		$post_query = new WP_Query( $post_args );

		if ( 'post' == get_post_type() ) {

			$this->widget_start( $args, $instance );

			if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
				echo $args['before_title'] . $title . $args['after_title'];
			}

			if ( $post_query->have_posts() ) : ?>

				<div class="rt__list_posts row">

				<?php while ( $post_query->have_posts() ) : $post_query->the_post(); ?>

					<div class="list_item col-md-<?php echo esc_attr( $instance['layout'] ); ?> clearfix">

						<?php if ( has_post_thumbnail() ) : ?>

						<div class="list_item-thumbnail thumbnail-left">

							<a href="<?php the_permalink(); ?>">
								<?php the_post_thumbnail( 'thumbnail' ); ?>
							</a>

						</div><!-- .list_item-thumbnail -->

						<?php endif; ?>

						<div class="list_item-details">

							<?php the_title( '<' . esc_attr( $instance['heading'] ) . ' class="list_item--title mt-0"><a href="' . get_the_permalink() . '">', '</a></' . esc_attr( $instance['heading'] ) . '>' ); ?>

							<?php if ( $instance['meta'] ) : ?>

							<div class="list_item--meta">
								<?php the_time( 'd/m/Y' ); ?>
							</div>

							<?php endif; ?>

							<?php if ( $instance['excerpt'] ) : ?>

							<div class="list_item--excerpt">
								<?php the_excerpt(); ?>
							</div>

							<?php endif; ?>

						</div><!-- .list_item-details -->

					</div><!-- .list_item -->

				<?php endwhile; ?>

				</div><!-- .rt__list_posts -->

				<?php wp_reset_postdata();

			endif;

			$this->widget_end( $args );
		}
	}
}
