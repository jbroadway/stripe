/**
 * Client-side helper for adding/removing plans.
 */
var plans = (function ($) {
	var self = {};

	self.plans = {};

	/**
	 * Initialize from a list of existing plans.
	 */	
	self.init = function (plans) {

		self.form = Handlebars.compile ($('#plan-form-template').html ());
		self.tpl = Handlebars.compile ($('#plan-template').html ());

		for (var i in plans) {
			var plan = plans[i];
				plan.plan = i;
			
			self.plans[i] = plan;

			$('#plan-list').append (self.tpl ({
				plan: plan.plan,
				label: plan.label,
				amount: (plan.amount / 100).toFixed (2),
				interval: plan.interval
			}));
		}

		$('#add-plan').click (self.add_plan_dialog);
		$('#plan-list').on ('click', '.remove-plan', self.remove_plan);
		$('#plan-list').on ('click', '.update-plan', self.update_plan_dialog);
	};

	/**
	 * Validate plan object and data.
	 */
	self.validate_plan = function (plan) {
		var invalid = [];
		if (! plan.plan || ! plan.plan.match (/^[a-zA-Z0-9 _-]+$/)) {
			invalid.push ('plan');
		}

		if (! plan.label || ! plan.label.length === 0) {
			invalid.push ('label');
		}
		
		if (! plan.amount || ! plan.amount.match (/^[0-9]+$/)) {
			invalid.push ('amount');
		}

		if (invalid.length > 0) {
			return invalid;
		}
		return false;
	};

	/**
	 * Initialize the Add Plan dialog.
	 */
	self.add_plan_dialog = function (event) {
		event.preventDefault ();

		$.open_dialog (
			$.i18n ('Add plan'),
			self.form ({
				plan: '',
				label: '',
				amount: '',
				interval: 'monthly',
				submit_button: $.i18n ('Add plan')
			})
		);

		$('#plan-submit').click (self.add_plan);
	};
	
	/**
	 * Add a plan when the dialog is submitted.
	 */
	self.add_plan = function (event) {
		event.preventDefault ();

		var plan = {
				plan: $('#plan-form-id').val (),
				label: $('#plan-form-label').val (),
				amount: $('#plan-form-amount').val (),
				interval: $('#plan-form-interval').val ()
			},
			invalid = self.validate_plan (plan);

		if (invalid) {
			$('.notice').hide ();
			for (var i = 0; i < invalid.length; i++) {
				$('#plan-notice-' + invalid[i]).show ();
			}
			return;
		}

		self.plans[plan.plan] = plan;
		self.sync ();

		$('#plan-list').append (self.tpl ({
			plan: plan.plan,
			label: plan.label,
			amount: (plan.amount / 100).toFixed (2),
			interval: plan.interval
		}));

		$.close_dialog ();
	};

	/**
	 * Initialize the Update Plan dialog.
	 */
	self.update_plan_dialog = function (event) {
		event.preventDefault ();

		var id = $(this).data ('id'),
			plan = self.plans[id];

		$.open_dialog (
			$.i18n ('Update plan'),
			self.form ({
				plan: plan.plan,
				label: plan.label,
				amount: plan.amount,
				interval: plan.interval,
				submit_button: $.i18n ('Update plan')
			})
		);

		$('#plan-submit').click (self.update_plan);
	};

	/**
	 * Update a plan when the dialog is submitted.
	 */
	self.update_plan = function (event) {
		event.preventDefault ();

		var orig = $('#plan-form-original').val (),
			plan = {
				plan: $('#plan-form-id').val (),
				label: $('#plan-form-label').val (),
				amount: $('#plan-form-amount').val (),
				interval: $('#plan-form-interval').val ()
			},
			invalid = self.validate_plan (plan);

		if (invalid) {
			$('.notice').hide ();
			for (var i = 0; i < invalid.length; i++) {
				$('#plan-notice-' + invalid[i]).show ();
			}
			return;
		}

		delete self.plans[orig];
		self.plans[plan.plan] = plan;
		self.sync ();

		$('#plan-' + orig).replaceWith (self.tpl ({
			plan: plan.plan,
			label: plan.label,
			amount: (plan.amount / 100).toFixed (2),
			interval: plan.interval
		}));

		$.close_dialog ();
	};

	/**
	 * Remove a plan from the list.
	 */
	self.remove_plan = function (event) {
		event.preventDefault ();

		if (! confirm ($.i18n ('Are you sure you want to delete this plan?'))) {
			return;
		}

		var id = $(this).data ('id');

		delete self.plans[id];
		self.sync ();

		$('#plan-' + id).remove ();
	};

	/**
	 * Sync the data structure with the hidden field.
	 */
	self.sync = function () {
		$('#plans').val (JSON.stringify (self.plans));
	};

	return self;
})(jQuery);