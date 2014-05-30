<?php

$this->require_acl ('admin', 'stripe');

$page->layout = 'admin';

if ($this->installed ('stripe', $appconf['Admin']['version']) === true) {
	$page->title = 'Already up-to-date';
	echo '<p><a href="/">Home</a></p>';
	return;
}

$page->title = 'Upgrading app: Stripe Payments';

if ($appconf['Admin']['version'] === '0.5.0-beta') {
	$updates = array (
		"alter table #prefix#payment add column email char(72) not null default ''",
		"alter table #prefix#payment add column billing_name char(72) not null default ''",
		"alter table #prefix#payment add column billing_address char(72) not null default ''",
		"alter table #prefix#payment add column billing_city char(72) not null default ''",
		"alter table #prefix#payment add column billing_state char(72) not null default ''",
		"alter table #prefix#payment add column billing_country char(72) not null default ''",
		"alter table #prefix#payment add column billing_zip char(72) not null default ''",
		"alter table #prefix#payment add column shipping_name char(72) not null default ''",
		"alter table #prefix#payment add column shipping_address char(72) not null default ''",
		"alter table #prefix#payment add column shipping_city char(72) not null default ''",
		"alter table #prefix#payment add column shipping_state char(72) not null default ''",
		"alter table #prefix#payment add column shipping_country char(72) not null default ''",
		"alter table #prefix#payment add column shipping_zip char(72) not null default ''"
	);

	DB::beginTransaction ();
	foreach ($updates as $update) {
		if (! DB::execute ($update)) {
			$error = DB::error ();
			DB::rollback ();
			printf ('<p class="visible-notice">%s: %s</p>', __ ('Error'), $error);
			printf ('<p>%s</p>', __ ('Upgrade failed.'));
			return;
		}
	}
	DB::commit ();
}

echo '<p>Done.</p>';

$this->mark_installed ('stripe', $appconf['Admin']['version']);

?>