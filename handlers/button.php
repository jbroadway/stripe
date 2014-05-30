<?php

/**
 * This generates a Stripe payment button to embed into your pages.
 */

$this->run ('stripe/init');

$data['publishable_key'] = $appconf['Stripe']['publishable_key'];
$data['button'] = isset ($data['button']) ? $data['button'] : 'Pay';
$data['address'] = isset ($data['address']) ? $data['address'] : 'No';

echo $tpl->render ('stripe/button', $data);

?>