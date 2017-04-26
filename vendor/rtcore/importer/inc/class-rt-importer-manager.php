<?php
/**
 * RT-Importer
 *
 * @package RT-Importer
 */

/**
 * RT_Importer
 */
class RT_Importer_Manager {

	/**
	 * //
	 *
	 * @var WP_Importer_Logger
	 */
	protected $logger;

	/**
	 * //
	 *
	 * @var array
	 */
	protected $metadata = array();

	/**
	 * Constructer
	 *
	 * @param array $metadata Metadata.
	 */
	public function __construct( $metadata = array() ) {
		$this->logger = new RT_Printer_Logger;
		$this->metadata = apply_filters( 'rt_importer_metadata', $metadata );

		$imported = get_option( 'rt_imported', array() );
		foreach ( $this->metadata as $id => &$meta ) {
			if ( in_array( $id, $imported ) ) {
				$meta['imported'] = true;
			}
		}
	}

	/**
	 * Registered callback function for the RT Importer
	 */
	public function dispatch() {
		$step = empty( $_GET['step'] ) ? 0 : (int) $_GET['step'];

		if ( ! empty( $_GET['export-customizer'] ) ) {
			RT_Customizer_Import_Export::handler_export();
			exit;
		}

		switch ( $step ) {
			case 0:
				$this->welcome();
				break;

			case 1:
				check_admin_referer( 'rt-import-demo' );

				$id = empty( $_GET['id'] ) ? '' : sanitize_key( $_GET['id'] );

				$demo = $this->metadata[ $id ];
				$this->handler_import( $id, $demo );
				break;
		}
	}

	/**
	 * //
	 *
	 * @param  string $id   //.
	 * @param  array  $demo //.
	 * @return mixed
	 */
	protected function handler_import( $id, $demo ) {
		if ( empty( $demo['archive'] ) || ! file_exists( $demo['archive'] ) ) {
			$this->logger->error( esc_html__( 'No zip file are found to import.', 'raothue' ) );
			return;
		}

		set_time_limit( 0 );
		WP_Filesystem();

		$upload_dir = wp_upload_dir();
		$unzip_path = $upload_dir['basedir'] . '/rt-importer/' . $id;

		$this->logger->info( esc_html__( 'Begin unzip import file', 'raothue' ) );

		unzip_file( $demo['archive'], $unzip_path );
		$metadata = $this->parser_metadata( $unzip_path );

		if ( $metadata['wordpress'] ) {
			$importer = $this->get_importer();
			$importer->import( $metadata['wordpress'] );
		}

		if ( $metadata['widgets'] ) {
			wie_process_import_file( $metadata['widgets'] );
			$this->logger->info( esc_html__( 'Imported widgets', 'raothue' ) );
		}

		if ( $metadata['revslider'] && is_array( $metadata['revslider'] ) ) {
			$this->import_revsliders( $metadata['revslider'] );
		}

		if ( $metadata['customizer'] ) {
			RT_Customizer_Import_Export::import( $metadata['customizer'] );
			$this->logger->info( esc_html__( 'Imported customizer', 'raothue' ) );
		}

		if ( ! empty( $metadata['setmenu'] ) ) {
			$this->import_menu_locations( $metadata['setmenu'] );
		}

		if ( ! empty( $metadata['options'] ) ) {
			$this->import_options( $metadata['options'] );
		}

		do_action( 'rt_import_' . $id, $metadata );

		// Mark imported demo.
		$imported = get_option( 'rt_imported', array() );

		if ( ! in_array( $id, $imported ) ) {
			$imported[] = $id;
		}

		update_option( 'rt_imported', $imported );

		echo '<p>' . __( 'All done.', 'raothue' ) . ' <a href="' . admin_url() . '">' . __( 'Have fun!', 'raothue' ) . '</a>' . '</p>';
		echo '<p>' . __( 'Remember to update the passwords and roles of imported users.', 'raothue' ) . '</p>';
	}

	/**
	 * //
	 *
	 * @param array $options //.
	 */
	protected function import_options( $options ) {
		$transform_page_id = array( 'page_on_front', 'page_for_posts' );

		foreach ( $options as $key => $value ) {
			$key = trim( $key );

			if ( in_array( $key, $transform_page_id ) ) {
				$page = get_page_by_title( $value );
				$value = isset( $page->ID ) ? $page->ID : 0;
			}

			update_option( $key, $value );
		}
	}

	/**
	 * //
	 *
	 * @param array $setmenu //.
	 */
	protected function import_menu_locations( $setmenu ) {
		$locations = array();

		foreach ( $setmenu as $id => $name ) {
			$menu = get_term_by( 'name', $name, 'nav_menu' );

			if ( isset( $menu->term_id ) ) {
				$locations[ $id ] = $menu->term_id;
			}
		}

		set_theme_mod( 'nav_menu_locations', $locations );
		$this->logger->info( esc_html__( 'Imported menus', 'raothue' ) );
	}

