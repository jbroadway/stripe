<?php

/**
 * This loads and initializes the Stripe API.
 */

Stripe::setApiKey ($appconf['Stripe']['secret_key']);

?>