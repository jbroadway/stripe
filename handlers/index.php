<?php

/**
 * This is the default admin area, which currently
 * just redirects to the settings form.
 */

$this->require_acl ('admin', 'stripe');

$page->title = __ ('Stripe Payments');
$page->layout = 'admin';

echo $tpl->render ('stripe/index', array ());

?>