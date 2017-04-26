<?php
/**
 * RTFramework
 * This file is a part of RTFW.
 *
 * @package RTFW
 */

/**
 * Exit if accessed directly
 */
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'RTFW_Container' ) ) {
	require_once dirname( __FILE__ ) . '/class-rtfw-container.php';
}

require_once dirname( __FILE__ ) . '/class-rtfw-field.php';
require_once dirname( __FILE__ ) . '/class-rtfw-metabox.php';
require_once dirname( __FILE__ ) . '/class-rtfw-customizer.php';

/**
 * RTFW
 */
class RTFW extends RTFW_Container {
	/**
	 * //
	 *
	 * @var array
	 */
	public $metaboxes = array();

	/**
	 * //
	 *
	 * @var array
	 */
	public $terms = array();

	/**
	 * //
	 *
	 * @var array
	 */
	public $menu_fields = array();

	/**
	 * //
	 *
	 * @var $this
	 */
	public static $instance;

	/**
	 * //
	 *
	 * @return $this
	 */
	public static function get_instance() {
		if ( ! static::$instance ) {
			static::$instance = new static;
		}

		return static::$instance;
	}

	/**
	 * //
	 */
	public function __construct() {
		static::$instance = $this;

		defined( 'CS_ACTIVE_FRAMEWORK' ) or define( 'CS_ACTIVE_FRAMEWORK', false );
		defined( 'CS_ACTIVE_METABOX' ) or define( 'CS_ACTIVE_METABOX', true );
		defined( 'CS_ACTIVE_TAXONOMY'   ) or  define( 'CS_ACTIVE_TAXONOMY',   true );
		defined( 'CS_ACTIVE_SHORTCODE' ) or define( 'CS_ACTIVE_SHORTCODE', true );
		defined( 'CS_ACTIVE_CUSTOMIZE' ) or define( 'CS_ACTIVE_CUSTOMIZE', false );

		$this['cs_url'] = CS_URI;
		$this['cs_path'] = CS_DIR;

		$this['url'] = dirname( $this['cs_url'] );
		$this['path'] = dirname( $this['cs_path'] );

		add_filter( 'init', array( $this, 'bootstrap' ), 10 );
		add_action( 'customize_register', array( $this, 'register_customize' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_filter( 'cs_locate_template', array( $this, 'locate_template' ), 10, 2 );
	}

	/**
	 * //
	 */
	public function bootstrap() {
		cs_locate_template( 'functions/deprecated.php' );
		cs_locate_template( 'functions/helpers.php' );
		cs_locate_template( 'functions/actions.php' );
		cs_locate_template( 'functions/enqueue.php' );
		cs_locate_template( 'functions/sanitize.php' );
		cs_locate_template( 'functions/validate.php' );

		cs_locate_template( 'classes/abstract.class.php' );
		cs_locate_template( 'classes/options.class.php' );
		cs_locate_template( 'classes/framework.class.php' );
		cs_locate_template( 'classes/metabox.class.php' );
		cs_locate_template( 'classes/shortcode.class.php' );
		cs_locate_template( 'classes/customize.class.php' );

		cs_locate_template( 'config/shortcode.config.php'  );

		cs_locate_template( 'inc/class-cs-menu.php' );
		cs_locate_template( 'inc/class-rt-taxonomy.php' );

		$this->autoload_fields();
		do_action( 'rtfw_init', array( $this ) );

		if ( $menu_fields = $this->menu_fields ) {
			CSFramework_Menu::instance( $menu_fields );
		}

		if ( $terms = $this->terms ) {
			RTFramework_Taxonomy::instance( $terms );
		}

		if ( $metaboxes = $this->get_metaboxes() ) {
			CSFramework_Metabox::instance( $metaboxes );
		}

		CSFramework_Customize::instance();
	}

	/**
	 * //
	 *
	 * @param  string $located       //.
	 * @param  string $template_name //.
	 * @return string
	 */
	public function locate_template( $located, $template_name ) {
		if ( file_exists( $path = rtfw( 'path' ) . '/' . $template_name ) ) {
			return $path;
		}

		return $located;
	}

	/**
	 * //
	 */
	public function enqueue_scripts() {
		wp_enqueue_script( 'jquery-ui-datepicker' );
		wp_enqueue_style( 'rtfw-jquery-ui-datepicker', rtfw( 'url' ) . '/css/datepicker.css' );

		wp_enqueue_style( 'rtfw', $this['url'] . '/css/rtfw.css',  array( 'cs-framework' ) );
		wp_enqueue_script( 'rtfw', $this['url'] . '/js/rtfw.js', array( 'jquery' ), false, true );
	}

	/**
	 * //
	 *
	 * @param  RTFW_Metabox $metabox //.
	 * @return RTFW_Metabox
	 */
	public function register_metabox( RTFW_Metabox $metabox ) {
		$id = $metabox->get_id();

		if ( ! isset( $this->metaboxes[ $id ] ) ) {
			$this->metaboxes[ $id ] = $metabox;
		}

		return $this->metaboxes[ $id ];
	}

	/**
	 * //
	 *
	 * @param  string $args   //.
	 * @param  array  $fields //.
	 */
	public function register_term_metabox( $args, $fields = array() ) {
		if ( ! isset( $args['id'] ) ) {
			return false;
		}

		if ( ! empty( $fields ) ) {
			if ( isset( $fields['type'] ) ) {
				$args['field'] = $fields;
			} else {
				$args['fields'] = $fields;
			}
		}

		$this->terms[ $args['id'] ] = $args;
	}

	/**
	 * //
	 *
	 * @param string|array $id    //.
	 * @param array        $field //.
	 */
	public function register_menu_field( $id, $field = array() ) {
		if ( is_array( $id ) && isset( $id['id'] ) ) {
			$field = $id;
			$id = $id['id'];
		}

		if ( empty( $id ) || empty( $field['type'] ) ) {
			return false;
		}

		$field['id'] = sanitize_key( $id );
		$this->menu_fields[ $field['id'] ] = $field;
	}

	/**
	 * //
	 *
	 * @param  WP_Customize_Manager $wp_customize //.
	 */
	public function register_customize( WP_Customize_Manager $wp_customize ) {
		$rt_customize = new RTFW_Customizer;

		do_action( 'rtfw_customize_register', $rt_customize, $wp_customize );
	}

	/**
	 * //
	 *
	 * @return array
	 */
	protected function get_metaboxes() {
		$metaboxes = array();

		foreach ( $this->metaboxes as $metabox ) {
			$metaboxes[] = $metabox->get_options();
		}

		return $metaboxes;
	}

	/**
	 * //
	 */
	protected function autoload_fields() {
		foreach ( glob( $this['path'] .'/fields/*/*.php' ) as $path ) {
			$path = '../' . ltrim( str_replace( $this['path'], '', $path ), '/' );
			cs_locate_template( $path );
		}
	}
}
