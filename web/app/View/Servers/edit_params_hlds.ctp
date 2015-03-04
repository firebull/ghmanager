<?php
/*
 * Created on 16.08.2010
 *
 * File created for project TeamServer
 * by nikita
 */
 include('loading_params.php');
 $id = $this->data['Server']['id'];
?>
<div id="server_params">
	<div id="flash"><?php echo $this->Session->flash(); ?></div>
<table border="0" cellpadding="1" cellspacing="0">
	<tr>
		<td align="center">
		<div id="action_positive"  style="height: 90px;">
		<h3>Установить карту по умолчанию:</h3>
		<?php /* Смена карты (начало) */?>
		<?php echo $this->Form->create('Server', array('action' => 'setMap')); ?>
		<table border="0" cellpadding="0" cellspacing="0">
						<tr>

							<td align="center">
							<?php
							if (@$currentMap) {
									$disabled = "enabled"; // :-))
								} else {
									$disabled = "disabled";
									$currentMap = "Не могу прочесть текущий конфиг";
								}
							echo $this->Form->input('map', array(
																'type'=>'text',
																'size' => '30',
																'div' => false,
																'label' => false,

																'title'=>'Текущая карта'));
							echo $this->Form->input('id', array('type'=>'hidden'));
							echo $this->Form->input('action', array('type'=>'hidden', 'value'=>'set'));

							?>

							</td>
							<td align="left" style="padding-left: 20px;">
							<?php


									echo $this->Js->submit('Сменить',
										array(
											'url'=> array(
															'controller'=>'Servers',
															'action'=>'setMap'
											 ),
											'update' => '#server_params',
											'class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
											'id'=>'submit',
											$disabled=>$disabled,
											'before' =>$loadingShow,
											'complete'=>$loadingHide,
											'buffer' => false));


							?>
							</td>
						</tr>
		</table>

		<?php echo $this->Form->end();?>
		</div>
		<?php /* Смена карты (конец) */?>
		</td>
		<td align="center">
		<?php /* Переинициализация сервера (начало) */?>
		<div id="action_negative" style="height: 90px;">
		<h3>Сброс настроек:</h3>

		<?php
		// Подтверждение переинициализации сервера
		$confirmMessage = 'Вы уверены, что хотите полностью переинициализировать сервер?'.
						  "\n<br/>Это необратимая операция!" .
						  "\n<br/>Будут уничтожены ВСЕ настройки сервера!!!" ;
		echo $this->Html->link('Переинициализировать сервер', '#',
						array ('id'=>'reinit_srcds_'.$id,
							   'escape' => false,
							   'onClick' => 'Confirm();',
							   'class'=>'button'));
		?>

		<div id="reinit_confirm" title="Подвердите сброс настроек сервера #<?php echo $id; ?>" style="display: none;">
							<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
								<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
								<?php echo $confirmMessage; ?>
							</div>
		</div>

		<script type="text/javascript">


			function Confirm() {

				$('#reinit_confirm').dialog({
										resizable: false,
										height:220,
										width: 350,
										modal: true,
										buttons: {

												'Подтверждаю': function() {
												window.location.href='<?php echo "servers/reInit/$id";?>';

												$(this).dialog('close');
											},
											'Нет!': function() {
												$(this).dialog('close');
											}
										}
									});

				}



			$(function() {
				$(".button, input:submit").button();
			});
		</script>

		</div>
		<?php /* Переинициализация сервера (начало) */?>
		</td>
	</tr>
</table>

<hr style="color: grey;"/>
<h3> Редактировать конфигурационные файлы </h3>
	<script type="text/javascript">
	$(function() {

		var loading = '<?php echo $this->Html->image('loading.gif', array('alt'=>'Loading...', 'width'=>'16', 'height'=>'16')); ?> Подождите...'

		$("#tabs").tabs({spinner: loading, selected: '-1',
			ajaxOptions: {
				error: function(xhr, status, index, anchor) {
					$(anchor.hash).html("Ошибка загрузки файла. Попробуйте чуть позже.");
				}
			}
		});
	});
<?php if ( count($configs) > 4) {
	// Если больше 4-х конфигов, то расположим их в столбик слева
	?>
	$(function() {
		$("#tabs").tabs().addClass('ui-tabs-vertical ui-helper-clearfix');
		$("#tabs li").removeClass('ui-corner-top').addClass('ui-corner-left');
	});
<?php }?>

	</script>

	<div class="config_files" style="margin-bottom: 20px;">

		<div id="tabs">
			<ul>
				<li><a href="#tabs-1">Помощь</a></li>
				<?php

				foreach ( $configs as $config ) {
				?>

				<li>

				<?php
	       		echo $this->Html->link("<span>".$configDir."/".$config['name']."</span>",
	       						array('action'=>'editConfigCommon',
	       							  $this->data['Server']['id'],
	       							  $config['id'],'read'),
	       						array('escape' => false)

	       						);
	       		?>
	       		</li>
	       		<?php
				}


				?>

			</ul>
			<div id="tabs-1">
				Щелкните по конфигу, который хотели бы просмотреть.
				Если хотите отредактировать его, щелкните по его содержимому.
				После редактирования, нажмите сохранить.
				Внимательно читайте комментарии  - в них содержится много важной информации.
			</div>
		</div>
	</div>
</div>
