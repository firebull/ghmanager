<?php
/*
 * Created on 16.08.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
 
 include('../loading_params.php');

  echo $form->create('GameTemplate', array('action' => 'editMap', 'type' => 'file'));
 
?>
<table border="0" cellpadding="0" cellspacing="3" width="95%">
	<tr>
		<td align="right">Тип карты:
		</td>
		<td align="left"><?php echo $form->input('Map.map_type_id', 
									array(	'options' => $mapTypes,
											'div' => false, 
											'label' => false));?>
		</td>
	</tr>
	<tr>
		<td align="right">Краткое имя:
		</td>
		<td align="left"><?php echo $form->input('Map.name', 
									array(	'size'=> '30',
											'div' => false, 
											'label' => false));?>
		</td>
	</tr>
	<tr>
		<td align="right">Полное имя:
		</td>
		<td align="left"><?php echo $form->input('Map.longname', 
									array(	'size'=> '30',
											'div' => false, 
											'label' => false));?>
		</td>
	</tr>
	<tr>
		<td align="right">Описание:
		</td>
		<td align="left"><?php echo $form->input('Map.desc', 
									array(	//'size'=> '30',
											'div' => false, 
											'label' => false));?>
		</td>
	</tr>
	<tr>
		<td align="right">Игра:
		</td>
		<td align="left"><?php echo $form->input('Map.GameTemplate', 
									array(	'options' => $gameList,
											'multiple' => 'multiple',
											'div' => false, 
											'label' => false));?>
		</td>
	</tr>
	<tr>
		<td align="right">Официальная карта:
		</td>
		<td align="left"><?php 
		
		if ($this->data['Map']['official'] == 1){
			echo $form->input( 'Map.official', 
								array(	'type'=> 'checkbox',
										'value' => 1,
										'checked' => true,
										'div' => false, 
										'label' => false));	
		}
		else
		{
			echo $form->input( 'Map.official', 
								array(	'type'=> 'checkbox',
										'value' => 1,
										'div' => false, 
										'label' => false));
		}
		
						?>
		</td>
	</tr>
	<tr>
		<td align="right">Изображение:
		</td>
		<td align="left"><?php echo $form->input('Map.map_image', array('type' => 'file',
																		'div' => false, 
																		'label' => false)); ?>
		</td>
	</tr>
	<tr>
		<td>
		</td>
		<td align="center" valign="top">
			<small></small>
		</td>
	</tr>
		<tr>
			<td colspan="2" align="center"><?php 
					// К сожалению, с помощью этого хелпера
					// нельзя отправить файл, поэтому пока будем отсылать обычной кнопкой
					echo $form->input(  'Map.id', array(									
										'div' => false,
										'label' => false));
										 
					echo $form->submit('Сохранить',
											array('class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only'));


//					echo $js->submit('Добавить',
//						array(
//							'url'=> array(
//											'controller'=>'GameTemplates',
//											'action'=>'addMap'
//							 ),
//							'update' => '#add_map',
//							'class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
//							'before' =>$loadingShow,
//							'complete'=>$loadingHide,
//							'buffer' => false));
					?>
			
			</td>
	</tr>
</table>

<?php echo $form->end();?>

