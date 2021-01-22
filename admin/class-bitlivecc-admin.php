<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The admin functionality of the Bitlive
 *
 * Class BITLIVECC_Admin
 */
class BITLIVECC_Admin {
	/**
	 * Instance variable of this class
	 * @var null
	 */
	private static $ins = null;

	/**
	 * Initialize the class and set its properties.
	 * BITLIVECC_Admin constructor.
	 */
	public function __construct() {
	}

	/**
	 * @return BITLIVECC_Admin|null
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}
}
