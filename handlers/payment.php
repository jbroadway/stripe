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
 * property for retrieving the payment record from the
 * database.
 *
 * Usage:
 *
 * In a handler:
 *
 *     echo $this->run ('stripe/payment', array (
 *         'amount' => 1000,
 *         'description' => 'Test payment',
 *         'callback' => function ($charge) {
 *             info ($charge);
 *         }
 *     ));
 *
 * In a view template, use:
 *
 *     {! stripe/payment
 *        ?amount=1000
 *        &description=Test payment
 *        &redirect=/thanks !}
 *
 * For a subscription:
 *
 *     {! stripe/payment
 *        ?plan=pro
 *        &redirect=/thanks !}
 */

// Verify that they're on an SSL connection
$this->force_https ();

// Verify the user is logged in
$this->require_login ();

// Initialize the Stripe API
$this->run ('stripe/init');

$page->add_style ('/apps/stripe/css/payment.css');
$page->add_script ('<script src="https://js.stripe.com/v2/"></script>');
$page->add_script ('<script>Stripe.setPublishableKey("' . Appconf::stripe ('Stripe', 'publishable_key') . '");</script>');
$page->add_script ('/apps/stripe/js/payment.js');

$form = new Form ('post', $this);
$form->js_validation = false;
if (isset ($data['plan'])) {
	$details = Appconf::stripe ('Plans', $data['plan']);
	if (! is_array ($details)) {
		error_log ('Error: Invalid plan name (' . $data['plan'] . ') or the plans may be misconfigured.');
		echo $this->error (500, 'An error occurred', 'We are unable to complete your request at this time.');
		return;
	}
	$data['description'] = $details['label'];
	$data['amount'] = $details['amount'];
	$data['interval'] = $details['interval'];
}
$form->data = array_merge ($data, array ('charge_failed' => false));

echo $form->handle (function ($form) use ($data, $page, $tpl) {
	// Get the details submitted by the form
	$token = $_POST['stripeToken'];
	$amount = $data['amount'];
	$description = $data['description'];
	$currency = Appconf::stripe ('Stripe', 'currency');
	$plan = isset ($data['plan']) ? $data['plan'] : false;
	$interval = false;

	if ($plan) {
		$details = Appconf::stripe ('Plans', $plan);
		if (! is_array ($details)) {
			error_log ('Error: Invalid plan name (' . $plan . ') or the plans may be misconfigured.');
			echo $form->controller->error (500, 'An error occurred', 'We are unable to complete your request at this time.');
			return;
		}
		$description = $details['label'];
		$amount = $details['amount'];
	}

	// Get the current user and check for a customer ID
	$user = User::current ();
	$customer_id = $user->ext ('stripe_customer');

	if (! $customer_id) {
		// Create a customer with Stripe
		$info = array (
			'card' => $token,
			'email' => $user->email,
			'description' => sprintf ('%d: %s', $user->id, $user->name)
		);
		if ($plan) {
			$info['plan'] = $plan;
		}
		try {
			$customer = Stripe_Customer::create ($info);
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
	} else {
		try {
			$customer = Stripe_Customer::retrieve ($customer_id);

			if ($plan) {
				$customer->updateSubscription (array ('plan' => $plan));
			}
		} catch (Exception $e) {
			// Handle error
			error_log ('Error calling Stripe_Customer::retrieve(): ' . $e->getMessage ());
			echo $form->controller->error (500, 'An error occurred', 'Unable to retrieve customer info at this time. Please try again later.');
			return;
		}
	}

	// Charge the customer
	if (! $plan) {
		try {
			$info = array (
				'amount' => $amount, // In cents, e.g., 1000 = $10.00
				'currency' => $currency,
				'customer' => $customer_id,
				'description' => $description
			);
			$charge = Stripe_Charge::create ($info);
		} catch (Stripe_CardError $e) {
			// The card was declined
			$form->data['charge_failed'] = true;
			return false;
		} catch (Exception $e) {
			// Handle error
			error_log ('Error saving stripe_charge: ' . $e->getMessage ());
			echo $form->controller->error (500, 'An error occurred', 'Unable to save customer info at this time. Please try again later.');
			return;
		}
		
		// Save the payment
		$p = new stripe\Payment (array (
			'user_id' => $user->id,
			'stripe_id' => $charge->id,
			'description' => $description,
			'amount' => $amount,
			'plan' => '',
			'ts' => gmdate ('Y-m-d H:i:s'),
			'ip' => ip2long ($_SERVER['REMOTE_ADDR']),
			'type' => 'payment'
		));
		if (! $p->put ()) {
			// Handle error
			error_log ('Error saving payment to database: ' . $p->error);
			echo $form->controller->error (500, 'An error occurred', 'Unable to save payment info at this time. Please contact support for assistance.');
			return;
		}
	} else {
		// Save the transaction
		$p = new stripe\Payment (array (
			'user_id' => $user->id,
			'stripe_id' => $customer_id,
			'description' => $description,
			'amount' => $amount,
			'plan' => $plan,
			'ts' => gmdate ('Y-m-d H:i:s'),
			'ip' => ip2long ($_SERVER['REMOTE_ADDR']),
			'type' => 'subscription'
		));
		if (! $p->put ()) {
			// Handle error
			error_log ('Error saving payment to database: ' . $p->error);
			echo $form->controller->error (500, 'An error occurred', 'Unable to save payment info at this time. Please contact support for assistance.');
			return;
		}
	}

	// Redirect if they've provided one
	if (isset ($data['redirect'])) {
		$form->controller->redirect ($data['redirect']);
	}

	// Send to a charge handler, if set
	if (isset ($data['callback'])) {
		if ($plan) {
			// Charge is actually customer
			$charge = $customer;
		}

		// Add the payment ID to the charge object
		$charge->payment_id = $p->id;

		if (is_callable ($data['callback'])) {
			// Treat as a callback
			echo call_user_func ($data['callback'], $charge);
			return;
		}
		// Treat as a handler
		echo $this->run ($data['callback'], $charge);
		return;
	}

	// Fallback handling
	$page->title = __ ('Payment completed');
	printf ('<p>%s</p>', __ ('Thank you, your payment has been made.'));
});

?>