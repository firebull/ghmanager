<?php
/*
 * Created on 02.07.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
 //pr($this->data);
 include('../loading_params.php');
?>
<div id="flash"><?php echo $session->flash(); ?></div>
<?php echo $form->create('Location', array('action' => 'linkRootserverToLocation')); ?>
				

	<table border="0" cellpadding="0" cellspacing="3" width="95%">
	<tr>
		<td align="right">Серверы:
		</td>
		<td align="left"><?php echo $form->input('RootServer', 
									array(	'options' => $rootServersList,
											'div' => false, 
											'label' => false));?>
		</td>
	</tr>
	<tr>
			<td colspan="2" align="center"><?php 
					echo $form->input('id', array('type'=>'hidden'));
					
					echo $form->submit('Сохранить',
											array('class' => 'btn btn-primary'));
					?>
			
			</td>
	</tr>
 </table>
				
<?php echo $form->end();?>