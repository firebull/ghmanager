<?php
/*
 * Created on 10.03.2011
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
?>
<h3>Открытые тикеты:</h3>
<table cellspacing="0" cellpadding="3" border=0 style="border-collapse: collapse; width: 99%;">
<?php 
	$i=1;
	foreach ( $supportTickets as $ticket ) {		
?>
	<tr>
		<td style="border-top: 1px solid grey;"><?php echo $ticket['User'][0]['username'];?></td>
		
		<td style="border-top: 1px solid grey;"><?php echo $this->Time->niceShort($ticket['SupportTicket']['modified']); ?></td>
	</tr>
	<tr>
		<td colspan="2"><?php 
		
				if (!empty($ticket['Server'][0]['id'])){
					echo 'Сервер #'.$ticket['Server'][0]['id'].
						 ' '.$ticket['GameTemplate'][0]['longname'];
				}
		
		?></td>
	</tr>
	<tr>
		<td colspan="2" style="border-bottom: 1px solid grey;">
		<?php 
			echo $this->Html->link($ticket['SupportTicket']['title'],
								   array('action'=>'viewPda', $ticket['SupportTicket']['id']));
		
		?></td>
	</tr>
<?php } ?>
</table>

