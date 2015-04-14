<?php
/*
 * Created on 28.07.2010
 *
 */
 include('loading_params.php');
 //pr(@$orderResult);
 //pr(@$this->data);
?>
<script type="text/javascript">
	$(function() {
		$('#add_server').empty();
	});
</script>
<div id="flash"><?php echo $this->Session->flash(); ?></div>
<?php
 echo $this->Form->create('Order', array('class' => 'form-inline'));
 $privateTypeId = $this->data['ServerTemplateUser']['privateType'];

 $slotDiscount = 0;
 $slotCost = $this->data['GameTemplate'][0]['price']; // Цена слота без скидок
 if ($privateTypeId == 1){
	$slotCost = $this->data['GameTemplate'][0]['pricePrivatePassword'];
 }
 else
 if ($privateTypeId == 2){
	$slotCost = $this->data['GameTemplate'][0]['pricePrivatePower'];
 }

 // Составить массив из ID подключенных услуг
 $serverServicesIds = array();
 if (!empty($this->data['Service'])){
	 foreach ( $this->data['Service'] as $service ) {
	 	$serverServicesIds[] = $service['id'];
	 }
 }
?>
<table class="new_order">

				<tr>
					<td class="param_name">Сервер:</td>
					<td class="param_value">
					<div class="accent_lower">
						<?php
								echo $this->data['GameTemplate'][0]['longname']." (".$typeDiscount[$privateTypeId].")";
						?>
					</div>
					</td>
				</tr>
				<tr>
					<td class="param_name">Слотов:</td>
					<td class="param_value">
					<div class="accent_lower">
					<?php
					echo $this->Form->input('slotsDisabled', array('div' => false,
														'label' => false,
														'type' => 'hidden',
														'size'=>'2',
														'style'=>'border:0; font-weight:bold;',
														'value' => $this->data['ServerTemplateUser']['slots']
															));
					echo $this->Form->input('slotCost', array('div' => false,
														'label' => false,
														'type' => 'hidden',
														'size'=>'4',
														'id' => 'slotPrice',
														'style'=>'border:0; font-weight:bold;',
														'value' => $slotCost));?>

					<?php echo $this->data['ServerTemplateUser']['slots']." x ".$slotCost; ?> руб. за слот
					</div>
					</td>
				</tr>
				<tr>
					<td class="param_name">Текущая аренда до:</td>
					<td class="param_value" style="vertical-align: top;">
						<div class="accent">
							<?php echo $this->Common->niceDate($this->data['ServerTemplateUser']['payedTill']);?>
						</div>
					</td>
				</tr>
				<tr>
					<td class="param_name">Продлить аренду на:</td>
					<td class="param_value">


					<?php
					echo $this->Form->input('month', array('div' => false,
														'label' => false,
														'type'=>'hidden'));?>
					<div id="OrderMonthDisabled" class="accent"></div>
					<div id="sliderMonthProlongate" title="Перемещайте ползунок для выбора количества месяцев"></div>
					</td>
				</tr>
				<tr>
					<td></td>
					<td class="param_value">

					<?php
					echo $this->Form->input('discountDisabled', array('div' => false,
														'label' => false,
														'title'=>'Скидка даётся при аренде на срок более 3 месяцев',
														'type' => 'hidden',
														'size'=>'2',
														'style'=>'border:0; font-weight:bold;'));?>
					<div id="OrderDiscount" style="display: inline;" title="Скидка даётся при аренде на срок более 3 месяцев"></div>
					</td>
				</tr>
				<tr>
					<td class="param_name" style="vertical-align: top;">Дополнительные услуги:</td>
					<td class="param_value" style="vertical-align: top; padding-top: 5px;">

					<?php

						$serviceSum = 0;
						if (!empty($this->data['Service'])){

							 foreach ( $this->data['Service'] as $service ) {
							 	$serviceSum += $service['price'];

							 	echo $this->Html->div('checkmark_circle_checked qlabs_tooltip_right qlabs_tooltip_style_1', '<span>'.$service['description'].'</span>'.$service['longname'].' - '.$service['price'].' руб./мес.');

	       						echo '<br/>';

							 }
						 }
						 else
						 {
						 	echo "Не подключены";
						 }

					?>

					</td>
				</tr>
				<tr>
					<td class="param_name">Учесть промо-код:<br/><small>(если есть)</small></td>
					<td class="param_value">

					<div class="controls">
						<div class="input-append"  style="padding-top: 5px;"><?php
							echo $this->Form->input('PromoCode.code', array('div' => false,
																'label' => false,
																'id' => 'promoCode',
																'class' => 'span2'));

							echo $this->Html->link( 'Проверить код',
											  '#',
											  array(
											  			'id' => 'checkCode',
											  			'class' => 'btn'
											  			)
											  );

							?>
						</div>
					</div>
					<div id="PromoCodeDisabled"></div>
					<?php
					echo $this->Form->input('Promo.discount', array('div' => false,
														'label' => false,
														'id' => 'promoDiscount',
														'type'=>'hidden',
														'value' => '0'));
					?>
					</td>
				</tr>
				<tr>
					<td style="vertical-align: top;" class="param_name">
					Оплата с Лицевого счёта:
					<?php

						if ($this->data['User'][0]['money'] > 0)
						{
							echo "<br/><small>(Доступно ".floatval($this->data['User'][0]['money'])." руб.)</small>";
						}
						else
						{
						 	echo "<br/><small>(Нет средств)</small>";
						}
					?>
					</td>
					<td>
					<?php

						echo $this->element('pay_from_account', array('userBalance' => $this->data['User'][0]['money']));

					?>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="separator">
					</td>
				</tr>
		</table>
		<table class="new_order">
				<tr>
					<td class="param_name" style="width: 50%;">Стоимость текущих услуг:</td>
					<td class="param_value">
					<?php
					if (empty($serviceSum)){
						$serviceSum = 0;
					}
					echo $this->Form->input('serviceSum', array('div' => false,
														'label' => false,
														'value' => $serviceSum,
														'id' => 'ServiceSum',
														'type'=>'hidden'));?>

					<div id="ServiceSumDisabled" class="accent"><?php echo $serviceSum; ?></div>
					руб./мес.
					</td>
				</tr>
				<tr>
					<td class="param_name">Итого<br/> с учётом услуг и скидки:</td>
					<td class="param_value">

					<?php
					echo $this->Form->input('sum', array('div' => false,
														'label' => false,
														'type'=>'hidden'));?>
					<div id="OrderSumDisabled" class="accent_more"></div>
					руб. за сервер
					</td>
				</tr>
				<tr>
					<td class="param_name">Скидка составляет:</td>
					<td class="param_value">

					<div id="OrderSumDiscountDisabled" class="accent"></div>
					руб.
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
					<?php 	echo $this->Form->input('Server.id', array(

									'div' => false,
									'label' => false)); ?>
					<?php

						echo $this->Js->submit('Продлить аренду',
													array(
														'url'=> array(
																		'controller'=>'Orders',
																		'action'=>'prolongate', $this->data['ServerTemplateUser']['id']
														 ),
														'update' => '#prolongate_server',
														'class' => 'btn btn-primary',
														'before' =>$loadingShow,
														'complete'=>$loadingHide,
														'buffer' => false));

						echo $this->Form->end();
					?>
					</td>
				</tr>

