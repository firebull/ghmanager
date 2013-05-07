<?php
/*
 * Created on 28.12.2010
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
 //pr($status);
 
 if (!empty($status)){
 ?>
 <table class="servers_info">
 <?php	
 	foreach ( $status as $server ) {
 		
 		if ($server['status'] = 'running'){
 		?>	
 		<tr>
			<th colspan='2'>
			<?php
					echo $server['info']['name'];
			?>
			</th>
		</tr>
		<tr>
			<td class="info_name">
				IP:
			</td>
			<td class="info_result">
			<?php
					echo 
					"<a href='steam://connect/".
					$server['ip'].":".$server['port']."'>".
					$server['ip'].":".$server['port']
					."</a>";
			?>
			</td>
		</tr>
		<tr>
			<td class="info_name">
				Карта:
			</td>
			<td class="info_result">
			<?php
					echo $server['info']['map'];
			?>
			</td>
		</tr>
		<tr>
			<td class="info_name">
				Игроков:
			</td>
			<td class="info_result">
			
			<?php
					if ($server['info']['players'] < $server['info']['max']){
						echo '<span class="info_players_current">';
						echo $server['info']['players'];
						echo '</span>';
					}
					else if ($server['info']['players'] >= $server['info']['max']){
						echo '<span class="info_players_current_full">';
						echo $server['info']['players'];
						echo '</span>';
					}
					echo "\\";
					echo '<span class="info_players_max">';
					echo $server['info']['max'];
					echo '</span>';
			?>
			</td>
		</tr>	
 		<?php	
 		}
 		else if ($server['status'] = 'stoped'){
 		?>	
 		<tr>
			<th colspan='2'>
			Сервер временно выключен
			</th>
		</tr>
				<tr>
			<td class="info_name">
				IP:
			</td>
			<td class="info_result">
			<?php
					echo $server['ip'].":".$server['port'];
			?>
			</td>
		</tr>	
 		<?php	
 		}
       
	}
 	
 	
 }
 
?>
 </table>