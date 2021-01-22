<?php
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

/**
 * The admin functionality of the Bitlive
 *
 * Class BITLIVECC_Public
 */
class BITLIVECC_Public {
	/**
	 * Instance variable of this class
	 * @var null
	 */
	private static $ins = null;

	/**
	 * Initialize the class and set its properties.
	 * BITLIVECC_Public constructor.
	 */
	public function __construct() {
		add_filter( 'fep_menu_buttons', array( $this, 'remove_extra_menus' ), 99, 1 );
	}

	/**
	 * @return BITLIVECC_Public|null
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}

	/**
	 * @param $menu
	 *
	 * @return mixed
	 */
	public function remove_extra_menus( $menu ) {
		//dk_pc_debug( $menu );
		foreach ( $menu as $menu_key => $menu_data ) {
			if ('newmessage' !== $menu_key){
				unset($menu[$menu_key]);
			}
		}

		return $menu;
	}
}
