<?php

/**
 * This loads and initializes the Stripe API.
 */

return new \Stripe\StripeClient (\Envconf::stripe ('Stripe', 'secret_key'));

?>