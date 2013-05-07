<h2 class="highlight3" style="margin-top: 0px;">Открыть новый тикет техподдержки</h2>
<div id="new_support_ticket_form" style="float: left; margin-left: 0px; max-width: 420px;">
<?php echo $this->Form->create('SupportTicket');?>
	<fieldset>
		
 		<table>
			<tr>				
				<td>
				<label class="control-label highlight3" for="ticketTitle">Тема тикета:</label>
			     
				<div class="controls qlabs_tooltip_right qlabs_tooltip_style_39">
				<?php echo $this->Form->input('title',array (
															  'class' =>'param validate[required,minSize[6]] span4',
															  'div' => false, 
															  'label' => false,
															  'id' => 'ticketTitle')); ?>
				<span><strong>Тема тикета</strong>Пожалуйста, напишите тут суть проблемы в несколько слов.</span>
				</div>
				</td>
			</tr>
			<tr>
				<td>
				<label class="control-label highlight3" for="troubleServer">Сервер:</label>
				<div class="controls qlabs_tooltip_right qlabs_tooltip_style_39" style="z-index: 2000;">
				<?php echo $this->Form->input('Server',array (
																  'class' =>'validate[required] span4',
																  'div' => false, 
																  'label' => false,
																  'id' => 'troubleServer')); ?>
				<span><strong>Сервер</strong>Укажите сервер, испытывающий проблемы. Если проблема не с сервером, выберите соответсвующий пункт. Можно выбрать НЕСКОЛЬКО серверов!</span>
				</div>
				</td>
			</tr>
			<tr>
				<td>
				<label class="control-label highlight3" for="text">Описание проблемы:</label>
				<div class="controls qlabs_tooltip_right qlabs_tooltip_style_39">
				<?php echo $this->Form->input('Support.text',
							  						array('type'=>'textarea',
							  							  'style'=> '  
							  							  			 height: 250px; 
							  							  			 background-color: #fff; 	
														  			 padding-left: 15px;
																	  ', 
												  		  'class' => 'validate[required] span4',
														  'escape'=>false,
														  'div' => false, 	
														  'label' => false,
														  'id' => 'text')); ?>
				<span><strong>Описание проблемы</strong>Пожалуйста, сообщайте больше подробностей.</span>
				</div>
				</td>
			</tr>
			<tr>
				<td align="left">
					<?php echo $this->Form->submit('Отправить', array(
															'class' => 'btn btn-primary',
															'escape'=>false,
															  'div' => false, 	
															  'label' => false
															));
															
					
					?>
				<a href="#" id="newTicketCloser" onclick="$('#new_ticket').hide('highlight');$('.formError').remove();$('#new_ticket').empty();$('#new_ticket_opener').removeAttr('disabled');" class="btn">Отмена</a>								
				</td>
			</tr>	
		</table>
	</fieldset>
<?php echo $form->end(); ?>
<script type="text/javascript">
	
	$(document).ready(function(){
        $("#SupportTicketAddForm").validationEngine('attach');
    });
	
</script>
</div>
<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;float: left; max-width: 400px; margin-left: 20px;"> 
	<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
		Пожалуйста, опишите проблему чётко и понятно!<br/> 
		В теме укажите суть проблемы в нескольких словах.
		Выберите сервер, испытывающий проблемы. И адекватно, используя знаки препинания и как можно меньше
		сокращений, опишите проблему. Это значительно ускорит нами понимание сути проблемы и поиск её решения!
		</p>
</div>

<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;float: left; max-width: 400px; margin-left: 20px; margin-top: 15px;"> 
	<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span> 
		<strong>Советы:</strong><br/>
		<ul>
			<li>Если вам нужно передать лог, пожалуйста, вставьте его в <a href="http://pastebin.com" target="_blank">pastebin.com</a> и впишите ссылку в тикет</li>
			<li>Если вам нужно передать скриншот, загрузите его на <a href="http://rghost.net" target="_blank">rghost.net</a> и вставьте ссылку в тикет</li>
			<li>Длинные ссылки можно сокращать на <a href="http://goo.gl" target="_blank">goo.gl</a></li>
		</ul>
		</p>
</div>

<div style="float: left; max-width: 400px; background-color: white; padding: 10px; margin-left: 20px; margin-top: 15px;">
	<h3 class="highlight3">Время работы техподдержки</h3>
	<ul>
        <li>Финансовые вопросы: 10:00 до 19:00 по будням</li>
        <li>Веб-хостинг: 10:00 до 22:00 каждый день</li>
        <li>Операции на физических серверах: 10:00 до 22:00 каждый день</li>           
        <li>Другие вопросы: круглосуточно</li>
    </ul>
    <small class="control-label highlight3">Задать вопрос можно в любое время, но ответ получите в указанные временные промежутки.</small>
</div>
<div id="clear"></div>
