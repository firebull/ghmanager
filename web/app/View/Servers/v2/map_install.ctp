<?php
/*
 * Created on 22.06.2011
 *
 * File created for project TeamServer
 * by nikita
 */
 include('loading_params.php');

 function mapLink( $serverId = null, $mapId = null, $official = false, $installed = null, $canDelete = false, $on = false, $html = null) {
 	if ($serverId !== null and $mapId !== null) {
	 	// официальная карта установлена и включена
		if ( $official  === true
				and
			 $installed === true
			 	and
			 $on === true) {

			return $html->tag('button','<i class="icon ban"></i> Выключить',
							   array( 'onClick' => "mapAction('".$serverId."', '".$mapId."', 'turnOff');",
							   		  'escape' => false,
							   		  'class' => 'ui small orange button') );



		}
		// карта выключена
		elseif ( $canDelete === true
				and
			 $installed === true
			 	and
			 $on === false) {
			return $html->tag('button','<i class="icon plus"></i> Включить',
							   array( 'onClick' => "mapAction('".$serverId."', '".$mapId."', 'turnOn');",
							   		  'escape' => false,
							   		  'class' => 'ui small green button') );

		}
		else
		//  Неофициальная карта известная нам
		if ( $canDelete === true
				and
			 $installed !== null
			 	and
			 $installed === true
			 	and
			 $official === false) {

			return $html->tag('button','<i class="icon remove circle"></i> Удалить',
							   array( 'onClick' => "mapAction('".$serverId."', '".$mapId."', 'delete');",
							   		  'escape' => false,
							   		  'class' => 'ui small red button') );


		} elseif ( $canDelete === true and $installed === false) {
			return $html->tag('button','<i class="icon plus"></i> Установить',
							   array( 'onClick' => "mapAction('".$serverId."', '".$mapId."', 'install');",
							   		  'escape' => false,
							   		  'class' => 'ui small green button') );

		} else {
			return false;
		}

	} else {
		return false;
	}
 }

?>

<?php
	echo $this->Html->link('<i class="grid layout icon" id="mosaic_i"></i> Мозаикой',
						'#',
						array('class' => 'ui right floated button',
							   'id' => 'mosaic',
							   'escape' => false));

	$event  = $this->Js->request(array (
									'controller'=>'Servers',
									'action'=>'mapInstall',
									$id,
									'all',
									$mapTypeActive,
									0,
									'html',
									'mosaic'
									),
						   array(	'update' => '#map_install',
								 	'before'=>$loadingShow,
								 	'complete'=>$loadingHide
								 	));

	$this->Js->get('#mosaic')->event('click', $event);

	echo $this->Html->link('<i class="list layout icon" id="list_i"></i> Списком',
						'#',
						array( 'class' => 'ui right floated button',
							   'id' => 'list',
							   'escape' => false));

	$effect = $this->Js->get('#map_install')->effect('slideIn');
	$event  = $this->Js->request(array (
									'controller'=>'Servers',
									'action'=>'mapInstall',
									$id,
									'all',
									$mapTypeActive,
									0,
									'html',
									'list'
									),
						   array(	'update' => '#map_install',
								 	'before'=>$loadingShow,
								 	'complete'=>$loadingHide
								 	));

	$this->Js->get('#list')->event('click', $event);

?>


<div style="clear:both;"></div>
<br/>
<cake:nocache>
	<div id="flash"><?php echo $this->Session->flash(); ?></div>
	<?php echo $this->element('v2/map_install_top_menu', array ( 'serverId' => $id,
															  'mapTypes' => $mapTypes,
															  'mapTypeActive' => $mapTypeActive

															  )); ?>
