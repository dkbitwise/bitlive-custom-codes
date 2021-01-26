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
		add_filter( 'fep_form_fields_after_process', array( $this, 'alter_message_recipients' ), 99, 2 );
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
	 * @return array
	 */
	public function get_recipients() {
		global $wpdb;
		$recipients      = [];
		$recipients[0] = '-Select Recipient-';
		$current_user_id = get_current_user_id();
		if ( $current_user_id < 1 ) {
			return $recipients;
		}

		$current_booking_table  = $wpdb->prefix . 'bookme_current_booking';
		$customers_table        = $wpdb->prefix . 'bookme_customers';
		$customer_booking_table = $wpdb->prefix . 'bookme_customer_booking';
		$employee_table         = $wpdb->prefix . 'bookme_employee';

		$current_user = wp_get_current_user();
		$user_roles   = $current_user->roles;
		if ( in_array( 'subscriber', $user_roles, true ) ) {
			$parent_email = $current_user->user_email;
			$customer_id  = $wpdb->get_var( "SELECT `id` FROM $customers_table WHERE `email`='$parent_email'" );
			if ( $customer_id > 0 ) {
				$booking_result = $wpdb->get_results( "SELECT DISTINCT(`booking_id`) FROM $customer_booking_table WHERE `customer_id`='$customer_id'", ARRAY_A );
				$booking_ids    = wp_list_pluck( $booking_result, 'booking_id' );
				if ( is_array( $booking_ids ) && count( $booking_ids ) > 0 ) {
					$employee_result = $wpdb->get_results( "SELECT DISTINCT(`emp_id`) FROM $current_booking_table WHERE `id` IN (" . implode( ',', $booking_ids ) . ")", ARRAY_A );
					$employee_ids    = wp_list_pluck( $employee_result, 'emp_id' );
					if ( is_array( $employee_ids ) && count( $employee_ids ) > 0 ) {
						$emp_result = $wpdb->get_results( "SELECT `id`,`name`, `email` FROM $employee_table WHERE `id` IN (" . implode( ',', $employee_ids ) . ")", ARRAY_A );
						foreach ( $emp_result as $emp ) {
							$recipients[ $emp['email'] ] = $emp['name'];
						}
					}
				}
			}
		}

		return $recipients;
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
}
