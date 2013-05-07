<?php
/*
 * Created on 30.07.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
 include('../loading_params.php');
 //pr($orderResult); //debug
 
?>
<div id="flash"><?php echo $session->flash(); ?></div>
<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
		<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
		<small>
		При оплате заказа вы соглашаетесь с <?php
		
			echo $html->link('Договором оферты',
							 'http://www.teamserver.ru/aboutus/dogovor', 
							 array(
									'target' => '_blank',
									'style' => 'text-decoration: underline; color: #f00;'
								  )
							 );
		
		?>.
		</small>
</p></div>

<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em; margin-top: 5px;"> 
			<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
			<small>
			Щелкните на выбранном способе оплаты, чтобы перейти на сайт платёжной системы для завершения процедуры.
			<br/><br/>
			Внимание! После оплаты Яндекс.Деньгами вам нужно будет вручную вернуться в панель и обновить страницу с серверами или заказами! Иначе вы не увидите изменений сразу!
			</small>
	</p></div>
<script>
	$(function() {
		$("#payment").tabs({event: "mouseover"}).addClass('ui-tabs-vertical-payment ui-helper-clearfix');
		$("#payment li").removeClass('ui-corner-top').addClass('ui-corner-left');
	});
</script>
<div id="payment" style="margin-top: 10px;">
	<ul>
		<li><a href="#tabs-1">Банковской картой</a></li>
		<li><a href="#tabs-2">Электронными деньгами</a></li>	
		<li><a href="#tabs-3">Терминалы оплаты</a></li>
		<li><a href="#tabs-4">Банковским платежом</a></li>
		<li><a href="#tabs-5">Денежным переводом</a></li>
		<li><a href="#tabs-6">Пункты приёма платежей</a></li>
		<li><a href="#tabs-7">Другие способы оплаты</a></li>
	</ul>
	<div id="tabs-1">
		<?php  /* ?>
		<div class="zone-white">
			<?php echo $this->element('purchase_form', 
									  array(
											'system' => 'platron',
											'using' => 'TESTCARD', 
											'order' => $order, 
											'serverTemplate' => @$serverTemplate, 
											'paymentParams' => $paymentParams
											)
										);?>
			<?php echo $html->link ('<div style="float: left;" class="rw-icon"></div>
		    <div style="float: left; width: 205px; padding-left: 5px;">Комиссия 0%, мгновенное поступление средств</div>
		    <div class="platron_mid" style="height: 30px;"></div>',
			'#',
			array('escape' => false, 'onClick' => "document['order_".$order['Order']['id']."_platron_TESTCARD'].submit(); return false;"));
			
			echo $form->end();
			?>	
		</div>
		<div id="clear"></div>
		<?php */ ?>
		<div class="zone-white">
			<?php echo $this->element('purchase_form', 
									  array(
											'system' => 'rbk',
											'using' => 'bankCard', 
											'order' => $order, 
											'serverTemplate' => @$serverTemplate, 
											'paymentParams' => $paymentParams
											)
										);?>
			<?php echo $html->link ('<div style="float: left;" class="rw-icon"></div>
		    <div style="float: left; width: 225px; padding-left: 5px;">Комиссия 0%, мгновенное поступление средств</div>
		    <div class="rw-h-logo-float-right"></div>',
			'#',
			array('escape' => false, 'onClick' => "document['order_".$order['Order']['id']."_bankCard'].submit(); return false;"));
			
			echo $form->end();
			?>	
		</div>    	 
	</div>
	<div id="tabs-2">
		<?php /* Из-за смены правил работы яндекса с магазинами, отключаю типовую форму ?>
		<div class="zone-white">
			<?php 
			echo $this->element('purchase_form', 
									  array(
											'system' => 'yandex',
											'using' => 'inner', 
											'order' => $order, 
											'serverTemplate' => @$serverTemplate, 
											'paymentParams' => $paymentParams
											)
										);?>
			<?php echo $html->link ('<div class="yandex_money_small"></div>
			<div style="float: left; width: 275px; padding-left: 5px;">Комиссия 0%, мгновенное поступление средств</div>' .
			'',
			'#',
			array('escape' => false, 'onClick' => "document['order_".$order['Order']['id']."_yandex_inner'].submit(); return false;"));
			
			echo $form->end();
			?>
		</div>	
		<div id="clear"></div>
		<?php */ ?>
		<?php ?>
		<div class="zone-white">
			<?php 
			echo $this->element('purchase_form', 
									  array(
											'system' => 'yamoney', // Перевод напрямую на кошелёк
											'using' => 'inner', 
											'order' => $order, 
											'serverTemplate' => @$serverTemplate, 
											'paymentParams' => $paymentParams
											)
										);?>
			<?php echo $html->link ('<div class="yandex_money_small"></div>
			<div style="float: left; width: 275px; padding-left: 5px;">Комиссия 0% от стоимости заказа, мгновенное поступление средств</div>' .
			'',
			'#',
			array('escape' => false, 'onClick' => "document['order_".$order['Order']['id']."_yamoney_inner'].submit(); return false;"));
			
			echo $form->end();
			?>
		</div>	
		<div id="clear"></div>
		<?php ?>
		<div class="zone-white">
			<?php 
			echo $this->element('purchase_form', 
									  array(
											'system' => 'rbk',
											'using' => 'inner', 
											'order' => $order, 
											'serverTemplate' => @$serverTemplate, 
											'paymentParams' => $paymentParams
											)
										);?>
			<?php echo $html->link ('<div class="rw-h-logo-float-left"></div>
			<div style="float: left; width: 275px; padding-left: 5px;">Комиссия 0%, мгновенное поступление средств</div>',
			'#',
			array('escape' => false, 'onClick' => "document['order_".$order['Order']['id']."_inner'].submit(); return false;"));
			
			echo $form->end();
			?>
		</div>
		<div id="clear"></div>
		<div class="zone-white">
			<?php 
			
			echo $this->element('purchase_form', 
									  array(
											'system' => 'webmoney',
											'using' => 'inner', 
											'order' => $order, 
											'serverTemplate' => @$serverTemplate, 
											'paymentParams' => $paymentParams
											)
										);
									
			echo $html->link ('<div class="webmoney_small"></div>
			<div style="float: left; width: 245px; padding-left: 5px;">Комиссия 0%, мгновенное поступление средств</div>',
			'#',
			array('escape' => false, 'onClick' => "document['order_".$order['Order']['id']."_webmoney_inner'].submit(); return false;"));
			
			echo $form->end();
			
			?>
		</div>
		<?php  ?>
		<div id="clear"></div>
		<div class="zone-white">
			<?php 
						
			echo $html->link ('<div class="qiwi_mid"></div>
			<div style="float: left; width: 245px; padding-left: 5px;">Комиссия 0%, мгновенное поступление средств</div>',
			'#',
			array('escape' => false, 'onClick' => "$('#qiwiFormWallet_".$order['Order']['id']."').show('blind');"));			

			?>
		<div id="clear"></div>
		<?php
			echo $this->element('purchase_form', 
									  array(
											'formId' => 'Wallet',
											'system' => 'qiwi',
											'using' => 'inner', 
											'order' => $order, 
											'serverTemplate' => @$serverTemplate, 
											'paymentParams' => $paymentParams
											)
										);
			echo $form->end();
			
			?>
		</div>
		<?php  ?>
		<div id="clear"></div>		
		<div class="zone-white">
			<?php echo $this->element('purchase_form', 
									  array(
											'system' => 'rbk',
											'using' => 'exchangers', 
											'order' => $order, 
											'serverTemplate' => @$serverTemplate, 
											'paymentParams' => $paymentParams
											)
										);?>
			<?php echo $html->link ('<div class="rw-icon rw-icon-emoney"></div>
			<div style="float: left; width: 245px; padding-left: 5px;">Множество других электронных платёжных систем, комиссия по тарифам обменного пункта, мгновенное поступление средств</div>
		    <div class="rw-h-logo-float-right"></div>',
			'#',
			array('escape' => false, 'onClick' => "document['order_".$order['Order']['id']."_exchangers'].submit(); return false;"));
			
			echo $form->end();
			?>
			<div style="float: right;">
			<?php
			echo $html->link('Подробности...',
							 'https://rbkmoney.ru/common/dpage.aspx?dynamicPageId=preferredexchangers',
							 array('target' => '_blank'));
			?>
			</div>
		</div>		
		<?php /*?>
		<div id="clear"></div>
		<div class="zone-white">
			<div style="float: left;" class="yandex_money_small" title="Скоро вы сможете оплачивать Яндекс.Деньгами!"></div>
			<div style="float: left; width: 245px; padding-left: 5px;">Комиссия 0%, мгновенное поступление средств</div>
		</div>
		<?php */?>
	</div>
	
	<div id="tabs-3">
		<div class="zone-white">
			<?php 			
			
			echo $html->link ('<div class="qiwi_mid"></div>
			<div style="float: left; width: 245px; padding-left: 5px;">Комиссия 0%, мгновенное поступление средств</div>',
			'#',
			array('escape' => false, 'onClick' => "$('#qiwiFormTerm_".$order['Order']['id']."').show('blind');"));

			?>
		<div id="clear"></div>
		<?php
			echo $this->element('purchase_form', 
									  array(
											'formId' => 'Term',
											'system' => 'qiwi',
											'using' => 'inner', 
											'order' => $order, 
											'serverTemplate' => @$serverTemplate, 
											'paymentParams' => $paymentParams
											)
										);
			echo $form->end();
			
			?>
		</div>
		<?php  ?>
		<div id="clear"></div>
		<div class="zone-white">
			<?php echo $this->element('purchase_form', 
									  array(
											'system' => 'rbk',
											'using' => 'terminals', 
											'order' => $order, 
											'serverTemplate' => @$serverTemplate, 
											'paymentParams' => $paymentParams
											)
										);?>
			<?php echo $html->link ('<div style="float: left;" class="rw-icon rw-icon-5"></div>
		    <div style="float: left; width: 245px; padding-left: 5px;">Через терминалы оплаты, комиссия <nobr>0-10%,</nobr> мгновенное поступление средств.</div>
		    <div class="rw-h-logo-float-right"></div>',
			'#',
			array('escape' => false, 'onClick' => "document['order_".$order['Order']['id']."_terminals'].submit(); return false;"));
			
			echo $form->end();
			?>
			<div style="float: right;">
			<?php
			echo $html->link('Подробности...',
							 'https://rbkmoney.ru/common/dpage.aspx?dynamicPageId=terminals&RN=',
							 array('target' => '_blank'));
			?>
			</div>
		</div>	
		<div id="clear"></div>
		<div class="zone-white">
			<?php echo $this->element('purchase_form', 
									  array(
											'system' => 'rbk',
											'using' => 'atm', 
											'order' => $order, 
											'serverTemplate' => @$serverTemplate, 
											'paymentParams' => $paymentParams
											)
										);?>
			<?php echo $html->link ('<div class="rw-icon rw-icon-atm"></div>
		   		<div style="float: left; width: 245px; padding-left: 5px;">Через банкоматы банков-партнёров, комиссия <nobr>1-4%,</nobr> поступление средств от немедленного до 2-х часов.</div>' .
		   				'<div class="rw-h-logo-float-right"></div>',
			'#',
			array('escape' => false, 'onClick' => "document['order_".$order['Order']['id']."_atm'].submit(); return false;"));
			
			echo $form->end();
		?>
			<div style="float: right;">
			<?php
			echo $html->link('Подробности...',
							 'https://rbkmoney.ru/common/dpage.aspx?dynamicPageId=atm&RN=',
							 array('target' => '_blank'));
			?>
			</div>
		
		</div>
	
		
	</div>
	<div id="tabs-4">
		<div class="zone-white">
			<?php
			$sbrf_link = '<div  class="sberbank_money_small"></div>
			     		  <div style="float: left; width: 250px; padding-left: 5px;">' .
			     		  'Через Сбербанк, комиссия 3%, поступление средств через несколько дней.' .
			     		  '</div>'; 
			echo $html->link( $sbrf_link, '#',
							 array ('id'=>'sberbank_kvit_fill_link_'.$order['Order']['id'], 
							 		'escape' => false,
							 		'title' => 'Кликните, чтобы распечатать квитанцию',
							 		'onClick'=>"$('#sberbank_kvit_fill_".$order['Order']['id']."').show();"));
			
			$js->get('#sberbank_kvit_fill_'.$order['Order']['id'])->effect('slideIn');?>	
			<!-- Заполнение полей квитанции начало -->
			<div id="clear"></div>
			<div id="sberbank_kvit_fill_<?php echo $order['Order']['id']; ?>" style="display: none; padding: 0 .7em;" class="flash ui-state-highlight ui-corner-all">
			<?php
			 	echo $form->create('Order', array('action' => 'payBySberbank', 'target'=>'_blank'));
			?>
			<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span>
				<small>Пожалуйста, введите ФИО и адрес плательщика. 
					   Эти данные на сервере не хранятся и будут уничтожены сразу после создания квитанции.
					   Вы можете не вводить эти данные здесь, а вписать их позже вручную.
				</small>
				<div style="float: left; width: 50px;"><strong>ФИО: </strong></div>
				<?php 
				echo $form->input('fio',array (	  'size' => '30',
												  'id'=>'fio',
												  'div' => true,
												  'style' => 'float: left;', 
												  'label' => false));
				?>
				<div id="clear"></div>
				<div style="float: left; width: 50px;"><strong>Адрес: </strong></div>
				<?php 
				echo $form->input('address',array (   'size' => '30',
													  'id'=>'address',
													  'div' => true,
													  'style' => 'float: left;', 
													  'label' => false));
				?>
				<?php
				echo $form->input('id', array('type'=>'hidden','value'=>$order['Order']['id']));
				echo $form->submit('Печать квитанции',
							array('class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only'));
				
				?>
			</p>
			<?php
				echo $form->end();
			?>
		</div>
		</div>
		<!-- Заполнение полей квитанции конец -->
		<div id="clear"></div>
		<div class="zone-white">
			<?php echo $this->element('purchase_form', 
									  array(
											'system' => 'rbk',
											'using' => 'bank', 
											'order' => $order, 
											'serverTemplate' => @$serverTemplate, 
											'paymentParams' => $paymentParams
											)
										);?>
			<?php echo $html->link ('<div class="rw-icon rw-icon-bank"></div>
		   		<div style="float: left; width: 245px; padding-left: 5px;">Квитанция на оплату в любом банке, комиссия 2-5%, поступление средств через несколько дней.</div>' .
		   		'<div class="rw-h-logo-float-right"></div>',
			'#',
			array('escape' => false, 'onClick' => "document['order_".$order['Order']['id']."_bank'].submit(); return false;"));
			
			echo $form->end();
		?>
		
		</div>
		<div id="clear"></div>
		<div class="zone-white">
			<?php echo $this->element('purchase_form', 
									  array(
											'system' => 'rbk',
											'using' => 'ibank', 
											'order' => $order, 
											'serverTemplate' => @$serverTemplate, 
											'paymentParams' => $paymentParams
											)
										);?>
			<?php echo $html->link ('<div class="rw-icon rw-icon-ibank"></div>
		   		<div style="float: left; width: 245px; padding-left: 5px;">Через интернет-банкинги банков-партнёров, комиссия <nobr>0-3%,</nobr> поступление средств от немедленного до нескольких дней.</div>' .
		   				'<div class="rw-h-logo-float-right"></div>',
			'#',
			array('escape' => false, 'onClick' => "document['order_".$order['Order']['id']."_ibank'].submit(); return false;"));
			
			echo $form->end();
		?>
			<div style="float: right;">
			<?php
			echo $html->link('Подробности...',
							 'https://rbkmoney.ru/common/dpage.aspx?dynamicPageId=ibank&RN=',
							 array('target' => '_blank'));
			?>
			</div>
		</div>
		
		
	</div>
	<div id="tabs-5">
		<div class="zone-white">
			<?php echo $this->element('purchase_form', 
									  array(
											'system' => 'rbk',
											'using' => 'transfers', 
											'order' => $order, 
											'serverTemplate' => @$serverTemplate, 
											'paymentParams' => $paymentParams
											)
										);?>
			<?php echo $html->link ('<div style="float: left;" class="rw-icon rw-icon-3""></div>
	    <div style="float: left; width: 225px; padding-left: 5px;">Денежным переводом, комиссия 0%, поступление средств занимает до нескольких часов.</div>
	    <div class="rw-h-logo-float-right"></div>',
			'#',
			array('escape' => false, 'onClick' => "document['order_".$order['Order']['id']."_transfers'].submit(); return false;"));
			
			echo $form->end();
		?>			
		<div style="float: right;">
			<?php
			echo $html->link('Подробности...',
							 'https://rbkmoney.ru/common/dpage.aspx?dynamicPageId=moneytransfer&RN=',
							 array('target' => '_blank'));
			?>
			</div>
		</div>	
		<div id="clear"></div>
		<div class="zone-white">
			<?php echo $this->element('purchase_form', 
									  array(
											'system' => 'rbk',
											'using' => 'postRus', 
											'order' => $order, 
											'serverTemplate' => @$serverTemplate, 
											'paymentParams' => $paymentParams
											)
										);?>
			<?php echo $html->link ('<div style="float: left;" class="rw-icon rw-icon-4""></div>
	    <div style="float: left; width: 225px; padding-left: 5px;">Почтовым переводом, комиссия 1,7%, поступление средств через несколько дней.</div>
	    <div class="rw-h-logo-float-right"></div>',
			'#',
			array('escape' => false, 'onClick' => "document['order_".$order['Order']['id']."_postRus'].submit(); return false;"));
			
			echo $form->end();
		?>			
		<div style="float: right;">
			<?php
			echo $html->link('Подробности...',
							 'https://rbkmoney.ru/client/InputpaymentPostRus.aspx',
							 array('target' => '_blank'));
			?>
			</div>
		</div>
			    
	</div>
	<div id="tabs-6">
		<div class="zone-white">
			<?php echo $this->element('purchase_form', 
									  array(
											'system' => 'rbk',
											'using' => 'mts', 
											'order' => $order, 
											'serverTemplate' => @$serverTemplate, 
											'paymentParams' => $paymentParams
											)
										);?>
			<?php echo $html->link ('<div style="float: left;" class="rw-icon rw-icon-mts""></div>
	    <div style="float: left; width: 245px; padding-left: 5px;">В салонах связи МТС, комиссия 0%, мгновенное поступление средств.</div>
	    <div class="rw-h-logo-float-right"></div>',
			'#',
			array('escape' => false, 'onClick' => "document['order_".$order['Order']['id']."_mts'].submit(); return false;"));
			
			echo $form->end();
		?>			
		<div style="float: right;">
			<?php
			echo $html->link('Подробности...',
							 'https://rbkmoney.ru/common/dpage.aspx?dynamicPageId=mts',
							 array('target' => '_blank'));
			?>
			</div>
		</div>	
		<div id="clear"></div>
		<div class="zone-white">
			<?php echo $this->element('purchase_form', 
									  array(
											'system' => 'rbk',
											'using' => 'euroset', 
											'order' => $order, 
											'serverTemplate' => @$serverTemplate, 
											'paymentParams' => $paymentParams
											)
										);?>
			<?php echo $html->link ('<div style="float: left;" class="rw-icon rw-icon-euroset""></div>
	    <div style="float: left; width: 245px; padding-left: 5px;">В салонах связи "Евросеть", комиссия 0%, мгновенное поступление средств.</div>
	    <div class="rw-h-logo-float-right"></div>',
			'#',
			array('escape' => false, 'onClick' => "document['order_".$order['Order']['id']."_euroset'].submit(); return false;"));
			
			echo $form->end();
		?>			
		<div style="float: right;">
			<?php
			echo $html->link('Подробности...',
							 'http://www.rbkmoney.ru/popolnenie-elektronnogo-koshelka-v-salonakh-svyazi',
							 array('target' => '_blank'));
			?>
			</div>
		</div>
		
		<div id="clear"></div>
		<div class="zone-white">
			<?php echo $this->element('purchase_form', 
									  array(
											'system' => 'rbk',
											'using' => 'svyaznoy', 
											'order' => $order, 
											'serverTemplate' => @$serverTemplate, 
											'paymentParams' => $paymentParams
											)
										);?>
			<?php echo $html->link ('<div style="float: left;" class="rw-icon rw-icon-svyaznoy""></div>
	    <div style="float: left; width: 245px; padding-left: 5px;">В салонах связи "Связной", комиссия 0%, поступление средств в течение 2-х часов.</div>
	    <div class="rw-h-logo-float-right"></div>',
			'#',
			array('escape' => false, 'onClick' => "document['order_".$order['Order']['id']."_svyaznoy'].submit(); return false;"));
			
			echo $form->end();
		?>			
		<div style="float: right;">
			<?php
			echo $html->link('Подробности...',
							 'http://www.rbkmoney.ru/popolnenie-elektronnogo-koshelka-v-salonakh-svyazi',
							 array('target' => '_blank'));
			?>
			</div>
		</div>	    
	</div>
	<div id="tabs-7">
		<div class="zone-white">
			<?php echo $this->element('purchase_form', 
									  array(
											'system' => 'rbk',
											'using' => 'common', 
											'order' => $order, 
											'serverTemplate' => @$serverTemplate, 
											'paymentParams' => $paymentParams
											)
										);?>
			<?php echo $html->link ('<div class="rw-h-logo-float-left"></div>
		    <div style="float: left; width: 225px; padding-left: 5px;">Выбрать способ оплаты самостоятельно на сайте RBK Money</div>
		    ',
			'#',
			array('escape' => false, 'onClick' => "document['order_".$order['Order']['id']."_common'].submit(); return false;"));
			
			echo $form->end();
			?>	
		</div>    	 
	</div>
</div>



<?php
 echo $form->create('Server', array('action' => 'index'));
?>
<?php
	// Пока неисправят баг в jQuery, будем отсылать обычной кнопкой
	echo $form->submit('Оплатить позже',
							array('class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only'));
					

//	echo $js->submit('Оплатить позже',
//								array(
//									'url'=> array(
//													'controller'=>'Servers',
//													'action'=>'index'
//									 ),
//									'update' => '#servers_list',
//									'class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
//									'before' =>$loadingShow,
//									'complete'=>"$('#add_server').dialog('close');".$loadingHide,
//									'buffer' => false));
							
	echo $form->end();
?>
<?php 
			
			echo $js->writeBuffer(); // Write cached scripts 
?>