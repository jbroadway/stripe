<?php

/**
 * Generates a payment form and handles the charge,
 * including one-time payments and subscription plans.
 * Unlike `stripe/charge`, this requires a registered
 * user account to link the payment to.
 *
 * Optionally redirects the user, or calls a custom
 * charge handler script, passing it the Stripe_Charge
 * object for the transaction, with an added `payment_id`
 * attribute for retrieving the payment record from the
 * database.
 */

// Verify the user is logged in
$this->require_login ();

// Initialize the Stripe app
$this->run ('stripe/init');

$form = new Form ('post', $this);

$form->data = array ('charge_failed' => false);

echo $form->handle (function ($form) use ($page, $tpl) {
	// Get the details submitted by the form
	$token = $_POST['stripeToken'];
	$amount = $_POST['amount'];
	$description = $_POST['description'];
	$currency = isset ($_POST['currency']) ? $_POST['currency'] : 'usd';
	$plan = isset ($_POST['plan']) ? $_POST['plan'] : false;

	// Get the current user and check for a customer ID
	$user = User::current ();
	$customer_id = $user->ext ('stripe_customer');

	if (! $customer_id) {
		// Create a customer with Stripe
		try {
			$customer = Stripe_Customer::create (array (
				'card' => $token,
				'email' => $user->email,
				'description' => sprintf ('%d: %s', $user->id, $user->name)
			));
		} catch (Exception $e) {
			// Handle error
			error_log ('Error calling Stripe_Customer::create(): ' . $e->getMessage ());
			echo $form->controller->error (500, 'An error occurred', 'Unable to save customer info at this time. Please try again later.');
			return;
		}

		// Save the customer ID
		$user->ext ('stripe_customer', $customer->id);
		if (! $user->put ()) {
			// Handle error
			error_log ('Error saving stripe_customer: ' . $user->error);
			echo $form->controller->error (500, 'An error occurred', 'Unable to save customer info at this time. Please try again later.');
			return;
		}

		$customer_id = $customer->id;
	}

	// Charge the customer
	try {
		$info = array (
			'amount' => $amount, // In cents, e.g., 1000 = $10.00
			'currency' => $currency,
			'customer' => $customer_id,
			'description' => $description
		);
		if ($plan) {
			$info['plan'] = $plan;
		}
		$charge = Stripe_Charge::create ($info);
	} catch (Stripe_CardError $e) {
		// The card was declined
		$form->data = array ('charge_failed' => true);
		return false;
	} catch (Exception $e) {
		// Handle error
		error_log ('Error saving stripe_customer: ' . $user->error);
		echo $form->controller->error (500, 'An error occurred', 'Unable to save customer info at this time. Please try again later.');
		return;
	}

	// Save the payment
	$p = new stripe\Payment (array (
		'user_id' => $user->id,
		'stripe_id' => $charge->id,
		'description' => $description,
		'amount' => $amount,
		'currency' => $currency,
		'plan' => $plan ? $plan : '',
		'ts' => gmdate ('Y-m-d H:i:s')
	));
	if (! $p->put ()) {
		// Handle error
		error_log ('Error saving payment to database: ' . $user->error);
		echo $form->controller->error (500, 'An error occurred', 'Unable to save payment info at this time. Please contact support for assistance.');
		return;
	}

	// Redirect if they've provided one
	if (isset ($_POST['redirect'])) {
		$form->controller->redirect ($_POST['redirect']);
	}

	// Send to a charge handler, if set
	$charge_handler = Appconf::stripe ('Stripe', 'charge_handler');
	if (! empty ($charge_handler)) {
		$charge->payment_id = $p->id;
		echo $this->run ($charge_handler, $charge);
		return;
	}

	// Fallback handling
	$page->title = __ ('Payment completed');
	printf ('<p>%s</p>', __ ('Thank you, your payment has been made.'));
});

?>