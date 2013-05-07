(function($) {
	$.fn.showStatusTs = function(server, type){
		
		var element = $(this);

		function getActionStatus(server, type) {

			$.getJSON('/servers/actionStatus/read/' + type + '/' + server, {},
					function(tmp) {
						if(tmp !== null) {
							setActionStatus(tmp, server);
						}
					}
				);
		}

		function setActionStatus(status, server) {

			if (status.state != 'error')
			{
				if (status.state == 'update'  || status.state == 'install')
				{
					var progress = status.progress;

					$(element).progressbar({
									value: eval(progress)
								});

					$('#' + element.attr('id') + '_Text').html('Готово: ' + progress + '%');

					if (progress < 100)
					{
						setTimeout(function() {
													getActionStatus(server, status.state);
											}, 5000 );
					}
				}
				else
				{
					$(element).progressbar("disable");
					$('#' + element.attr('id') + '_Text').html('<small>Статус недоступен</small>');
				}

			}
			else
			{
				var errorText = '<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>Во время процедуры возникла ошибка.<br/>Код ошибки: #' +
								status.errorNum +
								'<br/>Описание ошибки: ' + status.error +
								'<br/>Время: ' + status.time;

				$('#serverActionStatus_' + server).removeClass('ui-state-highlight').addClass('ui-state-error').html(errorText);
			}
			
		
		}

		getActionStatus(server, type);

	};
})(jQuery);
