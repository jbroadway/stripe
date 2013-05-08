<?php

/**
 * This is the settings form for the Stripe app.
 */

$this->require_acl ('admin', 'stripe');

require_once ('apps/admin/lib/Functions.php');

$page->layout = 'admin';
$page->title = __ ('Stripe Settings');

$form = new Form ('post', $this);

$form->data = array (
	'secret_key' => $appconf['Stripe']['secret_key'],
	'publishable_key' => $appconf['Stripe']['publishable_key'],
	'charge_handler' => $appconf['Stripe']['charge_handler']
);

echo $form->handle (function ($form) {
	if (! Ini::write (
		array (
			'Stripe' => array (
				'secret_key' => $_POST['secret_key'],
				'publishable_key' => $_POST['publishable_key'],
				'charge_handler' => $_POST['charge_handler']
			)
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