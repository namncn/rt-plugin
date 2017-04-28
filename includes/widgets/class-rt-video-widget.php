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
 * RT Video Widget.
 *
 * Show video.
 *
 * @author   NamNCN
 * @category Widgets
 * @package  RTCORE/Widgets
 * @version  1.0.0
 * @extends  RT_Widget
 */
class RT_Video_Widget extends RT_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'rt-video-widget';
		$this->widget_description = esc_html__( "Hiển thị video.", 'raothue' );
		$this->widget_id          = 'rt-video-widget';
		$this->widget_name        = esc_html__( 'RT: Video', 'raothue' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => esc_html__( 'Video', 'raothue' ),
				'label' => esc_html__( 'Tiêu đề:', 'raothue' ),
			),
			'image' => array(
				'type'   => 'image',
				'std'    => '',
				'label'  => esc_html__( 'Hình ảnh video:', 'raothue' ),
			),
			'icon' => array(
				'type'  => 'text',
				'std'   => 'play-circle-o',
				'label' => esc_html__( 'FontAwesome Icon:', 'raothue' ),
			),
			'link' => array(
				'type'   => 'text',
				'std'    => 'https://www.youtube.com/watch?v=moUqHH9O5Fs',
				'label'  => esc_html__( 'Link dẫn đến video:', 'raothue' ),
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
			'icon'  => 'play-circle-o',
			'link'  => 'https://www.youtube.com/watch?v=moUqHH9O5Fs',
		);

		$instance = wp_parse_args( $instance, $defaults );

		$this->widget_start( $args, $instance );

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		if ( $instance['image'] ) : ?>

		<div class="rt__video">
			<a href="<?php echo esc_url( $instance['link'] ); ?>">
				<?php if ( $instance['icon'] ) : ?>
				<div class="rt__video--overlay">
					<i class="<?php echo esc_attr( $instance['icon'] ); ?>"></i>
				</div>
				<?php endif; ?>

				<?php echo wp_get_attachment_image( $instance['image'], 'full' ); ?>
			</a>
		</div>

		<?php
		endif;

		$this->widget_end( $args );
	}
}
