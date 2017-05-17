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
 * RT Image Widget.
 *
 * Show image.
 *
 * @author   NamNCN
 * @category Widgets
 * @package  RTCORE/Widgets
 * @version  1.0.0
 * @extends  RT_Widget
 */
class RT_Image_Widget extends RT_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'rt-image-widget';
		$this->widget_description = esc_html__( "Hiển thị hình ảnh.", 'raothue' );
		$this->widget_id          = 'rt-image-widget';
		$this->widget_name        = esc_html__( 'RT: Hình ảnh', 'raothue' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => esc_html__( 'Hình ảnh', 'raothue' ),
				'label' => esc_html__( 'Tiêu đề:', 'raothue' ),
			),
			'image' => array(
				'type'   => 'image',
				'std'    => '',
				'label'  => esc_html__( 'Hình ảnh:', 'raothue' ),
			),
			'link' => array(
				'type'   => 'text',
				'std'    => '#',
				'label'  => esc_html__( 'Link dẫn đến trang đích:', 'raothue' ),
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
			'image'  => '',
			'link'  => '#',
		);

		$instance = wp_parse_args( $instance, $defaults );

		$this->widget_start( $args, $instance );

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		if ( $instance['image'] ) : ?>

		<div class="rt__image">
			<a href="<?php echo esc_url( $instance['link'] ); ?>" target="_blank">
				<?php echo wp_get_attachment_image( $instance['image'], 'full' ); ?>
			</a>
		</div>

		<?php
		endif;

		$this->widget_end( $args );
	}
}
