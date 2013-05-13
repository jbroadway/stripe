<?php

/**
 * This provides a webhook listener for Stripe to notify
 * of actions that occur outside of direct API requests.
 * To configure your Stripe account, add the following
 * URL (adjusted for your own domain) to your Stripe
 * dashboard:
 *
 *     http://www.example.com/stripe/webhook
 */

$page->layout = false;

$this->run ('stripe/init');

$data = $this->get_put_data ();
$event = json_decode ($data);

?>