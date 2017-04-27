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
class RT_Post_By_Category_2_Widget extends RT_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'rt_post_by_category_2_widget';
		$this->widget_description = esc_html__( "Hiển thị bài viết theo chuyên mục kiểu 2.", 'raothue' );
		$this->widget_id          = 'rt_post_by_category_2_widget';
		$this->widget_name        = esc_html__( 'RT: Danh sách bài viết 2', 'raothue' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'label' => esc_html__( 'Tiêu đề', 'raothue' ),
				'std'   => esc_html__( 'Bài viết theo chuyên mục', 'raothue' ),
			),
			'number' => array(
				'type'   => 'text',
				'std'    => 5,
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
			'title_position' => array(
				'type'  => 'select',
				'std'   => 'top',
				'options' => array(
					'default'  => esc_html__( 'Mặc định', 'raothue' ),
					'on' => esc_html__( 'Nằm trong ảnh đại diện', 'raothue' ),
				),
				'label' => esc_html__( 'Chọn kiểu hiển thị tiêu đề?', 'raothue' ),
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
				'label' => esc_html__( 'Bật/tắt mô tả ngắn ở bài đầu tiên', 'raothue' ),
				'std'   => true,
			),
			'readmore' => array(
				'type'  => 'checkbox',
				'label' => esc_html__( 'Bật/tắt nút xem thêm ở bài viết đầu tiên', 'raothue' ),
				'std'   => true,
			),
			'excerpt_lenght' => array(
				'type'  => 'number',
				'label' => esc_html__( 'Số lượng chữ mỗ tả ngắn', 'raothue' ),
				'std'   => 40,
				'step' => 1,
				'min' => 1,
				'max' => 1000,
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
			'number'         => 5,
			'cat'            => '-1',
			'title_position' => 'default',
			'heading'        => 'h4',
			'excerpt'        => true,
			'readmore'       => true,
			'excerpt_lenght' => 40,
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

			<?php
			$i = 0;
			while ( $post_query->have_posts() ) : $post_query->the_post(); $i++; ?>

				<?php if ( 1 == $i ) : ?>
				<div class="list_item col-md-6 col-sm-6 col-xs-12">
				<?php endif; ?>

				<?php if ( 2 == $i ) : ?>
				</div>
				<div class="list_item col-md-6 col-sm-6 col-xs-12">
				<?php endif; ?>

					<?php if ( has_post_thumbnail() ) : ?>

					<div class="list_item-thumbnail">

						<a href="<?php the_permalink(); ?>">
							<?php if ( 1 == $i ) {
								the_post_thumbnail( 'medium' );
							} else {
								the_post_thumbnail( 'thumbnail' );
							} ?>

							<?php if ( 'on' == $instance['title_position'] && 1 == $i ) : ?>
								<?php the_title( '<' . esc_attr( $instance['heading'] ) . ' class="list_item--title">', '</' . esc_attr( $instance['heading'] ) . '>' ); ?>
							<?php endif; ?>
						</a>

					</div>

					<?php endif; ?>

					<div class="list_item-details">

						<?php if ( 'on' != $instance['title_position'] || 1 != $i ) {
							the_title( '<' . esc_attr( $instance['heading'] ) . ' class="list_item--title"><a href="' . get_the_permalink() . '">', '</a></' . esc_attr( $instance['heading'] ) . '>' );
						} ?>

						<?php if ( $instance['excerpt'] ) : ?>

						<div class="list_item--excerpt">
							<p><?php echo esc_html( wp_trim_words( get_the_content(), $instance['excerpt_lenght'] ) ); ?></p>

							<?php if ( $instance['readmore'] && 1 == $i ) : ?>
								<a href="" class="readmore"><?php echo esc_html__( 'Xem thêm &raquo;', 'raothue' ); ?></a>
							<?php endif; ?>
						</div>

						<?php endif; ?>

					</div><!-- .list_item-details -->

			<?php endwhile; ?>

			</div><!-- .list_item -->

			</div><!-- .list__items -->

			<?php wp_reset_postdata();
		endif;

		$this->widget_end( $args );
	}
}
