<?php

/**
 * Embed a user's billing history into a page.
 *
 * Usage:
 *
 *     echo $this->run ('stripe/history');
 */

$this->require_login ();

$history = stripe\Payment::query ()
	->where ('user_id', User::val ('id'))
	->order ('ts', 'desc')
	->fetch_orig ();

echo $tpl->render (
	'stripe/history',
	array (
		'history' => $history
	)
);
