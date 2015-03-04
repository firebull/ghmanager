<?php
/*
 * Created on 31.08.2010
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
 $this->layout = 'ajax';
 include('loading_params.php');
 //pr($status);

?>
<script type="text/javascript" language="javascript">
		$(function() {
			$('<?php echo '#server_control_stop_'.$serverId;?>').dialog('destroy');
			$('<?php echo '#server_control_start_'.$serverId;?>').dialog('destroy');
		});
</script>
<table class="serverSrcds" align="center" style="margin-top: 5px;">
	<tr>
		<th>Управление</th>
		<th>ID</th>
		<th></th>
		<th></th>
		<th></th>
		<th></th>

	</tr>
<?php
if (@$status == 'runing') {
?>

	<tr>
		<td align="center" style="width: 170px;">
		<div class="btn-group">
			<?php
			//Иконка для остановки сервера
			echo $this->Html->link('<i class="icon-stop"></i>', '#',
								array( 'id'=>'server_start_stop_'.$serverId,
									   'escape' => false,
									   'title' => 'Остановить сервер',
									   'class' => 'btn'

								));
			$effect = $this->Js->get('#server_control_stop_'.$serverId)->effect('slideIn');
			$event  = $this->Js->request(array('controller'=>'servers',
										 'action'=>'script', $serverId,'stop'),
								   array('update' => '#server_control_stop_'.$serverId,
										 'before'=>$loadingShow,
										 'complete'=>$loadingHide.";$('#server_control_stop_$serverId').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});GetStatuses();"));

			$this->Js->get('#server_start_stop_'.$serverId)->event('click', $event);


			//Иконка для Рестарта сервера
			echo $this->Html->link('<i class="icon-refresh"></i>', '#',
								array(  'id'=>'server_restart_'.$serverId,
										'escape' => false,
										'title' => 'Перезапустить сервер',
										'class' => 'btn'

								));
			$effect = $this->Js->get('#server_control_restart_'.$serverId)->effect('slideIn');
			$event  = $this->Js->request(array('controller'=>'servers',
										 'action'=>'script', $serverId,'restart'),
								   array('update' => '#server_control_restart_'.$serverId,
										 'before'=>$loadingShow,
										 'complete'=>$loadingHide.";$('#server_control_restart_$serverId').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});"
										 ));

			$this->Js->get('#server_restart_'.$serverId)->event('click', $event);


			//Иконка для просмотра лога
			echo $this->Html->link('<i class="icon-list-alt"></i>', '#',
								array ('id'=>'server_log_'.$serverId,
									   'escape' => false,
									   'title' => 'Просмотр лога сервера',
									   'class' => 'btn'

								));
			$effect = $this->Js->get('#server_log_view_'.$serverId)->effect('slideIn');
			$event  = $this->Js->request(array('controller'=>'servers',
										 'action'=>'viewLog', $serverId),
								   array('update' => '#server_log_view_'.$serverId,
										 'before'=>$loadingShow,
										 'complete'=>$loadingHide.";$('#server_log_view_$serverId').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});"
										 ));

			$this->Js->get('#server_log_'.$serverId)->event('click', $event);


			//Иконка для просмотра и смены пароля SuperUser
			echo $this->Html->link('<i class="icon-lock"></i>', '#',
								array ( 'id'=>'mumble_change_superuser_pass_'.$serverId,
										'escape' => false,
										'title' => 'Смена пароля SuperUser',
										'class' => 'btn',
										'onClick'=>"Confirm()"

								));
			$effect = $this->Js->get('#server_control_'.$serverId)->effect('slideIn');
			$eventChangeRootPass  = $this->Js->request(array('controller'=>'servers',
										 'action'=>'changeMumbleRootPass', $serverId, 'change'),
								   array('update' => '#server_control_'.$serverId,
										 'before'=>$loadingShow,
										 'complete'=>$loadingHide.";$('#server_control_$serverId').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});"
										 ));



			?>

		</div>
		</td>
		<td><?php echo $serverId; ?></td>
		<td colspan="4">Просмотр состояния данного типа серверов пока недоступен.</td>

	</tr>
<?php
	 			} elseif (@$status == 'stoped') {
				//Сообщение незапущенном сервере
?>
	<tr>
		<td>
		<div class="btn-group">

			<?php
			//Иконка для запуска сервера
			echo $this->Html->link('<i class="icon-play"></i>', '#',
								array ( 'id'=>'server_start_stop_'.$serverId,
										'escape' => false,
										'title' => 'Запустить сервер',
										'class' => 'btn'

								));
			$effect = $this->Js->get('#server_control_start_'.$serverId)->effect('slideIn');
			$event  = $this->Js->request(array('controller'=>'servers',
										 'action'=>'script', $serverId,'start'),
								   array('update' => '#server_control_start_'.$serverId,
								   		 'async' => true,
										 'before'=>$loadingShow,
										 'complete'=>$loadingHide.";$('#server_control_start_$serverId').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});"
										 ));

			$this->Js->get('#server_start_stop_'.$serverId)->event('click', $event);

			//Иконка для просмотра лога
			echo $this->Html->link('<i class="icon-list-alt"></i>', '#',
								array ('id'=>'server_log_'.$serverId,
									   'escape' => false,
									   'title' => 'Просмотр лога сервера',
									   'class' => 'btn'

								));
			$effect = $this->Js->get('#server_log_view_'.$serverId)->effect('slideIn');
			$event  = $this->Js->request(array('controller'=>'servers',
										 'action'=>'viewLog', $serverId),
								   array('update' => '#server_log_view_'.$serverId,
										 'before'=>$loadingShow,
										 'complete'=>$loadingHide.";$('#server_log_view_$serverId').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});"
										 ));

			$this->Js->get('#server_log_'.$serverId)->event('click', $event);


			//Иконка для просмотра и смены пароля SuperUser
			echo $this->Html->link('<i class="icon-lock"></i>', '#',
								array ( 'id'=>'mumble_change_superuser_pass_'.$serverId,
										'escape' => false,
										'title' => 'Смена пароля SuperUser',
										'class' => 'btn',
										'onClick'=>"Confirm()"

								));
			$effect = $this->Js->get('#server_control_'.$serverId)->effect('slideIn');
			$eventChangeRootPass  = $this->Js->request(array('controller'=>'servers',
										 'action'=>'changeMumbleRootPass', $serverId, 'change'),
								   array('update' => '#server_control_'.$serverId,
										 'before'=>$loadingShow,
										 'complete'=>$loadingHide.";$('#server_control_$serverId').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});"
										 ));
							?>

		</div>

		</td>
		<td><?php echo $serverId; ?></td>
		<td colspan="4">Ошибка подключения. Сервер выключен?</td>
	</tr>

<?php
				} else {
?>
	<tr>
		<td colspan="5">Не могу запросить данные, попробуйте позднее<td>
	</tr>
<?php
				}
$confirmMessage = 'Вы уверены, что хотите сменить пароль SuperUser?'.
						 "\n<br/><br/>Это необратимая операция!" ;
?>
</table>
		<div id="server_control_<?php echo $serverId;?>" style="display:none" title="Управление сервером #<?php echo $serverId;?>"></div>
		<div id="server_control_start_<?php echo $serverId;?>" style="display:none" title="Управление сервером #<?php echo $serverId;?>: Запуск"><?php echo $this->Html->image("loading.gif");?></div>
		<div id="server_control_stop_<?php echo $serverId;?>" style="display:none" title="Управление сервером #<?php echo $serverId;?>: Остановка"></div>
		<div id="server_control_restart_<?php echo $serverId;?>" style="display:none" title="Управление сервером #<?php echo $serverId;?>: Перезапуск"></div>
		<div id="server_log_view_<?php echo $serverId;?>" style="display:none" title="Просмотр лога сервера #<?php echo $serverId;?>"></div>
		<div id="confirm" title="Подвердите смену пароля" style="display: none;">
		<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
			<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
			<?php echo $confirmMessage; ?>
		</div>
</div>
<br/>
<?php
			echo $this->Html->link('Обновить', '#',
							array ('id'=>'server_refresh_'.$serverId, 'escape' => false));

			$event  = $this->Js->request(array('controller'=>'Servers',
									 'action'=>'viewServer', $serverId),
							   array('update' => '#server_more_'.$serverId,
									 'before'=>$loadingShow,
									 'complete'=>$loadingHide.";GetStatuses();"));

			$this->Js->get('#server_refresh_'.$serverId)->event('click', $event);

?>
&nbsp;&nbsp;&nbsp;&nbsp;
<script type="text/javascript">


	function Confirm() {

		$('#confirm').dialog({
								resizable: false,
								height:200,
								width: 350,
								modal: true,
								buttons: {

										'Изменить пароль': function() {
										<?php echo $eventChangeRootPass;?>;
										$(this).dialog('close');
									},
									Отмена: function() {
										$(this).dialog('close');
									}
								}
							});

		}


</script>
<?php
			echo $this->Js->writeBuffer(); // Write cached scripts
?>
<a href="#" onclick="$('#server_more_<?php echo $serverId; ?>').hide('highlight');  return false;">Скрыть</a>
