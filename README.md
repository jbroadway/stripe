# Stripe payments integration for the Elefant CMS

This app provides everything you need to integrate [Stripe](https://stripe.com/)
payments into your site.

## Installation

First, you will need to register for Stripe. This takes less than half an hour,
including the banking setup.

Once you're signed up, the easiest way to install the app is:

1. Log into Elefant as a site admin
2. Go to Tools > Designer > Install App/Theme
3. Paste in the following link and click Install:

```
https://github.com/jbroadway/stripe.git
```

## Usage

In the Elefant admin area, go to Tools > Stripe > Settings and enter your
publishable and secret keys that Stripe will provide for you.

You will now find a new "Stripe: Button" option in the
[Dynamic Objects](http://www.elefantcms.com/wiki/Dynamic-Objects) menu.

You can also access the Stripe API, pre-initialized with your Stripe
credentials, via this line of code:

```
$this->run ('stripe/init');
```

You now have access to the full Stripe PHP library in your Elefant
applications.

From a view template, you can also include a payment button via the
following tag:

```
{! stripe/button
	?amount=10.00
	&description=Item+description
	&button=Pay
	&address=no
	&redirect=/thanks
!}
```

## Custom charge handler

After creating the charge, the Stripe charge handler can call a custom
charge handler script that you set in your app settings form. This script
will receive an array version of the Stripe_Charge object returned from
calling `Stripe_Charge::create()`, which you can then use to log payments
in your custom app.
