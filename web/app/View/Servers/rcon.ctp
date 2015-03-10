<?php
/*
 * Created on 05.08.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
 include('loading_params.php');
 //pr(@$rconResult);
?>
<cake:nocache>
	<style>
	.ui-autocomplete {
		max-height: 150px;
		max-width:  600px;
		overflow-y: auto;
	}
	/* IE 6 doesn't support max-height
	 * we use height instead, but this forces the menu to always be this tall
	 */
	* html .ui-autocomplete {
		height: 150px;
	}
	.cheatHihglight {
		color: red;
	}
	#ui-active-menuitem .cheatHihglight {
		color: white;
	}
	</style>
<pre>
<div id="rcon" style="height: 300px; overflow: auto;">
***********************************************************

 GHmanager RCON console
 Пишите команды в поле ниже. Вам автоматически будут
 предлагаться варианты. Если нужной вам команды нет в
 списке, сообщите нам, она будет добавлена в
 кратчайшие сроки.

***********************************************************</div>
</pre>
<?php


echo $this->Form->input('id', array('type'=>'hidden','value'=>$serverID,'id'=>'serverID'));
?>

<div class="controls">
	<div class="input-append input-prepend"><span class="add-on"><i class="icon-edit"></i></span><?php

echo $this->Form->input('command', array('div' => false,
									'label' => false,
									'title'=>'Введите команду',
									'id'=>'command',
									'class'=>'span4',
									'style'=>'font-weight:bold;'));
//echo $this->Js->submit('Отправить',
//							array(
//								'url'=> array(
//												'controller'=>'Servers',
//												'action'=>'rconResult'
//								 ),
//								'update' => '#rcon',
//								'class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
//								'before' =>$loadingShow,
//								'complete'=>$loadingHide,
//								'buffer' => false));






?><button
		id="sendRcon"
		class="btn btn-primary"
		role="button"
		aria-disabled="false">
		Отправить
	</button>

	</div>
</div>
</cake:nocache>

<script type="text/javascript">
		$(function() {

		// Функция отправки по нажатию Enter
		$('#command').keypress(function(e) {
			    if (e.keyCode == 13) {
			        Send();
			    }
			});



		function Send() {

			var serverID = $('#serverID').val();
			var command = $('#command').val();



			$.get("/servers/rconResult",
				{ 'id': serverID,
				  'command': command,
				  'isHltv': '<?php echo $isHltv; ?>'
				},
				function(data) {
				  $('#rcon').append(data);
				  var scrollDiv = document.getElementById("rcon");
				  var scroll = scrollDiv.scrollHeight;
				  $('#rcon').scrollTop(scroll);
				}


				);


			};

		$("#sendRcon").click(function() {
						Send();
					 });

		$("#sendRcon").ajaxStart(function() {
					   $('#loading').show();
					 });
		$("#sendRcon").ajaxStop(function() {
					   $('#loading').hide();
					 });


		$( "#command" ).autocomplete({
			minLength: 1,
			source: "/servers/rconAutoComplete/<?php echo @$serverType;?>",
			focus: function( event, ui ) {
				$( "#command" ).val( ui.item.command );
				return false;
			},
			select: function( event, ui ) {
				$( "#command" ).val( ui.item.command );

				return false;
			}
		})
		.data( "autocomplete" )._renderItem = function( ul, item ) {
			if (item.cheat == "1") {
				cheat = "<span class='cheatHihglight'>&nbsp;(cheat)&nbsp;</span>"
			} else {
				cheat = ""
			}
			return $( "<li></li>" )
				.data( "item.autocomplete", item )
				.append( "<a><strong><small>" + item.command + cheat + "</small></strong><br><small>" + item.desc + "</small></a>" )
				.appendTo( ul );
		};
	});
	</script>
