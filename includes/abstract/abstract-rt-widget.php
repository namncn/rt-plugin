<?php
/**
 * Abstract Widget Class.
 *
 * @author NamNCN
 * @category Widgets
 * @package RTCORE/Abstract
 * @version 1.0
 * @extends WP_Widget
 */
abstract class RT_Widget extends WP_Widget {

	/**
	 * CSS class.
	 *
	 * @var string
	 */
	public $widget_cssclass;

	/**
	 * Widget description.
	 *
	 * @var string
	 */
	public $widget_description;

	/**
	 * Widget ID.
	 *
	 * @var string
	 */
	public $widget_id;

	/**
	 * Widget name.
	 *
	 * @var string
	 */
	public $widget_name;

	/**
	 * Settings
	 *
	 * @var array
	 */
	public $settings;

	/**
	 * Constructor.
	 */
	public function __construct() {
		$widget_ops = array(
			'classname'    => $this->widget_cssclass,
			'description'  => $this->widget_description,
			'customize_selective_refresh' => true,
		);

		parent::__construct( $this->widget_id, $this->widget_name, $widget_ops );

		add_action( 'save_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'deleted_post', array( $this, 'flush_widget_cache' ) );
		add_action( 'switch_theme', array( $this, 'flush_widget_cache' ) );
	}

	/**
	 * Get cached widget.
	 *
	 * @param array $args
	 * @return bool true if the widget is cached otherwise false.
	 */
	public function get_cached_widget( $args ) {

		$cache = wp_cache_get( apply_filters( 'rt_cached_widget_id', $this->widget_id ), 'widget' );

		if ( ! is_array( $cache ) ) {
			$cache = array();
		}

		if ( isset( $cache[ $args['widget_id'] ] ) ) {
			echo $cache[ $args['widget_id'] ];
			return true;
		}

		return false;
	}

	/**
	 * Cache the widget.
	 *
	 * @param array $args
	 * @param string $content
	 * @return string the content that was cached
	 */
	public function cache_widget( $args, $content ) {
		wp_cache_set( apply_filters( 'rt_cached_widget_id', $this->widget_id ), array( $args['widget_id'] => $content ), 'widget' );

		return $content;
	}

	/**
	 * Flush the cache.
	 */
	public function flush_widget_cache() {
		wp_cache_delete( apply_filters( 'rt_cached_widget_id', $this->widget_id ), 'widget' );
	}

	/**
	 * Output the html at the start of a widget.
	 *
	 * @param array $args
	 * @return string
	 */
	public function widget_start( $args, $instance ) {
		echo $args['before_widget'];
	}

	/**
	 * Output the html at the end of a widget.
	 *
	 * @param array $args
	 * @return string
	 */
	public function widget_end( $args ) {
		echo $args['after_widget'];
	}

	/**
	 * Updates a particular instance of a widget.
	 *
	 * @see    WP_Widget->update
	 * @param  array $new_instance
	 * @param  array $old_instance
	 * @return array
	 */
	public function update( $new_instance, $old_instance ) {

		$instance = $old_instance;

		if ( empty( $this->settings ) ) {
			return $instance;
		}

		// Loop settings and get values to save.
		foreach ( $this->settings as $key => $setting ) {
			if ( ! isset( $setting['type'] ) ) {
				continue;
			}

			// Fomart the value based on settings type.
			switch ( $setting['type'] ) {
				case 'number' :
					$instance[ $key ] = absint( $new_instance[ $key ] );

					if ( isset( $setting['min'] ) && '' !== $setting['min'] ) {
						$instance[ $key ] = max( $instance[ $key ], $setting['min'] );
					}

					if ( isset( $setting['max'] ) && '' !== $setting['max'] ) {
						$instance[ $key ] = min( $instance[ $key ], $setting['max'] );
					}
				break;
				case 'textarea' :
					$instance[ $key ] = wp_kses( trim( wp_unslash( $new_instance[ $key ] ) ), wp_kses_allowed_html( 'post' ) );
				break;
				case 'checkbox' :
					$instance[ $key ] = empty( $new_instance[ $key ] ) ? 0 : 1;
				break;
				case 'upload' :
					$instance[ $key ] = empty( $new_instance[ $key ] ) ? 0 : esc_url_raw( $new_instance[ $key ] );
				break;
				case 'image' :
					$instance[ $key ] = empty( $new_instance[ $key ] ) ? 0 : absint( $new_instance[ $key ] );
				break;
				default:
					$instance[ $key ] = sanitize_text_field( $new_instance[ $key ] );
				break;
			}

			/**
			 * Sanitize the value of a setting.
			 */
			$setting[ $key ] = apply_filters( 'rt_widget_settings_sanitize_option', $instance[ $key ], $new_instance, $key, $setting );
		}

		$this->flush_widget_cache();

		return $instance;
	}

	/**
	 * Outputs the settings update form.
	 *
	 * @see   WP_Widget->form
	 * @param array $instance
	 */
	public function form( $instance ) {

		if ( empty( $this->settings ) ) {
			return;
		}

		foreach ( $this->settings as $key => $setting ) {

			$class    = isset( $setting['class'] ) ? $setting['class'] : '';
			$value    = isset( $instance[ $key ] ) ? $instance[ $key ] : $setting['std'];

			switch ( $setting['type'] ) {

				case 'text' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" />
						<?php if ( isset( $setting['desc'] ) ) : ?>
							<small><?php echo esc_html( $setting['desc'] ); ?></small>
						<?php endif; ?>
					</p>
					<?php
				break;

				case 'number' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<input class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="number" step="<?php echo esc_attr( $setting['step'] ? $setting['step'] : 1 ); ?>" min="<?php echo esc_attr( $setting['min'] ); ?>" max="<?php echo esc_attr( $setting['max'] ); ?>" value="<?php echo esc_attr( $value ); ?>" />
						<?php if ( isset( $setting['desc'] ) ) : ?>
							<small><?php echo esc_html( $setting['desc'] ); ?></small>
						<?php endif; ?>
					</p>
					<?php
				break;

				case 'select' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<select class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>">
							<?php foreach ( $setting['options'] as $option_key => $option_value ) : ?>
								<option value="<?php echo esc_attr( $option_key ); ?>" <?php selected( $option_key, $value ); ?>><?php echo esc_html( $option_value ); ?></option>
							<?php endforeach; ?>
						</select>
						<?php if ( isset( $setting['desc'] ) ) : ?>
							<small><?php echo esc_html( $setting['desc'] ); ?></small>
						<?php endif; ?>
					</p>
					<?php
				break;

				case 'taxonomy_select' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<?php
						$options = array();
						if ( $setting['options'] ) :
							foreach ( $setting['options'] as $option_key => $option_value ) :
								$options[ $option_key ] = $option_value;
							endforeach;
						endif; ?>
						<?php wp_dropdown_categories( array(
							'show_option_none' => '&mdash; ' . esc_html( $options['show_option_none'] ) . ' &mdash;',
							'taxonomy'         => esc_html( $options['taxonomy'] ),
							'class'            => 'widefat ' . esc_attr( $class ),
							'id'               => esc_attr( $this->get_field_id( $key ) ),
							'name'             => esc_attr( $this->get_field_name( $key ) ),
							'selected'         => $value,
						) ); ?>
						<?php if ( isset( $setting['desc'] ) ) : ?>
							<small><?php echo esc_html( $setting['desc'] ); ?></small>
						<?php endif; ?>
					</p>
					<?php
				break;

				case 'textarea' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<textarea class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" cols="20" rows="3"><?php echo esc_textarea( $value ); ?></textarea>
						<?php if ( isset( $setting['desc'] ) ) : ?>
							<small><?php echo esc_html( $setting['desc'] ); ?></small>
						<?php endif; ?>
					</p>
					<?php
				break;

				case 'checkbox' :
					?>
					<p>
						<input class="checkbox <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( $key ) ); ?>" type="checkbox" value="1" <?php checked( $value, 1 ); ?> />
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<?php if ( isset( $setting['desc'] ) ) : ?>
							<small><?php echo esc_html( $setting['desc'] ); ?></small>
						<?php endif; ?>
					</p>
					<?php
				break;

				case 'upload' :
					?>
					<p>
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<label class="rt-element cs-field-upload">
						<input class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" data-depend-id="<?php echo $this->get_field_id( $key ); ?>"/>
						<a href="#" class="button cs-add" data-frame-title="Upload" data-upload-type="image" data-insert-title="Use Image">Upload</a>
						</label>
						<?php if ( isset( $setting['desc'] ) ) : ?>
							<small><?php echo esc_html( $setting['desc'] ); ?></small>
						<?php endif; ?>
					</p>
					<?php
				break;

				case 'image' :
					?>
					<div style="margin: 1em 0;">
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<div class="rt-element cs-field-image">
							<input class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" data-depend-id="<?php echo $this->get_field_id( $key ); ?>"/>
							<div class="cs-image-preview<?php echo empty( $value ) ? ' hidden' : ''; ?>">
								<div class="cs-preview">
									<i class="fa fa-times cs-remove"></i>
									<img src="<?php echo esc_attr( wp_get_attachment_url( $value ) ); ?>" alt="preview" />
								</div>
							</div>
							<a href="#" class="button button-primary cs-add"><?php esc_html_e( 'Thêm hình ảnh', 'raothue' ); ?></a>
						</div><!-- .rt-element -->
						<?php if ( isset( $setting['desc'] ) ) : ?>
							<small><?php echo esc_html( $setting['desc'] ); ?></small>
						<?php endif; ?>
					</div>
					<?php
				break;

				case 'gallery' :
					?>
					<div style="margin: 1em 0;">
						<label for="<?php echo $this->get_field_id( $key ); ?>"><?php echo $setting['label']; ?></label>
						<div class="rt-element cs-field-gallery">
							<input class="widefat <?php echo esc_attr( $class ); ?>" id="<?php echo esc_attr( $this->get_field_id( $key ) ); ?>" name="<?php echo $this->get_field_name( $key ); ?>" type="text" value="<?php echo esc_attr( $value ); ?>" data-depend-id="<?php echo $this->get_field_id( $key ); ?>"/>
							<ul>
							<?php
								$value = explode( ',', $value);
								foreach ( $value as $id ) :
							?>
							<li><img src="<?php echo esc_attr( wp_get_attachment_url( $id ) ); ?>" alt="preview"></li>
							<?php endforeach; ?>
							</ul>
							<a href="#" class="button button-primary cs-add"><?php esc_html_e( 'Thêm Gallery', 'raothue' ); ?></a>
							<a href="#" class="button cs-edit"><?php esc_html_e( 'Chỉnh sửa Gallery', 'raothue' ); ?></a>
							<a href="#" class="button cs-warning-primary cs-remove"><?php esc_html_e( 'Xóa', 'raothue' ); ?></a>
						</div><!-- .rt-element -->
						<?php if ( isset( $setting['desc'] ) ) : ?>
							<small><?php echo esc_html( $setting['desc'] ); ?></small>
						<?php endif; ?>
					</div>
					<?php
				break;

				// Default: run an action
				default :
					do_action( 'rt_widget_field_' . $setting['type'], $key, $value, $setting, $instance );
				break;
			}
		}
	}

}
