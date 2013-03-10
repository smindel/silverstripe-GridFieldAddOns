(function($) {

	$.entwine('ss', function($) {

		$('.ss-gridfield .action.gridfield-setup').entwine({
			onclick: function() {
				var me = $(this);
				var formurl = $(this).getGridField().attr('data-url') + '/usercolumnsform';
				var updateurl = $(this).getGridField().attr('data-url') + '/saveusercolumns';
				var dialogelement = $('<div id="GridFieldUserColumnsDialog" data-url="' + updateurl + '"></div>').appendTo('body');
				dialogelement.dialog({
					width:100,
					height:400,
					close: function() {
						dialogelement.remove();
						me.getGridField().reload();
					}
				});
				dialogelement.load(formurl, function(){
					$('ul', dialogelement).sortable( { containment: 'parent', stop: function(){ $('#GridFieldUserColumnsDialog').save(); } } );
				});
				return false;
			}
		});

		$('#GridFieldUserColumnsDialog :checkbox').entwine({
			onchange: function() {
				$('#GridFieldUserColumnsDialog').save();
			}
		});

		$('#GridFieldUserColumnsDialog').entwine({
			save: function() {
				url = $(this).attr('data-url');
				data = $(':checkbox', $(this)).serialize();
				$.post(url, data);
			}
		});
	});

}(jQuery));