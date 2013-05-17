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

		$('#add-plan').click (self.add_plan);
		$('#plan-list').on ('click', '.remove-plan', self.remove_plan);
		$('#plan-list').on ('blur', 'input[type="text"]', self.update);
	};

	self.add_plan = function (event) {
		event.preventDefault ();
	};

	self.remove_plan = function (event) {
		event.preventDefault ();
	};

	self.update = function (event) {
		event.preventDefault ();
	};

	self.sync = function () {
		$('#plans').val (JSON.stringify (self.plans));
	};

	return self;
})(jQuery);