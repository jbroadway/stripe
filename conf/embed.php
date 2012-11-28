; <?php

[stripe/button]

label = "Stripe: Button"
icon = shopping-cart

description[label] = Description
description[type] = text
description[not empty] = 1
description[message] = Please enter a description for the charge.

amount[label] = Amount
amount[type] = text
amount[not empty] = 1
amount[type] = numeric
amount[regex] = "/^[0-9]+\.[0-9]{2}$/"
amount[message] = "Please enter the full amount to be charged (e.g., 10.00)."

redirect[label] = "Redirect to link (optional)"
redirect[type] = text

address[label] = "Collect mailing address?"
address[type] = select
address[callback] = "stripe\Util::yes_no"

button[label] = Button label
button[type] = select
button[callback] = "stripe\Util::get_labels"

; */ ?>