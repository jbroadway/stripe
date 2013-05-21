; <?php /*

[secret_key]

not empty = 1

[publishable_key]

not empty = 1

[charge_handler]

skip_if_empty = 1
regex = "/^[a-zA-Z0-9_-]+(\/[a-zA-Z0-9_-]+)+$/"

[webhook_handler]

skip_if_empty = 1
regex = "/^[a-zA-Z0-9_-]+(\/[a-zA-Z0-9_-]+)+$/"

[currency]

not empty = 1
regex = "/^[a-zA-Z]+$/"

; */ ?>