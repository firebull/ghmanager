<?php
/*
 * Created on 15.12.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
  include('loading_params.php');
?>
<div id="flash"><?php echo $this->Session->flash(); ?></div>
<?php
if (@$orders){
?>

<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em; margin-top: 5px;">
		<p>
		<small>
		Внимание! После оплаты Яндекс.Деньгами вам нужно будет вручную вернуться в панель и обновить страницу с заказами! Иначе вы не увидите изменения статуса заказа!
		</small>
		</p>
</div>

<div class="list_border">
	<h2>Мои неоплаченные заказы:</h2>
	<table class="intext"  style="background-color: #fff;" cellspacing="0" cellpadding="0" border="0">
		<tr>

			<th style="width: 22px;"></th>
			<th style="width: 40px;">№ заказа</th>
			<th style="width: 40px;"></th>
			<th style="width: 200px;">Дата формирования</th>
			<th>Назначение</th>
			<th>Сумма</th>
			<th>Действие</th>


		</tr>

		<!-- Here is where we loop through our array, printing out info -->

		<?php
		$i = 1;
		foreach ($orders as $order):
			if ($order['payed'] == '1' && @$pastStatus == '0'
				or
				$order['payed'] == '1' && !@$pastStatus){

				$pastStatus = '1';
				$i = 1;
				/*
				 * Закроем предыдущую таблицу и откроем новую,
				 * с закрытыми тикетами.
				 */

		?>
	</table>
</div>
<div class="list_border">
	<h2>Мои оплаченные заказы:</h2>
	<table class="intext"  style="background-color: #fff;" cellspacing="0" cellpadding="0" border="0">
		<tr>
			<th style="width: 22px;"></th>
			<th style="width: 40px;">№ заказа</th>
			<th style="width: 40px;"></th>
			<th style="width: 200px;">Дата формирования</th>
			<th>Назначение</th>
			<th>Сумма</th>
			<th>Статус</th>
			<th></th>

		</tr>
			<?php

				} //if


			?>

			<tr title="Информация о заказе" id="opener_<?php echo $order['id']; ?>">
				<td><?php echo $i++; ?></td>
				<td><?php echo $order['id']; ?></td>
				<td>
					<?php
					//Иконка для просмотра деталей заказа
					echo $this->Html->link('<i class="icon-list-alt"></i>', '#',
										array ('id'=>'details_'.$order['id'],
												'escape' => false,
												'class' => 'btn',
												'title' => 'Подробности заказа',
												'onClick'=>"$('#order_details').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 400});"

										));
					$effect = $this->Js->get('#details_'.$order['id'])->effect('slideIn');
					$event  = $this->Js->request(array('controller'=>'orders',
												 'action'=>'detail', $order['id']),
										   array('update' => '#order_details',
												 'before'=>$effect.$loadingShow,
												 'complete'=>$loadingHide));

					$this->Js->get('#details_'.$order['id'])->event('click', $event);

					?>

				</td>
				<td><?php echo $this->Time->niceShort($order['created']); ?></td>
				<td>
					<?php

						if (!empty($order['month'])){
							if (!empty($order['Server'][0]['id'])){
								echo 'Аренда сервера #'.$order['Server'][0]['id'];
							}
							else
							{
								echo 'Сервер';
							}

						}
						else
						{
							echo 'Пополнение счёта';
						}

					 ?>
				</td>
				<td>
					<?php

						echo $order['sum']." рублей";
						if ($order['sumToPay'] > 0 and $order['sum'] > $order['sumToPay'])
						{
							echo "<br/>К оплате: ".$order['sumToPay'].' руб.';
						}

					?>
				</td>
				<td>
					<?php
						if ($order['payed'] == 0){
							//echo "Оплатить";

							//Иконка для продления оплаты игрового сервера
							echo $this->Html->link('Оплатить', '#',
												array ('id'=>'server_cart_'.$order['id'],
													   'title' => 'Оплатить заказ',
													   'escape' => false,
													   'onClick'=>"$('#pay_order').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 620, minWidth: 620});"));
							$effect = $this->Js->get('#pay_order')->effect('slideIn');
							$event  = $this->Js->request(array('controller'=>'Orders',
														 'action'=>'pay', $order['id']),
												   array('update' => '#pay_order',
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide,
														 'buffer'=>false));

							$this->Js->get('#server_cart_'.$order['id'])->event('click', $event);


							?>
							<br/>
						<?php
							//Иконка для отмены заказа
							$confiremDeleteMessage = 'Вы уверены, что хотите отменить заказ #'.$order['id'].'?';
							echo $this->Html->link('Отменить', '#',
													array ('id'=>'order_cansel_'.$order['id'], 'escape' => false,
													'title' => 'Отменить (удалить) заказ'
													,'onClick'=>"$('#delete_confirm_".$order['id']."').dialog({
																										resizable: false,
																										height:200,
																										width: 350,
																										modal: true,
																										buttons: {

																												'Отменить заказ': function() {
																												window.location.href='/orders/cancel/".$order['id']."';
																												$(this).dialog('close');
																											},
																											Закрыть: function() {
																												$(this).dialog('close');
																											}
																										}
																									});"

													));
							?>
							<div id="delete_confirm_<?php echo $order['id']; ?>" title="Подвердите отмену заказа № <?php echo $order['id']; ?>" style="display: none;">
									<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
										<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
										<?php echo $confiremDeleteMessage; ?>
									</div>
							</div>
						<?php

						}
						else
						{
							echo "Оплачен<br/>";
							if (!empty($order['payedDate'])){
								echo $this->Html->tag('small', $this->Common->niceDate($order['payedDate']));
							}
						}

					?>
				</td>
				<?php
					if ($order['payed'] == 1){
				?>
				<td>
					<?php

						if (!empty($order['payedBy']) && ($order['payedBy'] != 'manual' or $order['payedBy'] != 'unknown')){
							$paymentImgs['yandex']   = 'yamoney_logo88x31.gif';
							$paymentImgs['rbk']      = 'rbk_58.png';
							$paymentImgs['webmoney'] = 'webmoney_blue_on_white_88.png';
							$paymentImgs['qiwi']     = 'qiwi_goriz_88.png';
							$paymentImgs['internal'] = 'personage01_x41x40.png';

							if (!empty($paymentImgs[$order['payedBy']])){
								echo $this->Html->image('icons/'.$paymentImgs[$order['payedBy']]);
							}

						}

					?>
				</td>
				<?php
					}
				?>
			</tr>

		<?php

			$pastStatus = $order['payed'];
		endforeach; ?>



	</table>
</div>
<div id="pay_order" style="display:none;"  title="Пожалуйста, выберите способ оплаты"></div>
<div id="order_details" style="display:none;"  title="Подробности заказа"></div>
<?php
	}
?>
<script type="text/javascript">
	$(function() {
		$(".button, input:submit").button();
	});
</script>
<?php
			echo $this->Js->writeBuffer(); // Write cached scripts
?>

