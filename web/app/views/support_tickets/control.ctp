<?php
/*
 * Created on 25.05.2010
 *
 * Made fot project TeamServer
 * by bulaev
 */
 include('../loading_params.php');
 //pr($ticketStates);
?>
<div id="searchButton" style="margin-bottom: 5px;">
	<?php
			$searchText = 'Поиск';
			$searchParam = '';
			$isSearch = false;

			if (!is_null(@$searchUserName) and @$searchUserName != 'all'){
				$searchParam .= 'Клиент: '.@$searchUserName.'; ';
				$isSearch = true;
			}

			if ($isSearch === true){
				echo $html->tag('strong', $searchParam, array('style' => 'color: #970405;'));
				$searchText = 'Изменить условия поиска';
			}

			echo $html->link($searchText, '#', array('onClick' => " $('#searchButton').hide(); $('#searchForm').show('highlight');"));

	?>
</div>
<div id="clear"></div>
<div id="searchForm" style="display: none;">
		<fieldset>
			<div class="control-group">
				<?php

				echo $form->create('SupportTicket', array('class' => 'form-inline'));

				?>
				<div class="input-prepend input-append">
					<span class="add-on">Клиент</span><?php echo $form->input('User.username',
											array(	'id' => 'searchByUser',
													'type' => 'text',
													'value' => @$searchServerId,
													'div' => false,
													'label' => false,
													'style' => 'width: 150px;'));

					echo $form->submit('Искать', array('class' => 'btn btn-primary', 'div' => false));?>
				</div>
				<?php echo $form->end(); ?>
			</div>
		</fieldset>

	<?php

		echo $form->create('SupportTicket', array('class' => 'form-inline'));
		echo $form->input('User.username', array(
											'type'=>'hidden',
											'value' => 'all'));

		echo $form->submit('Показать все', array('class' => 'btn', 'div' => false));

	?>

