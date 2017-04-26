<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * RT Facebook Comment Widget.
 *
 * Showing facebook comment.
 *
 * @link https://github.com/namncn/ncn-facebook-comment
 *
 * @author   NamNCN
 * @category Widgets
 * @package  RTCORE/Widgets
 * @version  1.0.0
 * @extends  NCN_Widget
 */
class RT_Facebook_Comment_Widget extends RT_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'rt_facebook_comment';
		$this->widget_description = esc_html__( "Hiển thị bình luận Facebook.", 'raothue' );
		$this->widget_id          = 'rt_facebook_comment';
		$this->widget_name        = esc_html__( 'RT: Bình luận Facebook', 'raothue' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => esc_html__( 'Bình luận', 'raothue' ),
				'label' => esc_html__( 'Tiêu đề', 'raothue' ),
			),
			'data_numposts' => array(
				'type'          => 'number',
				'min'           => 1,
				'max'           => 1000,
				'step'          => 1,
				'std'           => 5,
				'label'         => esc_html__( 'Số bình luận muốn hiển thị', 'raothue' ),
			),
			'data_width' => array(
				'type'   => 'text',
				'std'    => '100%',
				'desc'   => esc_html__( 'Chiều rộng của khung bình luận. Tối thiểu là 320. Bỏ qua nếu bạn muốn cho chiều rộng rộng 100% bằng với khung chứa.', 'raothue' ),
				'label'  => esc_html__( 'Chiều rộng:', 'raothue' ),
			),
			'data_order_by'  => array(
				'type'   => 'select',
				'std'    => 'social',
				'label'  => esc_html__( 'Sắp xếp bình luận theo thứ tự', 'raothue' ),
				'options' => array(
					'social'       => esc_html__( 'Hàng đầu', 'raothue' ),
					'reverse_time' => esc_html__( 'Mới nhất', 'raothue' ),
					'time'         => esc_html__( 'Cũ nhất', 'raothue' ),
				),
			),
			'data_colorscheme'  => array(
				'type'  => 'select',
				'std'   => 'light',
				'label' => esc_html__( 'Chọn màu sắc khung bình luận:', 'raothue' ),
				'options' => array(
					'light' => esc_html__( 'Trắng', 'raothue' ),
					'dark'  => esc_html__( 'Đen', 'raothue' ),
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
			'data_numposts'    => 5,
			'data_width'       => '100%',
			'data_order_by'    => 'social',
			'data_colorscheme' => 'light',
		);

		$instance = wp_parse_args( $instance, $defaults );

		$this->widget_start( $args, $instance );

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}
		?>

		<div class="fb-comments" data-href="<?php the_permalink(); ?>" data-order-by="<?php echo esc_attr( $instance['data_order_by'] ); ?>" data-colorscheme="<?php echo esc_attr( $instance['data_colorscheme'] ); ?>" data-width="<?php echo esc_attr( $instance['data_width'] ); ?>" data-numposts="<?php echo esc_attr( $instance['data_numposts'] ); ?>"></div>

		<?php
		$this->widget_end( $args );
	}
}