<?php
	if (!empty($maps)) {
?>
<div class="ui grid" style="margin-top:15px !important;">
<?php
	// Отображение мозаикой
	if ($output == 'mosaic') {
?>
	<table class="params" width="100%" cellpadding="0" cellspacing="5">
	<?php
	$i = 0;
	$j = 1;
	foreach ($maps as $mapName => $mapDesc):
	if ($i == 0) {

		echo "<tr>\n";
	}
	if (!empty($mapDesc['installed']) and $mapDesc['installed'] === true and $mapDesc['on'] === true) {
	?>
	<td width="280" valign="top" class="map_installed">
	<?php
	} else {
	?>
	<td width="280" valign="top" style="background-color: #fff;">
	<?php
	}
	?>
		<div>
		<table width="100%">

		<?php if (is_array($mapDesc)) { ?>
		<tr>
			<td rowspan="2" width="120" align="center">
			<?php
				if (!empty($mapDesc['image'])) {
					echo $this->Html->image('gameMaps/'.$mapDesc['image'].'_thumb.jpg', array('title'  => @$mapDesc['longname'],
																						'width'  => 100,
																						'height' => 75));
				} else {
					echo $this->Html->image('personage01_x100x75.png', array('title'  => 'Нет изображения карты',
																	   'width'  => 100,
																	   'height' => 75));
				}

			?>
			</td>
			<td align="left" valign="top">
			<?php

				if (!empty($mapDesc['longname'])) {

					if (!empty($mapDesc['image'])) {
						$mapImg = $this->Html->image('gameMaps/'.$mapDesc['image'].'.jpg');
					} else {
						$mapImg = $this->Html->image('personage01_x100x75.png', array('title' => 'Нет изображения карты',
																				'style' => 'margin-right: 30px;'));
					}

					$mapHeader = $mapDesc['longname'];
					$mapFileName = $mapDesc['name'];

					$mapDescText = '';

					if (@$mapDesc['official'] === true) {
						$mapDescText = "Официальная карта<br/>";
					}

					$mapDescText .= @$mapDesc['desc'];

					$mapTypeName = '';
					if (preg_match('/^(\S{1,5})_\S*$/', $mapDesc['name'], $mapType)) {

						if (array_key_exists($mapType[1], $mapTypes)) {
							$mapTypeName = $mapTypes[$mapType[1]];
						}
					}

					if ($j > 0 and $j < 13) {

						if (($i + 1) % 2 == 0) {
							$toolTipStyle = 'qlabs_tooltip_bottom';
						} elseif (($i + 1) % 3 == 0) {
							$toolTipStyle = 'qlabs_tooltip_diagonal_left';
						} else {
							$toolTipStyle = 'qlabs_tooltip_diagonal_right';
						}

					} else {
						if (($i + 1) % 2 == 0) {
							$toolTipStyle = 'qlabs_tooltip_top';
						} elseif (($i + 1) % 3 == 0) {
							$toolTipStyle = 'qlabs_tooltip_left';
						} else {
							$toolTipStyle = 'qlabs_tooltip_top';
						}
					}

					echo "<h3>
								<a class='$toolTipStyle qlabs_tooltip_style_1 delay_100' href='#'>
									<span>$mapImg
										<h3>$mapHeader ($mapFileName)</h3>
										$mapTypeName</br>
										$mapDescText
										</span>
													$mapHeader
								</a>
						  </h3>
						 ";

				}

				$mapNameSplit = preg_split('/_/', $mapDesc['name']);

				$mapName= '';
				foreach ($mapNameSplit as $mapNamePart) {
					$mapName .= ''.$mapNamePart.' ';
				}

				$mapName = trim($mapName, ' ');

				if (strlen($mapName) <= 20) {
					echo $this->Html->tag('strong', $mapName);
				} else {
					echo $this->Html->tag('small', $mapName);
				}

				if (preg_match('/^(\S{1,5})_\S*$/', $mapDesc['name'], $mapType)) {

					if (array_key_exists($mapType[1], $mapTypes)) {
						echo '<br/>';
						echo $mapTypes[$mapType[1]];
					}
				}


			?>
			</td>
		</tr>
		<tr>
			<td valign="bottom">
			<?php


				if (!empty($mapDesc['id'])) {

					$link = mapLink( $id, $mapDesc['id'], @$mapDesc['official'], @$mapDesc['installed'], @$mapDesc['canDelete'], @$mapDesc['on'], $this->Html);

					if ($link !== false) {
						echo $this->Html->tag('div', $link, array('id' => 'map_action_'.$mapDesc['id']));
					}
				}


			?>
			</td>
		</tr>

		<?php } ?>
		</table>
		</div>
	</td>

	<?php
	if ($i == 2) {

		echo "</tr>\n";
			$i--;
			$i--;
			$i--;
		}

		$i++;
		$j++;

		endforeach;
	?>
	</table>
<?php

	} elseif ($output == 'list') {
		$i = 0;
		$j = 1;
		foreach ($maps as $mapName => $mapDesc):
			$i++;
?>
			<div class="eight wide column">
<?php


			if (!empty($mapDesc['installed']) and $mapDesc['installed'] === true and $mapDesc['on'] === true) {
?>

				<i class="green toggle on icon"></i>
<?php
			} else {
?>
				<i class="toggle off icon"></i>
<?php
			}
?>
			<?php

				if (!empty($mapDesc['longname'])) {

					if (!empty($mapDesc['image'])) {
						$mapImg = $this->Html->image('gameMaps/'.$mapDesc['image'].'.jpg',
												array('title' => $mapDesc['name'],
													   'style' => 'margin-right: 30px;'));
					} else {
						$mapImg = $this->Html->image('personage01_x100x75.png', array('title' => 'Нет изображения карты',
																				'style' => 'margin-right: 30px;'));
					}

					$mapHeader = $mapDesc['longname'];
					$mapFileName = $mapDesc['name'];

					$mapDescText = '';

					if (@$mapDesc['official'] === true) {
						$mapDescText = "Официальная карта<br/>";
					}

					$mapDescText .= @$mapDesc['desc'];

					$mapTypeName = '';
					if (preg_match('/^(\S{1,5})_\S*$/', $mapDesc['name'], $mapType)) {

						if (array_key_exists($mapType[1], $mapTypes)) {
							$mapTypeName = $mapTypes[$mapType[1]];
						}
					}

					if ($j > 0 and $j < count($maps)/1.5) {

						if ($i == 1) {
							$toolTipStyle = 'qlabs_tooltip_diagonal_right';
						} else {
							$toolTipStyle = 'qlabs_tooltip_diagonal_left';
						}

					} else {

						$toolTipStyle = 'qlabs_tooltip_top';

					}

					echo "<a class='$toolTipStyle qlabs_tooltip_style_1 delay_100' href='#'>
									<span>$mapImg
										<h3>$mapHeader ($mapFileName)</h3>
										$mapTypeName</br>
										$mapDescText
										</span>
													$mapFileName
								</a>

						 ";

				} else {
					echo $mapName;
				}



				if (!empty($mapDesc['id'])) {

					$link = mapLink( $id, $mapDesc['id'], @$mapDesc['official'], @$mapDesc['installed'], @$mapDesc['canDelete'], @$mapDesc['on'], $this->Html);

					if ($link !== false) {
						echo $this->Html->tag('div', $link,
													['id' => 'map_action_'.$mapDesc['id'],
													 'style' => 'float: right;']);
					}
				}
			?>
				</div>
<?php
		if ($i == 2) {
			$i = 0;
			//echo "<div id='clear'></div>\n";
		}

		$j++;

		endforeach;
	}
?>
</div>
<?php } ?>
<div id='clear'></div>
<div class="ui-state-highlight ui-corner-all" style="margin-top: 8px; padding: 0 .7em;">
	<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
	<small>
	Названия желаемых карт, просьба присылать на <?php echo $this->Text->autoLinkEmails('support@teamserver.ru', array('style' => 'color: #DD1500 !important;')); ?>
	<br/>или опубликовать список в нашей группе <?php echo $this->Html->link( 'Вконтакте',
																		  'http://vkontakte.ru/topic-26301515_24913577',
																		  array('target' => '_blank',
																		        'style' => 'color: #DD1500 !important;'));?>

	</small>
	</p>
