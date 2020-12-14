<?php

/**
 * Receives a token and creates a charge.
 * Optionally redirects the user, or calls
 * a custom charge handler script, passing
 * it the Stripe_Charge object for the
 * transaction.
 */

// Verify that they're on an SSL connection
if (! conf ('General', 'debug')) {
	$this->force_https ();
}

// Initialize the Stripe API
$stripe = $this->run ('stripe/init');

// Perform the charge
try {
	$charge = $stripe->charges->create (array (
		'source' => $_POST['stripeToken'],
		'amount' => $_POST['amount'],
		'description' => $_POST['description'],
		'currency' => isset ($_POST['currency']) ? $_POST['currency'] : Appconf::stripe ('Stripe', 'currency')
	));
} catch (\Stripe\Exception\CardException $e) {
	// The card was declined
	echo $this->error (412, 'Card declined', 'Your card was declined. Please go back and verify your information.');
	return;
} catch (Exception $e) {
	// Handle error
	error_log ('Error saving Stripe charge: ' . $e->getMessage ());
	echo $this->error (500, 'An error occurred', 'Unable to save customer info at this time. Please try again later.');
	return;
}

if (User::require_login ()) {
	$user_id = User::val ('id');
} else {
	$user_id = 0;
}

// Save the payment
$p = new stripe\Payment (array (
	'user_id' => $user_id,
	'stripe_id' => $charge->id,
	'description' => $_POST['description'],
	'amount' => (int) $_POST['amount'],
	'plan' => '',
	'ts' => gmdate ('Y-m-d H:i:s'),
	'ip' => ip2long ($this->remote_addr ()),
	'type' => 'payment',
	'email' => $_POST['stripeEmail'],
	'billing_name' => isset ($_POST['stripeBillingName']) ? $_POST['stripeBillingName'] : '',
	'billing_address' => isset ($_POST['stripeBillingAddressLine1']) ? $_POST['stripeBillingAddressLine1'] : '',
	'billing_city' => isset ($_POST['stripeBillingAddressCity']) ? $_POST['stripeBillingAddressCity'] : '',
	'billing_state' => isset ($_POST['stripeBillingAddressState']) ? $_POST['stripeBillingAddressState'] : '',
	'billing_country' => isset ($_POST['stripeBillingAddressCountry']) ? $_POST['stripeBillingAddressCountry'] : '',
	'billing_zip' => isset ($_POST['stripeBillingAddressZip']) ? $_POST['stripeBillingAddressZip'] : '',
	'shipping_name' => isset ($_POST['stripeShippingName']) ? $_POST['stripeShippingName'] : '',
	'shipping_address' => isset ($_POST['stripeShippingAddressLine1']) ? $_POST['stripeShippingAddressLine1'] : '',
	'shipping_city' => isset ($_POST['stripeShippingAddressCity']) ? $_POST['stripeShippingAddressCity'] : '',
	'shipping_state' => isset ($_POST['stripeShippingAddressState']) ? $_POST['stripeShippingAddressState'] : '',
	'shipping_country' => isset ($_POST['stripeShippingAddressCountry']) ? $_POST['stripeShippingAddressCountry'] : '',
	'shipping_zip' => isset ($_POST['stripeShippingAddressZip']) ? $_POST['stripeShippingAddressZip'] : ''
));
if (! $p->put ()) {
	// Handle error
	error_log ('Error saving payment to database: ' . $p->error);
	echo $this->error (500, 'An error occurred', 'Unable to save payment info at this time. Please contact support for assistance.');
	return;
}

// Redirect if they've provided one
if (isset ($_POST['redirect']) && strlen ($_POST['redirect']) !== 0) {
	$this->redirect (sprintf ($_POST['redirect'], $p->id));
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