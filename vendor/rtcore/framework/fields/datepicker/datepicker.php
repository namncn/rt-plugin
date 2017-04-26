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
class CSFramework_Option_datepicker extends CSFramework_Options {

	/**
	 * //
	 *
	 * @param  array $attributes //.
	 * @return string
	 */
	public function element_attributes( $attributes = array() ) {
		if ( isset( $this->field['format'] ) ) {
			$attributes['data-format'] = $this->field['format'];
		} else {
			$attributes['data-format'] = get_option( 'date_format' );
		}

		$attributes['data-format'] = $this->dateformat( $attributes['data-format'] );

		return parent::element_attributes( $attributes );
	}

	/**
	 * Output the field.
	 */
	public function output() {
		echo $this->element_before(); // WPCS: XSS OK. ?>

		<input type="text" data-datepicker name="<?php echo esc_attr( $this->element_name() ); ?>" value="<?php echo esc_attr( $this->element_value() ); ?>" <?php echo $this->element_class() . $this->element_attributes(); // WPCS: XSS OK. ?>>

		<?php echo $this->element_after(); // WPCS: XSS OK.
	}

	/**
	 * Matches each symbol of PHP date format standard with jQuery equivalent codeword
	 *
	 * @author Tristan Jahier
	 * @link http://tristan-jahier.fr/blog/2013/08/convertir-un-format-de-date-php-en-format-de-date-jqueryui-datepicker
	 *
	 * @param string $php_format //.
	 */
	public function dateformat( $php_format ) {
		$symbols_matching = array(
			// Day.
			'd' => 'dd',
			'D' => 'D',
			'j' => 'd',
			'l' => 'DD',
			'N' => '',
			'S' => '',
			'w' => '',
			'z' => 'o',
			// Week.
			'W' => '',
			// Month.
			'F' => 'MM',
			'm' => 'mm',
			'M' => 'M',
			'n' => 'm',
			't' => '',
			// Year.
			'L' => '',
			'o' => '',
			'Y' => 'yy',
			'y' => 'y',
			// Time.
			'a' => '',
			'A' => '',
			'B' => '',
			'g' => '',
			'G' => '',
			'h' => '',
			'H' => '',
			'i' => '',
			's' => '',
			'u' => '',
		);

		$jqueryui_format = '';
		$escaping = false;

		for ( $i = 0; $i < strlen( $php_format ); $i++ ) {
			$char = $php_format[ $i ];

			if ( '\\' === $char ) {
				$i++;

				if ( $escaping ) { $jqueryui_format .= $php_format[ $i ];
				} else {
					$jqueryui_format .= '\'' . $php_format[ $i ];
				}

				$escaping = true;

			} else {
				if ( $escaping ) { $jqueryui_format .= "'";
					$escaping = false; }
				if ( isset( $symbols_matching[ $char ] ) ) {
					$jqueryui_format .= $symbols_matching[ $char ];
				} else {
					$jqueryui_format .= $char;
				}
			}
		}

		return $jqueryui_format;
	}
}
