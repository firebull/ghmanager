<?php
/*
 * Created on 16.08.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
 
 include('../loading_params.php');
  
  echo $form->create('GameTemplate', array('action' => 'addMod'));
 
?>
<table border="0" cellpadding="0" cellspacing="3" width="95%">
	<tr>
		<td align="right">Краткое название:
		</td>
		<td align="left"><?php echo $form->input('Mod.name', 
									array(	'size'=> '30',
											'div' => false, 
											'label' => false));?>
		</td>
	</tr>
		<tr>
		<td align="right">Полное название (без версии):
		</td>
		<td align="left"><?php echo $form->input('Mod.longname', 
									array(	'size'=> '30',
											'div' => false, 
											'label' => false));?>
		</td>
	</tr>
	<tr>
		<td align="right">Версия:
		</td>
		<td align="left"><?php echo $form->input('Mod.version', 
									array(	'size'=> '30',
											'div' => false, 
											'label' => false));?>
		</td>
	</tr>
	<tr>
		<td align="right">Краткое описание:
		</td>
		<td align="left"><?php echo $form->input('Mod.shortDescription', 
									array(	'size'=> '30',
											'div' => false, 
											'label' => false));?>
		</td>
	</tr>
	<tr>
		<td align="left" colspan="2">
		Описание:<br/>
		<?php echo $form->input('Mod.description', 
									array(	'type'=>'textarea',
				  						      'wrap'=>'off',
				  							  'style'=> 'width: 550px;  
				  							  			 height: 200px;
											  			 padding-left: 10px;
			
			
			
														  ',
											'div' => false, 
											'label' => false));?>
		</td>
	</tr>
	<tr>
		<td align="left" colspan="2">
		Какие параметры сервера передать:<br/>
		<?php echo $form->input('Mod.moreParams', 
									array(	'type'=>'textarea',
				  						      'wrap'=>'off',
				  							  'style'=> 'width: 550px;  
				  							  			 height: 150px;
											  			 padding-left: 10px;
			
			
			
														  ',
											'div' => false, 
											'label' => false));?>
		<small>Пишите поля в БД, разделённые запятой</small>
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

