<?php

/**
 * Receives a token and creates a charge.
 * Optionally redirects the user, or calls
 * a custom charge handler script, passing
 * it the Stripe_Charge object for the
 * transaction.
 */

// Initialize the Stripe API
$this->run ('stripe/init');

// Perform the charge
$charge = Stripe_Charge::create (array (
	'card' => $_POST['stripeToken'],
	'amount' => $_POST['amount'],
	'description' => $_POST['description'],
	'currency' => isset ($_POST['currency']) ? $_POST['currency'] : 'usd'
));

// Redirect if they've provided one
if (isset ($_POST['redirect'])) {
	$this->redirect ($_POST['redirect']);
}

// Send to a charge handler, if set
if (! empty ($appconf['Stripe']['charge_handler'])) {
	echo $this->run ($appconf['Stripe']['charge_handler'], $charge);
	return;
}

// Fallback handling
$page->title = __ ('Payment completed');
printf ('<p>%s</p>', __ ('Thank you, your payment has been made.'));

?>