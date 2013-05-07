<?php
/*
 * Created on 01.06.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
 include('../loading_params.php');
?>

	<?php echo $form->create('Location'); ?>
			<table border="0" cellpadding="0" cellspacing="3" width="95%">
				<tr>
					<td align="right">Название:</td>
					<td align="left"><?php echo $form->input('name', array('size' => '40', 'div' => false, 'label' => false));?></td>
				</tr>
				<tr>
					<td align="right">Расположение:</td>
					<td align="left"><?php echo $form->input('collocation', array('div' => false, 'label' => false));?></td>
				</tr>
				<tr>
					<td colspan="2" align="center"><?php 
					echo $form->submit('Сохранить',
											array('class' => 'btn btn-primary'));
							?></td>
				</tr>
			</table>
	<?php echo $form->end();?>

