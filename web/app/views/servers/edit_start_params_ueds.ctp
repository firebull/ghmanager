<?php
/*
 * Created on 16.08.2010
 *
 * File created for project TeamServer
 * by nikita
 */
 include('../loading_params.php');
 $id = $this->data['Server']['id'];
 $slots = $this->data['Server']['slots'];
 
?>
<div id="server_params">
	<div id="flash"><?php echo $session->flash(); ?></div>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
	<tr>					
		<td align="center" colspan="2">
		<div id="action_positive"  style="height: 75px;">
		
		<?php /* Смена карты (начало) */?>
		<?php echo $form->create('Server', array('action' => 'setMap')); ?>
		<table border="0" cellpadding="0" cellspacing="0" align="center">
			<tr>
				<td colspan="2"><h3>Установить карту по умолчанию:</h3></td>
			</tr>
			<tr>
				
				<td>
				<?php 
				echo $form->input('map', array(
													'options' => $this->data['Server']['maps'], 
													'selected' => $this->data['Server']['map'],
													'div' => false, 	
													'label' => false,
													'title'=>'Текущая карта'));
				echo $form->input('id', array('type'=>'hidden'));
				echo $form->input('action', array('type'=>'hidden', 'value'=>'set'));									
				
				?>
				
				</td>
				<td align="left" style="padding-left: 10px;">
				<?php
				
						
						echo $js->submit('Сменить',
							array(
								'url'=> array(
												'controller'=>'Servers',
												'action'=>'setMap'
								 ),
								'update' => '#server_start_params_container',
								'class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
								'id'=>'submit',
								'before' =>$loadingShow,
								'complete'=>$loadingHide,
								'buffer' => false));
							
						
				?>
				</td>
			</tr>
			<tr>
				<td colspan="2">
				<?php if ( $this->data['GameTemplate'][0]['name'] == 'l4d'
							or
					   	   $this->data['GameTemplate'][0]['name'] == 'l4d2'						  
					  ){
					  	?>
				
						<small>Загрузка карт из панели недоступна.</small>
				
						<?php
						}
						else
						{
						
	
						$uploaderScript = "http://".$this->data['Server']['address']."/uploadMap/?" .
										  "id=".$this->data['Server']['id']."&" .
										  "token=".$this->data['User'][0]['tokenhash'];
						echo $html->link('Загрузить свою карту на сервер', '#',
										array (
											   'onClick' => "window.open('".$uploaderScript."', 'newUploaderWin#".$this->data['Server']['id']."', 'Toolbar=yes, Location=no, Directories=no, Status=yes, Menubar=yes, Scrollbars=no, Resizable=yes, Width=550, Height=500')")
										);
				
				 
				 		} ?>
				</td>
			</tr>
		</table>
		
		<?php echo $form->end();?>		
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
	<tr>
		
		<td colspan="2"> 
		<?php /* Установка пароля сервера (начало) */?>	
		
		<div id="action_positive" style="height: 75px;">
			<?php 
			// Установить заголовок по наличию пароля
			if ($serverPassword === false){
				$passHeader = 'Установить';
			}
			else
			{
				$passHeader = 'Изменить';
			}
			
			echo $form->create('Server', array('action' => 'setConfigParam'));
			?>
			
			<table border="0" cellpadding="0" cellspacing="0" align="center">
				<tr>
					<td colspan="2"><h3><?php echo $passHeader; ?> пароль сервера:</h3></td>
				</tr>
				<tr>
					<td align="right">
						<?php
						echo $form->input('id', array('type' => 'hidden'));
						echo $form->input('paramName', array('type'  => 'hidden',
															 'value' => 'GamePassword'));
						echo $form->input('paramValue',array ( 
														'id'    => 'paramPassword',
														'value' => $serverPassword,
														'div'   => false, 
														'label' => false,
														'size'  => 24,
														'style' => 'font-weight: bold; color: #444; text-align: center;'));	
						?>
					</td>
					<td align="left">
						<?php
									
						echo $js->submit($passHeader,
							array(
								'url'=> array(
												'controller'=>'Servers',
												'action'=>'setConfigParam',
												$id
								 ),
								'update' => '#server_start_params_container',
								'class' => 'button',				
								'before' =>$loadingShow,
								'complete'=>$loadingHide,
								'buffer' => false));
						?>
					</td>
				</tr>
				<tr>
					<td colspan="2">
					

					</td>
				</tr>
			</table>
		<?php echo $form->end(); ?>
		</div>
		<?php /* Установка пароля сервера (конец) */?>	
		</td>		
		<td> 
		<?php if ($this->data['Server']['vac'] == 1){?>
					<div id="action_positive"  style="height: 75px;">
					<h3 style="margin-bottom: 0px;">VAC:</h3>
			<?php 
				  	echo $form->create('Server',array('action'=>'switchParam'));
				  	echo $form->input('id', array('type'=>'hidden'));
				  	echo $js->submit('Отключить',
							array(
								'url'=> array(
												'controller'=>'Servers',
												'action'=>'switchParam',
												'vac',
												'off'
								 ),
								'update' => '#server_start_params_container',
								'class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
								'id'=>'switchVac',
								'before' =>$loadingShow,
								'complete'=>$loadingHide,
								'buffer' => false));
				  	echo $form->end();
				  
				  }
				  else
				  {
			?>
					<div id="action_neutral"  style="height: 75px;">
					<h3 style="margin-bottom: 0px;">VAC:</h3>
			<?php	  	
				  	echo $form->create('Server',array('action'=>'switchParam'));
				  	echo $form->input('id', array('type'=>'hidden'));
				  	echo $js->submit('Включить',
							array(
								'url'=> array(
												'controller'=>'Servers',
												'action'=>'switchParam',
												'vac',
												'on'
								 ),
								'update' => '#server_start_params_container',
								'class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
								'id'=>'switchVac',
								'before' =>$loadingShow,
								'complete'=>$loadingHide,
								'buffer' => false));
					echo $form->end();
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
		echo $html->link('Сбросить', '#',
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
		<td align="center" colspan="2" style="width: 350px;">
		<?php /* Смена игры сервера (начало) */?>
			<?php if (@$gameTemplateList){ ?>
			<?php echo $form->create('Server', array('action' => 'changeGame','id'=>'setNewGame_'.$id)); ?>
			<div id="action_positive"  style="height: 75px;">
				<span style="margin-top: 5px; font-size: 12px;">Изменить игру на</span>
				<?php 
				echo $form->input('GameTemplate.id', array( 'options' => $gameTemplateList,
															'div' => false, 
															'label' => false));
				echo $form->input('id', array( 'type' => 'hidden',
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
				echo $html->link($serviceDesc.'<div class="ui-button-text ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only" style="padding: .2em .6em;">Установить новую игру</div>', '#',
								array ('id'=>'set_new_game_'.$id, 
									   'escape' => false,
									   'rel' => '#service_save_params_desc', 
									   'onClick' => 'ConfirmNewGame();',
									   'class'=>'qlabs_tooltip_right qlabs_tooltip_style_39',
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
					}
					else if(@$minTimeToUseService)
					{
						?>
						<div id="action_blocked"  style="height: 75px;">
							<h3 style="margin-bottom: 0px;">Смена игры сервера</h3> 
						<?php
						echo 'Вы не можете воспользоваться услугой ранее, чем<br/>'.$time->nice($minTimeToUseService);
						?>
						</div>
						<?php
					}
					else
					{
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
		<td align="center" colspan="2">
		<?php
				echo $form->create('Server', array('action' => 'setControlToken'));
				if ($this->data['Server']['controlByToken'] == 1){
			?>
			<div id="action_neutral" style="height: 75px;">
			<h3 style="margin-bottom: 0px;">Контроль сервера без пароля:</h3>
			<small>
			<?php
				echo $html->link('Ссылка для управления',
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
				}
				else if ($this->data['Server']['controlByToken'] == 0)
				{
			?>
			<div id="action_positive" style="height: 75px;">
			<h3 style="margin-bottom: 0px;">Контроль сервера без пароля:</h3>
			<small>(Запуск/Остановка/Состояние)</small>
			<?php
				$submitText = 'Включить';	
				}
			?>
			<?php
									
			echo $js->submit($submitText,
				array(
					'url'=> array(
									'controller'=>'Servers',
									'action'=>'setControlToken',
									$id
					 ),
					'update' => '#server_start_params_container',
					'class' => 'button',				
					'before' =>$loadingShow,
					'complete'=>$loadingHide,
					'buffer' => false));
			echo $form->end();
			?>
			</div>
		<td>
	</tr>
</table>


<script type="text/javascript">
	$(function() {
					
		$(".button, input:submit").button();

	});
</script>
<?php 
	echo $js->writeBuffer(); // Write cached scripts 
?>