<?php
/*
 * Created on 18.03.2011
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
 $vac = array ('1'=>'Активен', '0' => 'Отключен',
 			   true=>'Активен', false => 'Отключен');
 $yesNo = array('1' => 'Да', '0' => 'Нет',
 			    true => 'Да', false => 'Нет');
 $setNo = array('1' => 'Установлен', '0' => 'Нет',
 				true => 'Установлен', false => 'Нет');

 include('loading_params.php');
?>
<br/>
<script type="text/javascript">
		function init_<?php echo $serverId; ?> () {
			$('#server_control_<?php echo $serverId; ?>').empty();
			$('#server_control_<?php echo $serverId; ?>').dialog('destroy');
			$('#server_log_view_<?php echo $serverId; ?>').empty();
			$('#server_log_view_<?php echo $serverId; ?>').dialog('destroy');
		}

		function refresh_<?php echo $serverId; ?>() {
			$('#server_control_<?php echo $serverId; ?>').remove();
			$('#server_log_view_<?php echo $serverId; ?>').remove();
		}

		init_<?php echo $serverId; ?>();

</script>

<table class="serverSrcds" align="center">
	<tr>
		<th width="220">Управление</th>
		<th>Имя</th>
		<th>Версия</th>
		<th>Карта</th>
		<th>Игроков/Макс</th>
		<th>Пароль</th>
		<th>VAC</th>
	</tr>
<?php
if (!empty($info)) {
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
							$effect = $this->Js->get('#server_control_'.$serverId)->effect('slideIn');
							$event  = $this->Js->request(array('controller'=>'servers',
														 'action'=>'script', $serverId,'stop'),
												   array('update' => '#server_control_'.$serverId,
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide.";$('#server_control_$serverId').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});GetStatuses();return false;"
														 ));

							$this->Js->get('#server_start_stop_'.$serverId)->event('click', $event);

							?>
				</li>
				<li class="ui-state-default ui-corner-all" title="Перезапустить сервер">
							<?php
							//Иконка для перезапуска сервера
							echo $this->Html->link('<span class="ui-icon ui-icon-refresh"></span>', '#',
												array ('id'=>'server_restart_'.$serverId, 'escape' => false
												,'onClick'=>""

												));
							$effect = $this->Js->get('#server_control_'.$serverId)->effect('slideIn');
							$event  = $this->Js->request(array('controller'=>'servers',
														 'action'=>'script', $serverId,'restart'),
												   array('update' => '#server_control_'.$serverId,
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide.";$('#server_control_$serverId').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});"
														 ));

							$this->Js->get('#server_restart_'.$serverId)->event('click', $event);

							?>
				</li>

				<li class="ui-state-default ui-corner-all" title="Открыть собственную панель администрирования KillingFloor">
					<?php
							//Иконка для Web Admin Panel for KillinfFloor
							echo $this->Html->link('<span class="ui-icon ui-icon-wrench"></span>',
											 'http://'.@$info['server']['ip'].':'.@$info['server']['panelPort'],
												array ( 'id'=>'rcon_'.$serverId,
														'escape' => false,
														'target'=>"_blank"

												));

							?>
				</li>
				<li class="ui-state-default ui-corner-all" title="Просмотр Логов">
					<?php
					//Иконка для просмотра лога
					echo $this->Html->link('<span class="ui-icon ui-icon-note"></span>', '#',
										array ('id'=>'server_log_'.$serverId, 'escape' => false
										));
					$effect = $this->Js->get('#server_log_view_'.$serverId)->effect('slideIn');
					$event  = $this->Js->request(array('controller'=>'servers',
												 'action'=>'viewLog', $serverId),
										   array('update' => '#server_log_view_'.$serverId,
												 'before'=>$loadingShow.";$('#server_log_view_$serverId').empty();",
												 'complete'=>$loadingHide.";$('#server_log_view_$serverId').dialog({modal: true,position: ['center',60], show: 'clip', hide: 'clip', width: 900});"
												 ));

					$this->Js->get('#server_log_'.$serverId)->event('click', $event);

					?>
				</li>
			</ul>
		</div>
		</td>
		<td><?php echo @$info['server']['hostname'];?></td>
		<td><?php echo @$info['server']['version'];?></td>
		<td><?php
				  echo @$info['server']['mapname'];
			?>
		</td>
		<td><?php

					if (@$info['server']['clients'] == 0) {
						echo @$info['server']['clients']."/".@$info['server']['maxclients'];
					} elseif (@$info['server']['clients'] > 0) {
			?>
					<a class="playersTooltip" href="#" rel="#players_<?php echo $serverId; ?>">
			<?php
					echo @$info['server']['clients']."/".@$info['server']['maxclients'];
			?>
					</a>
					<div id="players_<?php echo $serverId; ?>" style="display: none;">
					<table cellpadding="4" cellspacing="2" border="0" class="score" width="100%">
					<tr>
	       				<th>#</th>
	       				<th align="left">Игрок</th>
	       				<th>Счёт</th>
	       				<th>Ping</th>
	       			</tr>
			<?php
						foreach ( $info['server']['players'] as $i => $player ) {
	       	?>
	       				<tr>
	       					<td><?php echo $i+1;?></td>
	       					<td style="text-align: left;"><?php echo $player['name'];?></td>
	       					<td><?php echo $player['score'];?></td>
	       					<td><?php echo $player['ping']."ms";?></td>
	       				</tr>
	       	<?php
						}

			?>
					</table>
					</div>
			<?php
					}
			?>
		</td>
		<td><?php echo $setNo[@$info['server']['pswrd']]; ?></td>
		<td><?php echo $vac[@$info['server']['vac']]; ?></td>
	</tr>

<?php } else {
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
							$effect = $this->Js->get('#server_control_'.$serverId)->effect('slideIn');
							$event  = $this->Js->request(array('controller'=>'servers',
														 'action'=>'script', $serverId,'start'),
												   array('update' => '#server_control_'.$serverId,
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide.";$('#server_control_$serverId').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});"
														 ));

							$this->Js->get('#server_start_stop_'.$serverId)->event('click', $event);

							?>
				</li>
				<li class="ui-state-default ui-corner-all" title="Принудительно запустить сервер (в случае ошибок запуска)">
							<?php
							//Иконка для принудительного запуска сервера
							echo $this->Html->link('<span class="ui-icon ui-icon-eject"></span>', '#',
												array ('id'=>'force_server_start_stop_'.$serverId, 'escape' => false
												,'onClick'=>""

												));
							$effect = $this->Js->get('#server_control_'.$serverId)->effect('slideIn');
							$event  = $this->Js->request(array('controller'=>'servers',
														 'action'=>'script', $serverId,'restart'),
												   array('update' => '#server_control_'.$serverId,
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide.";$('#server_control_$serverId').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});"
														 ));

							$this->Js->get('#force_server_start_stop_'.$serverId)->event('click', $event);

							?>
				</li>
				<li class="ui-state-default ui-corner-all" title="Запустить сервер в режиме отладки">
							<?php
							//Иконка для запуска сервера в режиме отладки
							echo $this->Html->link('<span class="ui-icon ui-icon-circle-triangle-e"></span>', '#',
												array ('id'=>'debug_server_start_'.$serverId, 'escape' => false
												,'onClick'=>""

												));
							$effect = $this->Js->get('#server_control_'.$serverId)->effect('slideIn');
							$event  = $this->Js->request(array('controller'=>'servers',
														 'action'=>'script', $serverId,'startDebug'),
												   array('update' => '#server_control_'.$serverId,
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide.";$('#server_control_$serverId').dialog({modal: true,position: ['center',130], show: 'clip', hide: 'clip', width: 600});"
														 ));

							$this->Js->get('#debug_server_start_'.$serverId)->event('click', $event);

							?>
				</li>
				<li class="ui-state-default ui-corner-all" title="Запустить сервер в режиме установки пароля администратора">
							<?php
							//Иконка для запуска сервера в режиме установки пароля администратора
							echo $this->Html->link('<span class="ui-icon ui-icon-locked"></span>', '#',
												array ('id'=>'setpass_server_start_'.$serverId, 'escape' => false
												,'onClick'=>""

												));
							$effect = $this->Js->get('#server_control_'.$serverId)->effect('slideIn');
							$event  = $this->Js->request(array('controller'=>'servers',
														 'action'=>'script', $serverId,'setServerPass'),
												   array('update' => '#server_control_'.$serverId,
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide.";$('#server_control_$serverId').dialog({modal: true,position: ['center',130], show: 'clip', hide: 'clip', width: 600});"
														 ));

							$this->Js->get('#setpass_server_start_'.$serverId)->event('click', $event);

							?>
				</li>
				<li class="ui-state-default ui-corner-all" title="Обновить сервер">
						<?php
						//Иконка для обновления сервера
						echo $this->Html->link('<span class="ui-icon ui-icon-circle-arrow-n"></span>', '#',
											array ('id'=>'server_update_'.$serverId, 'escape' => false
											,'onClick'=>""

											));
						$effect = $this->Js->get('#server_control_'.$serverId)->effect('slideIn');
						$event  = $this->Js->request(array('controller'=>'servers',
													 'action'=>'script', $serverId,'update'),
											   array('update' => '#server_control_'.$serverId,
													 'before'=>$loadingShow,
													 'complete'=>$loadingHide.";$('#server_control_$serverId').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});"
													 ));

						$this->Js->get('#server_update_'.$serverId)->event('click', $event);

						?>
				</li>
				<li class="ui-state-default ui-corner-all" title="Просмотр Лога">
					<?php
					//Иконка для просмотра лога
					echo $this->Html->link('<span class="ui-icon ui-icon-note"></span>', '#',
										array ('id'=>'server_log_'.$serverId, 'escape' => false
										));
					$effect = $this->Js->get('#server_log_view_'.$serverId)->effect('slideIn');
					$event  = $this->Js->request(array('controller'=>'servers',
												 'action'=>'viewLog', $serverId),
										   array('update' => '#server_log_view_'.$serverId,
												 'before'=>$loadingShow.";$('#server_log_view_$serverId').empty();",
												 'complete'=>$loadingHide.";$('#server_log_view_$serverId').dialog({modal: true,position: ['center',60], show: 'clip', hide: 'clip', width: 900});"
												 ));

					$this->Js->get('#server_log_'.$serverId)->event('click', $event);

					?>
				</li>
			</ul>
			</div>
		</td>
		<td colspan="7">
			<?php
				if (!empty($status['status'])) {
					echo '<span style="text-align: left;">';

					switch ( $status['status'] ) {
							case "exec_error":
								echo '<div class="ui-state-error ui-corner-all" style="padding: 8px; margin: 5px;">' .
									 '<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> ';
								echo "Ошибка запуска. <br/>" .
									 "Попробуйте перезапустить сервер. <br/>" .
									 "Если не поможет - обратитесь в техподдержку.";
							    echo "<br/> <small>Время статуса: ".$this->Time->nice($status['statusTime'])."</small>";
							    echo "</div>";
								break;

							case "exec_success":
								if ($this->Time->wasWithinLast('5 minutes', $status['statusTime'])) {
									echo '<div class="ui-state-highlight ui-helper-clearfix ui-corner-all" style="padding: 8px; margin: 5px;">'."\n";
									echo '<span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>'."\n";
									echo "Сервер запущен менее 5 минут назад. <br/>" .
										 "О текущем состоянии запуска читайте соответсвующий лог.";
									echo "</div>";
								} else {
									echo '<div class="ui-state-highlight ui-helper-clearfix ui-corner-all" style="padding: 8px; margin: 5px;">'."\n";
									echo '<span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>'."\n";
									echo "Сервер был запущен более 5 минут назад, но до сих пор не удаётся " .
										 "прочесть его статус. Вероятно возникла ошибка. " .
										 "Читайте соответсвующий лог. <br/>" .
										 "Если в логе нет данных, либо есть ошибки, " .
										 "попробуйте перезапустить сервер. <br/>" .
										 "Если не поможет - обратитесь в техподдержку.";
									echo "<br/> <small>Время статуса: ".$this->Time->nice($status['statusTime'])."</small>";
									echo "</div>";
								}

								break;

							case "stoped":
							case "stopped":
								echo '<div class="ui-state-highlight ui-helper-clearfix ui-corner-all" style="padding: 8px; margin: 5px;">'."\n";
								echo '<span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>'."\n";
								echo "Сервер выключен в ".$this->Time->nice($status['statusTime']).". ";
								if (@$status['statusDescription']) {
									echo "Причина: ".$status['statusDescription'];
								}
								echo "</div>";
								break;

							default:
								echo "Ошибка подключения. Сервер выключен?";
								break;
						}

				echo "</span>";
				} else {
					echo "Ошибка подключения. Сервер еще ни разу не включался?";
				}


			?>
		</td>
	</tr>
<?php
	  } ?>
</table>
<div id="server_control_<?php echo $serverId;?>" style="display:none" title="Управление сервером #<?php echo $serverId;?>"></div>
<div id="server_log_view_<?php echo $serverId;?>" style="display:none" title="Просмотр лога сервера #<?php echo $serverId;?>"></div>

<br/>
<?php
			echo $this->Html->link('Обновить', '#',
							array ('id'=>'server_refresh_'.$serverId, 'escape' => false));

			$event  = $this->Js->request(array('controller'=>'Servers',
									 'action'=>'viewServer', $serverId),
							   array('update' => '#server_more_'.$serverId,
									 'before'=>"init_".$serverId."(); refresh_".$serverId."(); $('#server_more_".$serverId."').showLoading({'addClass': 'loading-indicator-bars'});",
									 'complete'=>"$('#server_more_".$serverId."').hideLoading(); GetStatuses();"));

			$this->Js->get('#server_refresh_'.$serverId)->event('click', $event);

?>
&nbsp;&nbsp;&nbsp;&nbsp;
<?php
			echo $this->Js->writeBuffer(); // Write cached scripts
?>
<a href="#" onclick="$('#server_more_<?php echo $serverId; ?>').hide('highlight'); GetStatuses(); $('#server_log_view_<?php echo $serverId; ?>').empty(); return false;">Скрыть</a>
