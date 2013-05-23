<?php

/**
 * This is the settings form for the Stripe app.
 */

$this->require_acl ('admin', 'stripe');

require_once ('apps/admin/lib/Functions.php');

$page->layout = 'admin';
$page->title = __ ('Stripe Settings');
$page->add_script ('/js/json2.js');
$page->add_script ('/apps/stripe/js/handlebars-1.0.rc.1.js');
$page->add_script ('/apps/stripe/js/plans.js');

$form = new Form ('post', $this);

$form->data = array (
	'secret_key' => $appconf['Stripe']['secret_key'],
	'publishable_key' => $appconf['Stripe']['publishable_key'],
	'charge_handler' => $appconf['Stripe']['charge_handler'],
	'webhook_handler' => $appconf['Stripe']['webhook_handler'],
	'currency' => $appconf['Stripe']['currency'],
	'plans' => $appconf['Plans']
);

echo $form->handle (function ($form) {
	if (! Ini::write (
		array (
			'Stripe' => array (
				'secret_key' => $_POST['secret_key'],
				'publishable_key' => $_POST['publishable_key'],
				'charge_handler' => $_POST['charge_handler'],
				'webhook_handler' => $_POST['webhook_handler'],
				'currency' => strtolower ($_POST['currency'])
			),
			'Plans' => json_decode ($_POST['plans'])
		),
		'conf/app.stripe.' . ELEFANT_ENV . '.php'
	)) {
		printf ('<p>%s</p>', __ ('Unable to save changes. Check your folder permissions and try again.'));
		return;
	}

	$form->controller->add_notification (__ ('Settings saved.'));
	$form->controller->redirect ('/stripe');
});

?>