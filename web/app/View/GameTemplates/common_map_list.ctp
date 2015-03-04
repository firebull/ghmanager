<?php

include('loading_params.php');

?>

<small>**: Первый символ - наличие изображения, второй - описания.</small>

<cake:nocache>

<div id="flash"><?php echo $this->Session->flash(); ?></div>

<div class="vertical_scroll">
	<table class="params" width="100%" cellpadding="0" cellspacing="5">
	<tr>
	<?php

		foreach ( $this->data['MapList'] as $game => $gameMaps ) {
	?>
		<th valign="top">
		<?php  echo $game; ?>
		</th>
    <?php
		}

	?>
	</tr>
	<tr>
	<?php

		foreach ( $this->data['MapList'] as $game => $gameMaps ) {
	?>
	<td valign="top" style="padding-top: 5px;">

	<?php
			$mupNums[$game] = 0;
			foreach ( $gameMaps as $map ) {

				if ($map['Map']['image'] === true){
					$mapState = '*';
				}
				else
				{
					$mapState = '-';
				}

				if (!empty($map['Map']['desc'])){
					$mapState .= '*';
				}
				else
				{
					$mapState .= '-';
				}


				echo $this->Html->link($mapState.'&nbsp;'.$map['Map']['name'], '#',
							array ('id'=>'edit_map_'.$map['Map']['id'], 'escape' => false
							,'onClick'=>"$('#edit_map').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});"

							));
				$effect = $this->Js->get('#edit_map')->effect('slideIn');
				$event  = $this->Js->request(array('controller'=>'GameTemplates',
											 'action'=>'editMap',
											 $map['Map']['id']),
									   array('update' => '#edit_map',
											 'before'=>$loadingShow,
											 'complete'=>$loadingHide));

				$this->Js->get('#edit_map_'.$map['Map']['id'])->event('click', $event);

       			echo '</br>';

       			if ($map['Map']['official'] == 0){
       				$mupNums[$game]++;
       			}
			}
	?>

	</td>
    <?php
		}

	?>
	</tr>
	<tr>
	<?php

		foreach ( $mupNums as $game => $mapNum ) {
	?>
		<th valign="top">
		<?php  echo 'Всего дополнительных карт: '.$mapNum; ?>
		</th>
    <?php
		}

	?>
	</tr>
	</table>
</div>
<div id="edit_map" style="display:none" title="Изменить параметры карты"></div>
<?php
	  echo $this->Js->writeBuffer(); // Write cached scripts
?>
