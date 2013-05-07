<?php
/*
 * Created on 03.06.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
 //pr($session);
  include('../loading_params.php');
  //Отключить кэш, т.к. иначе Cake кэширует устаревшие данные формы
?>
<cake:nocache>
			<script type="text/javascript">
					$(function() {
						$("#datepicker").datepicker({ dateFormat: 'yy-mm-dd 23:59:59' });
						//$.datepicker.setDefaults($.datepicker.regional['']);
						//$("#datepicker").datepicker($.datepicker.regional['ru']);
					});
			</script>

					Введите данные сервера:

			<?php echo $form->create('Server', array('action' => 'edit')); ?>
				

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
					<td align="right">Игра:</td>
					<td align="left">
					<?php 	echo $form->input('GameTemplate.id', array('options' => $gameTemplatesList,
												'div' => false,
												'id' => 'games', 
												'label' => false,
												'selected'=>$gameTemplatesId)); ?>
					</td>
				</tr>
				<tr>
					<td align="right">Мод:
					</td>
					<td align="left"><?php echo $form->input('Mod.id', 
												array(	'options' => $modsList,
														'div' => false, 
														'id' => 'vars',
														'label' => false,
														'selected' => $modsId));?>
					</td>
				</tr>
				<tr>
					<td align="right">Слотов:</td>
					<td align="left">
					<?php 
					echo $form->input('slots', array('div' => false, 
														'label' => false, 
														'title'=>'Перемещайте ползунок для выбора значения',
														//'disabled'=>'disabled',
														'size'=>'2', 
														'style'=>'border:0; font-weight:bold;'));?>
					<div id="slider" title="Перемещайте ползунок для выбора значения"></div>
										</td>
				</tr>
				<tr>
					<td align="right">Оплачен до:</td>
					<td align="left"><?php echo $form->input('payedTill', array('type'=>'text','size' => '20', 'div' => false, 'label' => false, 'id'=>'datepicker'));?></td>
				</tr>
<?php /* ?>				
				<tr>
					<td align="right">Инициализирован:</td>
					<td align="left"><?php echo $form->checkbox('initialised', array('div' => false, 'label' => false));?></td>
				</tr>
<?php */ ?>
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
					<td align="right">Привязать к клиенту:
					</td>
					<td align="left">
						<div class="ui-widget">
							<?php echo $form->input('username', 
												array(	'id' => 'username',
														'div' => false, 
														'label' => false));?>
							
						</div>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center"><?php 
							echo $form->input('id', array('type'=>'hidden'));
							// Пока неисправят баг в jQuery, будем отсылать обычной кнопкой
							echo $form->submit('Сохранить',
													array('class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only'));
//							echo $js->submit('Сохранить',
//								array(
//									'url'=> array(
//													'controller'=>'Servers',
//													'action'=>'edit'
//									 ),
//									'update' => '#servers_list',
//									'class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
//									'id'=>'submit',
//									'before' =>$loadingShow,
//									'complete'=>$loadingHide,
//									'buffer' => false));
//									
//							$js->get('#submit')->event('click', '$("#edit_server").dialog("destroy");');


							?>
					
							</td>
				</tr>
			</table>
			<?php echo $form->end();?>
			<?php 
			echo $js->writeBuffer(); // Write cached scripts 
			?>
</cake:nocache>

<script type="text/javascript">	
				
					$(function() {	
												
						$("#username").autocomplete({
								source: "../users/autoComplete/",
								minLength: 1,
							});
													
								
						function GetMods () {
							  
							  										    
						      $.getJSON('../gameTemplates/getMods',
						                  {templateId: $('#games').val()},
						                  function(mods) {
						                    if(mods !== null) {
						                      populateModsList(mods);						                      						                      
						                    }
						        		  });
						      						    						
							}
						
						function GetTemplates () {
							  $('#games').attr('disabled','disabled');
							  $('#vars').attr('disabled','disabled');										    
						      $.getJSON('../gameTemplates/getTemplates',
						                  {typeId: $('#types').val()},
						                  function(tmps) {
						                    if(tmps !== null) {
						                      populateTemplatesList(tmps);
						                      SlotsSlider();
						                      GetMods();						                      
						                      $('#games').removeAttr('disabled');
											  $('#vars').removeAttr('disabled');
						                    }
						        		  });
						      						    						
							}
							
						function populateTemplatesList(tmps) {
							  var options = '';
							
							  $.each(tmps, function(index, tmp) {
							  	if (index == <?php echo $gameTemplatesId; ?>) {
							    	options += '<option value="' + index + '" selected="selected">' + tmp + '</option>';
							  	}
							  	else {
							  		options += '<option value="' + index + '">' + tmp + '</option>';
							  	}
							  });
							  $('#games').html(options);
							  $('#types').show();
							
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
							
							//Устанавливаем истиное значение слотов, которое уже привязано к серверу	
							// Если установлено в базе - брать его
							//
							v = $('#ServerSlots').val();
								
							$("#slider").slider({
								range: "max",
								value: v,
								min: mi,
								max: ma,
								step: 2,
								slide: function(event, ui) {
									$("#ServerSlots").val(ui.value);
								}
							});
						
							$("#ServerSlots").val($("#slider").slider("value"));
						};
						
						$("#types").change(function() {
									
								GetTemplates();							
								return false;
						});
						
						
						$("#games").change(function() {
								$('#vars').attr('disabled','disabled');								
								GetMods();
								SlotsSlider();
								$('#vars').removeAttr('disabled');
								return false;
						});
						
						
						GetTemplates();	
						GetMods();
						SlotsSlider();
					});
</script>

