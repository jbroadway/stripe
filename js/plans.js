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
		self.plans = plans;

		self.form = Handlebars.compile ($('#plan-form-template').html ());
		self.tpl = Handlebars.compile ($('#plan-template').html ());

		$('#add-plan').click (self.add_plan_dialog);
		$('#plan-list').on ('click', '.remove-plan', self.remove_plan);
		$('#plan-list').on ('blur', 'input[type="text"]', self.update);
	};

	self.add_plan_dialog = function (event) {
		event.preventDefault ();
		$.open_dialog (
			$.i18n ('Add plan'),
			self.form ({
				plan: '',
				label: '',
				amount: '',
				interval: 'monthly'
			})
		);
		$('#plan-submit').click (self.add_plan);
	};
	
	self.add_plan = function (event) {
		event.preventDefault ();
		/*$('#plan-list').append (self.tpl ({
			plan: 'basic',
			label: 'Basic',
			amount: (1500/100).toFixed (2),
			interval: 'monthly'
		}));*/
		$.close_dialog ();
	};

	self.remove_plan = function (event) {
		event.preventDefault ();
		$('#plan-' + $(this).data ('id')).remove ();
	};

	self.update = function (event) {
		event.preventDefault ();
	};

	self.sync = function () {
		$('#plans').val (JSON.stringify (self.plans));
	};

	return self;
})(jQuery);