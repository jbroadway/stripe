<?php

/**
 * This provides a webhook listener for Stripe to notify
 * of actions that occur outside of direct API requests.
 * To configure your Stripe account, add the following
 * URL (adjusted for your own domain) to your Stripe
 * dashboard:
 *
 *     https://www.example.com/stripe/webhook
 */

$page->layout = false;

$stripe = $this->run ('stripe/init');

// Get data and make sure it's okay
$data = $this->get_put_data ();
$event = json_decode ($data);
if ($event === null) {
	return;
}

// Retrieve the original event for added security
try {
	$event = $stripe->events->retrieve ($event->id);
} catch (Exception $e) {
	return;
}

if (! empty ($appconf['Stripe']['webhook_handler'])) {
	echo $this->run (
		$appconf['Stripe']['webhook_handler'],
		array (
			'event' => $event
		)
	);
}

?>