</table>
<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;">
<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
<small>
Чтобы изменить список подключенных услуг, обратитесь в техподдержку.
</small>
</p>
</div>


<script type="text/javascript">
	$(function() {

		var price = $('#slotPrice').val();
		var serviceSum = $('#ServiceSum').val();
		var balance = <?php echo floatval($this->data['User'][0]['money']); ?>;


		function MonthsSlider () {

			$("#sliderMonthProlongate").slider({
				range: "max",
				value: 1,
				min: 1,
				max: 9,
				step: 1,
				slide: function(event, ui) {
					$("#OrderMonth").val(ui.value);
					$("#OrderMonthDisabled").text(monthText(ui.value));
					Discount();
					Sum();
				}
			});

			$("#OrderMonth").val($("#sliderMonthProlongate").slider("value"));
			$("#OrderMonthDisabled").text(monthText($("#sliderMonthProlongate").slider("value")));

		};

		function monthText(monthValue){
			if (monthValue == 1){
				var monthText = '1 месяц';
			}
			else
			if (monthValue > 1 && monthValue < 5)
			{
				var monthText = monthValue + ' месяца';
			}
			else
			if (monthValue >= 5 && monthValue < 21)
			{
				var monthText = monthValue + ' месяцев';
			}
			return monthText;
		}

		function Sum () {

			var month = $("#OrderMonth").val();
			var slots = $("#OrderSlotsDisabled").val();
			var discount = $("#OrderDiscountDisabled").val();
			var payPart = $("#personalAccPartAmount").val();

			var sumNoDiscount = month*( eval(serviceSum) + eval(price*slots) );
			var sumDiscount = sumNoDiscount - Math.round(sumNoDiscount*((100-discount)/100));

			var sum = sumNoDiscount - sumDiscount;

			if (payPart > balance)
			{
				payPart = balance;
			}

			if ($("#personalAccPart").attr('checked') || $("#personalAccFull").attr('checked')){
				if (balance > 0 && balance < sum){
					$("#personalAccFull").removeAttr('checked').attr('disabled','disabled');
					$("#personalAccPart").removeAttr('disabled').attr('checked','checked');
					$('#personalAccPartPay').show();
					$('#zonePart').addClass('tbl_hover_green');
					$('#zoneFull').removeClass('tbl_hover_green');
				}
				else
				if (balance >= sum)
				{
					$("#personalAccFull").removeAttr('disabled');

				}

				if ($("#personalAccPart").attr('checked'))
				{

					if (payPart >= sum)
					{
						payPart = 0;
						$('#personalAccPartPay').hide();
						$("#personalAccPartAmount").val('');
						$("#personalAccFull").attr('checked','checked');
						$('#zonePart').removeClass('tbl_hover_green');
						$('#zoneFull').addClass('tbl_hover_green');
					}
					else
					if (payPart > 0 && payPart < sum)
					{
						sum = eval(sum) - eval(payPart);
						$("#personalAccPartAmount").val(payPart);
						$('#zonePart').addClass('tbl_hover_green');
						$('#zoneFull').removeClass('tbl_hover_green');
					}
				}
			}

			$("#OrderSum").val(sum);
			$("#OrderSumDisabled").text(sum);
			$("#OrderSumDiscountDisabled").text(sumDiscount);

		}

		function Discount (){
				var month = $("#OrderMonth").val();
				var clientDiscount = <?php echo $userDiscount; ?>;

				var promoDiscount = $('#promoDiscount').val();

				clientDiscount = eval(clientDiscount) + eval(promoDiscount);

				if (month < 3){
					discount = clientDiscount;
				}
				else if (month >= 3 && month < 6){
					discount = <?php echo @$discount[3];?> + clientDiscount;
				}
				else if (month >= 6 && month < 9){
					discount = <?php echo @$discount[6];?>+ clientDiscount;
				}
				else if (month >= 9){
					discount = <?php echo @$discount[9];?>+ clientDiscount;
				}

				if (discount == 0){
					discountText = '<div class="accent_lower">(без скидки)</div>';
				}
				else
				{
					discountText = '<div class="accent">со скидкой ' + discount + '%</div>';
				}

				$("#OrderDiscountDisabled").val(discount);
				$("#OrderDiscount").html(discountText);

			}

		function CheckPromo () {
          var code = $('#promoCode').val();

	      $.getJSON('/promos/checkCode/' + code, {},
	      		function(promo) {
                    if(promo !== null) {
                      $('#promoDiscount').val(promo.discount);
                      $('#promoCode').attr('style','display:none');
                      $('#checkCode').hide();
                      $('#PromoCodeDisabled').text('По коду ' + code + ' вам дана cкидка ' + promo.discount + '%');
                      Discount();
          			  Sum();
                    }

        		  });


		}

		$("#checkCode").ajaxStart(function() {
			$('#promoCode').attr('class','ui-autocomplete-loading');
		});

		$("#checkCode").ajaxStop(function(){
			$('#promoCode').removeAttr('class');
		});

		$("#checkCode").click(function() {
			CheckPromo();
		});

		$("#promoCode").keyup(function() {
			$('#checkCode').addClass('btn-warning');
		});

		$("#personalAccPartAmount").keyup(function() {
							Sum();
						});

		$("#personalAccPart").click(function() {
											Sum();
											$('#personalAccPartPay').show('highlight');
											$('#zoneFull').removeClass('tbl_hover_green');
										});

		$("#personalAccFull").click(function() {
											$('#personalAccPartPay').hide();
											$('#zonePart').removeClass('tbl_hover_green');
											$('#zoneFull').addClass('tbl_hover_green');
											Sum();
										});
		$("#personalAccNo").click(function() {
											$('#personalAccPartPay').hide();
											$('#zonePart').removeClass('tbl_hover_green');
											$('#zoneFull').removeClass('tbl_hover_green');
											Sum();
														});

		MonthsSlider();
		Discount();
		Sum();



	});
	</script>
<?php

			echo $this->Js->writeBuffer(); // Write cached scripts
?>
