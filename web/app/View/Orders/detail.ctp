<div id="flash"><?php echo $this->Session->flash(); ?></div>
<?php
/*
 * Created on 30.07.2010
 *
 * File created for project TeamServer
 * by nikita
 */
 //pr(@$order);
 //pr(@$server);
 echo @$order['User']['username']."<br/>\n";
 echo @$order['User']['first_name']." ";
 echo @$order['User']['second_name']."<br/><br/>\n";

 if (!empty($server)){
	 echo '#ID '.$server['Server']['id'].' '.@$server['GameTemplate'][0]['longname']."<br/>\n";
	 if (!empty($server['Mod'])){
	 	echo @$server['Mod'][0]['name']."<br/>\n";
	 }
	 if (!empty($server['Service'])){
	 	echo "<br/>Заказанные услуги:<br/>\n";
	 	foreach ( $server['Service'] as $service ) {
	        echo $service['longname']." за ".$service['price']." руб.<br/>\n";
		}
	 }
	 echo "<br/>\n";

	 switch ( $server['Server']['privateType'] ) {
		case 0:
			$slotText = 'Публичных слотов ';
			$slotPrice = @$server['GameTemplate'][0]['price'];
			break;
		case 1:
		    $slotText = 'Приватных слотов ';
			$slotPrice = @$server['GameTemplate'][0]['pricePrivatePassword'];
			break;
		case 2:
			$slotText = 'Приватных слотов с автоотключением ';
			$slotPrice = @$server['GameTemplate'][0]['pricePrivatePower'];
			break;
		default:
			$slotText = 'Публичных слотов ';
			$slotPrice = @$server['GameTemplate'][0]['price'];
			break;
	}

	 echo $slotText.@$server['Server']['slots']." по цене ".@$slotPrice." рублей за слот <br/>\n";
	 echo "Сроком на ".@$order['Order']['month']." мес. <br/>\n";
 }
 else
 if (!empty($order['Order']['month']) and $order['Order']['month'] > 0)
 {
 	 echo "Продление аренды сервера на ".@$order['Order']['month']." мес. <br/>\n";
 }
 else
 {
 	echo "Пополнение лицевого счёта <br/>\n";
 }

echo "На сумму ".@$order['Order']['sum']." рублей<br/><br/>\n";

if ($order['Order']['sumToPay'] > 0 and $order['Order']['sum'] > $order['Order']['sumToPay'])
{
	echo "К оплате: ".$order['Order']['sumToPay'].' руб.';

	if($order['Order']['payed'] == 1)
	{
		echo "<br/>С Лицевого счета списано ".round($order['Order']['sum'] - $order['Order']['sumToPay'], 2).' руб.<br/><br/>';
	}
	else
	{
		echo "<br/>С Лицевого счета будет списано ".round($order['Order']['sum'] - $order['Order']['sumToPay'], 2).' руб. после оплаты заказа.<br/><br/>';
	}


}

if (!empty($order['Order']['description'])){

	if($order['Order']['payed'] == 1){
		echo $this->Html->tag('strong', 'История платежа:');
	}
	else
	{
		echo $this->Html->tag('strong', 'Статус оплаты:');
	}

?>
	<div id="ui-widget" class="flash ui-state-highlight ui-corner-all" style="margin-bottom: 5px; margin-top: 6px; padding: 0 .7em;">
	<?php

		echo $this->Html->tag('small', $order['Order']['description']);

	?>
	</div>
<?php
}

?>


