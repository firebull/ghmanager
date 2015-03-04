<?php
/*
 * Created on 25.05.2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
 //echo $script;
?>

<?php
/*
 * Created on 27.05.2010
 *
 */
 include('loading_params.php');
echo $this->Form->create('Server');
?>
<table border="0" cellpadding="0" cellspacing="3" width="95%">
				<tr>
					<td align="right">Тип:</td>
					<td align="left">
					<?php echo $this->Form->input('Type.id',array ('options'=>$typesList,
														  'selected'=>$typeId,
														  'id'=>'types',
														  'div' => false,
														  'label' => false));?></td>
				</tr>

				<tr>
					<td align="right">Игра:</td>
					<td align="left">
					<?php 	echo $this->Form->input('GameTemplate.id', array('options' => $gameTemplatesList,
									'selected'=>$gameTemplatesId,
									'div' => false,
									'id'  => 'games',
									'label' => false)); ?>



				</td>
				</tr>
				<tr>
					<td align="right">Мод:
					</td>
					<td align="left"><?php echo $this->Form->input('Mod.id',
												array(	'options' => $modsList,
														'id' => 'vars',
														'div' => false,
														'label' => false,
														'selected' => $modsId));?>
					</td>
				</tr>
				<tr>
					<td align="right" valign="top">Слотов:</td>
					<td align="left">


					<?php
					echo $this->Form->input('slots', array('div' => false,
														'label' => false,
														'title'=>'Перемещайте ползунок для выбора значения',
														//'disabled'=>'disabled',
														'size'=>'2',
														'style'=>'border:0; font-weight:bold;'));?>
					<div id="slider" title="Перемещайте ползунок для выбора значения"></div>
					</td>
				</tr>
				<tr>
					<td colspan="2" align="center">
					<?php
						// Пока неисправят баг в jQuery, будем отсылать обычной кнопкой
						echo $this->Form->submit('Заказать',
												array('class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only'));

//						echo $this->Js->submit('Отправить',
//													array(
//														'url'=> array(
//																		'controller'=>'Servers',
//																		'action'=>'add'
//														 ),
//														'update' => '#add_server',
//														'class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
//														'before' =>$loadingShow,
//														'complete'=>$loadingHide,
//														'buffer' => false));

						echo $this->Form->end();
					?>
					</td>
				</tr>
</table>



<script type="text/javascript">
					$(function() {

						$('input [type=checkbox]').checkBox();

						function GetTemplates() {

						      $.getJSON('../gameTemplates/getTemplates',
						                  {typeId: $('#types').val()},
						                  function(tmps) {
						                    if (tmps !== null) {
						                      populateTemplatesList(tmps);
						                      GetMods();
						                      SlotsSlider();
						                    }
						        		  });

							}

						function GetMods() {

						      $.getJSON('../gameTemplates/getMods',
						                  {templateId: $('#games').val()},
						                  function(mods) {
						                    if (mods !== null) {
						                      populateModsList(mods);
						                    }
						        		  });

							}

						function populateTemplatesList(tmps) {
							  var options = '';

							  $.each(tmps, function(index, tmp) {
							  options += '<option value="' + index + '">' + tmp + '</option>';
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

						function SlotsSlider() {

							var selectedGame = $('#games').val();


							//Сюда добавлять значения слотов
							<?php echo $script;?>

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
								SlotsSlider();
								GetMods();
								return false;
						});

						SlotsSlider();
						GetMods();
					});
	</script>
<?php
			echo $this->Js->writeBuffer(); // Write cached scripts
?>
