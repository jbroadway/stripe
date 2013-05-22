<?php

/**
 * Receives a token and creates a charge.
 * Optionally redirects the user, or calls
 * a custom charge handler script, passing
 * it the Stripe_Charge object for the
 * transaction.
 */

// Verify that they're on an SSL connection
$this->force_https ();

// Initialize the Stripe API
$this->run ('stripe/init');

// Perform the charge
try {
	$charge = Stripe_Charge::create (array (
		'card' => $_POST['stripeToken'],
		'amount' => $_POST['amount'],
		'description' => $_POST['description'],
		'currency' => isset ($_POST['currency']) ? $_POST['currency'] : Appconf::stripe ('Stripe', 'currency')
	));
} catch (Stripe_CardError $e) {
	// The card was declined
	echo $this->error (412, 'Card declined', 'Your card was declined. Please go back and verify your information.');
	return;
} catch (Exception $e) {
	// Handle error
	error_log ('Error saving stripe_charge: ' . $e->getMessage ());
	echo $this->error (500, 'An error occurred', 'Unable to save customer info at this time. Please try again later.');
	return;
}

// Save the payment
$p = new stripe\Payment (array (
	'user_id' => 0,
	'stripe_id' => $charge->id,
	'description' => $_POST['description'],
	'amount' => $_POST['amount'],
	'plan' => '',
	'ts' => gmdate ('Y-m-d H:i:s'),
	'ip' => ip2long ($_SERVER['REMOTE_ADDR']),
	'type' => 'payment'
));
if (! $p->put ()) {
	// Handle error
	error_log ('Error saving payment to database: ' . $p->error);
	echo $this->error (500, 'An error occurred', 'Unable to save payment info at this time. Please contact support for assistance.');
	return;
}

// Redirect if they've provided one
if (isset ($_POST['redirect']) && strlen ($_POST['redirect']) !== 0) {
	$this->redirect ($_POST['redirect']);
}

// Send to a charge handler, if set
if (! empty ($appconf['Stripe']['charge_handler'])) {
	echo $this->run (
		$appconf['Stripe']['charge_handler'],
		array (
			'charge' => $charge,
			'payment' => $p
		)
	);
	return;
}

// Fallback handling
$page->title = __ ('Payment completed');
printf ('<p>%s</p>', __ ('Thank you, your payment has been made.'));

?>