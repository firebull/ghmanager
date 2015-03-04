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
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td>
		<div id="action_positive"  style="height: 75px;">

		<?php /* Смена карты (начало) */?>
		<?php echo $this->Form->create('Server', array('action' => 'setMap')); ?>
		<table border="0" cellpadding="0" cellspacing="0" align="center">
			<tr>
				<td colspan="2"><h3>Карта по умолчанию:</h3></td>
			</tr>
			<tr>

				<td>
					<?php

						if (@$currentMap) {
							$disabled = "enabled"; // :-))
						} else {
							$disabled = "disabled";
							$currentMap = "Не могу прочесть текущий конфиг";
						}

						echo $this->Form->input('id', array('type'=>'hidden'));
						echo $this->Form->input('action', array('type'=>'hidden', 'value'=>'set'));
					?>
						<div class="control-group">
						    <div class="controls">
						      <div class="input-append input-prepend">
						        <span class="add-on"><i class="icon-picture"></i></span><?php

								echo $this->Form->input('map', array(
																	'options' => $this->data['Server']['maps'],
																	'selected' => $this->data['Server']['map'],
																	'div' => false,
																	'label' => false,

																	'title'=>'Текущая карта'));

								echo $this->Js->submit('Сменить',
									array(
										'url'=> array(
														'controller'=>'Servers',
														'action'=>'setMap'
										 ),
										'update' => '#server_start_params_container',
										'class' => 'btn',
										'div' => false,
										'label' => false,
										'id'=>'submitMap',
										$disabled=>$disabled,
										'before' =>$loadingShow,
										'complete'=>$loadingHide,
										'buffer' => false));


						?>
						<label for="mapList" class="text-center">
						<?php
							// TODO: Пока прикрою загрузку карт, потом разберусь
							if ( $this->data['Type'][0]['name'] == 'cod') {
							  	?>

								<small>Загрузка карт из панели недоступна.</small>

								<?php
								} else {


								$uploaderScript = "http://".$this->data['Server']['address']."/uploadMap/?" .
												  "id=".$this->data['Server']['id']."&" .
												  "token=".$this->data['User'][0]['tokenhash'];
								echo $this->Html->link('Загрузить свою карту на сервер', '#',
												array (
													   'onClick' => "window.open('".$uploaderScript."', 'newUploaderWin#".$this->data['Server']['id']."', 'Toolbar=yes, Location=no, Directories=no, Status=yes, Menubar=yes, Scrollbars=no, Resizable=yes, Width=550, Height=500')")
												);


						 		} ?>
						</label>
						</div>
				    </div>
				</div>
				</td>
			</tr>
		</table>

		<?php echo $this->Form->end();?>
		</div>
		<?php /* Смена карты (конец) */?>
		</td>
		<td colspan="2" align="center">
			<?php
				if ($this->data['GameTemplate'][0]['name'] != 'cod4fixed') {

				 echo $this->element('set_slots', array(
														'id'=>$this->data['Server']['id'],
														'slots'=>$this->data['Server']['slots'],
														'slots_min'=>$this->data['GameTemplate'][0]['slots_min'],
														'slots_max' => $this->data['GameTemplate'][0]['slots_max'],
														'payedTill' => $this->data['Server']['payedTill']
														 ));

				} else {
			?>
			<div id="action_blocked"  style="height: 75px;">
				<h3 style="margin-bottom: 0px;">Изменить количество слотов:</h3>
				Невозможно для этого типа серверов
			</div>
			<?php
				}

			?>
		</td>

	</tr>
		<tr>
		<td>
			<div id="action_positive" style="height: 75px;">


				<?php
					if (!empty($mods) and count($mods) > 1) {
						echo $this->Form->create('Server', array('action'=>'codSetMod'));
						echo $this->Form->input('id', array('type'=>'hidden'));
				?>
						<table border="0" cellpadding="0" cellspacing="0" align="center">
						<tr>
							<td colspan="2"><h3 style="margin-bottom: 0px;">Мод по умолчанию:</h3></td>
						</tr>
						<tr>
							<td>
								<div class="control-group">
								    <div class="controls">
								      <div class="input-append input-prepend">
								        <span class="add-on"><i class="icon-flag"></i></span><?php

										echo $this->Form->input('mod',array ( 'options'=>$mods,
																	    'selected'=>$server['ServerModPlugin']['mod'],
																		'id'=>'codMods',
																		'div' => false,
																		'label' => false));


										echo $this->Js->submit('Сменить',
											array(
												'url'=> array(
																'controller'=>'Servers',
																'action'=>'codSetMod'
												 ),
												'update' => '#server_start_params_container',
												'class' => 'btn',
												'id'=>'submitMod',
												'div' => false,
												'label' => false,
												$disabled=>$disabled,
												'before' =>$loadingShow,
												'complete'=>$loadingHide,
												'buffer' => false));

											?>
										</div>
								    </div>
								</div>
							</td>
						</tr>
						</table>
				<?php
						echo $this->Form->end();
					} elseif (!empty($mods) and count($mods) == 1) {
				?>
				<h3>Мод сервера: <?php echo current($mods);?></h3>
				<small>
					После установки дополнительных модов, вы сможете выбирать
					здесь мод, который желаете запускать с сервером.
				</small>
				<?php
					}
				?>
			</div>
		</td>

		<td align="center">
			<?php if ($server['ServerModPlugin']['punkbuster'] == 1) {?>
					<div id="action_positive"  style="height: 75px;">
					<h3>Punkbuster:</h3>
			<?php
				  	echo $this->Form->create('Server', array('action'=>'switchPunkbuster'));
				  	echo $this->Form->input('id', array('type'=>'hidden'));
				  	echo $this->Js->submit('Отключить',
							array(
								'url'=> array(
												'controller'=>'Servers',
												'action'=>'switchParam',
												'punkbuster',
												'off'
								 ),
								'update' => '#server_start_params_container',
								'class' => 'btn',
								'id'=>'switchPunkbuster',
								'before' =>$loadingShow,
								'complete'=>$loadingHide,
								'buffer' => false));
				  	echo $this->Form->end();

				  } else {
			?>
					<div id="action_neutral"  style="height: 75px;">
					<h3>Punkbuster:</h3>
			<?php
				  	echo $this->Form->create('Server', array('action'=>'switchPunkbuster'));
				  	echo $this->Form->input('id', array('type'=>'hidden'));
				  	echo $this->Js->submit('Включить',
							array(
								'url'=> array(
												'controller'=>'Servers',
												'action'=>'switchParam',
												'punkbuster',
												'on'
								 ),
								'update' => '#server_start_params_container',
								'class' => 'btn',
								'id'=>'switchPunkbuster',
								'before' =>$loadingShow,
								'complete'=>$loadingHide,
								'buffer' => false));
					echo $this->Form->end();
				  }
			?>
					</div>
		</td>
		<td>
		<?php /* Переинициализация сервера (начало) */?>
		<div id="action_negative" style="height: 75px;">
		<h3>Настройки:</h3>

		<?php
		// Подтверждение переинициализации сервера
		$confirmMessage = 'Вы уверены, что хотите полностью переинициализировать сервер?'.
						  "\n<br/>Это необратимая операция!" .
						  "\n<br/>Будут уничтожены ВСЕ настройки сервера!!!" ;
		echo $this->Html->link('Сбросить', '#',
						array ('id'=>'reinit_srcds_'.$id,
							   'escape' => false,
							   'onClick' => 'Confirm();',
							   'class'=>'btn btn-danger'));
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
												window.location.href='<?php echo "/servers/reInit/$id";?>';

												$(this).dialog('close');
											},
											'Нет!': function() {
												$(this).dialog('close');
											}
										}
									});

				}
		</script>

		</div>
		<?php /* Переинициализация сервера (конец) */?>
		</td>
	</tr>
	<tr>
		<td>
		<?php /* Установка пароля сервера (начало) */?>
		<div id="action_positive" style="height: 75px;">
			<?php
			// Установить заголовок по наличию пароля
			if ($serverPassword === false) {
				$passHeader = 'Установить';
			} else {
				$passHeader = 'Изменить';
			}

			echo $this->Form->create('Server', array('action' => 'setConfigParam'));
			?>

			<table border="0" cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td colspan="2"><h3><?php echo $passHeader; ?> пароль сервера:</h3></td>
				</tr>
				<tr>
					<td align="right">
						<?php
						echo $this->Form->input('id', array('type' => 'hidden'));
						echo $this->Form->input('paramName', array('type'  => 'hidden',
															 'value' => 'set g_password'));
						?>

						<div class="control-group">
						    <div class="controls">
						        <div class="input-append input-prepend">
						        <span class="add-on"><i class="icon-lock"></i></span><?php
									echo $this->Form->input('paramValue',array (
																	'id'    => 'paramPassword',
																	'value' => $serverPassword,
																	'div'   => false,
																	'label' => false,
																	'size'  => 24,
																	'style' => 'font-weight: bold; color: #444; text-align: center;'));

									echo $this->Js->submit($passHeader,
										array(
											'url'=> array(
															'controller'=>'Servers',
															'action'=>'setConfigParam',
															$id
											 ),
											'update' => '#server_start_params_container',
											'class' => 'btn',
											'div'   => false,
											'label' => false,
											'before' =>$loadingShow,
											'complete'=>$loadingHide,
											'buffer' => false));
									?>
								</div>
						    </div>
						</div>
					</td>
				</tr>
			</table>
		<?php echo $this->Form->end(); ?>
		</div>
		<?php /* Установка пароля сервера (конец) */?>
		</td>

		<td colspan="2">
		<div id="action_positive"  style="height: 75px;">
			<?php
				// Если пароля нет - пишем Установить...
				// Если есть - пишем Изменить...

				if (empty($this->data['Server']['rconPassword'])) {
					$word = 'Установить';
				} else {
					$word = 'Изменить';
				}
				echo $this->Form->create('Server', array('action'=>'setRconPassword'));
				echo $this->Form->input('id', array('type'=>'hidden'));
			?>
			<table border="0" cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td colspan="2"><h3><?php echo $word; ?> пароль RCON:</h3></td>
				</tr>
				<tr>
					<td>
						<div class="control-group">
						    <div class="controls">
						        <div class="input-append input-prepend">
						        <span class="add-on"><i class="icon-lock"></i></span><?php


								echo $this->Form->input('rconPassword',array (
																		'id'=>'setRconPass',
																		'div' => false,
																		'label' => false,
																		'style' => 'font-weight: bold; color: #444; text-align: center;'));

								echo $this->Js->submit($word,
													array(
														'url'=> array(
																		'controller'=>'Servers',
																		'action'=>'setRconPassword'
														 ),
														'update' => '#server_start_params_container',
														'class' => 'btn',
														'div' => false,
														'label' => false,
														'before' =>$loadingShow,
														'complete'=>$loadingHide));


								?>
								</div>
						    </div>
						</div>
					</td>
				</tr>
			</table>
			<?php echo $this->Form->end(); ?>
			</div>
		</td>
	</tr>
	<tr>
	<td style="width: 400px;"> <?php /* Смена игры сервера (начало) */?>
			<?php if (@$gameTemplateList) { ?>
			<?php echo $this->Form->create('Server', array('action' => 'changeGame','id'=>'setNewGame_'.$id)); ?>
			<div id="action_positive"  style="height: 75px;">
				<span style="margin-top: 5px; font-size: 12px;">Изменить игру на</span>
				<?php
				echo $this->Form->input('GameTemplate.id', array( 'options' => $gameTemplateList,
															'div' => false,
															'label' => false));
				echo $this->Form->input('id', array( 'type' => 'hidden',
												'div' => false,
												'label' => false));


				$serviceDesc = '<span>
				После установки новой игры, ваш текущий сервер удалён НЕ БУДЕТ.
				Если в будущем вы выберете игру, которая была установлена ранее,
				то будут возвращены также и все настройки, моды и плагины.
				<br/>
				Если стоимость слота текущего сервера ниже стоимость слота нового
				сервера, то срок аренды будет увеличен. Если же наоборот - то уменьшен.
				<br/>
				Обращаем ваше внимание, что смену игры можно производить не чаще
				одного раза в сутки. Также нельзя выбрать игру, которая не поддерживается
				текущим приватным режимом сервера.
				</span>';

				// Подтверждение смены игры сервера
				$confirmMessage = 'Вы уверены, что хотите сменить игру сервера?'.
								  "\n<br/>Обращаем ваше внимание, что смену игры можно производить не чаще
										  одного раза в сутки.!";
				$confirmMessageDesc = "Все текущие настройки будут сохранены, вы сможете к ним вернуться " .
								      "при смене игры на нынешнюю." ;
				echo $this->Html->link($serviceDesc.'<div class="ui-button-text ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" style="padding: .2em .6em;">Установить новую игру</div>', '#',
								array ('id'=>'set_new_game_'.$id,
									   'escape' => false,
									   'onClick' => 'ConfirmNewGame();',
									   'class'=>'button qlabs_tooltip_right qlabs_tooltip_style_39',
									   'style' => 'margin-top: 10px;'));
				?>

				<div id="new_game_confirm" title="Подвердите смену игры сервера #<?php echo $id; ?>" style="display: none;">
					<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
						<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
						<?php echo $confirmMessage; ?>
					</div>
					<br/>
					<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;">
						<span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
						<?php echo $confirmMessageDesc; ?>
					</div>
				</div>

				<script type="text/javascript">
					function ConfirmNewGame() {

						$('#new_game_confirm').dialog({
												resizable: false,
												height:280,
												width: 400,
												modal: true,
												buttons: {

														'Подтверждаю': function() {
														$('#setNewGame_<?php echo $id; ?>').submit();

														$(this).dialog('close');
													},
													'Нет!': function() {
														$(this).dialog('close');
													}
												}
											});

						}
				</script>
			<?php echo $this->Form->end(); ?>
			</div>
			<?php
					} elseif (@$minTimeToUseService) {
						?>
						<div id="action_blocked"  style="height: 75px;">
							<h3 style="margin-bottom: 0px;">Смена игры сервера</h3>
						<?php
						echo 'Вы не можете воспользоваться услугой ранее, чем <nobr>'.$this->Time->nice($minTimeToUseService)."</nobr>";
						?>
						</div>
						<?php
					} else {
			?>
			<div id="action_blocked"  style="height: 75px;">
				<h3 style="margin-bottom: 0px;">Смена игры сервера</h3>
				Услуга не подключена.
			</div>
			<?php
					}
			?>
		</td> <?php /* Смена игры сервера (конец) */?>
	<td colspan="2">
	<!-- Управление без пароля начало -->
		<div id="action_positive" style="height: 75px;">
			<h3 style="margin-bottom: 0px;">Контроль сервера без пароля:</h3>
	<?php
				echo $this->Form->create('Server', array('action' => 'setControlToken'));
				echo $this->Form->input('id', array(  'type' => 'hidden',
												'div' => false,
												'label' => false));

				if ($this->data['Server']['controlByToken'] == 1) {
			?>
			<small>
			<?php
				echo $this->Html->link('Ссылка для управления',
								  array(
								  		'controller' => 'servers',
								  		'action' => 'controlByToken',
								  		@$this->data['Server']['controlToken']
								  		),
								  array(
								  		'target' => '_blank'
								  		));

			 ?>
			 </small>
			 <?php
				$submitText = 'Запретить';
				$submitClass = 'btn btn-warning';
				} elseif ($this->data['Server']['controlByToken'] == 0) {
			?>
			<small>(Запуск/Остановка/Состояние)</small>
			<?php
				$submitText = 'Включить';
				$submitClass = 'btn';
				}
			?>
			<br/>
			<?php

			echo $this->Js->submit($submitText,
				array(
					'url'=> array(
									'controller'=>'Servers',
									'action'=>'setControlToken',
									$id
					 ),
					'update' => '#server_start_params_container',
					'class' => $submitClass,
					'div' => false,
					'label' => false,
					'before' =>$loadingShow,
					'complete'=>$loadingHide,
					'buffer' => false));
			echo $this->Form->end();
			?>
			</div>
	<!-- Управление без пароля конец -->
	</td>
	</tr>
</table>

<script type="text/javascript">
	$(function() {
		$(".button").button();
	});
</script>
<?php
	echo $this->Js->writeBuffer(); // Write cached scripts
?>
