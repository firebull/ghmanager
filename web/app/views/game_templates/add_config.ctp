<?php
/*
 * Created on 16.08.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
 
 include('../loading_params.php');
  
  echo $form->create('GameTemplate', array('action' => 'addConfig'));
 
?>
<table border="0" cellpadding="0" cellspacing="3" width="95%">
	<tr>
		<td align="right">Имя файла:
		</td>
		<td align="left"><?php echo $form->input('Config.name', 
									array(	'size'=> '30',
											'div' => false, 
											'label' => false));?>
		</td>
	</tr>
	<tr>
		<td align="right">Путь:
		</td>
		<td align="left"><?php echo $form->input('Config.path', 
									array(	'size'=> '30',
											'div' => false, 
											'label' => false));?>
		</td>
	</tr>
	<tr>
		<td>
		</td>
		<td align="center" valign="top">
			<small>Введите путь относительно директории, где расположены addons, cfg и т.д.
			<br/>
			Пример: <strong>addons/sourcemod/configs</strong></small>
		</td>
	</tr>
		<tr>
			<td colspan="2" align="center"><?php 
					// Пока неисправят баг в jQuery, будем отсылать обычной кнопкой
					echo $form->submit('Добавить',
											array('class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only'));


//					echo $js->submit('Добавить',
//						array(
//							'url'=> array(
//											'controller'=>'GameTemplates',
//											'action'=>'addMod'
//							 ),
//							'update' => '#templates_list',
//							'class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
//							'before' =>$loadingShow,
//							'complete'=>"$('#add_mod').hide('clip');".$loadingHide,
//							'buffer' => false));
					?>
			
			</td>
	</tr>
</table>

<?php echo $form->end();?>

