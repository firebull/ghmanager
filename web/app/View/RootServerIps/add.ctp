<?php
/*
 * Created on 28.07.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
 //pr($debug);
 include('loading_params.php');
?>
<div id="flash"><?php echo $this->Session->flash(); ?></div>
<?php echo $this->Form->create('RootServerIp', array('action' => 'add')); ?>


	<table border="0" cellpadding="0" cellspacing="3" width="95%">
	<tr>
		<td align="right">IP:
		</td>
		<td align="left"><?php echo $this->Form->input('ip',
									array(	'div' => false,
											'label' => false));?>
		</td>
	</tr>
	<tr>
		<td align="right">Тип:
		</td>
		<td align="left"><?php echo $this->Form->input('type',
									array(	'options'=>array(
																'public'=>'Общий',
																'private'=>'Индивидуальный'
																),
											'div' => false,
											'label' => false));?>
		</td>
	</tr>
	<tr>
		<td align="right">Физический сервер:
		</td>
		<td align="left"><?php echo $this->Form->input('RootServer.id',
									array(	'options' => $rootServersList,
											'div' => false,
											'label' => false));?>
		</td>
	</tr>
	<tr>
			<td colspan="2" align="center"><?php

					echo $this->Js->submit('Сохранить',
						array(
							'url'=> array(
											'controller'=>'RootServerIps',
											'action'=>'add'
							 ),
							'update' => '#add_ip',
							'class' => 'btn btn-primary',
							'before' =>$loadingShow,
							'complete'=>$loadingHide,
							'buffer' => false));
					?>

			</td>
	</tr>
 </table>

<?php echo $this->Form->end();
//pr($this->Session);

?>
