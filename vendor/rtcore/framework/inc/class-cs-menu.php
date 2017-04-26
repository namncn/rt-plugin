<?php
/**
 * CSFramework_Menu
 *
 * @package RTCORE
 */

/**
 * CSFramework_Menu
 */
class CSFramework_Menu {

	/**
	 * //
	 *
	 * @var array
	 */
	public $fields = array();

	/**
	 * //
	 *
	 * @var $this
	 */
	protected static $instance;

	/**
	 * //
	 *
	 * @param  array $fields Options.
	 * @return instance
	 */
	public static function instance( $fields = array() ) {
		if ( is_null( static::$instance ) ) {
			static::$instance = new static( $fields );
		}

		return static::$instance;
	}

	/**
	 * //
	 *
	 * @param array $fields Options.
	 */
	public function __construct( array $fields ) {
		$this->fields = apply_filters( 'cs_menu_fields', $fields );

		if ( ! empty( $this->fields ) ) {
			add_filter( 'wp_edit_nav_menu_walker', array( $this, 'menu_walker' ), 99 );
			add_filter( 'wp_nav_menu_item_custom_fields', array( $this, 'add_fields' ), 10, 4 );
			add_action( 'wp_update_nav_menu_item', array( $this, 'handler_save' ), 10, 3 );
		}
	}

	/**
	 * Custom walker menu.
	 *
	 * @return string
	 */
	public function menu_walker() {
		if ( ! class_exists( 'Walker_Nav_Menu_Edit' ) ) {
			require_once ABSPATH . 'wp-admin/includes/class-walker-nav-menu-edit.php';
		}

		if ( ! class_exists( 'Menu_Item_Custom_Fields_Walker' ) ) {
			require_once dirname( __FILE__ ) . '/walker-nav-menu-edit.php';
		}

		return 'Menu_Item_Custom_Fields_Walker';
	}

	/**
	 * //
	 *
	 * @param int    $id    Nav menu ID.
	 * @param object $item  Menu item data object.
	 * @param int    $depth Depth of menu item. Used for padding.
	 * @param array  $args  Menu item args.
	 *
	 * @return void
	 */
	public function add_fields( $id, $item, $depth, $args ) {
		print '<div class="menu-fields cs-content">';

		foreach ( $this->fields as $field ) {
			$default = isset( $field['default'] ) ? $field['default'] : '';

			$value = get_post_meta( $item->ID, $field['id'] , false );
			$value = isset( $value[0] ) ? $value[0] : $default;

			print cs_add_element( $field, $value, '_cs_menu['.$item->ID .']' ); // WPCS: XSS OK.
		}

		print '</div>';
	}

	/**
	 * Save menu item's metadata
	 *
	 * @link https://developer.wordpress.org/reference/functions/wp_update_nav_menu_item
	 *
	 * @param int   $menu_id         Nav menu ID.
	 * @param int   $menu_item_db_id Menu item ID.
	 * @param array $menu_item_args  Menu item data.
	 */
	public function handler_save( $menu_id, $menu_item_db_id, $menu_item_args ) {
		if ( defined( 'DOING_AJAX' ) && DOING_AJAX ) {
			return;
		}

		$screen = get_current_screen();
		if ( ! $screen instanceof WP_Screen || 'nav-menus' !== $screen->id ) {
			return;
		}

		check_admin_referer( 'update-nav_menu', 'update-nav-menu-nonce' );
		$id = $menu_item_db_id;

		if ( ! empty( $_POST['_cs_menu'][ $id ] ) ) {
			$raw_values = array_map(
				'sanitize_text_field',
				wp_unslash( (array) $_POST['_cs_menu'][ $id ] )
			);

			foreach ( $this->fields as $field ) {
				if ( ! empty( $field['id'] ) ) {
					$key = $field['id'];

					$value = isset( $raw_values[ $field['id'] ] ) ? $raw_values[ $field['id'] ] : '';
					$value = rtfw_sanitize_field( $field, $value );

					if ( ! empty( $raw_values[ $key ] ) ) {
						update_post_meta( $id, $key, $value );
					} else {
						delete_post_meta( $id, $key );
					}
				}
			}
		}
	}
}
