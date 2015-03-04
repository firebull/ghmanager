<?php
/*
 * Created on 31.05.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
 $this->layout = 'ajax';
 $vac = array ('1'=>'Активен', '0' => 'Отключен');
 //pr($socket);
 include('loading_params.php');

 function styleMapName( $map = null) {

 	$cleanWords = array('l4d');
 	$changeWord = array('vs' => '(Versus)');
 	$words = split("_", $map);

 	$newMap='';
 	foreach ( $words as $word ) {
    	if (!in_array($word, $cleanWords)) {
    		if (!empty($changeWord[$word])) {
    			$newMap .= ' '.$changeWord[$word];
    		} else {
    			$newMap .= ' '.$word;
    		}
    	}
	}

 	if ( $newMap != '') {
 		return ucwords(trim($newMap));
 	} else {
 		return $map;
 	}
 }
?>
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

<?php

	$eventRefresh  = $this->Js->request(array('controller'=>'Servers',
							 'action'=>'viewServer', $serverId),
					   array('update' => '#server_more_'.$serverId,
							 'before'=>"init_".$serverId."(); refresh_".$serverId."(); $('#server_more_".$serverId."').showLoading({'addClass': 'loading-indicator-bars'});",
							 'complete'=>"$('#server_more_".$serverId."').hideLoading(); GetStatuses();"));

	$refreshFunc = 'setTimeout(function() { '.$eventRefresh.'; }, 6000 );';
?>

<br/>
<table class="serverSrcds" align="center">
	<tr>
		<th width="180">Управление</th>
		<th>Имя</th>
		<th>Версия</th>
		<th>Карта</th>
		<th>Игроков/Макс (Ботов)</th>
		<th>Пароль</th>
		<th>VAC</th>

	</tr>
<?php
if (!empty($info['Server']['info'])) {
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
												   array('update' => '#journal_'.$serverId,
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide.";GetStatuses();return false;"
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
												   array('update' => '#journal_'.$serverId,
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide.";"
														 ));

							$this->Js->get('#server_restart_'.$serverId)->event('click', $event);

							?>
				</li>
				<?php if ($update == 1) { // Если обновление включено, то выводим кнопку ?>
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
												   array('update' => '#journal_'.$serverId,
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide.";"
														 ));

							$this->Js->get('#server_update_'.$serverId)->event('click', $event);

							?>
				</li>
				<?php } // if?>
				<li class="ui-state-default ui-corner-all" title="RCON консоль">
							<?php
							//Иконка для RCON консоли
							echo $this->Html->link('<span class="ui-icon ui-icon-script"></span>', '#',
												array ('id'=>'rcon_'.$serverId, 'escape' => false
												,'onClick'=>""

												));
							$effect = $this->Js->get('#server_rcon')->effect('slideIn');
							$event  = $this->Js->request(array('controller'=>'servers',
														 'action'=>'rcon', $serverId),
												   array('update' => '#server_rcon',
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide.";$('#server_rcon').dialog({modal: true,position: ['center',80], show: 'clip', hide: 'clip', width: 700});"
														 ));

							$this->Js->get('#rcon_'.$serverId)->event('click', $event);

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
														 'complete'=>$loadingHide.";$('#server_log_view_$serverId').dialog({modal: true,position: ['center',60], show: 'clip', hide: 'clip', width: 1050});"
														 ));

							$this->Js->get('#server_log_'.$serverId)->event('click', $event);

							?>
				</li>
			</ul>
		</div>
		</td>
		<td><?php echo @$info['Server']['info']['serverName']; ?></td>
		<td><?php echo @$info['Server']['info']['gameVersion']; ?></td>
		<td>
			<?php
				echo styleMapName($info['Server']['info']['mapName']);
				if (!empty($info['Server']['info']['nextmap'])) {
					echo "<br/>";
					echo "<small>След. ".$info['Server']['info']['sm_nextmap']."</small>";
				}

			?>
		</td>
		<td>
			<?php
			if (!empty($info['Server']['info']['playerNumber'])) {

				$i=1;
				$playerForList = '';
				foreach ( $info['Server']['players'] as $player ) {

	       			$playerForList .= "<tr>";
	       			$playerForList .= "<td>".$i++."</td>";
	       			$playerForList .= '<td style="text-align: left;">';
	       			$playerForList .= $this->Text->truncate(
														    htmlspecialchars($player->getname()),
														    25,
														    array(
														        'ending' => '*',
														        'exact' => true
														    )
														);
					$playerForList .= "</td>";
					$playerForList .= '<td align="center">';

					if ($player->getscore() > 100000) {
	       						$playerForList .= '0';
	       					} else {
	       						$playerForList .= $player->getscore();
	       					}
	       			$playerForList .= "</td>";

					$playerForList .= '<td align="center">';

					$secondsFull = intval($player->getconnectTime());
									$minutesFull = intval($secondsFull/60);
									$hours = intval($minutesFull/60);
									$minutes = $minutesFull - $hours*60;
									$seconds = $secondsFull - $minutes*60;

					$playerForList .= sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);;

					$playerForList .= "</td>";
					$playerForList .= "</tr>\n";

				}

				$playersList = "

					<table cellpadding=4 cellspacing=2 border=0 class=score width=100%>
						<tr>
		       				<th>#</th>
		       				<th align=left>Игрок</th>
		       				<th>Счёт</th>
		       				<th>Время</th>
		       			</tr>

		       			$playerForList

		       		</table>
				";
			?>
			<a class="qlabs_tooltip_left qlabs_tooltip_style_1" href="#">
			<span>
			<?php
			echo $playersList;
			?>
			</span>
			<?php
				echo @$info['Server']['info']['playerNumber']."/".
				     @$info['Server']['info']['maxPlayers'];

			    if (@$info['Server']['info']['botNumber'] > 0) {
			     	echo " (".@$info['Server']['info']['botNumber'].")";
			    }
			?>
			</a>
			<?php

			} else {
				echo  @$info['Server']['info']['playerNumber']."/".
					  @$info['Server']['info']['maxPlayers'];

				if (@$info['Server']['info']['botNumber'] > 0) {
			     	echo " (".@$info['Server']['info']['botNumber'].")";
			     }
			}

			?>

		</td>
		<td>
			<?php

			if (!empty($info['Server']['info']['passwordProtected'])) {
				echo "Установлен";
			} else {
				echo "Нет";
			}

			?>
		</td>
		<td><?php echo @$vac[ @$info['Server']['info']['secureServer'] ]; ?></td>
	</tr>
<?php
	 			} else {
				//Сообщение об ошибке при таймауте соединения
?>
	<tr>
		<td valign="top">
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
												   array('update' => '#journal_'.$serverId,
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide.";"
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
												   array('update' => '#journal_'.$serverId,
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide.";"
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
												   array('update' => '#journal_'.$serverId,
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide.";"
														 ));

							$this->Js->get('#debug_server_start_'.$serverId)->event('click', $event);

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
												   array('update' => '#journal_'.$serverId,
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide.";"));

							$this->Js->get('#server_update_'.$serverId)->event('click', $event);

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
														 'complete'=>$loadingHide.";$('#server_log_view_$serverId').dialog({modal: true,position: ['center',60], show: 'clip', hide: 'clip', width: 1050});"
														 ));

							$this->Js->get('#server_log_'.$serverId)->event('click', $event);

							?>
				</li>

			</ul>
		</div>
		</td>
		<td colspan="6" >

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
									 "Если сервер требует обновления, процесс запуска может затянуться. " .
									 "Читайте соответсвующий лог.";
								echo "</div>";
							} else {
								echo '<div class="ui-state-highlight ui-helper-clearfix ui-corner-all" style="padding: 8px; margin: 5px;">'."\n";
								echo '<span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>'."\n";
								echo "Сервер был запущен более 5 минут назад, но до сих пор не удаётся " .
									 "прочесть его статус.<br/>" .
									 "Если вы не включали читы параметром 'sv_cheats 1', то возможно, " .
									 "что сервер обновляется. В этом случае процесс запуска может затянуться. " .
									 "Читайте соответсвующий лог. <br/>" .
									 "Также вероятной проблемой может быть то, что вы внесли IP вашего сервера в бан-лист. " .
									 "Проверьте banned_ip.cfg на наличие в нем IP-адреса вашего сервера.<br/>" .
									 "Если в логе нет данных, либо есть ошибки, " .
									 "попробуйте перезапустить сервер. <br/>" .
									 "Если не поможет - обратитесь в техподдержку.";
								echo "<br/> <small>Время статуса: ".$this->Time->nice($status['statusTime'])."</small>";
								echo "</div>";
							}

							break;

						case "update_error":
							echo '<div class="ui-state-error ui-corner-all" style="padding: 8px; margin: 5px;">' .
								 '<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> ';
							echo "Ошибка обновления сервера. <br/>" .
								 "Попробуйте повторить обновление. <br/>" .
								 "Если не поможет - обратитесь в техподдержку.";
							echo "<br/> <small>Время статуса: ".$this->Time->nice($status['statusTime'])."</small>";
							echo "</div>";
							break;

						case "update_started":
							echo $this->element('action_progress', array('id' => $serverId));
							echo '<div class="ui-state-highlight ui-helper-clearfix ui-corner-all" style="padding: 8px; margin: 5px;">'."\n";
							echo '<span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>'."\n";
							echo "Запущено обновление сервера. <br/>" .
								 "О состоянии обновления вы можете прочитать в соответсвующем логе.<br/>" .
								 "После завершения обновления вам необходимо запустить сервер вручную и " .
								 "это сообщение исчезнет.";
							echo "<br/> <small>Время статуса: ".$this->Time->nice($status['statusTime'])."</small>";
							echo "</div>";

							echo $this->Html->scriptStart();
							echo "$('#progressBar_$serverId').showStatusTs($serverId, 'update');";
							echo $this->Html->scriptEnd();
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
				}
?>
</table>

<?php echo $this->element('graphs', array( 'graphs'=>$graphs )); ?>

<div id="server_control_<?php echo $serverId;?>" style="display:none" title="Управление сервером #<?php echo $serverId;?>"></div>
<div id="server_log_view_<?php echo $serverId;?>" style="display:none" title="Просмотр лога сервера #<?php echo $serverId;?>"></div>

<br/>

<?php
	echo $this->Html->link('Обновить статус', '#',
					array ('id'=>'server_refresh_'.$serverId, 'escape' => false));
	$this->Js->get('#server_refresh_'.$serverId)->event('click', $eventRefresh);

?>
&nbsp;&nbsp;&nbsp;&nbsp;
<?php
			echo $this->Js->writeBuffer(); // Write cached scripts
?>
<a href="#" onclick="$('#server_more_<?php echo $serverId; ?>').hide('highlight'); GetStatuses(); $('#server_log_view_<?php echo $serverId; ?>').empty(); return false;">Скрыть</a>
