<?php
/*
 * Created on 29.07.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
 //pr($orders);
 include('loading_params.php');

 // Создадим массив, в котором опишем значения статуса и цвета строк

 $payedStatus = array(
 						'1'=>array(
 									'status'=>'<div style="color: green;">Оплачен</div>',
 									'color'=>'green'
 									),
 						'0'=>array(
 									'status'=>'<div style="color: grey;">Не оплачен</div>',
 									'color'=>'red'
 									)

 						);

?>
<div id="orders_list">

<div id="searchButton" style="margin-bottom: 5px;">
	<?php
			$searchText = 'Поиск';
			$searchParam = '';
			$isSearch = false;

			if (!is_null(@$searchUserName) and @$searchUserName != 'all'){
				$searchParam .= 'Клиент: '.@$searchUserName.'; ';
				$isSearch = true;
			}

			if (!is_null(@$searchServerId) and @$searchServerId != 'all'){
				$searchParam .= 'ID сервера: #'.@$searchServerId.'; ';
				$isSearch = true;
			}

			if (@$searchServerIp != 'all'){
				$searchParam .= '; ';
			}

			if ($isSearch === true){
				echo $this->Html->tag('strong', $searchParam, array('style' => 'color: #970405;'));
				$searchText = 'Изменить условия поиска';
			}

			echo $this->Html->link($searchText, '#', array('onClick' => " $('#searchButton').hide(); $('#searchForm').show('highlight');"));



	?>
</div>
<div id="clear"></div>
<div id="searchForm" style="display: none;">

		<fieldset>
			<div class="control-group">
				<?php

				echo $this->Form->create('Order', array('class' => 'form-inline'));
				echo $this->Form->input('Server.id', array(
													'type'=>'hidden',
													'value' => 'all'));

				?>
				<div class="input-prepend input-append">
					<span class="add-on">Клиент</span><?php echo $this->Form->input('User.username',
											array(	'id' => 'usernameSort',
													'value' => @$searchUserName,
													'div' => false,
													'label' => false,
													'style' => 'width: 150px;'));

					echo $this->Form->submit('Искать', array('class' => 'btn btn-primary', 'div' => false));?>
				</div>
				<?php echo $this->Form->end(); ?>
			</div>
		</fieldset>

		<fieldset>
			<div class="control-group">
				<?php

				echo $this->Form->create('Order', array('class' => 'form-inline'));
				echo $this->Form->input('User.username', array(
													'type'=>'hidden',
													'value' => 'all'));

				?>
				<div class="input-prepend input-append">
					<span class="add-on">Сервер</span><?php echo $this->Form->input('Server.id',
											array(	'id' => 'searchByServerId',
													'type' => 'text',
													'value' => @$searchServerId,
													'div' => false,
													'label' => false,
													'style' => 'width: 150px;'));

					echo $this->Form->submit('Искать', array('class' => 'btn btn-primary', 'div' => false));?>
				</div>
				<?php echo $this->Form->end(); ?>
			</div>
		</fieldset>

	<?php

		echo $this->Form->create('Order', array('class' => 'form-inline'));
		echo $this->Form->input('Server.id', array(
											'type'=>'hidden',
											'value' => 'all'));
		echo $this->Form->input('User.username', array(
											'type'=>'hidden',
											'value' => 'all'));

		echo $this->Form->submit('Показать все', array('class' => 'btn', 'div' => false));

	?>

</div>

В текущем месяце оплачено: <strong><?php echo @$sumForMonth['payed']; ?> руб.;</strong>

Поступило: <strong><?php echo @$sumForMonth['got']; ?> руб.</strong>
<?php
	echo $this->Html->link('Подробнее','#', array('id' => 'orderStatsLink'));
	$effect = $this->Js->get('#orderStatsLink')->effect('slideIn');
	$event  = $this->Js->request(array('controller'=>'stats',
								 'action'=>'orderStats', 8),
						   array('update' => '#orderStats',
								 'before'=>$loadingShow,
								 'complete'=>$loadingHide.";$('#orderStats').show('highlight');$('#orderStatsLink').hide('highlight');"));

	$this->Js->get('#orderStatsLink')->event('click', $event);
?>
	<div id="orderStats" style="display: none; margin-top: 10px;"></div>
	<table class="intext">
		<tr>
			<th></th>
			<th>№ заказа</th>
			<th>Клиент</th>
			<th>Месяцев</th>
			<th>Сумма</th>
			<th>Дата формирования</th>
			<th>Статус</th>
		</tr>
	<?php
	foreach ($orders as $order):
	$id = $order['Order']['id'];

	?>
		<tr class="tbl_out" onmouseout="this.className='tbl_out'" onmouseover="this.className='tbl_hover_<?php echo $payedStatus[ $order['Order']['payed'] ]['color']   ;?>'">
			<td>
				<div class="btn-group">
					<?php
					//Иконка для просмотра деталей заказа
					echo $this->Html->link('<i class="icon-list-alt"></i>', '#',
										array ( 'id'=>'details_'.$id,
												'escape' => false,
												'class' => 'btn',
												'title' => 'Подробности заказа',
												'onClick'=>"$('#order_details_".$id."').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 400});"

										));
					$effect = $this->Js->get('#details_'.$id)->effect('slideIn');
					$event  = $this->Js->request(array('controller'=>'orders',
												 'action'=>'detail', $id),
										   array('update' => '#order_details_'.$id,
												 'before'=>$effect.$loadingShow,
												 'complete'=>$loadingHide));

					$this->Js->get('#details_'.$id)->event('click', $event);


					//Иконка для отмены заказа
					$confiremDeleteMessage = 'Вы уверены, что хотите отменить заказ #'.$id.'?'.
											 "\n<br/><br/>Это необратимая операция!" ;
					echo $this->Html->link('<i class="icon-trash"></i>', '#',
											array ( 'id'=>'order_cansel_'.$id,
												    'escape' => false,
												    'class' => 'btn',
												    'title' => 'Отменить заказ',
												    'onClick'=>"$('#delete_confirm_".$id."').dialog({
																								resizable: false,
																								height:200,
																								width: 350,
																								modal: true,
																								buttons: {
																										'Отменить заказ': function() {
																										window.location.href='/orders/cancel/$id';
																										$(this).dialog('close');
																									},
																									Закрыть: function() {
																										$(this).dialog('close');
																									}
																								}
																							});"

											));
							?>
							<div id="delete_confirm_<?php echo $id; ?>" title="Подвердите отмену" style="display: none;">
									<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
										<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
										<?php echo $confiremDeleteMessage; ?>
									</div>
							</div>
						</li>
					  <?php
					  // Вывести подверждение, только если заказ еще не подвеждён
					  if ($order['Order']['payed'] == 0)
					  {

					  ?>
						<?php
						//Иконка для подверждения оплаты
						echo $this->Html->link('<i class="icon-ok"></i>', array('action'=>'confirm','admin',$id),
											array( 'id'=>'server_confirm_'.$id,
												   'escape' => false,
												   'class' => 'btn',
												   'title' => 'Подвердить заказ'
											));




					  }
					?>

				</div>
				<div id="order_details_<?php echo $id;?>" style="display: none;" title="Подробности заказа <?php echo $id;?>"></div>
			</td>
			<td><?php echo $id;?></td>
			<td><?php echo $this->element('client_view', array( 'id'   => @$order['User']['id'],
																'name' => @$order['User']['username'])); ?>	</td>
			<td>
				<?php
						if ($order['Order']['month'] > 0)
						{
							echo $order['Order']['month'];
						}
						else
						{
							echo '-';
						}

				?>
			</td>
			<td><?php

					if ($order['Order']['sumToPay'] > 0)
					{
						echo $order['Order']['sumToPay'];
					}
					else
					{
						echo $order['Order']['sum'];
					}

				?> руб.</td>
			<td>
				<?php echo $this->Common->niceDate($order['Order']['created']);?>
			</td>
			<td><?php
			if (!empty($order['Order']['description'])){
				if ($order['Order']['payed'] == 1){
					$tipHeader = 'История платежа';
				}
				else
				{
					$tipHeader = 'Текущее состояние';
				}
				echo $this->Html->div('qlabs_tooltip_left qlabs_tooltip_style_1',
								'<span><strong>'.$tipHeader.'</strong><pre>'.$order['Order']['description'].'</pre></span>'.
								$payedStatus[ $order['Order']['payed'] ]['status'],
								array('style' => 'cursor: pointer;'));
			}
			else
			{
				echo $this->Html->div('order_status', $payedStatus[ $order['Order']['payed'] ]['status']);
			}

				?></td>
		</tr>
	<?php
	endforeach;
	?>
	</table>
<center>
<!-- Shows the next and previous links -->
<?php
	echo $this->Paginator->prev('«««', null, null, array('class' => 'disabled'));
	echo '&nbsp;&nbsp;';
	echo $this->Paginator->numbers();
	echo '&nbsp;&nbsp;';
	echo $this->Paginator->next('»»»', null, null, array('class' => 'disabled'));

?>
<br/>
<!-- prints X of Y, where X is current page and Y is number of pages -->
<?php echo $this->Paginator->counter(array('format' => 'Страница %page% из %pages%')); ?>
</center>

<script type="text/javascript">
	$(function() {
		$("#usernameSort").autocomplete({
									source: "/users/autoComplete/",
									minLength: 1,
								});
	});

</script>

<?php
	echo $this->Js->writeBuffer(); // Write cached scripts
?>

</div>

<!-- Контейнер для создания окна просмотра данных клиента -->
<div id="client_view" style="display:none;" title="Данные клиента"></div>


