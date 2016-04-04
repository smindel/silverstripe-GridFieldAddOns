(function($) {

	$.entwine('ss', function($) {

		$('.ss-gridfield .ss-gridfield-item td[data-gridfield-editable-cell-column]').entwine({
			onclick: function() {
				return false;
			}
		});

		$('.ss-gridfield .ss-gridfield-item td[data-gridfield-editable-cell-column] select').entwine({
			onmatch: function() {
				this.css({'width': $(this).width()}).addClass('has-chzn').chosen({
					allow_single_deselect: true,
					disable_search_threshold: 20
				});
			}
		});

		$('.ss-gridfield .ss-gridfield-item td[data-gridfield-editable-cell-column] input, .ss-gridfield .ss-gridfield-item td[data-gridfield-editable-cell-column] select, .ss-gridfield .ss-gridfield-item td[data-gridfield-editable-cell-column] textarea').entwine({
			onclick: function() {
				if($(this).attr('type') == 'checkbox') {
					$(this).submit();
				}
			},
			onchange: function() {
				$(this).submit();
			},
			submit: function() {

				var me = $(this);
				var id = me.closest('tr').attr('data-id');
				var action = me.closest('.ss-gridfield').attr('data-url').split('?');
				var url = action[0] + "/savecomponent/" + id + (action[1] ? '?' + action[1] : '');
				var field = me.attr('name');
				var val = me.attr('type') == 'checkbox' ? (me.attr('checked') ? 1 : 0) : $(this).val();
				var data = field + '=' + val;

				$.post(url, data, function(data, textStatus) {

					statusMessage(data.message, data.type);

					if(data.type == 'bad') {
						me.closest('td').attr('data-gridfield-editable-cell-dirty', 1);
					} else {
						me.getGridField().reload();
					}

				}, 'json');
			}
		});

	});

}(jQuery));