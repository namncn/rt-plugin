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
 * RT Popular Posts Widget.
 *
 * Show popular posts.
 *
 * @author   NamNCN
 * @category Widgets
 * @package  RTCORE/Widgets
 * @version  1.0.0
 * @extends  RT_Widget
 */
class RT_Popular_Posts_Widget extends RT_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'rt-popular-posts-widget';
		$this->widget_description = esc_html__( "Hiển thị bài viết phổ biến.", 'raothue' );
		$this->widget_id          = 'rt-popular-posts-widget';
		$this->widget_name        = esc_html__( 'RT: Bài viết phổ biến', 'raothue' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'label' => esc_html__( 'Tiêu đề:', 'raothue' ),
				'std'   => esc_html__( 'Bài viết phổ biến', 'raothue' ),
			),
			'number' => array(
				'type'   => 'text',
				'std'    => 5,
				'label'  => esc_html__( 'Số lượng bài viết muốn hiển thị:', 'raothue' ),
				'desc'   => esc_html__( 'Điền "-1" để show tất cả bài viết', 'raothue' ),
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
			'number' => 5,
		);

		$instance = wp_parse_args( $instance, $defaults );

		$post_args = array(
			'posts_per_page'      => absint( $instance['number'] ),
			'ignore_sticky_posts' => 1,
			'meta_key'            => 'postview_number',
			'orderby'             => 'meta_value_num',
		);

		$post_query = new WP_Query( $post_args );

		$this->widget_start( $args, $instance );

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		if ( $post_query->have_posts() ) : ?>

			<div class="rt__list_posts">

			<?php while ( $post_query->have_posts() ) : $post_query->the_post(); ?>

				<div class="list_item">

					<?php if ( has_post_thumbnail() ) : ?>

					<div class="list_item-thumbnail">

						<a href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail( 'medium' ); ?>
						</a>

					</div><!-- .entry-thumbnail -->

					<?php endif; ?>

					<div class="list_item-details">

						<?php the_title( '<a href="' . get_the_permalink() . '" class="list_item--title">', '</a>' ); ?>

						<div class="list_item--excerpt">
							<?php the_excerpt(); ?>
						</div>

					</div><!-- .list_item-details -->

				</div><!-- .list_item -->

			<?php endwhile; ?>

			</div><!-- .rt_list_posts -->

			<?php wp_reset_postdata();

		endif;

		$this->widget_end( $args );
	}
}
