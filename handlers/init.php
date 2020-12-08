<?php

/**
 * This loads and initializes the Stripe API.
 */

return new \Stripe\StripeClient (Appconf::stripe ('Stripe', 'secret_key'));

?>