<?php

namespace stripe;

/**
 * Model for storing payment records.
 *
 * Fields:
 *
 * - id
 * - user_id
 * - stripe_id
 * - description
 * - amount
 * - plan
 * - ts
 * - ip
 * - type
 * - email
 * - billing_name
 * - billing_address
 * - billing_city
 * - billing_state
 * - billing_country
 * - billing_zip
 * - shipping_name
 * - shipping_address
 * - shipping_city
 * - shipping_state
 * - shipping_country
 * - shipping_zip
 */
class Payment extends \Model {
	public $table = '#prefix#payment';
}

?>