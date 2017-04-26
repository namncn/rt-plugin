<?php
/**
 * Frontend class
 *
 * @author Yithemes
 * @package YITH WooCommerce Quick View
 * @version 1.1.1
 */

if ( ! defined( 'YITH_WCQV' ) ) {
	exit;
} // Exit if accessed directly

if( ! class_exists( 'YITH_WCQV_Frontend' ) ) {
	/**
	 * Admin class.
	 * The class manage all the Frontend behaviors.
	 *
	 * @since 1.0.0
	 */
	class YITH_WCQV_Frontend {

		/**
		 * Single instance of the class
		 *
		 * @var \YITH_WCQV_Frontend
		 * @since 1.0.0
		 */
		protected static $instance;

		/**
		 * Plugin version
		 *
		 * @var string
		 * @since 1.0.0
		 */
		public $version = YITH_WCQV_VERSION;

		/**
		 * Returns single instance of the class
		 *
		 * @return \YITH_WCQV_Frontend
		 * @since 1.0.0
		 */
		public static function get_instance(){
			if( is_null( self::$instance ) ){
				self::$instance = new self();
			}

			return self::$instance;
		}

		/**
		 * Constructor
		 *
		 * @access public
		 * @since 1.0.0
		 */
		public function __construct() {

			// custom styles and javascripts
			add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_styles_scripts' ) );

			// quick view ajax
		    add_action( 'wp_ajax_yith_load_product_quick_view', array( $this, 'yith_load_product_quick_view_ajax' ) );
		    add_action( 'wp_ajax_nopriv_yith_load_product_quick_view', array( $this, 'yith_load_product_quick_view_ajax' ) );

			// add button
			add_action( 'rt_add_to_cart', array( $this, 'yith_add_quick_view_button' ), 15 );
			add_action( 'yith_wcwl_table_after_product_name', array( $this, 'yith_add_quick_view_button' ), 15, 0 );

			// load modal template
			add_action( 'wp_footer', array( $this, 'yith_quick_view' ) );

			// load action for product template
			$this->yith_quick_view_action_template();

			add_shortcode( 'yith_quick_view', array( $this, 'quick_view_shortcode' ) );
		}

		/**
		 * Enqueue styles and scripts
		 *
		 * @access public
		 * @return void
		 * @since 1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function enqueue_styles_scripts() {

			$suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

			wp_register_script( 'yith-wcqv-frontend', YITH_WCQV_ASSETS_URL . '/js/frontend.js', array('jquery'), $this->version, true);
			wp_enqueue_script( 'yith-wcqv-frontend' );
			wp_enqueue_style( 'yith-quick-view', YITH_WCQV_ASSETS_URL . '/css/yith-quick-view.css' );
		}

		/**
		 * Add quick view button in wc product loop
		 *
		 * @access public
		 * @param integer|string $product_id The product id
		 * @param string $label The button label
		 * @param boolean $return
		 * @return string|void
		 * @since  1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function yith_add_quick_view_button( $product_id = 0, $label = '', $return = false ) {

			global $product;

			// get product id
			! $product_id && $product_id = yit_get_prop( $product, 'id', true );
			// get label
			! $label && $label = $this->get_button_label();

			$button = '<a href="#" class="button yith-wcqv-button pull-right" data-product_id="' . $product_id . '">' . $label . '</a>';
            $button = apply_filters( 'yith_add_quick_view_button_html', $button, $label, $product );

			if( $return ) {
				return $button;
			}

			echo $button;
		}

		/**
		 * Enqueue scripts and pass variable to js used in quick view
		 *
		 * @access public
		 * @return bool
		 * @since 1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function yith_woocommerce_quick_view() {

			wp_enqueue_script( 'wc-add-to-cart-variation' );

			// enqueue wc color e label variation style
			wp_enqueue_script( 'yith_wccl_frontend' );
			wp_enqueue_style( 'yith_wccl_frontend' );

            // loader gif
            $loader = apply_filters( 'yith_quick_view_loader_gif', YITH_WCQV_ASSETS_URL . '/image/qv-loader.gif' );

            // Allow user to load custom style and scripts
            do_action( 'yith_quick_view_custom_style_scripts' );

			wp_localize_script( 'yith-wcqv-frontend', 'yith_qv', array (
					'ajaxurl'           => admin_url( 'admin-ajax.php', 'relative' ),
					'loader'            => $loader,
					// 'is2_2'             => $version
				)
			);

			return true;
		}

		/**
		 * Ajax action to load product in quick view
		 *
		 * @access public
		 * @return void
		 * @since 1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function yith_load_product_quick_view_ajax() {

			if ( ! isset( $_REQUEST['product_id'] ) ) {
				die();
			}

			$product_id = intval( $_REQUEST['product_id'] );

			// set the main wp query for the product
			wp( 'p=' . $product_id . '&post_type=product' );

			// remove product thumbnails gallery
			// remove_action( 'woocommerce_product_thumbnails', 'woocommerce_show_product_thumbnails', 20 );

			// change template for variable products
			if ( isset( $GLOBALS['yith_wccl'] ) ) {
				$GLOBALS['yith_wccl']->obj = new YITH_WCCL_Frontend( YITH_WCCL_VERSION );
				$GLOBALS['yith_wccl']->obj->override();
			}

			ob_start();

			// load content template
			wc_get_template( 'yith-quick-view-content.php', array(), '', YITH_WCQV_DIR . 'templates/' );

			echo ob_get_clean();

			die();
		}

		/**
		 * Load quick view template
		 *
		 * @access public
		 * @return void
		 * @since 1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function yith_quick_view() {
			$this->yith_woocommerce_quick_view();
			wc_get_template( 'yith-quick-view.php', array(), '', YITH_WCQV_DIR . 'templates/' );
		}

		/**
		 * Load wc action for quick view product template
		 *
		 * @access public
		 * @return void
		 * @since 1.0.0
		 * @author Francesco Licandro <francesco.licandro@yithemes.com>
		 */
		public function yith_quick_view_action_template() {

			// Image
			add_action( 'yith_wcqv_product_image', 'woocommerce_show_product_sale_flash', 10 );
			add_action( 'yith_wcqv_product_image', 'rt_qv_woocommerce_show_product_images', 20 );

			// Summary
			add_action( 'yith_wcqv_product_summary', 'rt_qv_woocommerce_template_single_title', 5 );
			// add_action( 'yith_wcqv_product_summary', 'woocommerce_template_single_rating', 15 );
			add_action( 'yith_wcqv_product_summary', 'woocommerce_template_single_meta', 10 );
			add_action( 'yith_wcqv_product_summary', 'rt_qv_woocommerce_template_single_price', 10 );
			add_action( 'yith_wcqv_product_summary', 'rt_qv_woocommerce_template_single_excerpt', 20 );
			add_action( 'yith_wcqv_product_summary', 'rt_qv_woocommerce_template_single_add_to_cart', 25 );
		}

		/**
		 * Get Quick View button label
		 *
		 * @author Francesco Licandro
		 * @since 1.2.0
		 * @return string
		 */
		public function get_button_label() {
			$label = get_option( 'yith-wcqv-button-label' );
			$label = call_user_func( '__' , $label, 'yith-woocommerce-quick-view' );

			return apply_filters( 'rt_qv_button_label', esc_html( $label ) );
		}

		/**
		 * Quick View shortcode button
		 *
		 * @access public
		 * @since 1.0.7
		 * @param array $atts
		 * @return string
		 * @author Francesco Licandro
		 */
		public function quick_view_shortcode( $atts ) {

			$atts = shortcode_atts(array(
				'product_id' => 0,
				'label'		 => ''
			), $atts );

			extract( $atts );

			if( ! intval( $product_id ) ) {
				return '';
			}

			return $this->yith_add_quick_view_button( $product_id, $label, true );
		}
	}
}
/**
 * Unique access to instance of YITH_WCQV_Frontend class
 *
 * @return \YITH_WCQV_Frontend
 * @since 1.0.0
 */
function YITH_WCQV_Frontend(){
	return YITH_WCQV_Frontend::get_instance();
}
