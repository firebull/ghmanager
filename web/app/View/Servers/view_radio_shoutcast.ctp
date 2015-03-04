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
<div id="flash"><?php echo $this->Session->flash(); ?></div>
<script type="text/javascript" language="javascript">
		$(function() {
			$('<?php echo '#server_control_stop_'.$serverId;?>').dialog('destroy');
			$('<?php echo '#server_control_start_'.$serverId;?>').dialog('destroy');
		});
</script>
<table class="serverSrcds" align="center">
	<tr>
		<th></th>
		<th></th>
		<th colspan="4" style="border-left: 1px solid grey;">Слушатели</th>
		<th colspan="3" style="border-left: 1px solid grey;">Сервер</th>


	</tr>
	<tr>
		<th>Управление</th>
		<th>ID</th>
		<th>Сейчас</th>
		<th>Максимально</th>
		<th>Слотов</th>
		<th>Всего</th>
		<th>Состояние</th>
		<th>Битрейт</th>
		<th>Композиция</th>


	</tr>
<?php
if (@$status == 'runing') {
?>

	<tr>
		<td>
		<div style="display: inline;" class="icons">
			<ul id="icons_<?php echo $serverId;?>" class="ui-widget ui-helper-clearfix">
				<li class="ui-state-default ui-corner-all" title="Остановить сервер">
							<?php
							//Иконка для остановки сервера
							echo $this->Html->link('<span class="ui-icon ui-icon-stop"></span>', '#',
												array ('id'=>'server_start_stop_'.$serverId, 'escape' => false
												,'onClick'=>""

												));
							$effect = $this->Js->get('#server_control_stop_'.$serverId)->effect('slideIn');
							$event  = $this->Js->request(array('controller'=>'servers',
														 'action'=>'script', $serverId,'stop'),
												   array('update' => '#server_control_stop_'.$serverId,
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide.";$('#server_control_stop_$serverId').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});"));

							$this->Js->get('#server_start_stop_'.$serverId)->event('click', $event);

							?>
				</li>
				<li class="ui-state-default ui-corner-all" title="Перезапустить сервер">
							<?php
							//Иконка для Рестарта сервера
							echo $this->Html->link('<span class="ui-icon ui-icon-refresh"></span>', '#',
												array ('id'=>'server_restart_'.$serverId, 'escape' => false
												,'onClick'=>""

												));
							$effect = $this->Js->get('#server_control_restart_'.$serverId)->effect('slideIn');
							$event  = $this->Js->request(array('controller'=>'servers',
														 'action'=>'script', $serverId,'restart'),
												   array('update' => '#server_control_restart_'.$serverId,
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide.";$('#server_control_restart_$serverId').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});"
														 ));

							$this->Js->get('#server_restart_'.$serverId)->event('click', $event);

							?>
				</li>
				<li class="ui-state-default ui-corner-all" title="Просмотр Лога">
							<?php
							//Иконка для просмотра лога
							echo $this->Html->link('<span class="ui-icon ui-icon-note"></span>', '#',
												array ('id'=>'server_log_'.$serverId, 'escape' => false
												,'onClick'=>""

												));
							$effect = $this->Js->get('#server_log_view_'.$serverId)->effect('slideIn');
							$event  = $this->Js->request(array('controller'=>'servers',
														 'action'=>'viewLog', $serverId),
												   array('update' => '#server_log_view_'.$serverId,
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide.";$('#server_log_view_$serverId').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 700});"
														 ));

							$this->Js->get('#server_log_'.$serverId)->event('click', $event);

							?>
				</li>
				<li class="ui-state-default ui-corner-all" title="Просмотр и смена паролей">
							<?php
							//Иконка для просмотра и смены паролей трансляции и Админа
							echo $this->Html->link('<span class="ui-icon ui-icon-locked"></span>', '#',
												array ('id'=>'shotcast_change_pass_'.$serverId, 'escape' => false
												,'onClick'=>""

												));
							$effect = $this->Js->get('#server_control_'.$serverId)->effect('slideIn');
							$event  = $this->Js->request(array('controller'=>'servers',
														 'action'=>'changeShoutcastPass', $serverId),
												   array('update' => '#server_password_'.$serverId,
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide.";$('#server_password_$serverId').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 400});"
														 ));

							$this->Js->get('#shotcast_change_pass_'.$serverId)->event('click', $event);

							?>
				</li>
			</ul>
		</div>

		</td>
		<td><?php echo $serverId; ?></td>
		<td><?php echo $stats['listeners']; ?></td>
		<td><?php echo $stats['listeneresPeak']; ?></td>
		<td><?php echo $stats['maxListeners']; ?></td>
		<td><?php echo $stats['totalListeners']; ?></td>
		<td><?php echo $stats['status']; ?></td>
		<td><?php
					if ( $stats['bitrate'] <= $maxBitrate) {
						echo $stats['bitrate']." (".$maxBitrate.")";
					} else {
						echo "<strong><span style='color: red;' title='Вы превысили доступный вам битрейт. В ближайшие минуты ваш сервер будет отключен. Используйте меньший или равный оплаченному значению битрейт.'>".$stats['bitrate']."</span></strong> (".$maxBitrate.")";
					}
			?>
		</td>
		<td><?php echo $stats['songTitle']; ?></td>

	</tr>
<?php
	 			} elseif (@$status == 'stoped') {
				//Сообщение незапущенном сервере
?>
	<tr>
		<td>
		<div style="display: inline;" class="icons">
			<ul id="icons_<?php echo $serverId;?>" class="ui-widget ui-helper-clearfix">
				<li class="ui-state-default ui-corner-all" title="Запустить сервер">
							<?php
							//Иконка для запуска сервера
							echo $this->Html->link('<span class="ui-icon ui-icon-play"></span>', '#',
												array ('id'=>'server_start_stop_'.$serverId, 'escape' => false
												,'onClick'=>""

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

							?>
				</li>
				<li class="ui-state-default ui-corner-all" title="Просмотр Лога">
							<?php
							//Иконка для просмотра лога
							echo $this->Html->link('<span class="ui-icon ui-icon-note"></span>', '#',
												array ('id'=>'server_log_'.$serverId, 'escape' => false
												,'onClick'=>""

												));
							$effect = $this->Js->get('#server_log_view_'.$serverId)->effect('slideIn');
							$event  = $this->Js->request(array('controller'=>'servers',
														 'action'=>'viewLog', $serverId),
												   array('update' => '#server_log_view_'.$serverId,
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide.";$('#server_log_view_$serverId').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 700});"
														 ));

							$this->Js->get('#server_log_'.$serverId)->event('click', $event);

							?>
				</li>
				<li class="ui-state-default ui-corner-all" title="Просмотр и смена паролей">
							<?php
							//Иконка для просмотра и смены паролей трансляции и Админа
							echo $this->Html->link('<span class="ui-icon ui-icon-locked"></span>', '#',
												array ('id'=>'shotcast_change_pass_'.$serverId, 'escape' => false
												,'onClick'=>""

												));
							$effect = $this->Js->get('#server_control_'.$serverId)->effect('slideIn');
							$event  = $this->Js->request(array('controller'=>'servers',
														 'action'=>'changeShoutcastPass', $serverId),
												   array('update' => '#server_password_'.$serverId,
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide.";$('#server_password_$serverId').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 400});"
														 ));

							$this->Js->get('#shotcast_change_pass_'.$serverId)->event('click', $event);

							?>
				</li>

			</ul>
		</div>

		</td>
		<td><?php echo $serverId; ?></td>
		<td colspan="7">Ошибка подключения. Сервер выключен?</td>
	</tr>

<?php
				} else {
?>
	<tr>
		<td colspan="8">Не могу запросить данные, попробуйте позднее<td>
	</tr>
<?php
				}

?>
</table>
		<div id="server_control_<?php echo $serverId;?>" style="display:none" title="Управление сервером #<?php echo $serverId;?>"></div>
		<div id="server_password_<?php echo $serverId;?>" style="display:none" title="Пароли сервера #<?php echo $serverId;?>"></div>
		<div id="server_control_start_<?php echo $serverId;?>" style="display:none" title="Управление сервером #<?php echo $serverId;?>: Запуск"><?php echo $this->Html->image("loading.gif");?></div>
		<div id="server_control_stop_<?php echo $serverId;?>" style="display:none" title="Управление сервером #<?php echo $serverId;?>: Остановка"></div>
		<div id="server_control_restart_<?php echo $serverId;?>" style="display:none" title="Управление сервером #<?php echo $serverId;?>: Перезапуск"></div>
		<div id="server_log_view_<?php echo $serverId;?>" style="display:none" title="Просмотр лога сервера #<?php echo $serverId;?>"></div>
		<div id="confirm" title="Подвердите смену пароля" style="display: none;">
</div>
<br/>
<?php
			echo $this->Html->link('Обновить', '#',
							array ('id'=>'server_refresh_'.$serverId, 'escape' => false));

			$event  = $this->Js->request(array('controller'=>'Servers',
									 'action'=>'viewServer', $serverId),
							   array('update' => '#server_more_'.$serverId,
									 'before'=>$loadingShow,
									 'complete'=>$loadingHide));

			$this->Js->get('#server_refresh_'.$serverId)->event('click', $event);

?>
&nbsp;&nbsp;&nbsp;&nbsp;

<?php
			echo $this->Js->writeBuffer(); // Write cached scripts
?>
<a href="#" onclick="$('#server_more_<?php echo $serverId; ?>').hide('highlight');  return false;">Скрыть</a>
