// Add listerner "prise_rendez_vous--save";
(function ($, Drupal) {
	function validCheckoutPane(context) {
		once(
			"prise_rendez_vous_valid_checkout_pane",
			".priserendezvous_checkf",
			context
		).forEach((item) => {
			item.disabled = false;
			item.click();
		});
	}
	/**
	 * --
	 */
	Drupal.behaviors.prise_rendez_vous = {
		attach: function (context, settings) {
			document.addEventListener(
				"prise_rendez_vous--save",
				() => {
					validCheckoutPane(context);
				},
				false
			);
		},
	};
})(jQuery, Drupal);