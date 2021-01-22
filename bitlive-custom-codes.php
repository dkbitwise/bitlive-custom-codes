<?php
/**
 * Plugin Name:       Bitwise Live Custom Codes
 * Plugin URI:        https://bitwiseacademy.com/
 * Description:       Adding custom codes in Bitwise live for different feature
 * Version:           1.0.0
 * Author:            Bitwise
 * Author URI:        https://bitwiseacademy.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       bitlive-custom-codes
 * Domain Path:       /languages
 */

defined( 'ABSPATH' ) || exit; //Exit if accessed directly

if ( ! class_exists( 'BITLIVECC_Core' ) ) {

	class BITLIVECC_Core {
		/**
		 * @var BITLIVECC_Core
		 */
		public static $_instance = null;

		/**
		 * @var BITLIVECC_Admin
		 */
		public $admin;

		/**
		 * @var BITLIVECC_Public
		 */
		public $public;

		/**
		 * BITLIVECC_Core constructor.
		 */
		public function __construct() {
			/**
			 * Load important variables and constants
			 */
			$this->define_plugin_properties();

			/**
			 * Initiates and load hooks
			 */
			$this->load_hooks();
		}

		/**
		 * Defining constants
		 */
		public function define_plugin_properties() {
			define( 'BITLIVECC_VERSION', '1.0.0' );
			define( 'BITLIVECC_PLUGIN_FILE', __FILE__ );
			define( 'BITLIVECC_PLUGIN_DIR', __DIR__ );
			define( 'BITLIVECC_PLUGIN_SLUG', 'bitlive-custom-codes' );
			add_action( 'plugins_loaded', array( $this, 'load_wp_dependent_properties' ), 1 );
		}

		/**
		 * Defining WP dependent properties
		 */
		public function load_wp_dependent_properties() {
			define( 'BITLIVECC_PLUGIN_URL', untrailingslashit( plugin_dir_url( BITLIVECC_PLUGIN_FILE ) ) );
			define( 'BITLIVECC_PLUGIN_BASENAME', plugin_basename( __FILE__ ) );
		}

		/**
		 * Loading hooks
		 */
		public function load_hooks() {
			/**
			 * Initialize Localization
			 */
			add_action( 'init', array( $this, 'localization' ) );
			add_action( 'plugins_loaded', array( $this, 'load_classes' ), 1 );
		}

		/**
		 * Localizing the plugin text strings
		 */
		public function localization() {
			load_plugin_textdomain( 'bitlive-custom-codes', false, __DIR__ . '/languages/' );
		}

		/**
		 * Loading plugin classes
		 */
		public function load_classes() {
			/**
			 * Loads the Admin file
			 */
			require __DIR__ . '/admin/class-bitlivecc-admin.php';
			$this->admin = BITLIVECC_Admin::get_instance();

			require __DIR__ . '/public/class-bitlivecc-public.php';
			$this->public = BITLIVECC_Public::get_instance();
		}

		/**
		 * @return BITLIVECC_Core|null
		 */
		public static function get_instance() {
			if ( null === self::$_instance ) {
				self::$_instance = new self;
			}

			return self::$_instance;
		}
	}
}

if ( ! function_exists( 'BITLIVECC_Core' ) ) {
	/**
	 * @return BITLIVECC_Core
	 */
	function BITLIVECC_Core() {
		return BITLIVECC_Core::get_instance();
	}
}

BITLIVECC_Core();