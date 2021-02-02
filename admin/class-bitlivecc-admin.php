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

	public $logger;

	/**
	 * Initialize the class and set its properties.
	 * BITLIVECC_Admin constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'admin_student_sync_menu' ) );
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

	/**
	 * Creating an admin menu for syncing students
	 */
	public function admin_student_sync_menu() {
		add_menu_page( __( 'Sync Students', 'bitlive-custom-codes' ), 'Sync Students', 'manage_options', 'bitlive-sync-live', array(
			$this,
			'bitlive_sync_students'
		), 'dashicons-welcome-learn-more' );
	}

	/**
	 *
	 */
	public function bitlive_sync_students() {
		include_once __DIR__ . '/templates/bitlivecc-student-template.php';
		$admin_page = filter_input( INPUT_GET, 'page', FILTER_SANITIZE_STRING );
		$sync       = filter_input( INPUT_GET, 'sync', FILTER_SANITIZE_STRING );

		if ( 'bitlive-sync-live' === $admin_page && 'yes' === $sync ) {
			global $wpdb;
			$bitlive_student_table = $wpdb->prefix . 'bwlive_students';
			$altered               = get_option( 'bitlive_student_altered', false );
			if ( ! $altered ) {
				$sql = "ALTER TABLE $bitlive_student_table ADD COLUMN `user_id` BIGINT(20) DEFAULT 0 AFTER `student_slot`";
				$wpdb->query( $sql );
				update_option( 'bitlive_student_altered', true );
			}
			$student_query = "SELECT `id`, `student_email`, `student_fname`, `student_lname` FROM $bitlive_student_table";
			$students      = $wpdb->get_results( $student_query, ARRAY_A );
			foreach ( $students as $student ) {
				$student_id    = $student['id'];
				$student_email = $student['student_email'];
				$student_fname = $student['student_fname'];
				$student_lname = $student['student_lname'];
				$password      = 'Psswrd@123';

				if ( username_exists( $student_email ) == null && email_exists( $student_email ) == false ) {
					$user_id = wp_create_user( $student_email, $password, $email );
					$user    = get_user_by( 'id', $user_id );
					$user->remove_role( 'subscriber' );
					$user->add_role( 'student' );
					update_user_meta( $user_id, 'first_name', $student_fname );
					update_user_meta( $user_id, 'last_name', $student_lname );

					$sql = "UPDATE $bitlive_student_table SET `user_id`=$user_id WHERE `id`=$student_id";
					$wpdb->query( $sql );
				}
			}
		}
	}

	/**
	 * Write a message to log in WC log tab if logging is enabled
	 *
	 * @param string $context
	 * @param string $message
	 */
	public function log( $message, $context = "Info" ) {
		if ( class_exists( 'WC_Logger' ) && ! is_a( $this->logger, 'WC_Logger' ) ) {
			$this->logger = new WC_Logger();
		}
		$log_message = $context . ' - ' . $message;

		if ( class_exists( 'WC_Logger' ) ) {
			$this->logger->add( 'bitlivecc', $log_message );
		}

		if ( defined( 'WP_DEBUG' ) && WP_DEBUG ) {
			error_log( $log_message );
		}
	}
}
