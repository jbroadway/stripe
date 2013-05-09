<?php

$this->require_acl ('admin', 'stripe');

$page->layout = 'admin';

$cur = $this->installed ('stripe', $appconf['Admin']['version']);

if ($cur === true) {
	$page->title = 'Already installed';
	echo '<p><a href="/stripe/index">Continue</a></p>';
	return;
} elseif ($cur !== false) {
	header ('Location: /' . $appconf['Admin']['upgrade']);
	exit;
}

$page->title = 'Installing App: Stripe Payments';

$conn = conf ('Database', 'master');
$driver = $conn['driver'];
DB::beginTransaction ();

$error = false;
$sqldata = sql_split (file_get_contents ('apps/stripe/conf/install_' . $driver . '.sql'));
foreach ($sqldata as $sql) {
	if (! DB::execute ($sql)) {
		$error = DB::error ();
		break;
	}
}

if ($error) {
	DB::rollback ();
	echo '<p class="visible-notice">Error: ' . $error . '</p>';
	echo '<p>Install failed.</p>';
	return;
}
DB::commit ();

echo '<p><a href="/stripe/index">Done.</a></p>';

$this->mark_installed ('stripe', $appconf['Admin']['version']);

?>