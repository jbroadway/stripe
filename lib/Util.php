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
}

?>