</div>
<div class="list_border">
<?php
	// Показать заголовок таблицы, только если есть открытые тикеты
	if ($supportTickets[0]['SupportTicket']['status'] == 'open') { ?>
	<h2>Текущие тикеты:</h2>
	<table class="intext"  style="background-color: #fff;" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<th>№</th>
			<th style="width: 22px;"></th>
			<th align="left">Тема</th>
			<th>Клиент</th>
			<th>Сервер</th>
			<th>Последний ответ</th>
		</tr>

<?php } ?>
		<!-- Here is where we loop through our array, printing out info -->

		<?php
		$i = 1;
		foreach ($supportTickets as $ticket):
			if ($ticket['SupportTicket']['status'] == 'closed' && @$pastStatus == 'open'
				or
				$ticket['SupportTicket']['status'] == 'closed' && !@$pastStatus){


				/*
				 * Закроем предыдущую таблицу и откроем новую,
				 * с закрытыми тикетами.
				 */

		if (@$pastStatus == 'open'){ // Показать огрызкок таблицы, только если прошлый тикет был открытым

		?>
	</table>
</div>

		<?php
				}

			$pastStatus = 'closed';
			$i = 1;

		?>
<div class="list_border">
	<h2>Закрытые тикеты:</h2>
	<table class="intext"  style="background-color: #fff;" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<th>№</th>
			<th style="width: 22px;"></th>
			<th align="left">Тема</th>
			<th>Клиент</th>
			<th>Сервер</th>
			<th>Дата</th>
		</tr>
			<?php

				} //if

				$style = "";
				// Выделить открытые тикеты цветами
				if ($ticket['SupportTicket']['status'] == 'open')
				{
					if (empty($ticket['SupportTicket']['supporter']))
					{
						if (@$ticketStates[$ticket['SupportTicket']['id']]['answerBy'] == 'owner')
						{
							$style = 'background-color: #FFCCCC;';
						}
					}
					else
					{
						if ($ticket['SupportTicket']['supporter'] == $userinfo['User']['username'])
						{
							if (@$ticketStates[$ticket['SupportTicket']['id']]['answerBy'] == 'owner')
							{
								$style = 'background-color: #FFCCCC;';
							}
							else
							if
							( empty($ticketStates[$ticket['SupportTicket']['id']]['answerBy']) or $ticketStates[$ticket['SupportTicket']['id']]['answerBy'] == 'support')
							{
								$style = 'background-color: #FFFF99;';
							}
						}
					}
				}


			?>

			<tr <?php echo 'style="'.$style.'"';?> >
				<td><?php echo $ticket['SupportTicket']['id']; ?></td>
				<td>
					<div id="loading_<?php echo $ticket['SupportTicket']['id']; ?>" style="margin:0px;display:none;position:relative; top: 0px;">
					<?php
					echo $html->image('loading.gif', array('alt'=>'Loading...', 'width'=>'16', 'height'=>'16'));
					?>
					</div>
					<div id="status_<?php echo $ticket['SupportTicket']['id']; ?>">
					<?php
						if (@$ticketStates[$ticket['SupportTicket']['id']]['answerBy'] == 'owner')
						{
							echo '<span class="ui-icon ui-icon-mail-open"></span>';
						}
						else
						if
						( empty($ticketStates[$ticket['SupportTicket']['id']]['answerBy']) or $ticketStates[$ticket['SupportTicket']['id']]['answerBy'] == 'support')
						{
							echo '<span class="ui-icon ui-icon-mail-closed"></span>';
						}
					?>
					</div>

				</td>
				<td class="left"  style="cursor:pointer;" title="Раскрыть/обновить ветку" id="opener_<?php echo $ticket['SupportTicket']['id']; ?>">
					<?php

						echo $ticket['SupportTicket']['title'];

						if (!empty($ticket['SupportTicket']['supporter']))
						{
							if ($ticket['SupportTicket']['supporter'] == $userinfo['User']['username'])
							{
								echo '<span><strong class="highlight3">';
							}
							else
							{
								echo '<span><strong>';
							}

							echo ' <i class="icon-arrow-right"></i> '.$ticket['SupportTicket']['supporter'];

							echo '</strong></span>';
						}


					?>

					<?php
		                $effect = $js->get('#ticket_more_'.$ticket['SupportTicket']['id'])->effect('slideIn');

						$event  = $js->request(array('controller'=>'SupportTickets',
												 'action'=>'view', $ticket['SupportTicket']['id']),
										   array('update' => '#ticket_more_'.$ticket['SupportTicket']['id'],
												 'before'=>$effect.";$('#status_".$ticket['SupportTicket']['id']."').hide();$('#loading_".$ticket['SupportTicket']['id']."').show();",
												 'complete'=>"$('#loading_".$ticket['SupportTicket']['id']."').hide();$('#status_".$ticket['SupportTicket']['id']."').show();"));

						$js->get('#opener_'.$ticket['SupportTicket']['id'])->event('click', $event);
					?>
				</td>
				<td><?php echo $this->element('client_view', array( 'id'   => @$ticket['User']['0']['id'],
																	'name' => @$ticket['User']['0']['username'])); ?>
				</td>

				<td><?php

						$j = 1;
						if (!empty($ticket['Server'])){
							foreach ( $ticket['Server'] as $serverId ) {
       							echo "#".$serverId['id'];

       							if (count($ticket['Server']) > $j){
       								echo '; ';
       							}
       							$j++;
							}
						}
						else
						{
							echo "-";
						}


					?>
				</td>
				<td style="text-align: left;"><?php
							if ( !empty($ticketStates[$ticket['SupportTicket']['id']]))
							{

								$timeText = $common->niceDateDiff($ticketStates[$ticket['SupportTicket']['id']]['created'], date("d.m.Y H:i:s"));

								if ($ticketStates[$ticket['SupportTicket']['id']]['answerBy'] == 'owner')
								{
									echo $html->tag('span', $timeText.'!', array('class' => 'highlight2'));
								}
								else
								if
								( empty($ticketStates[$ticket['SupportTicket']['id']]['answerBy']) or $ticketStates[$ticket['SupportTicket']['id']]['answerBy'] == 'support')
								{
									echo $common->niceDateDiff($ticketStates[$ticket['SupportTicket']['id']]['created'], date("d.m.Y H:i:s"));
								}
							}
							else
							{
								echo $html->tag('nobr', $common->niceDate($ticket['SupportTicket']['created']));
							}
							 ?>
				</td>
			</tr>
			<tr id="container_<?php echo $ticket['SupportTicket']['id']; ?>">
			<td colspan="6" class="ticket_more">
				<div id="ticket_more_<?php echo $ticket['SupportTicket']['id']; ?>" style="display:none;text-align:center; margin: 1em; width: 97%;">
				Загрузка сообщений...
				<a href="#" onclick="$('#ticket_more_<?php echo $ticket['SupportTicket']['id']; ?>').hide(); return false;">Скрыть ветку</a>
				</div>
			</td>
		</tr>

		<?php

			$pastStatus = $ticket['SupportTicket']['status'];
		endforeach; ?>



	</table>
</div>
<center>
<!-- Shows the next and previous links -->
	<?php
		echo $paginator->prev('«««', null, null, array('class' => 'disabled'));
		echo '&nbsp;&nbsp;';
		echo $paginator->numbers();
		echo '&nbsp;&nbsp;';
		echo $paginator->next('»»»', null, null, array('class' => 'disabled'));

	?>
	<br/>
	<!-- prints X of Y, where X is current page and Y is number of pages -->
	<?php echo $paginator->counter(array('format' => 'Страница %page% из %pages%')); ?>
</center>
<script type="text/javascript" language="javascript">
		$(function() {
			$('#new_ticket_opener').click(

				function() {

					$('#new_ticket').show('clip');
					$('#new_ticket_opener').hide();

				}

			);

		});

	$(function() {
		$("#searchByUser").autocomplete({
									source: "/users/autoComplete/",
									minLength: 1,
								});
	});

</script>
<!-- Контейнер для создания окна просмотра данных клиента -->
<div id="client_view" style="display:none" title="Данные клиента"></div>
<?php
			echo $js->writeBuffer(); // Write cached scripts
?>