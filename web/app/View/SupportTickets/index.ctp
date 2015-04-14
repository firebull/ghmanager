<?php
/*
 * Created on 25.05.2010
 *
 * Made fot project TeamServer
 * by bulaev
 */
 include('loading_params.php');
?>
<div class="ui padded grid">
	<div class="twelve wide column">
		<div class="ui segment">

		<div id="new_ticket" style="display: none;" class="well">
			<?php
			echo $this->Html->image('loading.gif', array('alt'=>'Loading...', 'width'=>'16', 'height'=>'16'));
			?>
			<span style="margin-bottom: 2px;">Открытие формы...</span>
		</div>

		<div id="clear"></div>
		<div class="list_border">
			<h2>Мои текущие тикеты:</h2>
			<table class="ui table">
				<thead>
					<th>№</th>
					<th style="width: 22px;"></th>
					<th align="left">Тема</th>
					<th>Дата</th>
				</thead>

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
			<table class="ui table">
				<thead>
					<th>№</th>
					<th style="width: 22px;"></th>
					<th align="left">Тема</th>
					<th>Дата</th>
				</thead>
					<?php

						} //if


					?>

					<tr style="cursor:pointer;" title="Раскрыть/обновить ветку" id="opener_<?php echo $ticket['id']; ?>">
						<td><?php echo $i++; ?></td>
						<td>
							<div id="loading_<?php echo $ticket['id']; ?>" style="margin:0px;display:none;position:relative; top: 0px;">
							<?php
							echo $this->Html->image('loading.gif', array('alt'=>'Loading...', 'width'=>'16', 'height'=>'16'));
							?>
							</div>
						</td>
						<td class="left">
							<?php echo $ticket['title']; ?>

							<?php
				                $effect = $this->Js->get('#ticket_more_'.$ticket['id'])->effect('slideIn');

								$event  = $this->Js->request(array('controller'=>'SupportTickets',
														 'action'=>'view', $ticket['id']),
												   array('update' => '#ticket_more_'.$ticket['id'],
														 'before'=>$effect."$('#loading_".$ticket['id']."').show();",
														 'complete'=>"$('#loading_".$ticket['id']."').hide();"));

								$this->Js->get('#opener_'.$ticket['id'])->event('click', $event);
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


		</div>
	</div>
	<div class="four wide column">
		<?php
			echo $this->element('support_menu', []);
		?>
		<?php

			//$effectOpener = $this->Js->get('#new_ticket_opener')->effect('fadeOut');
			$effect 	  = $this->Js->get('#new_ticket')->effect('slideIn');

			$event  = $this->Js->request(array('controller'=>'SupportTickets',
									 'action'=>'add'),
							   array('update' => '#new_ticket'));

			$this->Js->get('#new_ticket_opener')->event('click', $event);

			?>
			<?php
				echo $this->Html->tag('button','<i class="icon-comment icon-white"></i> Открыть новый тикет',  array(  'id' => 'new_ticket_opener',
																'class' => 'ui fluid orange button'));
			?>
	</div>
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
			echo $this->Js->writeBuffer(); // Write cached scripts
?>
