<?php
/*
 * Created on 02.07.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
 //pr($modsList);
 include('../loading_params.php');
?>
<?php if (!empty($mapsList)){?>
<?php echo $form->create('GameTemplate', array('action' => 'linkMapToTemplate')); ?>
				
	<div id="flash"><?php echo $session->flash(); ?></div>
	<table border="0" cellpadding="0" cellspacing="3" width="95%">
	<tr>
		<td align="right">Карта:
		</td>
		<td align="left"><?php echo $form->input('Map.Map', 
									array(	'options' => $mapsList,
											'multiple' => 'multiple',
											'div' => false, 
											'label' => false));?>
		</td>
	</tr>
	<tr>
			<td colspan="2" align="center"><?php 
					echo $form->input('GameTemplateMap.id', array('type'=>'hidden'));
					
					echo $form->submit('Сохранить',
											array('class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only'));

					?>
			
			</td>
	</tr>
 </table>
				
<?php echo $form->end();
	}
	else
	{
		echo $html->tag('strong', 'Нет доступных для привязки к этому шаблону карт.');
	}

?>
