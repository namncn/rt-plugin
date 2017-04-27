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
 * RT Textarea Widget.
 *
 * Show textarea.
 *
 * @author   NamNCN
 * @category Widgets
 * @package  RTCORE/Widgets
 * @version  1.0.0
 * @extends  RT_Widget
 */
class RT_Textarea_Widget extends RT_Widget {

	/**
	 * Constructor.
	 */
	public function __construct() {
		$this->widget_cssclass    = 'rt-textarea-widget';
		$this->widget_description = esc_html__( "Hiển thị văn bản.", 'raothue' );
		$this->widget_id          = 'rt-textarea-widget';
		$this->widget_name        = esc_html__( 'RT: Văn bản', 'raothue' );
		$this->settings           = array(
			'title'  => array(
				'type'  => 'text',
				'std'   => esc_html__( 'Văn bản', 'raothue' ),
				'label' => esc_html__( 'Tiêu đề:', 'raothue' ),
			),
			'textarea' => array(
				'type'   => 'textarea',
				'std'    => '',
				'label'  => esc_html__( 'Điền văn bản muốn hiển thị:', 'raothue' ),
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
			'textarea'  => 6,
		);

		$instance = wp_parse_args( $instance, $defaults );

		$this->widget_start( $args, $instance );

		if ( $title = apply_filters( 'widget_title', empty( $instance['title'] ) ? '' : $instance['title'], $instance, $this->id_base ) ) {
			echo $args['before_title'] . $title . $args['after_title'];
		}

		$textarea = ! empty( $instance['textarea'] ) ? $instance['textarea'] : '';

		/**
		 * Filters the content of the Text widget.
		 *
		 * @since 2.3.0
		 * @since 4.4.0 Added the `$this` parameter.
		 *
		 * @param string         $textarea The widget content.
		 * @param array          $instance    Array of settings for the current widget.
		 * @param RT_Textarea_Widget $this        Current Text widget instance.
		 */
		$textarea = apply_filters( 'rt_textarea_widget', $textarea, $instance, $this );
		?>

		<div class="rt__textarea-widget"><?php echo $textarea; // WPCS: XSS Ok ?></div>

		<?php
		$this->widget_end( $args );
	}
}
