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
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_public_scripts' ) );
		add_filter( 'fep_menu_buttons', array( $this, 'remove_extra_menus' ), 99, 1 );
		add_filter( 'fep_form_fields_after_process', array( $this, 'alter_message_recipients' ), 99, 2 );
		add_filter( 'fep_enable_email_send', array( $this, 'disable_fep_email_on_message' ) );
		add_filter( 'fep_query_url_filter', array( $this, 'maybe_remove_new_message_link' ), 10, 2 );
		add_action( 'fep_footer_note', array( $this, 'popup_message_model' ) );
	}

	/**
	 * Create an instance of this class
	 * @return BITLIVECC_Public|null
	 */
	public static function get_instance() {
		if ( null === self::$ins ) {
			self::$ins = new self;
		}

		return self::$ins;
	}

	/**
	 * Enqueuing public styles and scripts
	 */
	public function enqueue_public_scripts() {
		wp_enqueue_style( 'bitlive-public-style', plugin_dir_url( __FILE__ ) . 'css/bitlivecc-public.css', array(), '1.0.0', 'all' );
		wp_enqueue_script( 'bitlive-public-script', plugin_dir_url( __FILE__ ) . 'js/bitlivecc-public.js', array(), '1.0.0', 'all' );
	}

	/**
	 * @param $menu
	 *
	 * @return mixed
	 */
	public function remove_extra_menus( $menu ) {
		foreach ( $menu as $menu_key => $menu_data ) {
			if ( 'newmessage' !== $menu_key ) {
				unset( $menu[ $menu_key ] );
			}
		}

		return $menu;
	}

	/**
	 * @param $fields
	 * @param $where
	 *
	 * @return mixed
	 */
	public function alter_message_recipients( $fields, $where ) {
		foreach ( $fields as $field_key => $field ) {
			if ( 'message_to' === $field_key ) {
				$fields[ $field_key ]['type'] = 'select';
				$tutors                       = $this->get_recipients();

				$fields[ $field_key ]['options'] = $tutors;
				break;
			}
		}

		return $fields;
	}

	/**
	 * Return recipients for sending message on chat page.
	 * @return array
	 */
	public function get_recipients() {
		global $wpdb;
		$recipients      = [];
		$recipients[0]   = '-Select Recipient-';
		$current_user_id = get_current_user_id();
		if ( $current_user_id < 1 ) {
			return $recipients;
		}

		$current_booking_table      = $wpdb->prefix . 'bookme_current_booking';
		$customers_table            = $wpdb->prefix . 'bookme_customers';
		$customer_booking_table     = $wpdb->prefix . 'bookme_customer_booking';
		$customer_booking_ref_table = $wpdb->prefix . 'bookme_customer_booking_ref';
		$employee_table             = $wpdb->prefix . 'bookme_employee';
		$bwlive_students            = $wpdb->prefix . 'bwlive_students';

		$current_user = wp_get_current_user();
		$user_roles   = $current_user->roles;
		if ( in_array( 'subscriber', $user_roles, true ) ) {
			$parent_email = $current_user->user_email;
			$customer_id  = $wpdb->get_var( "SELECT `id` FROM $customers_table WHERE `email`='$parent_email'" );
			if ( $customer_id > 0 ) {
				$booking_result = $wpdb->get_results( "SELECT DISTINCT(`booking_id`) FROM $customer_booking_table WHERE `customer_id`='$customer_id'", ARRAY_A );
				$booking_ids    = wp_list_pluck( $booking_result, 'booking_id' );
				if ( is_array( $booking_ids ) && count( $booking_ids ) > 0 ) {
					$employee_result = $wpdb->get_results( "SELECT `emp_id`,max(`date`) as date FROM $current_booking_table WHERE `id` IN (" . implode( ',', $booking_ids ) . ") GROUP BY `emp_id`", ARRAY_A );
					$employee_ids    = [];
					foreach ( $employee_result as $emp_data ) {
						if ( strtotime( $emp_data['date'] ) > strtotime( date( 'Y-m-d' ) ) ) {
							$employee_ids[] = $emp_data['emp_id'];
						}
					}
					if ( is_array( $employee_ids ) && count( $employee_ids ) > 0 ) {
						$emp_result = $wpdb->get_results( "SELECT `id`,`name`, `email` FROM $employee_table WHERE `id` IN (" . implode( ',', $employee_ids ) . ")", ARRAY_A );
						foreach ( $emp_result as $emp ) {
							$emp_user                               = get_user_by( 'email', $emp['email'] );
							$recipients[ $emp_user->user_nicename ] = $emp['name'];
						}
					}
				}
			}
		}

		if ( in_array( 'lp_teacher', $user_roles, true ) ) {
			$tutor_email     = $current_user->user_email;
			$tutor_bookme_id = $wpdb->get_var( "SELECT `id` FROM $employee_table WHERE `email`='$tutor_email'" );
			if ( $tutor_bookme_id > 0 ) {
				$booking_result = $wpdb->get_results( "SELECT DISTINCT(`id`) as booking_id FROM $current_booking_table WHERE `emp_id`='$tutor_bookme_id'", ARRAY_A );
				$booking_ids    = wp_list_pluck( $booking_result, 'booking_id' );
				if ( is_array( $booking_ids ) && count( $booking_ids ) > 0 ) {
					$student_result = $wpdb->get_results( "SELECT DISTINCT (`student_id`) FROM $customer_booking_ref_table WHERE `booking_id` IN (" . implode( ',', $booking_ids ) . ")", ARRAY_A );
					$student_ids    = wp_list_pluck( $student_result, 'student_id' );
					if ( is_array( $student_ids ) && count( $student_ids ) > 0 ) {
						$std_id_str = '';
						foreach ( $student_ids as $student_id ) {
							$std_id_str .= "'" . $student_id . "'";
							if ( next( $student_ids ) ) {
								$std_id_str .= ",";
							}
						}
						$student_details = $wpdb->get_results( "SELECT `student_fname`, `student_lname`, `student_email` FROM $bwlive_students WHERE `student_id` IN ($std_id_str)", ARRAY_A );
						foreach ( $student_details as $student ) {
							$student_user                               = get_user_by( 'email', $student['student_email'] );
							$recipients[ $student_user->user_nicename ] = $student['student_fname'] . ' ' . $student['student_lname'];
						}
					}
				}
			}
		}

		return $recipients;
	}

	/**
	 * @param $enable
	 *
	 * @return false
	 */
	public function disable_fep_email_on_message( $enable ) {
		return false;
	}

	/**
	 * @param $url
	 * @param $args
	 *
	 * @return mixed
	 */
	public function maybe_remove_new_message_link( $url, $args ) {
		if ( 'newmessage' === $args['fepaction'] ) {
			$current_user = wp_get_current_user();
			$user_roles   = $current_user->roles;
			if ( in_array( 'subscriber', $user_roles, true ) ) {
				$recipients = $this->get_recipients();
				if ( count( $recipients ) < 2 ) {
					$url = 'javascript:void(0);';
				}
			}
		}

		return $url;
	}

	/**
	 * Creating an overlay message for no course registered
	 */
	public function popup_message_model() { ?>
        <div class="bwlive-bit-hide bwlive_overlay">
            <span class="bw-close">X</span>
            <p class="bwlive-msg"><?php esc_html_e( 'You don\'t have a booking right now', 'bitlive-custom-codes' ); ?></p>
        </div>
		<?php
	}
}
