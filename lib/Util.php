<?php

namespace stripe;

/**
 * Utility methods for the Stripe app.
 */
class Util {
	/**
	 * Fetch a list of valid button labels.
	 */
	public static function get_labels () {
		return array ('Pay', 'Buy', 'Donate', 'Add Card', 'Checkout');
	}

	/**
	 * Fetch yes/no for the collect address option.
	 */
	public static function yes_no () {
		return array ('No', 'Yes');
	}
	
	/**
	 * Convert currency into display format.
	 */
	public static function money_format ($cents) {
		return money_format ('%^!n', $cents / 100);
	}
	
	/**
	 * Filter names and show 'None' if empty.
	 */
	public static function user_name ($name) {
		if (strlen ($name) === 0) {
			return __ ('None');
		}
		return (strlen ($name) > 15)
			? substr ($name, 0, 12) . '...'
			: $name;
	}
	
	/**
	 * Filter plans and show 'None' if empty.
	 */
	public static function plan_name ($name) {
		if (strlen ($name) === 0) {
			return __ ('None');
		}
		$plan = \Appconf::stripe ('Plans', $name);
		if (! is_array ($plan)) {
			return $name;
		}
		return $plan['label'];
	}
	
	/**
	 * Filters the description length for display.
	 */
	public static function description_length ($desc) {
		return (strlen ($desc) > 28)
			? substr ($desc, 0, 25) . '...'
			: $desc;
	}
}

?>