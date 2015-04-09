<?php
/*
 * Created on 28.07.2010
 *
 */
 include('loading_params.php');
 //pr(@$orderResult);
 //pr($script);
?>
<script type="text/javascript">
	$(function() {
		$('#prolongate_server').empty();
	});
</script>
<div id="flash"><?php echo $this->Session->flash(); ?></div>
<?php

if (!empty($locationsList))
{
 echo $this->Form->create('Order', array('class' => 'form-horizontal'));
?>
<table class="new_order">
				<tr>
					<td class="param_value" colspan="2" style="padding-left: 23px;">

			            <label class="control-label highlight6" for="locations">Локация:</label>
			            <div class="controls">

						<?php echo $this->Form->input('Location.id',array ('options'=>$locationsList,
															  'selected'=>$locationId,
															  'id'=>'locations',
															  'div' => false,
															  'label' => false));?>
						</div>

					</td>
				</tr>
				<tr>
					<td class="param_value" colspan="2" style="padding-left: 23px;">
						<label class="control-label highlight6" for="types">Вид:</label>
			            <div class="controls">

						<?php echo $this->Form->input('Type.id',array ('options'=>$typesList,
															  'selected'=>$typeId,
															  'id'=>'types',
															  'div' => false,
															  'label' => false));?>
						</div>
					</td>
				</tr>

				<tr>
					<td class="param_value" colspan="2" style="padding-left: 23px;">

						<label class="control-label highlight6" for="games">Сервер:</label>
			            <div class="controls">
						<?php 	echo $this->Form->input('GameTemplate.id', array('options' => $gameTemplatesList,
										'selected' => @$gameTemplateId,
										'div' => false,
										'id'  => 'games',
										'label' => false)); ?>

						</div>

				</td>
				</tr>
				<tr>
					<td class="param_value" colspan="2" style="padding-left: 23px;">
						<label class="control-label highlight6" for="typeDiscount">Тип:</label>
			            <div class="controls">
							<div style="float: left;">
							<?php 	echo $this->Form->input('Server.privateType', array('options' => $typeDiscount,
											'selected' => '1',
											'div' => false,
											'id'  => 'typeDiscount',
											'label' => false)); ?>
							</div>

							<a href="#" class="qlabs_tooltip_right qlabs_tooltip_style_1">
							<span id="privateHelp"></span>
							<div class="icons" style="display: inline;">
								<div id ="privateHelpIco"
									  class="ui-icon ui-icon-info"
									  style="float: left; margin-top: .5em;  margin-left: 5px; cursor:pointer;"
									  rel="#privateHelp">
								</div>

							</div>
							</a>
						</div>

				</td>
				</tr>
				<?php /* ?>
				<tr>
					<td class="param_name">Мод:</td>
					<td class="param_value"><?php echo $this->Form->input('Mod.id',
												array(	'options' => $modsList,
														'id' => 'vars',
														'div' => false,
														'label' => false));?>
					</td>
				</tr>
				<?php */ ?>
				<tr>
					<td class="param_value" colspan="2" style="padding-left: 23px;">

			            <div class="controls">
							<?php
							echo $this->Form->input('slots', array('div' => false,
																'label' => false,
																'type'=>'hidden'));?>
							<?php
							echo $this->Form->input('GameTemplate.0.price', array('div' => false,
																'label' => false,
																'id'=>'price',
																'type'=>'hidden'));?>
							<div class="accent">
								<div id="OrderSlotsDisabled" title="Перемещайте ползунок для выбора значения" style="display: inline;"></div>
								<div style="display: inline;">X</div>
								<div id="priceDisabled" title="Перемещайте ползунок для выбора значения" style="display: inline;"></div>
								руб.
							</div>
							<div class="accent_lower">
							за слот
							</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class="param_value" colspan="2" style="padding-left: 23px;">
						<div style="float: left;">
							<label class="control-label accent" for="slider" style="margin-right: 25px; padding-top: 0px;">Слотов:</label>
						</div>
					<div id="slider" title="Перемещайте ползунок для выбора значения" style="width: 220px;float: left; margin-top: 5px;"></div>
					</td>
				</tr>
				<tr>
					<td class="param_value" colspan="2" style="padding-left: 23px;">
					<div class="controls">
						<?php
						echo $this->Form->input('month', array('div' => false,
															'label' => false,
															'type'=>'hidden'));?>
						<div id="OrderMonthDisabled" class="accent"></div>
						<?php
						echo $this->Form->input('discount', array('div' => false,
															'label' => false,
															'title'=>'Скидка даётся при аренде на срок более 3 месяцев',
															'type'=>'hidden',
															'size'=>'2',
															'style'=>'border:0; font-weight:bold;'));?>

						<div id="discountDisabled" style="display: inline;"></div>
					</div>
					</td>
				</tr>
				<tr>
					<td class="param_value" colspan="2" style="padding-left: 23px;">
					<div style="float: left;">
						<label class="control-label accent" for="sliderMonth" style="margin-right: 25px; padding-top: 0px;">Срок оплаты:</label>
					</div>
					<div id="sliderMonth" title="Перемещайте ползунок для выбора количества месяцев"  style="width: 220px;float: left; margin-top: 5px;"></div>
					</td>
				</tr>
				<tr>
					<td class="param_value" colspan="2">
						<div class="well" style=" margin-bottom: 5px; min-height: 50px;">
							<div style="display: block; float: left; height: 60px; margin-right: 25px;">
								<label class="control-label accent" for="services">Дополнительные услуги:</label>
							</div>
							<div id="services" style=""><?php echo $this->Html->image('loading.gif');?> Загрузка списка доступных услуг...</div>
						</div>
					</td>
				</tr>
				<tr>
					<td class="param_value" colspan="2" style="padding-left: 23px;">

						<label class="control-label accent" for="promoCode" style="padding-top: 0px;">Промо-код:<br/><small>(если есть)</small></label>
			            <div class="controls">
							<div class="input-append"  style="padding-top: 5px;"><?php

							echo $this->Form->input('PromoCode.code', array('div' => false,
																'label' => false,
																'id' => 'promoCode'));

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
						<div id="PromoCodeDisabled" style="margin-left: 35px; float: left;"></div>
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
					<td class="param_value" colspan="2">
						<div class="well">
						<label class="control-label accent" for="personalAccPartAmount" style="padding-top: 10px; ">Оплата с Лицевого счёта:
						<?php

							if ($balance > 0)
							{
								echo "<br/><small>(Доступно ".$balance." руб.)</small>";
							}
							else
							{
							 	echo "<br/><small>(Нет средств)</small>";
							}
						?></label>
						<?php

							echo $this->element('pay_from_account', array('userBalance' => $balance));

						?>
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2" class="separator">
					</td>
				</tr>
				<tr>
					<td class="param_name">Стоимость услуг:</td>
					<td class="param_value">

					<?php
					echo $this->Form->input('serviceSum', array('div' => false,
														'label' => false,
														'id' => 'ServiceSum',
														'type'=>'hidden',
														'value'=>'0'));?>

					<div id="ServiceSumDisabled" class="accent">0</div>
					<div class="accent_lower"> руб. в месяц</div>

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
						<div class="accent"> рублей за сервер</div>

					</td>
				</tr>
				<tr>
					<td class="param_name">Скидка составляет:</td>
					<td class="param_value">
					<div id="sumDiscount" class="accent"></div>
					<div class="accent_lower"> руб.</div>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
					<div id='warning-csgo' class="ui-state-highlight ui-corner-all" style="margin-top: 8px; margin-bottom: 8px; padding: 0 .7em; text-align: left; display: none;">
							<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
							<small>
							<strong>Перед оплатой заказа вы должны согласиться со следующими утверждениями:</strong>
							<ul>
								<li>Я понимаю, что как игра Counter-strike: Global Offensive (далее CS:GO), так и серверы для неё, еще находятся в разработке и имеют множество нерешенных проблем и ошибок;</li>
								<li>Я понимаю, что компания ООО "БС Гейм" (далее TeamServer) не несёт ответственности за исправление ошибок в CS:GO и не может решить все проблемы, связанные с этими ошибками, а также с недостающей функциональностью серверов;</li>
								<li>Я подтверждаю, что у меня есть незаблокированный Steam-аккаунт, в котором есть игра CS:GO, чтобы с помощью этого аккаунта иметь возможность обновлять арендуемый игровой сервер. С другой стороны, TeamServer обязуется предоставить возможность обновления сервера без ограничений;</li>
								<li>Я знаю, что после официального выхода игры CS:GO и серверов для неё (ориентировочно 21 августа 2012г.), все оплаченные дни аренды на эту дату будут перерассчитаны по новым ценам, которые будут объявлены TeamServer не менее чем за три дня до официального выхода игры;</li>
								<li>Я обязуюсь не предъявлять претензий по поводу указанной выше информации.</li>
							</ul>
							</small>
							</p>
					</div>
					<div id='warning-l4d-t100' class="ui-state-highlight ui-corner-all" style="margin-top: 8px; margin-bottom: 8px; padding: 0 .7em; text-align: left; display: none;">
							<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
							<small>
							<strong>Перед оплатой заказа вы должны согласиться со следующими утверждениями:</strong>
							<ul>
								<li>Я понимаю, что движок игры создан под tickrate 30 и поднятие этого значения может привести к непредсказуемым последствиям;</li>
								<li>Я понимаю, что компания ООО "БС Гейм" (далее TeamServer) не несёт ответственности за ошибки в работе сервера из-за увеличения tickrate;</li>
								<li>Я понимаю, что увеличение tickrate более чем в три раза от базового значения 30, ведёт к повышенной нагрузке сервера и TeamServer может вводить дополнительные ограничения на потребляемые сервером ресурсы (с уведомлением по e-mail или тикеты);</li>
								<li>Я обязуюсь не предъявлять претензий по поводу указанной выше информации.</li>
							</ul>
							</small>
							</p>
					</div>
					<?php 	echo $this->Form->input('Server.id', array(

									'div' => false,
									'label' => false)); ?>
					<?php

						echo $this->Js->submit('Отправить',
													array(
														'url'=> array(
																		'controller'=>'Orders',
																		'action'=>'add'
														 ),
														'update' => '#add_server',
														'class' => 'btn btn-primary',
														'before' =>$loadingShow,
														'complete'=>$loadingHide,
														'buffer' => false));

						echo $this->Form->end();
					?>
					</td>
				</tr>
</table>
<?php

			echo $this->Js->writeBuffer(); // Write cached scripts
?>
<script type="text/javascript">

					$(function() {
						var balance = <?php echo $balance; ?>;

						function GetTemplates () {
							  $('#games').attr('disabled','disabled');
							  $('#vars').attr('disabled','disabled');
							  $('#typeDiscount').attr('disabled','disabled');
						      $.getJSON('/gameTemplates/getTemplates/' + $('#types').val(),
						                  {}, function(tmps) {
							                    if(tmps !== null) {
							                      populateTemplatesList(tmps);
							                      GetServices ();
							                      populatePrivateTypes ();
							                      SlotsSlider();
												  GetMods();
												  Price();
												  TypeDiscount();
												  Sum();
												  $('#games').removeAttr('disabled');
												  $('#vars').removeAttr('disabled');
												  $('#typeDiscount').removeAttr('disabled');

							                    }
						        		  	});

							}

						function populateTemplatesList(tmps) {
							  var options = '';
							  <?php
							  if (@$gameTemplateId){
							  	  echo "var selectedGame = '".$gameTemplateId."'";
							  }
							  else
							  {
							  	  echo "var selectedGame = 0";
							  }
							  ?>


							  $.each(tmps, function(index, tmp) {
							  	if (selectedGame == index){
							  		options += '<option value="' + index + '" selected="selected">' + tmp + '</option>';
							  	}
							  	else
							  	{
							  		options += '<option value="' + index + '">' + tmp + '</option>';
							  	}

							  });
							  $('#games').html(options);
							  $('#types').show();

							}

						function GetMods () {
					      $.getJSON('/gameTemplates/getMods/' + $('#games').val(),
					                  {}, function(mods) {
						                    if(mods !== null) {
						                      populateModsList(mods);
						                    }
						        		  });
						}

						function GetServices () {
						  $("#services").html('<?php echo $this->Html->image('loading.gif');?> Загрузка списка доступных услуг...');


					      $.getJSON('/services/getServices/' + $('#games').val() + '/all/' + $('#locations').val(),
					                  {}, function(services) {
						                    if(services !== null) {
						                      populateServices(services);
						                    }
						                    else
						                    {
						                    	$("#services").html('Отсутствуют.');
						                    }
						        		  });
						        		  servicesSum();
							}

						function populateServices(services){
							  var game = $('#games').val();
							  var options = '';

						  	$.each(services, function(id, service) {
						  			/*if (id == 1 || id == 2){
									  	// Тут сделать из этих услуг выбор радио-кнопкой
									  	var choiseType = 'radio';
									  	var choiseName = 'data[Service][0][id]';
									  }
									  else
									  {
									  	*/
									  	<?php /* Иначе рисовать чекбоксы */ ?>
									  	var choiseType = 'checkbox';
									  	var choiseName = 'data[Service][' + id + '][id]';
									  //}
									options +='<div class="qlabs_tooltip_right qlabs_tooltip_style_1">';
									options +='<span><strong>' + service['longname'] + '</strong>' + service['desc'] + '</span>';
									options += '<label class="checkbox">';
							  		options +='<input type="' + choiseType + '" id="service_' + id + '" name="' + choiseName + '" value="' + id + '" class="service_' + id +'"><div class="accent_lower" style="z-index: 1;">"' + service['longname']	+ '" за</div> <div class="accent">' + service['price'] + ' руб./мес.</div></input>';
							  		options += '</label>';
							  		options +='</div>';

							  		options +='<input type="hidden" value="' + service['price'] + '" id="service_price_' + id + '"/>';


							  });


						  		$("#services").html(options);

						  		$.each(services, function(id, service) {
							  		var service_id_button = '#service_' + id;

									$(service_id_button).click(function() {

										servicesSum();
										Sum();

									});

								});

						}

						function servicesSum(){
							var newSum = 0;
							$('#services input:checked').each(function() {

								if( $(this).attr('checked') ) {
									newSum = newSum + eval($('#service_price_' + $(this).val()).val());
							     }

							});

							$("#ServiceSum").val(newSum);
							$("#ServiceSumDisabled").text(newSum);
						}

						function populatePrivateTypes (){
							  var type = $('#types').val();
							  var game = $('#games').val();
							  var options = '';
							  if (type >= 2 && type < 5){
							  	  options = '<option value="0">Публичный сервер</option>';
							  }
							  else
							  {
							  	  if (game == 7){
								  	  options +='<option value="0">Публичный сервер</option>';
								  	  options +='<option value="2">Приватный с автоотключением</option>';
							  	  }
							  	  else
							  	  {
							  	      options +='<option value="0">Публичный сервер</option>';
								  	  options +='<option value="1" selected="selected">Приватный с паролем</option>';
								  	  options +='<option value="2">Приватный с автоотключением</option>';
							  	  }


							  }
							  $('#typeDiscount').html(options);
							  TypeDiscountHelp();

						}

						function populateModsList(mods) {
							  var options = '';

							  $.each(mods, function(index, mod) {
							    options += '<option value="' + index + '">' + mod + '</option>';
							  });
							  $('#vars').html(options);
							  $('#games').show();

							}

						function SlotsSlider () {

							var selectedGame = $('#games').val();


							//Сюда добавлять значения слотов
							<?php echo $script;?>

							$("#slider").slider({
								range: "max",
								value: v,
								min: mi,
								max: ma,
								step: 1,
								slide: function(event, ui) {
									$("#OrderSlots").val(ui.value);
									$("#OrderSlotsDisabled").text(ui.value);
									Sum();
								}
							});

							$("#OrderSlots").val($("#slider").slider("value"));
							$("#OrderSlotsDisabled").text($("#slider").slider("value"));
						};

						function MonthsSlider () {

							$("#sliderMonth").slider({
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


							$("#OrderMonth").val($("#sliderMonth").slider("value"));
							$("#OrderMonthDisabled").text(monthText($("#sliderMonth").slider("value")));

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
							var slots = $("#OrderSlots").val();
							var price = $('#price').val();
							var discount = $("#OrderDiscount").val();
							var serviceSum = $('#ServiceSum').val();
							var payPart = $("#personalAccPartAmount").val();

							var sumNoDiscount = month*(eval(serviceSum) + eval(price*slots));
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
							$("#sumDiscount").text(sumDiscount);

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

							$("#OrderDiscount").val(discount);
							$("#discountDisabled").html(discountText);

						}

						function TypeDiscount (){
							var typeDiscountVar = $("#typeDiscount").val();
							var price = $('#price').val();

							if (typeDiscountVar == 1) {
								price = pricePassword;
							}
							else if (typeDiscountVar == 2) {
								price = pricePower;
							}

							$("#price").val(price);
							$("#priceDisabled").text(price);

						}

						function TypeDiscountHelp(){
							var typeDisc = $("#typeDiscount").val();
							var hlp = "";
							if (typeDisc == 0){
								hlp = "<strong>Публичный сервер</strong>Сервер может быть включён постоянно, \
									   наличие пароля необязательно.";
								$("#privateHelp").html(hlp);
							}
							else
							if (typeDisc == 1){
								hlp = "<strong>Приватный с паролем</strong>Сервер может быть включён постоянно, \
									   наличие пароля обязательно. \
									   Если пароль не будет установлен,\
									   сервер будет отключён автоматически. ";
								$("#privateHelp").html(hlp);
							}
							else
							if (typeDisc == 2){
								hlp = "<strong>Приватный с паролем и автоотключением</strong>Сервер выключается автоматически, \
									   если на нём нет игроков в течение\
									   30 минут.\
									   Наличие пароля обязательно. \
									   Если пароль не будет установлен,\
									   сервер будет отключён автоматически. ";
								$("#privateHelp").html(hlp);
							}

						}

						function Price (){
							$("#price").val(price);
							$("#priceDisabled").text(price);
						}

						$("#types").change(function() {
								GetTemplates();
								return false;
						});

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

						$("#locations").change(function() {
							GetServices();
							Price();
							TypeDiscount()
							Sum();
							return false;
						});

						$("#games").change(function() {

								var game = $('#games').val();

								SlotsSlider();
								populatePrivateTypes ();
								GetMods();
								GetServices();
								Price();
								TypeDiscount()
								Sum();

								if (game == 39)
								{
									$('#warning-l4d-t100').hide();
									$('#warning-csgo').show();
								}
								else
								if (game == 2 || game == 8)
								{
									$('#warning-csgo').hide();
									$('#warning-l4d-t100').show();
								}
								else
								{
									$('#warning-csgo').hide();
									$('#warning-l4d-t100').hide();
								}

								return false;
						});

						$("#typeDiscount").change(function() {
								TypeDiscountHelp();
								Price();
								TypeDiscount();
								Sum();
								return false;
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

						GetTemplates();
						SlotsSlider();
						//GetServices();
						MonthsSlider();
						Discount();
						GetMods();
						Price();
						TypeDiscount();
						TypeDiscountHelp();
						Sum();
					});
	</script>
<?php
}
?>