	/**
	 * //
	 *
	 * @param array $revslider //.
	 */
	protected function import_revsliders( $revslider ) {
		if ( ! class_exists( 'RevSlider' ) ) {
			return;
		}

		$slider = new RevSlider();

		foreach ( $revslider as $name => $zip ) {
			if ( ! RevSlider::isAliasExists( $name ) ) {
				@$slider->importSliderFromPost( true, true, $zip );
				$this->logger->info( esc_html__( 'Imported revslider', 'raothue' ) );
			}
		}
	}

	/**
	 * //
	 *
	 * @param  string $base_path //.
	 * @return array
	 */
	protected function parser_metadata( $base_path ) {
		$metadata = array(
			'wordpress'  => 'wordpress.xml',
			'customizer' => 'customizer.json',
			'widgets'    => 'widgets.json',
			'revslider'  => array(),
			'setmenu'    => array(),
		);

		// Parser metadata from metadata.json.
		if ( file_exists( $metadata_path = $base_path . '/metadata.json' ) ) {
			$raw_metadata = (array) json_decode( file_get_contents( $metadata_path ) , true );
			$metadata = wp_parse_args( $raw_metadata, $metadata );
		}

		$whitelist = array( 'setmenu', 'options' );

		foreach ( $metadata as $key => &$path ) {
			if ( ! in_array( $key, $whitelist ) ) {
				$path = $this->parser_path( $path, $base_path );
			}
		}

		return $metadata;
	}

	/**
	 * //
	 *
	 * @param  string $path //.
	 * @param  string $base_path //.
	 * @return string
	 */
	protected function parser_path( $path, $base_path ) {
		if ( is_array( $path ) ) {
			foreach ( $path as &$_path ) {
				$_path = $this->parser_path( $_path, $base_path );
			}

			return $path;
		}

		if ( is_string( $path ) ) {
			$subpath = $base_path . '/' . basename( $path );

			if ( file_exists( $subpath ) && is_readable( $subpath ) ) {
				return realpath( $subpath );
			}
		}

		return '';
	}

	/**
	 * //
	 *
	 * @return WXR_Importer
	 */
	protected function get_importer() {
		$settings = apply_filters( 'rt_importer_settings', array(
			'fetch_attachments' => true,
			'dummy_attachments' => true,
		) );

		$importer = new RT_Importer( $settings );
		$importer->set_logger( $this->logger );

		return $importer;
	}

	/**
	 * Welcome template importer
	 */
	protected function welcome() {
		?><div class="themes-php rt-importer">
			<div class="wrap">
				<h1 class="theme-title">
					<span><?php echo esc_html__( 'Imports Demo', 'raothue' ); ?></span>
					<span class="title-count theme-count"><?php echo esc_html__( count( $this->metadata ) ); ?></span>
				</h1>

				<div class="theme-browser rendered">
					<div class="themes">

						<?php foreach ( $this->metadata as $id => $meta ) : ?>
						<div class="theme">

							<?php if ( ! empty( $meta['screenshot'] ) ) { ?>
								<div class="theme-screenshot">
									<img src="<?php echo esc_url( $meta['screenshot'] ); ?>" alt="<?php echo esc_html__( $meta['name'] ) ?>">
								</div>
							<?php } else { ?>
								<div class="theme-screenshot blank"></div>
							<?php } ?>

							<?php if ( empty( $meta['imported'] ) ) : ?>
								<h2 class="theme-name"><?php echo esc_html__( $meta['name'] ); ?></h2>
							<?php else : ?>
								<h2 class="theme-name">
									<?php printf( wp_kses( __( '<span>Imported:</span> %s' ), array( 'span' => array() ) ), esc_html__( $meta['name'] ) ); ?>
								</h2>
							<?php endif; ?>

							<div class="theme-actions">
								<?php if ( empty( $meta['imported'] ) ) : ?>
									<a class="button button-secondary" onclick="return confirm('<?php echo esc_html__( 'Are you sure you want to do this?', 'raothue' ); ?>');" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?import=rt-importer&amp;step=1&id=' . $id ), 'rt-import-demo' ) ); ?>">
										<?php echo esc_html__( 'Import', 'raothue' ); ?>
									</a>

									<?php if ( ! empty( $meta['preview'] ) ) : ?>
										<a class="button button-primary" target="_blank" href="<?php echo esc_url( $meta['preview'] ); ?>">
											<?php echo esc_html__( 'Preview', 'raothue' ); ?>
										</a>
									<?php endif ?>

								<?php else : ?>
									<a class="button button-secondary" onclick="return confirm('<?php echo esc_html__( 'Are you sure you want to do this?', 'raothue' ); ?>');" href="<?php echo esc_url( wp_nonce_url( admin_url( 'admin.php?import=rt-importer&amp;step=1&id=' . $id ), 'rt-import-demo' ) ); ?>">
										<?php echo esc_html__( 'Re-Import', 'raothue' ); ?>
									</a>
								<?php endif; ?>
							</div>

						</div>
						<?php endforeach; ?>

					</div>
				</div>
			</div>
		</div><?php
	}
}
