<?php
/*
 * Created on 21.12.2010
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */

 /*
 * system = null,
   using = null,
   order = array(),
   serverTemplate = array(),
   paymentParams = array()
 * @system - Система приёма платежей
 * 		- rbk
 * 		- yandex
 * 		- webmoney
 * @using - чем платить
 * 		- Выбор системы оплаты на сайте RBK - common
 * 		- Оплата с кошелька Rbk Money - inner
			Банковская карта Visa/MasterCard - bankCard
			Электронные платежные системы - exchangers
			Предоплаченная карта RBK Money - prepaidcard
			Системы денежных переводов - transfers
			Платѐжные терминалы - terminals
			SMS - iFree
			Банковский платѐж - bank
			Почта России - postRus
			Банкоматы - atm
			Интернет банкинг – ibank
 */

 	if (!empty($serverTemplate))
 	{
 		$orderMessage = 'Аренда сервера \''.$serverTemplate['longname'].'\' ID'.$order['Server'][0]['id'];
 	}
 	else
 	{
 		$orderMessage = 'Пополнение счёта #'.$this->Common->userBill($order['User']['id']).', логин: '.$order['User']['username'];
 	}

 	if (@$system == 'platron'){

	$salt = $this->Common->genSalt();
	$sig = strtolower(md5( 'payment.php;'.
			    number_format($order['Order']['sumToPay'], 2, '.', '').';'.
			   'RUR;'.
			    $orderMessage.';'.
			    $paymentParams['platron']['merchant_id'].';'.
			    $order['Order']['id'].';'.
			    $using.';'.
			    $salt.';'.
			    $order['User']['email'].';'.
			    $paymentParams['platron']['secret_key']));


?>
	<form name="order_<?php echo $order['Order']['id'].'_platron_'.@$using;?>" action="https://www.platron.ru/payment.php" method="POST">
		<input type="hidden" name="pg_amount" value="<?php echo number_format($order['Order']['sumToPay'], 2, '.', '');?>">
		<input type="hidden" name="pg_currency" value="RUR">
		<input type="hidden" name="pg_description" value="<?php echo $orderMessage; ?>">
		<input type="hidden" name="pg_merchant_id" value="<?php echo $paymentParams['platron']['merchant_id'];?>">
		<input type="hidden" name="pg_order_id" value="<?php echo $order['Order']['id'];?>">

		<input type="hidden" name="pg_payment_system" value="<?php echo @$using?>">
		<input type="hidden" name="pg_salt" value="<?php echo $salt; ?>">
		<input type="hidden" name="pg_user_contact_email" value="<?php echo $order['User']['email'];?>">
		<input type="hidden" name="pg_sig" value="<?php echo $sig; ?>">



<?php

 	}
 	else
 	if (@$system == 'rbk'){
?>
	<form name="order_<?php echo $order['Order']['id'].'_'.@$using;?>" action="https://rbkmoney.ru/acceptpurchase.aspx" method="POST">
		<input type="hidden" name="eshopId" value="<?php echo $paymentParams['rbk']['siteID'];?>">
		<input type="hidden" name="orderId" value="<?php echo $order['Order']['id'];?>">
		<input type="hidden" name="serviceName" value="<?php echo $orderMessage; ?>">
		<input type="hidden" name="recipientAmount" value="<?php echo number_format($order['Order']['sumToPay'], 2, '.', '');?>">
		<input type="hidden" name="recipientCurrency" value="RUR">
		<input type="hidden" name="user_email" value="<?php echo $order['User']['email'];?>">
		<input type="hidden" name="version" value="2">
		<input type="hidden" name="preference" value="<?php echo @$using?>">
		<input type="hidden" name="successUrl" value="https://panel.teamserver.ru/">
		<input type="hidden" name="failUrl" value="https://panel.teamserver.ru/orders">

<?php

 	}
 	else
 	if (@$system == 'webmoney'){
 	/* <form name="order_<?php echo $order['Order']['id'].'_webmoney_'.@$using;?>" action="https://paymaster.ru/Payment/Init" method="POST" >*/

?>
	<form name="order_<?php echo $order['Order']['id'].'_webmoney_'.@$using;?>" action="https://paymaster.ru/Payment/Init" method="POST" >
		<input type="hidden" name="LMI_PAYMENT_AMOUNT" value="<?php echo number_format($order['Order']['sumToPay'], 2, '.', '');?>">
		<input type="hidden" name="LMI_PAYMENT_DESC" value="<?php echo $orderMessage; ?>">
		<input type="hidden" name="LMI_PAYMENT_NO" value="<?php echo $order['Order']['id'];?>">
		<input type="hidden" name="LMI_MERCHANT_ID" value="<?php echo $paymentParams['wm']['wallet'];?>">
		<input type="hidden" name="LMI_CURRENCY" value="RUB">
		<input type="hidden" name="LMI_PAYER_EMAIL" value="<?php echo $order['User']['email'];?>">
<?php

 	}
 	else
 	if (@$system == 'yandex'){
?>
	<form name="order_<?php echo $order['Order']['id'].'_yandex_'.@$using;?>" action="https://money.yandex.ru/eshop.xml" method="POST" >
		<input type="hidden" name="scid" value="<?php echo $paymentParams['yandex']['scid'];?>">
		<input type="hidden" name="ShopID" value="<?php echo $paymentParams['yandex']['ShopID'];?>">
		<input type="hidden" name="customerNumber" value="<?php echo $order['Order']['id']?>">
		<input type="hidden" name="orderNumber" value="<?php echo $order['Order']['id']?>">
		<input type="hidden" name="Sum" value="<?php echo number_format($order['Order']['sumToPay'], 2, '.', '');?>">
		<input type="hidden" name="MyField" value="<?php echo $orderMessage; ?>">
<?php
 	}
 	else
 	if (@$system == 'yamoney'){
?>
	<form name="order_<?php echo $order['Order']['id'].'_yamoney_'.@$using;?>" method="POST" action="https://money.yandex.ru/quickpay/confirm.xml">
		<input type="hidden" name="receiver" value="<?php echo $paymentParams['yamoney']['wallet'];?>">
		<input type="hidden" name="formcomment" value="<?php echo 'TeamServer.ru: '.$orderMessage; ?>">
		<input type="hidden" name="short-dest" value="<?php echo $orderMessage; ?>">
		<input type="hidden" name="writable-targets" value="false">
		<input type="hidden" name="comment-needed" value="false">
		<input type="hidden" name="label" value="<?php echo $order['Order']['id']; ?>">
		<input type="hidden" name="quickpay-form" value="shop">
		<input type="hidden" name="targets" value="<?php echo $orderMessage; ?>">
		<input type="hidden" name="sum" value="<?php echo number_format($order['Order']['sumToPay'], 2, '.', '');?>" data-type="number" >
		<input type="hidden" name="fio" value="0">
		<input type="hidden" name="mail" value="0" >
		<input type="hidden" name="phone" value="0">
		<input type="hidden" name="address" value="0">
<?php
 	}
	else
	if (@$system == 'qiwi') {
?>
		<div style="display: none;" class="ui segment" id="qiwiForm<?php echo $formId.'_'.$order['Order']['id']?>">
			<form name="order_<?php echo $order['Order']['id'].'_qiwi_'.@$using;?>" action="https://w.qiwi.ru/setInetBill_utf.do" method="POST" class="ui form">
			<div class="ui small message">
				Пожалуйста, введите номер своего мобильного телефона, на который привязан кошелёк Qiwi (последние 10 цифр).
			</div>
			<input type="hidden" name="from" value="<?php echo $paymentParams['qiwi']['login'];?>">
			<input type="hidden" name="summ" value="<?php echo number_format($order['Order']['sumToPay'], 2, '.', '');?>">
			<input type="hidden" name="com" value="<?php echo $orderMessage; ?>">
			<input type="hidden" name="lifetime" value="336">
			<input type="hidden" name="txn_id" value="<?php echo $order['Order']['id']?>">
			<?php
						echo $this->Form->input('to',array (	'size' => '25',
														'name' => 'to',
														'id'=>'qiwiPhoneNum',
														'div' => 'field',
														'label' => false));
						?>

						<?php
						echo $this->Form->submit( 'Оплатить',
											array('class' => 'ui fluid orange button'));
						?>

			<small><i>Мы не сохраняем номер у себя, он нужен только для создания счёта в системе Qiwi.</i> </small>
		<script type="text/javascript">
			$(function() {

				$("#qiwiPhoneNum").keyup(function() {

					var qiwiReq = $('#qiwiPhoneNum').val().trim();
					var qiwiReqFiltered = '';

					if (qiwiReq.length > 10){
						qiwiReq = qiwiReq.substr(0, 10);
					}

					if (qiwiReq.match(/^[0-9]{1,10}$/)) {
						$('#qiwiPhoneNum').val(qiwiReq);
					}
					else
					{
						for (var i = 0; i < qiwiReq.length - 1; i++) {
							if (qiwiReq[i].match(/\d/)){
								qiwiReqFiltered += qiwiReq[i];
							}

						};

						$('#qiwiPhoneNum').val(qiwiReqFiltered);
					}

					return true;
				});

			});
		</script>
		</div>
<?php
	}

	// Форму не закрываю, т.к. отдельно будет закрытие по $this->Form->js



?>

