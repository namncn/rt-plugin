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
 * RT Support Widget.
 *
 * Show support.
 *
 * @author   NamNCN
 * @category Widgets
 * @package  RTCORE/Widgets
 * @version  1.0.0
 * @extends  RT_Widget
 */
class RT_Support_Widget extends RT_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'rt-support-widget';
		$this->widget_description = esc_html__( "Hiển thị thông tin hỗ trợ.", 'raothue' );
		$this->widget_id          = 'rt-support-widget';
		$this->widget_name        = esc_html__( 'RT: Hỗ trợ trực tuyến', 'raothue' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => esc_html__( 'Hỗ trợ trực tuyến', 'raothue' ),
				'label' => esc_html__( 'Tiêu đề:', 'raothue' ),
			),
			'image' => array(
				'type'   => 'image',
				'std'    => '',
				'label'  => esc_html__( 'Hình ảnh hỗ trợ viên:', 'raothue' ),
			),
			'line1' => array(
				'type'  => 'text',
				'std'   => esc_html__( 'Tư vấn dịch vụ', 'raothue' ),
				'label' => esc_html__( 'Nội dung dòng 1:', 'raothue' ),
			),
			'line2' => array(
				'type'   => 'text',
				'std'    => '0986 334 556',
				'label'  => esc_html__( 'Nội dung dòng 2:', 'raothue' ),
			),
			'line3' => array(
				'type'   => 'text',
				'std'    => 'abc@yourdomain.com',
				'label'  => esc_html__( 'Nội dung dòng 3:', 'raothue' ),
			),
			'line4' => array(
				'type'   => 'text',
				'std'    => esc_html__( 'Tư vấn dịch vụ', 'raothue' ),
				'label'  => esc_html__( 'Nội dung dòng 4:', 'raothue' ),
			),
			'line5' => array(
				'type'   => 'text',
				'std'    => '0986 334 556',
				'label'  => esc_html__( 'Nội dung dòng 5:', 'raothue' ),
			),
			'line6' => array(
				'type'   => 'text',
				'std'    => 'abc@yourdomain.com',
				'label'  => esc_html__( 'Nội dung dòng 6:', 'raothue' ),
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
			'image' => '',
			'line1' => esc_html__( 'Tư vấn dịch vụ', 'raothue' ),
			'line2' => '0986 334 556',
			'line3' => 'abc@yourdomain.com',
			'line4' => esc_html__( 'Tư vấn dịch vụ', 'raothue' ),
			'line5' => '0986 334 556',
			'line6' => 'abc@yourdomain.com',
		);

		$instance = wp_parse_args( $instance, $defaults );

		$this->widget_start( $args, $instance );

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		?>

		<div class="rt__support">

			<?php if ( $instance['image'] ) : ?>
			<div class="rt__support_top">
				<?php echo wp_get_attachment_image( $instance['image'], 'full' ); ?>
			</div>
			<?php endif; ?>

			<?php
			unset( $instance['title'] );
			unset( $instance['image'] );

			if ( $instance ) : ?>

				<div class="rt__support_bottom">

				<?php $i = 0;
				foreach ($instance as $key => $value) : ?>
					<div class="<?php echo esc_attr( $key ); ?>"><?php echo esc_html( $value ); ?></div>
				<?php endforeach; ?>

				</div>

			<?php endif; ?>

		</div>

		<?php
		$this->widget_end( $args );
	}
}
