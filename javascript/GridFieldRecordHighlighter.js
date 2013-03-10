(function($) {

	$.entwine('ss', function($) {

		$('.ss-gridfield-alert[data-record-alert-message]').entwine({
			onmatch: function() {
				$(this).tooltip();
			}
		});
	});

}(jQuery));