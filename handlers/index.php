<?php

/**
 * This is the default admin area, which currently
 * just redirects to the settings form.
 */

$this->redirect ('/stripe/settings');

$this->require_admin ();

$page->title = __ ('Stripe');
$page->layout = 'admin';

echo $tpl->render ('stripe/index', array ());

?>