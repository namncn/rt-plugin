<?php
/*
Plugin Name: RT: Sidebar Manager
Description: Description
Plugin URI: http://#
Author: Author
Author URI: http://#
Version: 1.0
License: GPL2
Text Domain: Text Domain
Domain Path: Domain Path
*/

/**
 * Class RT_Sidebar_Manager.
 */
class RT_Sidebar_Manager {
	/**
	 * RT_Sidebar_Manager version.
	 */
	const VERSION = '0.1.1-dev';

	/**
	 * //
	 *
	 * @var string
	 */
	protected $option_key = '_rt_sidebars';

	/**
	 * //
	 *
	 * @var array
	 */
	protected $translation = array();

	/**
	 * //
	 *
	 * @var RT_Sidebar_Manager
	 */
	protected static $instance;

	/**
	 * Singleton implementation.
	 *
	 * @return RT_Sidebar_Manager
	 */
	public static function instance() {
		if ( ! static::$instance ) {
			static::$instance = new RT_Sidebar_Manager;
		}

		return static::$instance;
	}

	/**
	 * Constructor class
	 */
	public function __construct() {
		// Set default translation.
		$this->translation = $this->register_translation();

		// Register the custom sidebars.
		add_action( 'widgets_init', array( $this, 'register_sidebars' ) , 100 );

		// Enqueue the UI scripts and localize on the widgets page.
		add_action( 'sidebar_admin_setup', array( $this, 'enqueue_scripts' ) );

		// Setup template.
		add_action( 'sidebar_admin_page', array( $this, 'setup_template' ) );

		// Handler actions request.
		add_action( 'sidebar_admin_setup', array( $this, 'action_handler' ) );
		add_action( 'wp_ajax_rt_add_sidebar', array( $this, 'action_handler' ) );
		add_action( 'wp_ajax_rt_delete_sidebar', array( $this, 'action_handler' ) );

		static::$instance = $this;
	}

	/**
	 * //
	 *
	 * @return array
	 */
	protected function register_translation() {
		$translation = array(
			'new' => esc_html__( 'Thêm Sidebar mới', 'raothue' ),
			'edit' => esc_html__( 'Chỉnh sửa', 'raothue' ),
			'delete' => esc_html__( 'Xóa', 'raothue' ),
		);

		return apply_filters( 'rt_sidebar_translation', $translation );
	}

	/**
	 * Enqueue the UI scripts.
	 */
	public function enqueue_scripts() {
		add_thickbox();

		wp_enqueue_style( 'rt-sidebar-manager', plugin_dir_url( __FILE__ ) . '/sidebar-ui.css', array(), static::VERSION );
		wp_enqueue_script( 'rt-sidebar-manager', plugin_dir_url( __FILE__ ) . '/sidebar-ui.js', array( 'jquery' ), static::VERSION, true );

		wp_localize_script( 'rt-sidebar-manager', 'RTSidebar', array(
			'nonce' => wp_create_nonce( 'rt-sidebar-nonce' ),
			'button' => $this->create_button(),
			'sidebars' => $this->get_sidebars(),
		) );
	}

	/**
	 * Handle action requests.
	 *
	 * @return array|void Output JSON if DOING_AJAX, otherwise return an array
	 */
	public function action_handler() {
		if ( empty( $_POST['action'] ) || empty( $_POST['_rtnonce'] ) ) {
			return;
		}

		if ( ! wp_verify_nonce( $_POST['_rtnonce'], 'rt-sidebar-nonce' ) ) { // WPCS: Sanitization OK.
			return;
		}

		$action = $_POST['action']; // WPCS: Sanitization OK.
		$result = false;

		switch ( $action ) {
			case 'rt_add_sidebar':
				$result = $this->add_sidebar( $_POST['rt-sidebar'] );
				break;

			case 'rt_delete_sidebar':
				if ( ! empty( $_POST['id'] ) ) {
					$id = sanitize_title( wp_unslash( $_POST['id'] ) );
					$result = $this->delete_sidebar( $id );
				}

				break;
		}

		$response = array(
			'success' => false,
			'error' => null,
		);

		if ( is_wp_error( $result ) ) {
			$response['error'] = $result->get_error_message();
		} else {
			$response['success'] = (bool) $result;
		}

		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			wp_send_json( $response );
		}

