<?php

/**
 * This loads and initializes the Stripe API.
 */

Stripe::setApiKey (Appconf::stripe ('Stripe', 'secret_key'));

?>