<?php
/*
 * Created on 28.07.2010
 *
 */
 include('../loading_params.php');
 //pr(@$orderResult);
 //pr($this->data);
?>
<div id="flash"><?php echo $session->flash(); ?></div>
<?php
 echo $form->create('Server');
?>
<script type="text/javascript">
		$(function() {
			$("#datepicker").datepicker({ dateFormat: 'yy-mm-dd 23:59:59' });
			//$.datepicker.setDefaults($.datepicker.regional['']);
			//$("#datepicker").datepicker($.datepicker.regional['ru']);
		});
</script>
<table border="0" cellpadding="0" cellspacing="3" width="95%">
								<tr>
					<td align="right">Тип:</td>
					<td align="left">
					<?php echo $form->input('Type.id',array ('options'=>$typesList,
														  'selected'=>$typeId,
														  'id'=>'types',
														  'div' => false, 
														  'label' => false));?></td>
				</tr>
				
				<tr>
					<td align="right">Сервер:</td>
					<td align="left">
					<?php 	echo $form->input('GameTemplate.id', array('options' => $gameTemplatesList,
									'selected' => @$gameTemplateId,
									'div' => false, 
									'id'  => 'games',
									'label' => false)); ?>
									
				
            	
				</td>
				</tr>
				<tr>
					<td align="right">Приватный/Публичный:</td>
					<td align="left">
					<div style="float: left;">
					<?php 	echo $form->input('Server.privateType', array('options' => $typeDiscount,
									'selected' => @$privateTypeId,
									'div' => false, 
									'id'  => 'typeDiscount',
									'label' => false)); ?>
					</div>
								
					<a href="#" class="qlabs_tooltip_right qlabs_tooltip_style_1">
					<span id="privateHelp"></span>
					<div class="icons" style="display: inline;">
						<div id ="privateHelpIco" 
							  class="ui-icon ui-icon-info" 
							  style="float: left; margin-top: .3em; cursor:pointer;"
							  rel="#privateHelp">
						</div>
						
					</div>
					</a>
            	
				</td>
				</tr>
				<tr>
					<td align="right" valign="top">Слотов:</td>
					<td align="left">														
					<?php 
					echo $form->input('Server.slots', array('div' => false,
														'id' => 'slots', 
														'label' => false));?>										
					
					</td>
				</tr>
				<tr>
					<td align="right" valign="top"></td>
					<td align="left">
					<div id="slider" title="Перемещайте ползунок для выбора значения"></div>
					</td>
				</tr>
				<tr>
					<td align="right">Привязать на сервер:
					</td>
					<td align="left"><?php echo $form->input('RootServer.id', 
												array(	'options' => $rootServersList,
														'div' => false, 
														'label' => false,
														'selected' => $rootServersId));?>
					</td>
				</tr>
				<tr>
					<td align="right">Оплачен до:</td>
					<td align="left"><?php echo $form->input('payedTill', array('type'=>'text','size' => '20', 'div' => false, 'label' => false, 'id'=>'datepicker'));?></td>
				</tr>
				<tr>
					<td align="right">Привязать к клиенту:
					</td>
					<td align="left">
						<div class="ui-widget">
							<?php echo $form->input('User.0.username', 
												array(	'id' => 'username',
														'div' => false, 
														'label' => false));?>
							
						</div>
					</td>
				</tr>
				
				
				<tr>
					<td colspan="2" align="center">
					<?php 	echo $form->input('Server.id', array(
									
									'div' => false,
									'label' => false)); ?>
					<?php
					
						echo $form->submit('Сохранить',
													array('class' => 'btn btn-primary'));

												
						echo $form->end();
					?>
					</td>
				</tr>
</table>
<br/>
<div class="well">
	<h3>Остаток средств по серверу:</h3>
	<strong>Дней:</strong> <?php echo floatval(@$dayLeft);?><br/>
	<strong>В день:</strong> <?php echo floatval(@$moneyPerDay);?> руб.<br/>
	<strong>Итого:</strong> <?php echo floatval(@$moneyLeft);?> руб.<br/><br/>
	<?php
			
			echo $this->Html->link('Перевести весь остаток на счёт', array('action' => 'edit',
																		   $this->data['Server']['id'],
																		   'moneyToAcc'),
																	 array('class' => 'btn btn-primary'));
	?>
</div>
<script type="text/javascript">					
					$(function() {								
						
						$("#username").autocomplete({
								source: "/users/autoComplete/",
								minLength: 1,
							});
						
						function GetTemplates () {
							  $('#games').attr('disabled','disabled');
							  $('#vars').attr('disabled','disabled');
							  $('#typeDiscount').attr('disabled','disabled');										    
						      $.getJSON('/gameTemplates/getTemplates',
						                  {typeId: $('#types').val()},
						                  function(tmps) {
						                    if(tmps !== null) {
						                      populateTemplatesList(tmps);
						                      SlotsSlider();
											  GetMods();
											  
											  $('#games').removeAttr('disabled');
											  $('#vars').removeAttr('disabled');
											  $('#typeDiscount').removeAttr('disabled');
											  
						                    }
						        		  });
						    
						    var type = $('#types').val();

							if (type == 8)
							{
								$('#typeDiscount').attr('disabled','disabled');
								$('#RootServerId').attr('disabled','disabled');
							}
							else
							{
								$('#typeDiscount').removeAttr('disabled');
								$('#RootServerId').removeAttr('disabled');
							}

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
					      $.getJSON('/gameTemplates/getMods',
					                  {templateId: $('#games').val()},
					                  function(mods) {
					                    if(mods !== null) {
					                      populateModsList(mods);
					                    }
					        		  });
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
								  	  options +='<option value="0" <?php if (@$privateTypeId == 0){ echo 'selected="selected"';} ?>>Публичный сервер</option>';
								  	  options +='<option value="2" <?php if (@$privateTypeId == 2){ echo 'selected="selected"';} ?>>Приватный с автоотключением</option>';
							  	  }
							  	  else
							  	  {
							  	      options +='<option value="0" <?php if (@$privateTypeId == 0){ echo 'selected="selected"';} ?>>Публичный сервер</option>';
								  	  options +='<option value="1" <?php if (@$privateTypeId == 1){ echo 'selected="selected"';} ?>>Приватный с паролем</option>';
								  	  options +='<option value="2" <?php if (@$privateTypeId == 2){ echo 'selected="selected"';} ?>>Приватный с автоотключением</option>';
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
								max: ma*2, // Админам позволить ставить больше слотов, чем разрешено в шаблоне
								step: 1,
								slide: function(event, ui) {
									$("#slots").val(ui.value);								
								}
							});
						
							$("#slots").val($("#slider").slider("value"));
						};

						$("#slots").keyup(function() {
												$("#slider").slider( "value", $("#slots").val() );
												return false;
						});
		
						
					
						
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
						
						
						
						$("#types").change(function() {																	
								GetTemplates();
								return false;
						});
						
						$("#games").change(function() {
								SlotsSlider();								
								GetMods();
								populatePrivateTypes ();
								return false;
						});
						
						$("#typeDiscount").change(function() {
								TypeDiscountHelp();
								
								return false;
						});	
						
						GetTemplates();						
						GetMods();
						TypeDiscountHelp();						
						
					});
	</script>
<?php 
			
			echo $js->writeBuffer(); // Write cached scripts 
?>				