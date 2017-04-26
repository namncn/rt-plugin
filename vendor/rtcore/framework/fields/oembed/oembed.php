<?php
/**
 * CS-Framework Oembed.
 *
 * @package cs-ncnteam
 */

// Cannot access pages directly.
if ( ! defined( 'ABSPATH' ) ) {
	die;
}

/**
 * Field: Oembed
 */
class CSFramework_Option_oembed extends CSFramework_Options {

	/**
	 * Output the field.
	 */
	public function output() {
		$oembed_link = $this->element_value();

		echo $this->element_before(); // WPCS: XSS OK. ?>

		<div class="cs-oembed-form-control">
			<input type="url" name="<?php echo esc_attr( $this->element_name() ); ?>" value="<?php echo esc_attr( $this->element_value() ); ?>" <?php echo $this->element_class() . $this->element_attributes(); // WPCS: XSS OK. ?>>
			<button type="button" class="button button-primary js-preview-cs-oembed"><?php echo esc_html__( 'Preview', 'cs-framework' ); ?></button>
			<button type="button" class="button js-remove-cs-oembed"><?php echo esc_html__( 'Remove', 'cs-framework' ); ?></button>
			<span class="spinner"></span>
		</div>

		<div class="cs-oembed-preview embed-responsive embed-responsive-16by9 <?php echo $oembed_link ? '' : 'hide' ?>">
			<?php if ( $oembed_link ) : ?>
				<?php echo wp_oembed_get( $oembed_link ); // WPCS: XSS OK. ?>
			<?php endif ?>
		</div>

		<?php echo $this->element_after(); // WPCS: XSS OK.
	}
}

/**
 * CS-oEmbed Ajax handler.
 */
function _cs_oembed_handler() {
	if ( ! empty( $_REQUEST['link'] ) ) {
		$link = esc_url_raw( wp_unslash( $_REQUEST['link'] ) );

		if ( $embed = wp_oembed_get( $link ) ) {
			wp_send_json_success( $embed );
		}
	}

	wp_send_json_error( esc_html__( 'Opps, Invalid oembed link!', 'cs-framework' ) );
}
add_action( 'wp_ajax_cs_oembed_handler', '_cs_oembed_handler' );

/**
 * //
 *
 * @return void
 */
function _cs_oembed_enqueue_scripts() {
	wp_enqueue_style( 'cs-oembed', plugin_dir_url( __FILE__ ) . 'cs-oembed.css',  array( 'cs-framework' ), '1.0.0' );
	wp_enqueue_script( 'cs-oembed', plugin_dir_url( __FILE__ ) . 'cs-oembed.js',  array( 'cs-framework' ), '1.0.0', true );
}
add_action( 'admin_enqueue_scripts', '_cs_oembed_enqueue_scripts' );
