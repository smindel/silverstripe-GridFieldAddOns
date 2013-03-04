(function($) {

	$.entwine('ss', function($) {

		$('.expandable-forms .ss-gridfield-item td:not(.col-buttons)').entwine({
			onclick: function() {

				var row = $(this).closest('tr');
				var iseditmode = row.hasClass('editmode');
				
				$('.editmode', $(this).getGridField()).collapse();
				
				if(!iseditmode) row.populate();
				
				return false;
			}
		});

		$('.expandable-forms .ss-gridfield-item').entwine({
			expand: function() {

				var id = $(this).attr('data-id');
				$('.EditFormContainer[data-id=' + id + '] .EditFormDiv', $(this).getGridField())
					.css({
						display: 'none',
						height: 'auto',
						overflow: 'visible'
					})
					.slideDown('fast');
			},
			collapse: function(callback) {

				var row = $(this).closest('tr');
				var id = row.attr('data-id');

				$('.EditFormContainer[data-id=' + id + '] .EditFormDiv', $(this).getGridField()).slideUp('fast', function(){
					$('.EditFormContainer[data-id=' + id + ']', $(this).getGridField()).remove();
					row.removeClass('editmode');
					if(callback) callback();
				});
			},
			populate: function() {

				var self = this;
				var id = $(this).attr('data-id');
				var cols = $(this).children().length;
		
				$(this).addClass('editmode');

				var url = $(this).getGridField().attr('data-pseudo-form-url') + '/' + id;

				var formrow = $('<tr class="EditFormContainer" data-id="' + id + '"><td colspan="' + cols + '"><div class="EditFormDiv"></div></td></tr>');
		
				$(this).after(formrow);

				$(this).closest('form').addClass('loading');
				$('.EditFormDiv', formrow).load(url, function(){
					$(this).closest('form').removeClass('loading');

					// replace fieldnames to avoid collision with fields of the main form with the same name
					var prefix = $(this).getGridField().attr('data-name') + '_GFEF_Detail_';
					$('input, select, textarea', $(this)).each(function(){
						$(this).attr('name', prefix + $(this).attr('name'));
					});

					self.expand();
				});
			}
		});

		$('.cms-edit-form .ss-gridfield.expandable-forms .EditFormContainer .Actions input.action[type=submit], .cms-edit-form .ss-gridfield .EditFormContainer .Actions button.action').entwine({
		
			onclick: function() {

				var gridfield = $(this).getGridField();
					
				var container = $(this).closest('.EditFormContainer');
						
				var id = container.attr('data-id');

				// var url = $(this).getGridField().attr('data-pseudo-form-action') + '/' + id + '/ExpandableForm';
				var url = $('*[data-pseudo-form-action]', $(this).getGridField()).attr('data-pseudo-form-action');

				var rawdata = $('input, select, textarea', container).serializeArray();

				// restore replaced fieldnames to send data with correct fieldnames
				var data = [];
				var prefix = $(this).getGridField().attr('data-name') + '_GFEF_Detail_';
				$('input[name], select[name], textarea[name]', container).each(function(){
					if($(this).attr('name').substr(0, prefix.length) == prefix) {
						data.push({
							name: $(this).attr('name').substr(prefix.length),
							value: $(this).val()
						});
					}
				});

				data.push({ name: this.name, value: 1 });

				$(this).closest('form').addClass('loading');
				$('.EditFormDiv', container).load(url, data, function(data, textStatus){
					if($('.message.validation', $(this)).length == 0) {
						$('.ss-gridfield-item[data-id=' + id + ']', gridfield).collapse(function(){
							gridfield.reload();
						});
					} else {
						$(this).closest('form').removeClass('loading');
					}
				});

				return false;
			}
		});

	});

}(jQuery));