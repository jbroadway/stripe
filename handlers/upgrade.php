<?php

$this->require_acl ('admin', 'stripe');

$page->layout = 'admin';

if ($this->installed ('stripe', $appconf['Admin']['version']) === true) {
	$page->title = 'Already up-to-date';
	echo '<p><a href="/">Home</a></p>';
	return;
}

$page->title = 'Upgrading app: Stripe Payments';

echo '<p>Done.</p>';

$this->mark_installed ('stripe', $appconf['Admin']['version']);

?>