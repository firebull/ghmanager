<?php
/*
 * Created on 14.01.2011
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
 // pr($status);
 include('loading_params.php');
 echo $this->Html->script(array (
        'https://ajax.googleapis.com/ajax/libs/jquery/1.7.2/jquery.min.js',
        'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.19/jquery-ui.min.js'
    ));
 ?>
 <table>
	<tr>
		<td align="right"><?php echo $this->Html->image('icons/servers/'.$status['gameshort'].'.png',
														array('alt'=>$status['gamefull'], 'width'=>'24', 'height'=>'24')); ?></td>
		<td><?php echo $status['gamefull'];?></td>
	</tr>
	<tr>
		<td align="right">IP:</td>
		<td>
		<?php
			echo $status['ip'].":".$status['port'];
		?>
		</td>
	</tr>
	<?php
 	if (@$status['status'] == 'stoped') {
 	?>
	<tr>
		<td colspan="2" align="center">
			<strong>
				<span style="color: #900;">Сервер выключен</span>
			</strong>
		</td>
	</tr>
	<tr>
		<td></td>
		<td>
		<div style="display: inline;" class="icons">
			<ul id="icons_<?php echo $status['id'];?>">
				<li title="Запустить сервер">
							<?php
							//Иконка для запуска сервера
							echo $this->Html->link('<i class="icon-play"></i>',
											 array('controller'=>'servers',
												   'action'=>'script',
												   $status['id'],
												   'start',
												   @$token),
											 array ('escape' => false,
											 	    'class' => 'btn'));
							?>
				</li>
				<li title="Принудительно запустить сервер">
							<?php
							//Иконка для перезапуска сервера
							echo $this->Html->link('<i class="icon-step-forward"></i>',
											 array('controller'=>'servers',
												   'action'=>'script',
												   $status['id'],
												   'restart',
												   @$token),
											 array ('escape' => false,
											 	    'class' => 'btn'));

							?>
				</li>
			</ul>
		</div>
		</td>
	</tr>
	<?php
	 } else {
	?>
	<tr>
		<td></td>
		<td>
		<div style="display: inline; height: 30px;" class="icons">
			<ul id="icons_<?php echo $status['id'];?>">
				<li title="Остановить сервер">
							<?php
							//Иконка для остановки сервера
							echo $this->Html->link('<i class="icon-stop"></i>',
											 array('controller'=>'servers',
												   'action'=>'script',
												   $status['id'],
												   'stop',
												   @$token),
											 array ('escape' => false,
											 	    'class' => 'btn'));
							?>
				</li>
				<li title="Перезапустить сервер">
							<?php
							//Иконка для перезапуска сервера
							echo $this->Html->link('<i class="icon-step-forward"></i>',
											 array('controller'=>'servers',
												   'action'=>'script',
												   $status['id'],
												   'restart',
												   @$token),
											 array ('escape' => false,
											 	    'class' => 'btn'));

							?>
				</li>
			</ul>
		</div>
		</td>
	</tr>
	<tr>
		<td align="right">Имя:</td>
		<td>
		<?php
			echo $status['serverName'];
		?>
		</td>
	</tr>
	<tr>
		<td align="right">Карта:</td>
		<td>
		<?php

			if (in_array($status['gameshort'], array('cs16', 'cs16-old', 'css', 'cssv34',
													'l4d', 'l4d-t100', 'l4d2', 'l4d2-t100',
													'csgo', 'csgo-t128',
													'dods', 'tf',
													'dmc', 'hl1',
													'hl2mp', 'zps'))) {
				if (!empty($status['mapName'])) {
					//Иконка для RCON смены карты
					echo $this->Html->link($status['mapName'].' <i class="icon-share"></i>', '#',
										array('id'=>'rcon_map_'.$status['id'],
											  'escape' => false,
											  'title' => "Кликните, чтобы сменить карту на включенном сервере",
											  'style' => ''

										));
					$effect = $this->Js->get('#server_auto_rcon')->effect('slideIn');
					$event  = $this->Js->request(array('controller'=>'servers',
												 'action'=>'setMapRcon', $status['id']),
										   array('update' => '#server_auto_rcon',
												 'before'=>$loadingShow,
												 'complete'=>$loadingHide.";$('#server_auto_rcon').dialog({modal: true,position: ['center',80], show: 'clip', hide: 'clip', width: 500, height: 400, title: 'Cменить карту на включенном сервере #".$status['id']."'});"
												 ));

					$this->Js->get('#rcon_map_'.$status['id'])->event('click', $event);
				}
			} else {
				echo $status['mapName'];
			}
		?>
		</td>
	</tr>
	<tr>
		<td align="right">Игроков:</td>
		<td>
		<?php
			echo $status['numberOfPlayers']."/".$status['maxPlayers']." (".$status['botNumber']." ботов)";
		?>
		</td>
	</tr>
	<tr>
		<td align="right">Пароль:</td>
		<td>
			<?php

			if (!empty($status['passwordProtected'])) {
				echo "Установлен";
			} else {
				echo "Не установлен";
			}

			?>
		</td>
	</tr>
	<tr>
		<td align="right">Версия:</td>
		<td>
			<?php
			echo $this->Common->versionStatus(@$currentVersion, @$status['gameVersion'], 'srcds');
		?>
		</td>
	</tr>
	<?php
	 }
	?>
</table>
<div id="server_auto_rcon" style="display:none" title="Выполнение команд на включенном сервере"></div>
<?php
	echo $this->Js->writeBuffer(); // Write cached scripts
?>
