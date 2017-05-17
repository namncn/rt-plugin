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
 * RT Partner Widget.
 *
 * Show partner.
 *
 * @author   NamNCN
 * @category Widgets
 * @package  RTCORE/Widgets
 * @version  1.0.0
 * @extends  RT_Widget
 */
class RT_Partner_Widget extends RT_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'rt-partner-widget';
		$this->widget_description = esc_html__( "Hiển thị logo các đối tác.", 'raothue' );
		$this->widget_id          = 'rt-partner-widget';
		$this->widget_name        = esc_html__( 'RT: Đối tác', 'raothue' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => esc_html__( 'Đối tác', 'raothue' ),
				'label' => esc_html__( 'Tiêu đề:', 'raothue' ),
			),
			'number' => array(
				'type'   => 'text',
				'std'    => 6,
				'label'  => esc_html__( 'Số đối tác muốn hiển thị:', 'raothue' ),
				'desc'   => esc_html__( 'Điền "-1" để hiển thị tất cả', 'raothue' ),
			),
			'items' => array(
				'type'  => 'number',
				'step' => 1,
				'std'   => 5,
				'min'   => 1,
				'max'   => 15,
				'label' => esc_html__( 'Chọn số cột muốn hiển thị', 'raothue' ),
			),
			'slider' => array(
				'type'  => 'checkbox',
				'std'   => true,
				'label' => esc_html__( 'Bật/Tắt chế độ cuộn?', 'raothue' ),
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
				'step' => 1,
				'std'   => 3,
				'min'   => 1,
				'max'   => 5,
				'label' => esc_html__( 'Chọn số slide một lần cuộn', 'raothue' ),
			),
			'speed' => array(
				'type'  => 'number',
				'step' => 1,
				'std'   => 5000,
				'min'   => 1000,
				'max'   => 50000,
				'label' => esc_html__( 'Chọn tốc độ cuộn', 'raothue' ),
			),
			'autoplaySpeed' => array(
				'type'  => 'number',
				'step' => 1,
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
			'slider'          => 1,
			'items'           => 5,
			'style'           => 'horizontal',
			'scroll'          => 3,
			'speed'           => 5000,
			'autoplaySpeed'   => 5000,
			'autoplay'        => true,
			'arrows'          => true,
		);

		$instance = wp_parse_args( $instance, $defaults );

		$post_args = array(
			'post_type'           =>'partner',
			'posts_per_page'      => $instance['number'],
			'ignore_sticky_posts' => 1,
		);

		$post_query = new WP_Query( $post_args );

		$this->widget_start( $args, $instance );

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {

			echo $args['before_title'] . $title . $args['after_title'];

		}

		$rand = wp_rand( 10, 1000 );

		if ( $post_query->have_posts() ) : ?>

			<div class="rt__partner_sliders-<?php echo $rand; ?>">

			<?php while ( $post_query->have_posts() ) : $post_query->the_post();
				$link = '';
				$link = get_post_meta( get_the_ID(), 'partner', true )['link'];
			?>

				<div class="slider_item">

					<?php if ( has_post_thumbnail() ) : ?>

					<div class="slider_item-thumbnail">

						<a href="<?php echo esc_url( $link ); ?>">
							<?php the_post_thumbnail( 'medium' ); ?>
						</a>

					</div><!-- .slider_item-thumbnail -->

					<?php endif; ?>

				</div><!-- .slider_item -->

			<?php endwhile; ?>

			</div><!-- .rt__partner_sliders -->

			<?php if ( $instance['slider'] ) : ?>

			<script type="text/javascript">
				jQuery(document).ready(function($) {
					"use strict";
					$('.rt__partner_sliders-<?php echo $rand; ?>').slick({
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

			<?php endif; ?>

			<?php wp_reset_postdata();

		endif;

		$this->widget_end( $args );
	}
}
