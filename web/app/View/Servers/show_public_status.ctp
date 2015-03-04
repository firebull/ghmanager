<?php
/*
 * Created on 25.04.2011
 *
 * File created for project TeamServer(Git)
 * by nikita
 */
 //pr($status);

echo "<!-- status start -->";
echo $this->Form->create(null, array( 'url' => 'http://www.teamserver.ru/showServersList',
											'type' => 'GET'));
?>
<table>
	<tr>
		<td>Выбрать игру:</td>
		<td>
		<?php

			echo $this->Form->input('game', array(   'options' => @$gameTemplatesList,
											   'selected' => @$gameTemplateCurrent,
											   'onchange' => 'this.form.submit();',
											   'div' => false,
											   'label' => false));
			echo $this->Form->input('size', array( 'type' => 'hidden',
											   'value' => 'all'));

			echo $this->Form->input('output', array( 'type' => 'hidden',
											   'value' => 'html'));



		 ?>
		</td>
		<td>
			<?php
				//echo $this->Form->submit('Выбрать');
			?>
		</td>
	</tr>
</table>
<?php
	echo $this->Form->end();
	if (!empty($status)) {
?>
<table class="params" border="0" cellpadding="5" cellspacing="0">
<?php

	foreach ( $status as $server ) {
		if ($server['status'] == 'running') {

	?>
		<tr valign="top">
			<td><?php echo $this->Html->image('https://panel.teamserver.ru/img/icons/servers/'.$server['gameshort'].'.png',
														array('alt'=>$server['gamefull'],
														'title'=>$server['gamefull'],
														'width'=>'24',
														'height'=>'24'));
														?></td>
			<td style="min-width: 330px;"><?php echo "<strong>".$server['serverName']."<br/>";
					  echo "<small>".$server['mapName']."</small></strong><br/>";
				?>
				<div style="max-width: 250px;">
					<?php echo  @htmlspecialchars($server['desc']); ?>
				</div>
			</td>

			<?php if ($mapsImages === true) { ?>
			<td>
				<?php
					if (!empty($server['mapImage'])) {

				?>
					<a id="<?php echo 'map_desc_'.$server['mapImage']['id']; ?>" style="display: none;" href="#" class="qlabs_tooltip_diagonal_left qlabs_tooltip_style_1">
						<span>
						<strong><?php echo @$server['mapImage']['name']; ?></strong>
						<?php
							echo $this->Html->image('http://www.teamserver.ru/images/gameMaps/'.$server['mapImage']['id'].'.jpg',
												array(	'alt'  => @$server['mapImage']['name'],
														'width'  => 320,
														'height' => 240));
						?>
						</span>
						<?php
						echo $this->Html->image( 'http://www.teamserver.ru/images/gameMaps/'.$server['mapImage']['id'].'_thumb.jpg',
											array(  'alt'  => @$server['mapImage']['name'],
													'width'  => 100,
													'height' => 75));
						?>
					</a>
				<?php
					} else {
						echo $this->Html->image('https://panel.teamserver.ru/img/personage01_x100x75.png', array('title'  => 'Нет изображения карты',
																		   'width'  => 100,
																		   'height' => 75));
					}
				?>
			</td>
			<?php } ?>
			<td><?php

					echo $server['playerNumber']."/".$server['maxPlayers'];

					$load = ($server['playerNumber']/$server['maxPlayers'])*100;

					if ($load == 0) {
						$div = 'players_load_free';
					} elseif ($load >= 0 and $load <= 20) {
						$div = 'players_load_20';
					} elseif ($load > 20 and $load <= 40) {
						$div = 'players_load_40';
					} elseif ($load > 40 and $load <= 60) {
						$div = 'players_load_60';
					} elseif ($load > 60 and $load <= 80) {
						$div = 'players_load_80';
					} elseif ($load > 80 and $load < 100) {
						$div = 'players_load_100';
					} elseif ($load >= 100) {
						$div = 'players_load_full';
					}

					echo $this->Html->tag('div','', array('class' => 'players_load '.$div));

				?>
			</td>
			<td><?php

				if ($server['type'] == 'srcds' or $server['type'] == 'hlds') {
					echo '<a title="Подключиться к серверу" href="steam://connect/'.$server['ip'].":".$server['port'].'">'.$server['ip'].":".$server['port'].'</a><br/>';
					echo '<small><strong>'.$server['location'].'<strong></small>';
				} else {
					echo $server['ip'].":".$server['port'].'<br/>';
					echo '<small><strong>'.$server['location'].'<strong></small>';
				}
				?></td>
		</tr>

<?php 	}

	}
?>
</table>
<?php
	  } else {
	  	echo "Нет доступных публичных серверов для этой игры.";
	  }
?>
<?php echo "<!-- status end -->";?>
<p style="	background: url('http://www.teamserver.ru/plugins/content/xtypo/icon/info.gif') center no-repeat;
			background-color:#F8FAFC ; background-position: 15px 50%; text-align: left;
			padding: 5px 20px 5px 45px; color:#5E6273; border-top: 2px solid #B5D4FE ;
			border-bottom: 2px solid #B5D4FE;">
	Если вы хотите, чтобы ваш сервер отображался в этом списке, напишите об этом в техподдержку.
</p>
