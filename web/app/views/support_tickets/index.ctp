<?php
/*
 * Created on 25.05.2010
 *
 * Made fot project TeamServer
 * by bulaev
 */
 include('../loading_params.php');
?>
<div class="control-group">
	<?php 
	
	//$effectOpener = $js->get('#new_ticket_opener')->effect('fadeOut');
	$effect 	  = $js->get('#new_ticket')->effect('slideIn');
					
	$event  = $js->request(array('controller'=>'SupportTickets',
							 'action'=>'add'), 
					   array('update' => '#new_ticket'));
	
	$js->get('#new_ticket_opener')->event('click', $event);
	
	?>
	<form>
	<?php
		echo $html->tag('button','<i class="icon-comment icon-white"></i> Открыть новый тикет',  array(  'id' => 'new_ticket_opener',
														'class' => 'btn btn-primary'));
	?>
	
	<?php
		echo $html->tag('a', '<i class="icon-book icon-white"></i> Частые вопросы (FAQ)', array(  'id' => 'faq_opener',
													'class' => 'btn btn-primary'));
	?>
	</form>		

</div>

<div id="new_ticket" style="display: none;" class="well">
	<?php 
	echo $html->image('loading.gif', array('alt'=>'Loading...', 'width'=>'16', 'height'=>'16'));
	?>
	<span style="margin-bottom: 2px;">Открытие формы...</span>
</div>

<div id="faq_area" style="display: none;  float: left;  height: auto; width: auto;">
	<?php 
	echo $this->element('faq');
	?>
	
</div>

<div id="clear"></div>
<div class="list_border">
	<h2>Мои текущие тикеты:</h2>
	<table class="intext"  style="background-color: #fff;" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<th>№</th>
			<th style="width: 22px;"></th>
			<th align="left">Тема</th>
			<th>Дата</th>			
		</tr>
	
		<!-- Here is where we loop through our array, printing out info -->
	
		<?php
		$i = 1;
		foreach ($supportTickets as $ticket): 			
			if ($ticket['status'] == 'closed' && @$pastStatus == 'open'
				or
				$ticket['status'] == 'closed' && !@$pastStatus){
					
				$pastStatus = 'closed';
				$i = 1;
				/*
				 * Закроем предыдущую таблицу и откроем новую, 
				 * с закрытыми тикетами.
				 */
				 
		?>
	</table>
</div>
<div class="list_border">
	<h2>Мои закрытые тикеты:</h2>
	<table class="intext"  style="background-color: #fff;" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<th>№</th>
			<th style="width: 22px;"></th>
			<th align="left">Тема</th>
			<th>Дата</th>			
		</tr>		
			<?php	
				
				} //if
				
				
			?>
			
			<tr style="cursor:pointer;" title="Раскрыть/обновить ветку" id="opener_<?php echo $ticket['id']; ?>">
				<td><?php echo $i++; ?></td>
				<td>
					<div id="loading_<?php echo $ticket['id']; ?>" style="margin:0px;display:none;position:relative; top: 0px;">
					<?php 
					echo $html->image('loading.gif', array('alt'=>'Loading...', 'width'=>'16', 'height'=>'16'));
					?>
					</div>
				</td>				
				<td class="left">
					<?php echo $ticket['title']; ?>			
					
					<?php
		                $effect = $js->get('#ticket_more_'.$ticket['id'])->effect('slideIn');
				
						$event  = $js->request(array('controller'=>'SupportTickets',
												 'action'=>'view', $ticket['id']), 
										   array('update' => '#ticket_more_'.$ticket['id'],	  
												 'before'=>$effect."$('#loading_".$ticket['id']."').show();",
												 'complete'=>"$('#loading_".$ticket['id']."').hide();"));
						
						$js->get('#opener_'.$ticket['id'])->event('click', $event);				
					?>
				</td>
				<td><?php echo $this->Common->niceDate($ticket['created']); ?></td>
			</tr>
			<tr id="container_<?php echo $ticket['id']; ?>">
			<td colspan="4" class="ticket_more">
				<div id="ticket_more_<?php echo $ticket['id']; ?>" style="display:none;text-align:center; margin: 1em;">
				Загрузка сообщений...
				<a href="#" onclick="$('#ticket_more_<?php echo $ticket['id']; ?>').hide(); return false;">Скрыть ветку</a>
				</div>
			</td>
		</tr>
			
		<?php
			
			$pastStatus = $ticket['status']; 
		endforeach; ?>

	
		
	</table>
</div>
<script type="text/javascript" language="javascript">
		$(function() {
			$('#new_ticket_opener').click(
				
				function() {
				  
					$('#new_ticket').show('highlight');
					$('#new_ticket_opener').attr('disabled', 'disabled');

				}
			
			);

			$('#faq_opener').click(
				
				function() {
				  
					$('#faq_area').show('highlight');
					$('#faq_opener').attr('disabled', 'disabled');

				}
			
			);
		
		});
</script>
<?php 
			echo $js->writeBuffer(); // Write cached scripts 
?>