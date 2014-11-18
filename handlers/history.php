<?php

/**
 * Embed a user's billing history into a page.
 *
 * Usage:
 *
 *     printf ('<h2>%s</h2>', __ ('Billing history'))
 *     echo $this->run ('stripe/history');
 */

$this->require_login ();

$user = User::val ('id');

$history = stripe\Payment::query ()
	->where ('user_id', $user)
	->order ('ts', 'desc')
	->fetch_orig ();

echo $tpl->render (
	'stripe/history',
	array (
		'history' => $history
	)
);
