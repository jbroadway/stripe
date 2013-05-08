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
 */
class Payment extends \Model {
	public $table = '#prefix#payment';
}

?>