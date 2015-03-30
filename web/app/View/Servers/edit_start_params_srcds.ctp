<?php
/*
 * Created on 16.08.2010
 *
 * File created for project TeamServer
 * by nikita
 */
 include('loading_params.php');
 $id = $this->data['Server']['id'];
 $slots = $this->data['Server']['slots'];

 if ($slots > 0 and $slots <= 12) {
	$fpsmax = 1000;
 } elseif ($slots > 12 and $slots <= 32) {
	$fpsmax = 500;
 } else {
	$fpsmax = 300;
 }

?>
<div id="server_params">
	<div id="flash"><?php echo $this->Session->flash(); ?></div>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>
		<td align="center" colspan="2">
		<div id="action_positive"  style="height: 75px;">

		<?php /* Смена карты (начало) */?>
		<?php echo $this->Form->create('Server', array('action' => 'setMap')); ?>
		<table border="0" cellpadding="0" cellspacing="0" align="center">
			<tr>
				<td><h3>Установить карту по умолчанию:</h3></td>
			</tr>
			<tr>

				<td>
					<?php
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
													'id' => 'mapList',
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
								'before' =>$loadingShow,
								'complete'=>$loadingHide,
								'buffer' => true));


						?>
						<label for="mapList" class="text-center">
							<?php if ( $this->data['GameTemplate'][0]['name'] == 'l4d'
									or
							   	   $this->data['GameTemplate'][0]['name'] == 'l4d2'
							  ) {
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
		<td align="center" colspan="2">
		<?php echo $this->element('set_slots', array(
														'id'=>$this->data['Server']['id'],
														'slots'=>$this->data['Server']['slots'],
														'slots_min'=>$this->data['GameTemplate'][0]['slots_min'],
														'slots_max' => $this->data['GameTemplate'][0]['slots_max'],
														'payedTill' => $this->data['Server']['payedTill']
														 )); ?>
		</td>

	</tr>
<?php

	if ($this->data['GameTemplate'][0]['name'] == 'csgo' or $this->data['GameTemplate'][0]['name'] == 'csgo-t128') {
?>
	<tr>
		<td align="center" colspan="2">
			<?php /* Смена Группы карт (начало) */?>
			<div id="action_positive"  style="height: 75px;">
				<h3>Установить группу карт:</h3>
				<?php
					echo $this->Form->create('Server', array('action' => 'setMapGroup'));
					echo $this->Form->input('id', array('type'=>'hidden'));
					echo $this->Form->input('action', array('type'=>'hidden', 'value'=>'set'));
				?>
				<div class="control-group">
				    <div class="controls">
				      <div class="input-append input-prepend">
				        <span class="add-on"><i class="icon-list"></i></span><?php

							echo $this->Form->input('mapGroup', array(
													'options' => @$mapGroups,
													'selected' => $this->data['Server']['mapGroup'],
													'div' => false,
													'label' => false,
													'id' => 'mapGroups',
													'title'=>'Текущая группа карт'));



							echo $this->Js->submit('Установить',
							array(
								'url'=> array(
												'controller'=>'Servers',
												'action'=>'setMapGroup'
								 ),
								'update' => '#server_start_params_container',
								'class' => 'btn',
								'div' => false,
								'label' => false,
								'before' =>$loadingShow,
								'complete'=>$loadingHide,
								'buffer' => true));


						?>
					</div>
				</div>
				<?php echo $this->Form->end(); ?>
			</div>
		</td>
		<td align="center" colspan="2">
		<div id="action_positive"  style="height: 75px;">
			<h3>Установить режим игры:</h3>
				<?php
					echo $this->Form->create('Server', array('action' => 'setGameMode'));
					echo $this->Form->input('id', array('type'=>'hidden'));
					echo $this->Form->input('action', array('type'=>'hidden', 'value'=>'set'));
				?>
				<div class="control-group">
				    <div class="controls">
				      <div class="input-append input-prepend">
				        <span class="add-on"><i class="icon-screenshot"></i></span><?php

							echo $this->Form->input('gameMode', array(
													'options' => @$gameModesList,
													'selected' => $this->data['Server']['mod'],
													'div' => false,
													'label' => false,
													'id' => 'gameModes',
													'title'=>'Текущий режим игры'));



							echo $this->Js->submit('Установить',
							array(
								'url'=> array(
												'controller'=>'Servers',
												'action'=>'setGameMode'
								 ),
								'update' => '#server_start_params_container',
								'class' => 'btn',
								'div' => false,
								'label' => false,
								'before' =>$loadingShow,
								'complete'=>$loadingHide,
								'buffer' => true));


						?>
					</div>
				</div>
				<?php echo $this->Form->end(); ?>
		</div>
		</td>
	</tr>
	<tr>
		<!-- Host map начало-->
		<td align="center" colspan="2">
			<div id="action_positive" style="height: 75px;">
				<?php
				// Установить заголовок по наличию пароля
				if (empty($this->data['Server']['hostmap'])) {
					$hostMapHeader = 'Установить';
				} else {
					$hostMapHeader = 'Изменить';
				}

				echo $this->Form->create('Server', array('action' => 'setHostMap'));
				?>

				<table border="0" cellpadding="0" cellspacing="0" align="center">
					<tr>
						<td><h3><?php echo $hostMapHeader; ?> Host Map:</h3></td>
					</tr>
					<tr>
						<td align="right">
							<?php
							echo $this->Form->input('id', array('type' => 'hidden'));
							?>

							<div class="control-group">
							    <div class="controls">
							        <div class="input-append input-prepend">
							        <span class="add-on"><i class="icon-picture"></i></span><?php

									echo $this->Form->input('hostmap',array (
																	'id'    => 'hostMap',
																	'value' => @$this->data['Server']['hostmap'],
																	'div'   => false,
																	'label' => false,
																	//'size'  => 20,
																	'style' => 'color: #444; text-align: center;'));


									echo $this->Js->submit($hostMapHeader,
										array(
											'url'=> array(
															'controller'=>'Servers',
															'action'=>'setHostMap',
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
		</td>
		<!-- Host map конец-->
		<!-- Collections начало -->
		<td align="center" colspan="2">
			<div id="action_positive" style="height: 75px;">
			<?php
				echo $this->Form->create('Server', array('action' => 'setHostCollection'));
			?>
				<table border="0" cellpadding="0" cellspacing="0" align="center">
					<tr>
						<td><h3>Коллекция Host Map:</h3></td>
					</tr>
					<tr>
						<td align="right">
							<?php
							echo $this->Form->input('id', array('type' => 'hidden'));
							?>

							<div class="control-group">
							    <div class="controls">
							        <div class="input-append input-prepend">
							        <?php

							        $hostCollectionList = array('0' => 'Нет',
							        					  '125499590' => '_SE',
							        					  '125499818' => '_SE + Mirage'	);

									echo $this->Form->input('hostCollectionList',array (
																	'options' => $hostCollectionList,
																	'selected' => @$this->data['Server']['hostcollection'],
																	'id'    => 'hostMapList',
																	'div'   => false,
																	'label' => false,
																	'style' => 'width: 110px; color: #444; text-align: left;'));

									if (!array_key_exists(@$this->data['Server']['hostcollection'], $hostCollectionList)) {
										$hostCollectionVal = @$this->data['Server']['hostcollection'];
									} else {
										$hostCollectionVal = '';
									}

									echo $this->Form->input('hostcollection',array (
																	'id'    => 'hostCollection',
																	'value' => $hostCollectionVal,
																	'div'   => false,
																	'label' => false,
																	'placeholder'  => 'Свой вариант',
																	'style' => 'width: 110px; font-weight: bold; color: #444; text-align: center;'));


									echo $this->Js->submit('Установить',
										array(
											'url'=> array(
															'controller'=>'Servers',
															'action'=>'setHostCollection',
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
		</td>
		<!-- Collections конец -->
	</tr>
<?php

	}
?>
	<tr>
		<!-- Создание админа начало -->
		<td align="center" colspan="2">
			<div id="action_positive" style="height: 75px;">
				<?php /* Создание администратора сервера */?>
				<?php
						echo $this->element('server_admin_regexp');
				?>
			</div>
		</td>
		<!-- Создание админа конец -->
		<td align="center" colspan="2">
		<?php /* Установка пароля сервера (начало) */?>
		<?php if ($this->data['GameTemplate'][0]['name'] == 'l4d2') {
		?>
		<div id="action_blocked"  style="height: 75px;">
			<h3 style="margin-bottom: 0px;">Смена пароля сервера</h3>
		К сожалению, в L4D2 установить <br/>пароль сервера невозможно.
		</div>
		<?php
		} else {

		?>
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
					<td><h3><?php echo $passHeader; ?> пароль сервера:</h3></td>
				</tr>
				<tr>
					<td align="right">
						<?php
						echo $this->Form->input('id', array('type' => 'hidden'));
						echo $this->Form->input('paramName', array('type'  => 'hidden',
															 'value' => 'sv_password'));
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
																//'size'  => 20,
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
		<?php } ?>
		<?php /* Установка пароля сервера (конец) */?>
		</td>
	</tr>
	<tr>
	<!-- Управление без пароля начало -->
	<td colspan="2">
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
	</td>
	<!-- Управление без пароля конец -->
	<!-- Пароль RCON начало -->
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
			?>
			<table border="0" cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td><h3><?php echo $word; ?> пароль RCON:</h3></td>
				</tr>
				<tr>
					<td>
				<?php

					echo $this->Form->input('id', array('type'=>'hidden'));

				?>
					<div class="control-group">
					    <div class="controls">
					        <div class="input-append input-prepend">
					        <span class="add-on"><i class="icon-edit"></i></span><?php
					echo $this->Form->input('rconPassword',array ( 'value' => @$rconPassword,
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
											'div'   => false,
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
	<!-- Пароль RCON конец -->
	</tr>
	<tr>
		<!-- FPS и VAC начало -->
		<td>
		<div id="action_positive"  style="height: 75px; width: 90%;" class="qlabs_tooltip_right qlabs_tooltip_style_39">
		<span><strong>Внимание!</strong>
		Не работает на CS:S после v68, DOS:S, TF2!
		<br/><br/>
		Какое бы значение вы не ставили для этих игр,
		FPS всегда будет 66!
		<br/><br/>
		Для L4D и L4D2 не рекомендуется ставить значения
		больше стандартных 30FPS - качество игры это не
		изменит, зато может сильно увеличить VAR.
		<br/><br/>
		Причины описаны в FAQ на нашем сайте.
		</span>
		<?php echo $this->Form->create('Server', array('action'=>'setFps'));  ?>
		<table border="0" cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td><h3>Установить FPS:</h3></td>
				</tr>
				<tr>
					<td>
				<?php

					echo $this->Form->input('id', array('type'=>'hidden'));

				?>
					<div class="control-group">
					    <div class="controls">
					        <div class="input-append">
							<?php
								echo $this->Form->input('fpsmax',array (
																'id'=>'fps',
																'div' => false,
																'label' => false,
																'size' => 6,
																'class' => 'span1',
																'style' => 'text-align: center;'));

								echo $this->Js->submit('Изменить',
													array(
														'url'=> array(
																		'controller'=>'Servers',
																		'action'=>'setFps'
														 ),
														'id' => 'fpsButton',
														'update' => '#server_start_params_container',
														'class' => 'btn',
														'div' => false,
														'label' => false,
														'before' =>$loadingShow,
														'complete'=>$loadingHide));


				?>

								<label for="fpsButton" class="text-center">
									<small><div id="fpsMsg"></div></small>
								</label>

							</div>
					    </div>
					</div>

					</td>
				</tr>

			</table>
			<?php echo $this->Form->end(); ?>
		</div>
		</td>
		<td>
		<?php if ($this->data['Server']['vac'] == 1) {?>
					<div id="action_positive"  style="height: 75px;">
					<h3 style="margin-bottom: 0px;">VAC:</h3>
			<?php
				  	echo $this->Form->create('Server', array('action'=>'switchParam'));
				  	echo $this->Form->input('id', array('type'=>'hidden'));
				  	echo $this->Js->submit('Отключить',
							array(
								'url'=> array(
												'controller'=>'Servers',
												'action'=>'switchParam',
												'vac',
												'off'
								 ),
								'update' => '#server_start_params_container',
								'class' => 'btn',
								'style' => 'margin-top: 3px;',
								'id'=>'switchVac',
								'before' =>$loadingShow,
								'complete'=>$loadingHide,
								'buffer' => false));
				  	echo $this->Form->end();

				  } else {
			?>
					<div id="action_neutral"  style="height: 75px;">
					<h3 style="margin-bottom: 0px;">VAC:</h3>
			<?php
				  	echo $this->Form->create('Server', array('action'=>'switchParam'));
				  	echo $this->Form->input('id', array('type'=>'hidden'));
				  	echo $this->Js->submit('Включить',
							array(
								'url'=> array(
												'controller'=>'Servers',
												'action'=>'switchParam',
												'vac',
												'on'
								 ),
								'update' => '#server_start_params_container',
								'class' => 'btn',
								'style' => 'margin-top: 3px;',
								'id'=>'switchVac',
								'before' =>$loadingShow,
								'complete'=>$loadingHide,
								'buffer' => false));
					echo $this->Form->end();
				  }
			?>
					</div>
		</td>
		<!-- FPS и VAC конец -->
		<td>
		<?php
		/* Отмена автобновления (начало) */
		if ($this->data['Server']['autoUpdate'] == 1) { // Если автообноление включено, рисуем кнопку Отключить
		?>
		<div id="action_neutral" style="height: 75px;">
		<h3>Авто-обновление:</h3>

		<?php

		echo $this->Form->create('Server', array('action' => 'switchUpdate'));
		echo $this->Form->input('id', array('type'=>'hidden'));
		echo $this->Js->submit('Отключить',
			array(
				'url'=> array(
								'controller'=>'Servers',
								'action'=>'switchParam',
								'autoupdate',
								'off'
				 ),
				'update' => '#server_start_params_container',
				'class' => 'btn',
				'before' =>$loadingShow,
				'complete'=>$loadingHide,
				'buffer' => false));
		echo $this->Form->end();
		?>


		</div>
		<?php
		} else {
		?>
		<div id="action_positive" style="height: 75px;">
		<h3>Авто-обновление:</h3>

		<?php
		// Подтверждение
		$confirmMessage = 'Вы уверены, что хотите включить автоматическое обновление сервера?'.
						  "\n<br/>Это приведет к существенной задержке при запуске" .
						  "\n<br/>сервера - от 30 секунд до нескольких минут." .
						  "\n<br/>Также скрипт автоматического поднятия сервера
						   \n<br/>может ложно сработать и попытаться перегрузить сервер." ;

		echo $this->Html->link('Включить', '#',
						array ('id'=>'update_srcds_'.$id,
							   'escape' => false,
							   'onClick' => 'ConfirmUpdateOn();',
							   'class'=>'btn'));
		$event  = $this->Js->request(array('controller'=>'Servers',
							 'action'=>'switchParam','autoupdate', 'on', $id),
					   array('update' => '#server_start_params_container',
							 'before'=>$loadingShow,
							 'complete'=>$loadingHide,
							 'buffer'=>false));

		?>
		<div id="update_on_confirm" title="Подвердите включение авто-обновления #<?php echo $id; ?>" style="display: none;">
							<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
								<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
								<?php echo $confirmMessage; ?>
							</div>
		</div>

		<script type="text/javascript">
			function ConfirmUpdateOn() {

				$('#update_on_confirm').dialog({
										resizable: false,
										height:220,
										width: 450,
										modal: true,
										buttons: {

												'Подтверждаю': function() {
												<?php echo $event;?>;

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
		<?php
		}

		/* Отмена автобновления (конец) */?>
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
	<?php
		if (in_array($this->data['GameTemplate'][0]['name'], array('l4d-t100', 'l4d2-t100', 'cssv34', 'csgo-t128'))) {
	?>
			<div id="action_positive"  style="height: 75px;">
				<h3 style="margin-bottom: 0px;">Tickrate:</h3>
			<?php
					echo $this->Form->create('Server', array('action'=>'setTickrate'));
					echo $this->Form->input('id', array('type'=>'hidden'));
					echo $this->Form->input('action', array('type'=>'hidden', 'value'=>'set'));
					?>
				<div class="control-group">
				    <div class="controls">
				      <div class="input-append">
				        <?php

				        	if (empty($this->data['Server']['tickrate'])) {
				        		if ($this->data['GameTemplate'][0]['name'] == 'cssv34') {
									$this->request->data['Server']['tickrate'] = '66';
								} elseif ($this->data['GameTemplate'][0]['name'] == 'csgo-t128') {
									$this->request->data['Server']['tickrate'] = '64';
								} else {
				        			$this->request->data['Server']['tickrate'] = '30';
				        		}
				        	}

				        	/* Разрешённые значения тиков */
							if (in_array($this->data['GameTemplate'][0]['name'], array('l4d-t100', 'l4d2-t100', 'cssv34'))) {
								$allowedTicks = array(  '30' => '30',
														'33' => '33',
														'60' => '60',
														'66' => '66',
														'90' => '90',
														'100' => '100');
							} elseif (in_array($this->data['GameTemplate'][0]['name'], array('csgo-t128'))) {
								$allowedTicks = array(  '64' => '64',
														'66' => '66',
														'100' => '100',
														'128' => '128');
							}

							echo $this->Form->input('tickrate', array(
													'options' => $allowedTicks,
													'selected' => $this->data['Server']['tickrate'],
													'div' => false,
													'label' => false,
													'class' => 'span1',
													'id' => 'tickrate',
													'title'=>'Tickrate сервера'));



							echo $this->Js->submit('Изменить',
							array(
								'url'=> array(
												'controller'=>'Servers',
												'action'=>'setTickrate'
								 ),
								'update' => '#server_start_params_container',
								'class' => 'btn',
								'div' => false,
								'label' => false,
								'before' =>$loadingShow,
								'complete'=>$loadingHide,
								'buffer' => true));


						?>
						</div>
				    </div>
				</div>
			</div>
	<?php
		} else {
	?>
			<div id="action_blocked"  style="height: 75px;"></div>
	<?php
		}
	?>
	</td>
	<td>
		<?php if ($this->data['Server']['nomaster'] == 0) {?>
				<div id="action_positive"  style="height: 75px;">
				<h3 style="margin-bottom: 0px;">Nomaster:</h3>
		<?php
			  	echo $this->Form->create('Server', array('action'=>'switchParam'));
			  	echo $this->Form->input('id', array('type'=>'hidden'));
			  	echo $this->Js->submit('Включить',
						array(
							'url'=> array(
											'controller'=>'Servers',
											'action'=>'switchParam',
											'nomaster',
											'off'
							 ),
							'update' => '#server_start_params_container',
							'class' => 'btn',
							'style' => 'margin-top: 3px;',
							'id'=>'switchNomaster',
							'before' =>$loadingShow,
							'complete'=>$loadingHide,
							'buffer' => false));
			  	echo $this->Form->end();

			  } else {
		?>
				<div id="action_neutral"  style="height: 75px;">
				<h3 style="margin-bottom: 0px;">Nomaster:</h3>
		<?php
			  	echo $this->Form->create('Server', array('action'=>'switchParam'));
			  	echo $this->Form->input('id', array('type'=>'hidden'));
			  	echo $this->Js->submit('Отключить',
						array(
							'url'=> array(
											'controller'=>'Servers',
											'action'=>'switchParam',
											'nomaster',
											'on'
							 ),
							'update' => '#server_start_params_container',
							'class' => 'btn',
							'style' => 'margin-top: 3px;',
							'id'=>'switchNomaster',
							'before' =>$loadingShow,
							'complete'=>$loadingHide,
							'buffer' => false));
				echo $this->Form->end();
			  }
		?>
				</div>
	</td>
	<td align="center" colspan="2" style="width: 350px;">
<?php /* Смена игры сервера (начало) */?>
		<?php if (@$gameTemplateList) { ?>
		<?php echo $this->Form->create('Server', array('action' => 'changeGame','id'=>'setNewGame_'.$id)); ?>
		<div id="action_positive"  style="height: 75px;">
			<h3>Изменить игру на</h3>
			<?php

			echo $this->Form->input('id', ['type' => 'hidden']);


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
			?>

			<div class="control-group">
			    <div class="controls">
			        <div class="input-append">
			<?php
			echo $this->Form->input('GameTemplate.id', array( 'options' => $gameTemplateList,
														'div' => false,
														'label' => false));

			echo $this->Html->link($serviceDesc.'Установить', '#',
							array ('id'=>'set_new_game_'.$id,
								   'escape' => false,
								   'onClick' => 'ConfirmNewGame();',
								   'class'=>'btn qlabs_tooltip_right qlabs_tooltip_style_39',
								   'style' => ''));
			?>
					</div>
			    </div>
			</div>

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
					echo 'Вы не можете воспользоваться услугой ранее, чем<br/>'.$this->Time->nice($minTimeToUseService);
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
		<?php /* Смена игры сервера (конец) */?>
	</td>
	</tr>
</table>


<script type="text/javascript">
	$(function() {

		function checkFps() {
			var fps = parseInt($('#fps').val());
			var fpsMax = <?php echo $fpsmax; ?>;

			if (fps < 30) {
				$('#fpsMsg').text('Минимум 30FPS').addClass('highlight2');
				$('#fpsButton').attr('disabled','disabled');
			} else if (fps >= 30 && fps <= fpsMax) {
				$('#fpsMsg').text('от 30 до ' + fpsMax + 'FPS').removeClass('highlight2');
				$('#fpsButton').removeAttr('disabled');
			} else if (fps > fpsMax) {
				$('#fpsMsg').text('Максимум ' + fpsMax + 'FPS').addClass('highlight2');
				$('#fpsButton').attr('disabled','disabled');
			}
		}

		$("#fps").keyup(function() {
								checkFps();
								return false;
		});

		$("#hostCollection").keyup(function() {
								$("#hostMapList").val('0');
								return false;
		});

		$(".button").button();

	});
</script>
<?php
	echo $this->Js->writeBuffer(); // Write cached scripts
?>
