; <?php /*

[Stripe]

; Your Stripe account's publishable key value.

publishable_key = ""

; Your Stripe account's secret key value.

secret_key = ""

; A custom charge handler for the stripe/charge button handler.

charge_handler = ""

; A custom handler for processing the stripe/webhook events.

webhook_handler = ""

; The default currency for payments (e.g., usd, cad, gbp, eur).

currency = usd

[Plans]

; Each plan is an array with a label, amount (in cents), and
; an interval value (monthly or yearly). The plan key name (not
; the label) must match a plan you have added to the Stripe
; admin dashboard.

basic[label] = Basic
basic[amount] = 1000
basic[interval] = monthly

pro[label] = Pro
pro[amount] = 2500
pro[interval] = monthly

[Admin]

handler = stripe/index
name = Stripe Payments
install = stripe/install
upgrade = stripe/upgrade
version = 0.5.0-beta

; */ ?>