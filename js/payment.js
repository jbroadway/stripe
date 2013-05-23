$(function () {
	var stripe_response_handler = function (status, response) {
		var $form = $('.payment-form');
		
		if (response.error) {
			$form.find ('.payment-errors').text (response.error.message).show ();
			$form.find ('button').prop ('disabled', false);
		} else {
			var token = response.id;
			$form.append ($('<input type="hidden" name="stripeToken" />').val (token));
			$form.get (0).submit ();
		}
	};
	
	$('.payment-form').submit (function (event) {
		var $form = $(this);

		if ($form.get (0).elements.existing.value == 'yes') {
			$form.get (0).submit ();
			return false;
		}
		
		$form.find ('button').prop ('disabled', true);
		
		Stripe.createToken ($form, stripe_response_handler);
		
		return false;
	});
});