</div>
<script type="text/javascript">


		function mapAction( serverId, mapId, action ) {

			var okMessage  = '';
			var errMessage = 'Ошибка';
			var reqLink = '/servers/mapInstall/' + serverId + '/' + mapId + '/installed/' + action + '/json';

			if (action == 'install') {
				okMessage  = 'Установлена на сервер';
			} else if (action == 'delete') {
				okMessage  = 'Удалена с сервера';
			} else if (action == 'turnOn') {
				okMessage  = 'Включена в конфигах';
			} else if (action == 'turnOff') {
				okMessage  = 'Отключена в конфигах';
			}

			$('#map_action_' + mapId).html('<?php echo $this->Html->image('loading.gif');?> Выполняю');

			$.getJSON(reqLink, {}, function(data) {
					                    if (data.result == 'ok') {
					                        $('#map_action_' + mapId).html('<strong class="highlight5">' + okMessage + '</strong>');
					                    } else if (data.result == 'error') {
					                    	$('#map_action_' + mapId).html('<span class="highlight2">' + errMessage + '</span>');
					                    } else {
					                    	$('#map_action_' + mapId).html('<span class="highlight2">Неизвестная ошибка</span>');
					                    }
					        		  });
		}

		function styleListButton() {
			var buttonId = '#<?php echo $output;?>';

			$(buttonId).addClass('primary');
			$(buttonId + '_i').addClass('white');
		}

		styleListButton();

</script>
</cake:nocache>
<?php
	echo $this->Js->writeBuffer(); // Write cached scripts
?>
