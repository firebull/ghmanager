<?php
/*
 * Created on 31.08.2010
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
 include('../loading_params.php');
?>
<cake:nocache>
<div id="voice_params" style="margin-left: 30px;">

	<div id="flash"><?php echo $session->flash();?></div>
	<div id="errors" style="color: red;">
	<?php
	if (@$errors){
		foreach ( @$errors as $field => $error ) {
	       echo "Ошибка в поле ".$field.": ".$error."<br/>";
		}
	}
	?>
	</div>

	<?php echo $form->create('Server', array('action' => 'editParamsVoiceMumble')); ?>
			
	<table border="0" cellpadding="2" cellspacing="5">
	<tr>
		<td>defaultchannel</td>
		<td>
		<div class="qlabs_tooltip_right qlabs_tooltip_style_1">
		<?php echo $form->input('VoiceMumbleParam.0.defaultchannel', 
									array(
										'div' => true,	
										'label' => false,
										'error' => true,
										'class' => 'param span1'));?>
		<span><strong>Подсказка</strong>Канал по умолчанию, на который будут перебрасываться вошедшие при заходе на сервер. Указывайте номер канала, а не имя!</span>
		</div>
		</td>
	</tr>
	<tr>
		<td>autobanAttempts</td>
		<td>
		<div class="qlabs_tooltip_right qlabs_tooltip_style_1">
		<?php echo $form->input('VoiceMumbleParam.0.autobanAttempts', 
									array(														
										'size' => '10', 
										'div' => true,	
										'label' => false,
										'error' => true,
										'class' => 'param span1'));?>
		<span><strong>Подсказка</strong>Количество попыток подключения с одного IP - как удачных, так и неудачных. Напишите 0, чтобы отключить функцию.</span>
		</div>
		</td>
	</tr>
	<tr>
		<td>autobanTimeframe</td>
		<td>
		<div class="qlabs_tooltip_right qlabs_tooltip_style_1">
		<?php echo $form->input('VoiceMumbleParam.0.autobanTimeframe', 
									array(														
										'size' => '10', 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param span1'));?>
		<span><strong>Подсказка</strong>Промежуток времени в секундах, во время которого считается количество попыток подключения. Напишите 0, чтобы отключить функцию.</span>
		</div>
		</td>
	</tr>
	<tr>
		<td>autobanTime</td>
		<td>
		<div class="qlabs_tooltip_right qlabs_tooltip_style_1">
		<?php echo $form->input('VoiceMumbleParam.0.autobanTime', 
									array(														
										'size' => '10', 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param span1'));?>
		<span><strong>Подсказка</strong>Время в секундах, на которое создаётся бан.</span>
		</div>
		</td>
	</tr>
	<tr>
		<td valign="top">Welcometext</td>
		<td>
		<div class="qlabs_tooltip_right qlabs_tooltip_style_1">
		<?php echo $form->input('VoiceMumbleParam.0.welcometext', 
									array(																								 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'span4'));?>
		<span><strong>Подсказка</strong>Сообщение, которое показывается всем вошедшим. Можно использовать HTML-теги.</span>
		</div>
		</td>
	</tr>
	<tr>
		<td>ServerPassword</td>
		<td>
		<div class="qlabs_tooltip_right qlabs_tooltip_style_1">
		<?php echo $form->input('VoiceMumbleParam.0.serverpassword', 
									array(														
										'size' => '20', 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param span4'));?>
		<span><strong>Подсказка</strong>Пароль сервера. <br/>Должен быть пустым, если сервер публичный.</span>
		</div>
		</td>
	</tr>
	<tr>
		<td>textmessagelength</td>
		<td>
		<div class="qlabs_tooltip_right qlabs_tooltip_style_1">
		<?php echo $form->input('VoiceMumbleParam.0.textmessagelength', 
									array(														
										'size' => '10', 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param span2'));?>
		<span><strong>Подсказка</strong>Максимальная длина текстового сообщения в символах. Введите 0, чтобы отключить ограничение.</span>
		</div>
		</td>
	</tr>
	<tr>
		<td>imagemessagelength</td>
		<td>
		<div class="qlabs_tooltip_right qlabs_tooltip_style_1">
		<?php echo $form->input('VoiceMumbleParam.0.imagemessagelength', 
									array(														
										'size' => '10', 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param span2'));?>
		<span><strong>Подсказка</strong>Максимальная длина текстового сообщения, содержащего изображения, в символах. Введите 0, чтобы отключить ограничение.</span>
		</div>
		</td>
	</tr>
	<tr>
		<td>allowhtml</td>
		<td>
		<div class="qlabs_tooltip_right qlabs_tooltip_style_1">
		<?php echo $form->input('VoiceMumbleParam.0.allowhtml', 
									array(														
										'options'=>array(
														'true' => 'Да',
														'false'=> 'Нет'
														), 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param span1'));?>
		<span><strong>Подсказка</strong>Позволять ли использовать HTML в сообщениях, комментариях и описании канала.</span>
		</div>
		</td>
	</tr>
	<tr>
		<td>registerName</td>
		<td>
		<div class="qlabs_tooltip_right qlabs_tooltip_style_1">
		<?php echo $form->input('VoiceMumbleParam.0.registerName', 
									array(														
										'size' => '25', 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param span4'));?>
		<span><strong>Подсказка</strong>Публичное имя сервера.<br/>Чтобы позволить публичную регистрацию, необходимо заполнить все поля register, а пароль сервера должен быть пустым.</span>
		</div>
		</td>
	</tr>
	<tr>
		<td>registerPassword</td>
		<td>
		<div class="qlabs_tooltip_right qlabs_tooltip_style_1">
		<?php echo $form->input('VoiceMumbleParam.0.registerPassword', 
									array(														
										'size' => '15', 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param span4'));?>
		<span><strong>Подсказка</strong>Пароль для публичной регистрации сервера.<br/> Чтобы позволить публичную регистрацию, необходимо заполнить это поле, а пароль сервера должен быть пустым.</span>
		</td>
	</tr>
	<tr>
		<td>registerUrl</td>
		<td>
		<div class="qlabs_tooltip_right qlabs_tooltip_style_1">
		<?php echo $form->input('VoiceMumbleParam.0.registerUrl', 
									array(														
										'size' => '25', 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param span4'));?>
		<span><strong>Подсказка</strong>URL web-сервера при публичной регистрации сервера.</span>
		</div>
		</td>
	</tr>
	<tr>
		<td>registerHostname</td>
		<td>
		<div class="qlabs_tooltip_right qlabs_tooltip_style_1">
		<?php echo $form->input('VoiceMumbleParam.0.registerHostname', 
									array(														
										'size' => '25', 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param span4'
												));?>
		<span><strong>Подсказка</strong>Если не заполнено поле выше, то впишите сюда IP-адрес web-сервера для публичной регистрации.</span>
		</div>
		</td>
	</tr>
	<tr>
	<td>bandwidth</td>
		<td>
		<div class="qlabs_tooltip_right qlabs_tooltip_style_1" style="width: 270px;">
		<?php echo $form->input('VoiceMumbleParam.0.bandwidth', 
									array(														
										'type' => 'hidden', 
										'div' => false, 
										'id' => 'bandwidthHidden',	
										'label' => false,
										'error' => true
										
												));
												
				echo $html->tag('div','', array ('id' => 'bandwidthDisabled' ));
													
				echo $html->tag('div','', array ('id' => 'bandwidth'));
			?>
		
		<span><strong>Подсказка</strong>Максимальная полоса пропускания, с которой клиентам разрешено отправлять данные на сервер. <br/><br/>Перемещайте ползунок для выбора значения.</span>
		</div>
		</td>
	</tr>
	<tr>
		<td valign="top">sslCert</td>
		<td>
		<div class="qlabs_tooltip_right qlabs_tooltip_style_1">
		<?php echo $form->input('VoiceMumbleParam.0.sslCert', 
									array(	
										'size' => '30',																							 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param span'));?>
		<span><strong>Подсказка</strong>Если у вас уже есть SSL-сертификат, загрузите его в директорию сервера через FTP и укажите здесь имя файла.</span>
		</div>
		</td>
	</tr>
	<tr>
		<td valign="top">sslKey</td>
		<td>
		<div class="qlabs_tooltip_right qlabs_tooltip_style_1">
		<?php echo $form->input('VoiceMumbleParam.0.sslKey', 
									array(
										'size' => '30',																								 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param span4'));?>
		<span><strong>Подсказка</strong>Если у вас уже есть ключ SSL-сертификата, загрузите его в директорию сервера через FTP и укажите здесь имя файла.</span>
		</div>
		</td>
	</tr>
	<tr>
		<td valign="top">certrequired</td>
		<td>
		<div class="qlabs_tooltip_right qlabs_tooltip_style_1">
		<?php echo $form->input('VoiceMumbleParam.0.certrequired', 
									array(
										'options'=>array(
														'true' => 'Да',
														'false'=> 'Нет'
														),																								 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param span1' 
												));?>
		<span><strong>Подсказка</strong>Требовать ли сертификат у клиента.</span>
		</div>
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
		<?php
			echo $form->input('id', array('type'=>'hidden'));

			echo $js->submit('Сохранить',
										array(
											'url'=> array(
															'controller'=>'Servers',
															'action'=>'editParamsVoiceMumble'
											 ),
											'update' => '#voice_params',
											'class' => 'btn btn-primary',
											'before' =>$loadingShow,
											'complete'=>$loadingHide,
											'buffer' => false));
									
			echo $form->end();
		?>
		</td>
	</tr>
</table>

	
	
	<?php echo $form->end();?>
<script type="text/javascript">
	$(function() {
		
		
		$("#bandwidth").slider({
								range: "max",
								value: <?php echo $this->data['VoiceMumbleParam'][0]['bandwidth']; ?>,
								min: 8192,
								max: 131072,
								step: 2048,
								slide: function(event, ui) {
									$("#bandwidthHidden").val(ui.value);
									countKbits();

								}
							});
							
		function countKbits () {
			
			var bits = $("#bandwidthHidden").val();
			
			kbits = Math.round(eval(bits/1024)) + 'Кбит';
			$("#bandwidthDisabled").text(kbits);
			
		}
		
		countKbits();
		
	});
</script>
</div>
<?php //pr($this->data['VoiceMumbleParam'][0]); 
?>
</cake:nocache>