		wp_redirect( admin_url( 'widgets.php' ) );
		exit();
	}

	/**
	 * Add a new custom sidebar.
	 *
	 * @param  array $args //.
	 * @return bool|WP_Error
	 */
	public function add_sidebar( $args ) {
		if ( empty( $args['name'] ) ) {
			return false;
		}

		// Registered sidebars.
		$registered_sidebars = $this->get_sidebars();

		$args['id'] = empty( $args['id'] ) ? $args['name'] : $args['id'];

		// Sanitize the sidebar ID the same way as dynamic_sidebar().
		$args['id'] = sanitize_title( $args['id'] );

		if ( isset( $registered_sidebars[ $args['id'] ] ) ) {
			return new WP_Error( 'sidebar-exists', __( 'Sidebar với ID tương tự đã được đăng ký.', 'raothue' ) );
		}

		$registered_sidebars[ $args['id'] ] = $args;

		return update_option( $this->option_key, $registered_sidebars );
	}

	/**
	 * Remove a custom sidebar by ID.
	 *
	 * @param string $id Sidebar ID.
	 * @return bool|WP_Error
	 */
	public function delete_sidebar( $id ) {
		$registered_sidebars = $this->get_sidebars();

		if ( isset( $registered_sidebars[ $id ] ) ) {
			unset( $registered_sidebars[ $id ] );
		} else {
			return new WP_Error( 'sidebar-not-found', __( 'Sidebar không tồn tại.', 'raothue' ) );
		}

		return update_option( $this->option_key, $registered_sidebars );
	}

	/**
	 * Get all the registered custom sidebars.
	 *
	 * @return array
	 */
	public function get_sidebars() {
		$raw_sidebars = (array) get_option( $this->option_key, array() );

		$registered_sidebars = array_map( array( $this, 'parse_args' ), $raw_sidebars );

		return apply_filters( 'rt_sidebars', $registered_sidebars );
	}

	/**
	 * Register the custom sidebars.
	 */
	public function register_sidebars() {
		$registered_sidebars = $this->get_sidebars();

		foreach ( $registered_sidebars as $id => $args ) {
			$args['class'] = 'rt-sidebar';

			if ( ! empty( $args['id'] ) ) {
				register_sidebar( $args );
			}
		}
	}

	/**
	 * //
	 */
	public function setup_template() {
		?>
		<!-- // -->
		<div id="rt-sidebar-manager-popup" style="display:none;"></div>
		<div id="rt-sidebar-manager-edit" style="display:none;"></div>

		<!-- / -->
		<script type="text/html" id="tmpl-rt-sidebar-manager">
			<form class="rt-create-sidebar" action="widgets.php" method="POST">
				<?php wp_nonce_field( 'rt-sidebar-nonce', '_rtnonce' ); ?>
				<input type="hidden" name="action" value="rt_add_sidebar">

				<p>
					<label for="rt_sidebar_name">Name</label>
					<input type="text" id="rt_sidebar_name" name="rt-sidebar[name]" placeholder="<?php esc_html_e( 'Điền tên Sidebar', 'raothue' ); ?>">
				</p class="">

				<!-- <a href="#" class="show">Show</a> -->

				<div class="display">
					<p>
						<label for="rt_sidebar_id"><?php esc_html_e( 'Sidebar ID', 'raothue' ) ?></label>
						<input type="text" id="rt_sidebar_id" name="rt-sidebar[id]" placeholder="<?php esc_html_e( 'Sidebar ID', 'raothue' ) ?>">
					</p>

					<p>
						<label for="rt_sidebar_description"><?php esc_html_e( 'Điền mô tả Sidebar mới', 'raothue' ) ?></label>
						<textarea id="rt_sidebar_description" placeholder="<?php esc_html_e( 'Điền mô tả Sidebar mới', 'raothue' ) ?>" name="rt-sidebar[description]"></textarea>
					</p>
				</div>

		    	<div class="rt-sidebar-actions">
		    		<input class="button" type="submit" value="<?php esc_html_e( 'Thêm mới', 'raothue' ) ?>">
		    	</div>
			</form>
		</script>

		<script type="text/html" id="tmpl-rt-sidebar-action">
			<div class="abc submitbox ">
				<a href="#" class="submitdelete" data-id="{{{ data.id }}}"><?php esc_html_e( 'Xóa', 'raothue' ) ?></a>
				<a href="#" class="button carbon-btn-remove-sidebar"><?php esc_html_e( 'Chỉnh sửa', 'raothue' ) ?></a>
			</div>
		</script>
		<?php
	}

	/**
	 * //
	 *
	 * @return string
	 */
	protected function create_button() {
		$output = '<a class="page-title-action rt-create-sidebar thickbox" href="#TB_inline?width=320&height=auto&inlineId=rt-sidebar-manager-popup" title="' . $this->translation['new'] . '">' . $this->translation['new'] . '</a>';

		/**
		 * //
		 *
		 * @param string $output
		 * @param RT_Sidebar_Manager $this
		 * @var string
		 */
		$output = apply_filters( 'rt_sidebar_manager_button', $output, $this );

		return $output;
	}

	/**
	 * //
	 *
	 * @param  string $args //.
	 * @return array
	 */
	protected function parse_args( $args ) {
		$default = array(
			'id' => '',
			'name' => '',
			'description' => '',
			'before_widget' => '<div id="%1$s" class="widget %2$s">',
			'after_widget' => '</div>',
			'before_title' => '<h3 class="widget-title"><span>',
			'after_title' => '</h3></span>',
		);

		/**
		 * //
		 *
		 * @var string
		 */
		$default = apply_filters( 'rt_widget_default_args', $default );

		return wp_parse_args( $args, $default );
	}
}

new RT_Sidebar_Manager();
