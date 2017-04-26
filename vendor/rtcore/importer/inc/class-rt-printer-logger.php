<?php
/**
 * Importer Logger
 *
 * @package RT-Importer
 */

/**
 * RT_Printer_Logger Class
 */
class RT_Printer_Logger extends WP_Importer_Logger {
	/**
	 * //
	 *
	 * @var array
	 */
	protected $messages = array();

	/**
	 * Logs with an arbitrary level.
	 *
	 * @param mixed  $level   //.
	 * @param string $message //.
	 * @param array  $context //.
	 */
	public function log( $level, $message, array $context = array() ) {
		parent::log( $level, $message, $context );
		echo sprintf( '<p class="ncn-log-%3$s" %2$s>%1$s</p>', $message, $this->level2color( $level ), $level ); // WPCS: XSS OK.
	}

	/**
	 * //
	 *
	 * @param  string $level //.
	 * @return string
	 */
	protected function level2color( $level ) {
		$levels = array(
			'emergency' => '',
			'alert'     => '',
			'critical'  => '',
			'notice'    => '',
			'error'     => '#eb2214',
			'warning'   => '#FF8600',
			'info'      => '#5C8808',
			'debug'     => '#A29F9F',
		);

		$color = '';

		if ( isset( $levels[ $level ] ) ) {
			$color = $levels[ $level ];
		}

		return $color ? sprintf( 'style="color: %s;"', $color ) : '';
	}
}
