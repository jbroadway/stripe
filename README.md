# Stripe payments app for the Elefant CMS

This app provides everything you need to integrate [Stripe payments](https://stripe.com/)
into your Elefant site, including a simple payment button you can embed through
the page editor, and helpers for implementing subscriptions and payments into
custom applications.

Note that this app is not a shopping cart, but could easily be the basis for
building one.

#### To do:

* Additional helpers or examples:
  * Cancel subscription
  * Metered billing, one-time charges, and per-user pricing
  * Responding to failed payments

## Installation

First, you will need to register for [Stripe](https://stripe.com/). This takes
less than half an hour, including the banking setup.

Once you're signed up, the easiest way to install the app is:

1. Log into Elefant as a site admin
2. Go to Tools > Designer > Install App/Theme
3. Paste in the following link and click Install:

```
https://github.com/jbroadway/stripe/archive/master.zip
```

Alternately, you can run the following from the command line:

```bash
cd /path/to/your/site
./elefant install https://github.com/jbroadway/stripe/archive/master.zip
```

## Usage

### Configuring the app

In the Elefant admin area, go to Tools > Stripe Payments > Settings and enter
your publishable and secret keys that Stripe will provide for you. This is
where you also set your currency, subscription plans, and other settings.

### Embedding a payment button into a page

Log in and edit the page you wish to add the payment button to. In the WYSIWYG
editor, click on the [Dynamic Objects](http://www.elefantcms.com/wiki/Dynamic-Objects)
icon. In the list of Dynamic Objects, you will also find a new "Stripe: Button"
option.

### Using the Stripe API

You can access the Stripe API, pre-initialized with your Stripe credentials, by first
calling the `stripe/init` handler:

```
$this->run ('stripe/init');
```

You now have access to the full Stripe PHP library in your Elefant applications.

### Embedding a payment button in a template

From a view template, you can include a payment button via the following tag:

```
{! stripe/button
	?amount=10.00
	&description=Item+description
	&button=Pay
	&address=no
	&redirect=/thanks
!}
```

> Adding `%d` to the redirect value will insert the payment ID into the URL at that place.

### Creating a member payment or subscription form

To create a payment form for site members, run the `stripe/payment`
handler like this:

```php
<?php

echo $this->run ('stripe/payment', array (
	'amount' => 1000 // in cents
	'description' => 'eBook: Become a master prankster',
	'callback' => function ($charge, $payment) {
		info ($charge);
		info ($payment);
	}
));

?>
```

To create a subscription form, run the `stripe/payment` handler like this:

```php
<?php

echo $this->run ('stripe/payment', array (
	'plan' => 'basic',
	'callback' => function ($customer, $payment) {
		info ($customer);
		info ($payment);
	}
));

?>
```

The plan name should correspond to a plan you've created in your Stripe dashboard
and in your Stripe settings.

### Creating a custom charge handler

After creating the charge via the `stripe/button` handler, the app can call a
custom charge handler script that you set in the Stripe Payments settings form.
This script will receive a `$data['charge']` parameter which is the `Stripe_Charge`
object returned from calling `Stripe_Charge::create()`, which you can then use
to take action on new payments in your app, such as offering a digital file download.

The script will also receive a `$data['payment']` parameter which is the
`stripe\Payment` object for the transaction.

To ensure your charge handler is secure, here is a sample script outline:

```php
<?php

if (! $this->internal) return $this->error ();

info ($data['charge']);
info ($data['payment']);

?>
```

### Creating a custom webhooks handler

Stripe can be configured to send notifications back to your site whenever an event
occurs, such as refunding a payment, or creating a new customer account. You can
set the webhooks handler in the Stripe Payments settings form. This script will
receive a `$data['event']` parameter which is the `Stripe_Event` object returned
from calling `Stripe_Event::retrieve()`, which you can then use to take action
on the event that occurred, such as logging activity or disabling accounts after
too many failed billing attempts.

To ensure your webhooks handler is secure, here is a sample script outline:

```php
<?php

if (! $this->internal) return $this->error ();

info ($data['event']);

?>
```

Note that you will also have to set your webhooks handler in your Stripe dashboard.
Set it to the following URL (adjusted for your own domain):

    https://www.example.com/stripe/webhook
