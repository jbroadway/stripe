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
		return number_format ($cents / 100, 2);
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
	
	/**
	 * Creates links to orders if the description is in the format "Order #123",
	 * otherwise crops it via `description_length`.
	 */
	public static function description ($payment_id, $desc) {
		if (preg_match ('/^Order #([0-9]+)$/', $desc, $regs)) {
			return '<a href="/products/order/' . $payment_id . '/' . $regs[1] . '/completed">' . $desc . '</a>';
		}
		return self::description_length ($desc);
	}
}

?>