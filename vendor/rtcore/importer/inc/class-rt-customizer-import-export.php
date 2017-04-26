<?php
/**
 * Customizer Import Export
 *
 * @package RT-Importer
 */

/**
 * RT_Customizer_Import_Export class
 */
class RT_Customizer_Import_Export {

	public static function import( $path ) {
		$raw_data = file_get_contents( $path );
		$data = json_decode( $raw_data, true );

		if ( isset( $data['theme_mods'] ) && is_array( $data['theme_mods'] ) ) {
			foreach ( $data['theme_mods'] as $key => $value ) {
				set_theme_mod( $key, $value );
			}
		}
	}

	/**
	 * Handler download customizer settings.
	 */
	public static function handler_export() {
		ob_clean();

		header( 'Content-disposition: attachment; filename=' . get_stylesheet() . '-customizer.json' );
		header( 'Content-Type: application/octet-stream; charset=' . get_option( 'blog_charset' ) );

		echo json_encode( static::export_data() );
		exit;
	}

	/**
	 * Export customizer settings.
	 *
	 * @return array
	 */
	public static function export_data() {
		$exports = array(
			'options' => array(),
			'template' => get_template(),
			'theme_mods' => (array) get_theme_mods(),
		);

		// Plugin developers can specify additional option keys to export.
		$export_options = apply_filters( 'rt_customizer_export_options', array() );

		foreach ( $export_options as $key ) {
			if ( $opt_values = get_option( $key ) ) {
				$data['options'][ $key ] = $opt_values;
			}
		}

		return $exports;
	}
}
