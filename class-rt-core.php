<?php
/**
 * RT CORE class.
 *
 * @package RT_CORE
 */

/**
 * Class RT_CORE
 */
final class RT_CORE {

	/**
	 * Plugin instance.
	 *
	 * @var RT_CORE
	 * @access private
	 */

	private static $instance = null;
	/**
	 * Get plugin instance.
	 *
	 * @return RT_CORE
	 * @static
	 */

	public static function get_instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new self;
		}
		return self::$instance;
	}

	/**
	 * Constructor.
	 *
	 * @access private
	 */
	private function __construct() {
		add_action( 'plugins_loaded', array( $this, 'init' ) );
		add_action( 'init', array( $this, 'rtcore_load_plugin_textdomain' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'rtcore_admin_enqueue_script' ) );
	}

	/**
	 * Code you want to run when all other plugins loaded.
	 */
	public function init() {
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );

		require_once RT_CORE_PATH . 'vendor/load.php';
		require_once RT_CORE_PATH . 'includes/abstract/abstract-rt-widget.php';
		require_once RT_CORE_PATH . 'includes/class-rt-autoloader.php';
		require_once RT_CORE_PATH . 'includes/rt-widget-functions.php';
		require_once RT_CORE_PATH . 'includes/shortcode.php';
		require_once RT_CORE_PATH . 'includes/posttype.php';

		do_action( 'rtcore_init' );
	}

	/**
	 * Load Plugin Textdomain.
	 */
	function rtcore_load_plugin_textdomain() {
		load_plugin_textdomain( 'raothue', false, RT_CORE_PATH . 'languages' );
	}

	/**
	 * Enqueue all main and scripts.
	 */
	public function enqueue_scripts() {
		wp_enqueue_style( 'slick', RT_CORE_URL . 'assets/css/slick.min.css', array(), '1.6.0' );
		if ( function_exists( 'WC' ) ) {
			wp_enqueue_style( 'thumbelina', RT_CORE_URL . 'assets/css/thumbelina.css', array(), '1.0.0' );
			wp_enqueue_style( 'cloudzoom', RT_CORE_URL . 'assets/css/cloudzoom.css', array(), '3.1.0' );
			wp_enqueue_script( 'cloudzoom',  RT_CORE_URL . 'assets/js/cloudzoom.js', array( 'jquery' ), '3.1.0', true );
			wp_enqueue_script( 'thumbelina',  RT_CORE_URL . 'assets/js/thumbelina.js', array( 'jquery' ), '1.0.0', true );
		}
		wp_enqueue_style( 'rtcore-main', RT_CORE_URL . 'assets/css/main.css', array(), '1.0.0' );
		wp_enqueue_script( 'slick',  RT_CORE_URL . 'assets/js/slick.min.js', array( 'jquery' ), '1.6.0', true );
		wp_enqueue_script( 'rtcore-main', RT_CORE_URL . 'assets/js/main.js', array( 'jquery' ), '1.0.0', true );
	}

	/**
	 * Admin enqueue scripts.
	 */
	function rtcore_admin_enqueue_script() {
		wp_enqueue_media();
		wp_enqueue_script( 'ncnteam-image-uploader', RT_CORE_URL . 'assets/js/image-uploader.js', array( 'jquery' ), '1.0.0', true );
	}
}
