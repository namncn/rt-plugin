<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RT Posts By Category Widget.
 *
 * Show posts by category.
 *
 * @author   NamNCN
 * @category Widgets
 * @package  RTCORE/Widgets
 * @version  1.0.0
 * @extends  RT_Widget
 */
class RT_Post_By_Category_Widget extends RT_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'rt_post_by_category_widget';
		$this->widget_description = esc_html__( "Hiển thị bài viết theo chuyên mục.", 'raothue' );
		$this->widget_id          = 'rt_post_by_category_widget';
		$this->widget_name        = esc_html__( 'RT: Danh sách bài viết', 'raothue' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'label' => esc_html__( 'Tiêu đề', 'raothue' ),
				'std'   => esc_html__( 'Bài viết theo chuyên mục', 'raothue' ),
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
			'number' => array(
				'type'   => 'text',
				'std'    => 6,
				'label'  => esc_html__( 'Số bài viết muốn hiển thị:', 'raothue' ),
				'desc'   => esc_html__( 'Điền -1 để hiển thị tất cả bài viết', 'raothue' ),
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
			'thumb_position' => array(
				'type'  => 'select',
				'std'   => 'top',
				'options' => array(
					'top'  => esc_html__( 'Phía trên tiêu đề', 'raothue' ),
					'left' => esc_html__( 'Phía bên trái tiêu đề', 'raothue' ),
				),
				'label' => esc_html__( 'Chọn kiểu hiển thị hình ảnh đại diện?', 'raothue' ),
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
			'on_off_thumb' => array(
				'type'  => 'checkbox',
				'label' => esc_html__( 'Bật/tắt hình ảnh đại diện', 'raothue' ),
				'std'   => true,
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

		// extract( $instance ); Don't extract variable $args, $instance cuz its not work when selective refresh.
		$defaults = array(
			'layout'         => 6,
			'number'         => 6,
			'cat'            => '-1',
			'thumb_position' => 'top',
			'heading'        => 'h4',
			'on_off_thumb'   => true,
			'excerpt'        => true,
			'meta'           => true,
		);

		$instance = wp_parse_args( $instance, $defaults );

		$post_args = array(
			'posts_per_page'       => $instance['number'],
			'ignore_sticky_posts'  => true,
		);

		if ( '-1' != $instance['cat'] ) {
			$post_args['cat'] = $instance['cat'];
		}

		$post_query = new WP_Query( $post_args );

		$this->widget_start( $args, $instance );

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {

			echo $args['before_title'];

			if ( '-1' != $instance['cat'] ) {
				echo '<a href="' . get_category_link( $instance['cat'] ) . '">' . get_the_category_by_ID( $instance['cat'] ) . '</a>';
			} else {
				echo $title;
			}

			echo $args['after_title'];

		}

		if ( $post_query->have_posts() ) : ?>

			<div class="rt__grid_posts row">

			<?php while ( $post_query->have_posts() ) : $post_query->the_post(); ?>

				<div class="list_item col-md-<?php echo esc_attr( $instance['layout'] ); ?><?php echo ( 'left' == $instance['thumb_position'] ) ? ' clearfix' : ''; ?>">

					<?php if ( has_post_thumbnail() && $instance['on_off_thumb'] ) : ?>

					<div class="list_item-thumbnail<?php echo ( 'left' == $instance['thumb_position'] ) ? ' thumbnail-left' : ''; ?>">

						<a href="<?php the_permalink(); ?>">
							<?php the_post_thumbnail( 'medium' ); ?>
						</a>

					</div>

					<?php endif; ?>

					<div class="list_item-details">

						<?php the_title( '<' . esc_attr( $instance['heading'] ) . ' class="list_item--title' . esc_attr( 'left' == $instance['thumb_position'] ? ' mt-0' : '' ) . '"><a href="' . get_the_permalink() . '">', '</a></' . esc_attr( $instance['heading'] ) . '>' ); ?>

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

			</div><!-- .list__items -->

			<?php wp_reset_postdata();
		endif;

		$this->widget_end( $args );
	}
}
