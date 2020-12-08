<?php

/**
 * This generates a Stripe payment button to embed into your pages.
 */

$data['publishable_key'] = Envconf::stripe ('Stripe', 'publishable_key');
$data['button'] = isset ($data['button']) ? $data['button'] : 'Pay';
$data['address'] = isset ($data['address']) ? $data['address'] : 'No';

echo $tpl->render ('stripe/button', $data);

?>