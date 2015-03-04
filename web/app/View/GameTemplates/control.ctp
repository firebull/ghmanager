<?php
/*
 * Created on 21.06.2010
 *
 * File created for project TeamServer
 * by nikita
 */
 include('loading_params.php');
?>
<div class="btn-toolbar">
	<div class="btn-group">
		<a class="btn btn-primary" href="#"><i class="icon-plus icon-white"></i> Добавить</a>
		<a class="btn btn-primary dropdown-toggle" data-toggle="dropdown" href="#"><span class="caret"></span></a>
		<ul class="dropdown-menu">
			<li><?php
					//Ссылка  для создания типа шаблона
					echo $this->Html->link('<i class="icon-tags"></i> Новый тип серверов', '#',
										array ('id'=>'type_add_new', 'escape' => false
										,'onClick'=>"$('#add_type').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 400});"

										));
					$effect = $this->Js->get('#add_type')->effect('slideIn');
					$event  = $this->Js->request(array('controller'=>'GameTemplates',
												 'action'=>'addType'),
										   array('update' => '#add_type',
												 'before'=>$loadingShow,
												 'complete'=>$loadingHide));

					$this->Js->get('#type_add_new')->event('click', $event);

				?>
			</li>
			<li><?php
				//Ссылка  для создания шаблона сервера
				echo $this->Html->link('<i class="icon-tag"></i> Новый шаблон', '#',
									array ('id'=>'template_add_new', 'escape' => false
									,'onClick'=>"$('#add_template').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});"

									));
				$effect = $this->Js->get('#add_template')->effect('slideIn');
				$event  = $this->Js->request(array('controller'=>'GameTemplates',
											 'action'=>'add'),
									   array('update' => '#add_template',
											 'before'=>$loadingShow,
											 'complete'=>$loadingHide));

				$this->Js->get('#template_add_new')->event('click', $event);

				?>
			</li>
			<li class="divider"></li>
			<li><?php
				//Ссылка  для создания карты
				echo $this->Html->link('<i class="icon-picture"></i> Новую карту', '#',
									array ('id'=>'map_add_new', 'escape' => false
									,'onClick'=>"$('#add_map').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});"

									));
				$effect = $this->Js->get('#add_map')->effect('slideIn');
				$event  = $this->Js->request(array('controller'=>'GameTemplates',
											 'action'=>'addMap'),
									   array('update' => '#add_map',
											 'before'=>$loadingShow,
											 'complete'=>$loadingHide));

				$this->Js->get('#map_add_new')->event('click', $event);
				?>
			</li>
			<li class="divider"></li>
			<li><?php
				//Ссылка  для создания мода
				echo $this->Html->link('<i class="icon-briefcase"></i> Новый мод', '#',
									array ('id'=>'mod_add_new', 'escape' => false
									,'onClick'=>"$('#add_mod').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});"

									));
				$effect = $this->Js->get('#add_mod')->effect('slideIn');
				$event  = $this->Js->request(array('controller'=>'GameTemplates',
											 'action'=>'addMod'),
									   array('update' => '#add_mod',
											 'before'=>$loadingShow,
											 'complete'=>$loadingHide));

				$this->Js->get('#mod_add_new')->event('click', $event);

				?>
			</li>
			<li><?php
				//Ссылка  для создания плагина
				echo $this->Html->link('<i class="icon-screenshot"></i> Новый плагин', '#',
									array ('id'=>'plugin_add_new', 'escape' => false
									,'onClick'=>"$('#add_plugin').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});"

									));
				$effect = $this->Js->get('#add_plugin')->effect('slideIn');
				$event  = $this->Js->request(array('controller'=>'GameTemplates',
											 'action'=>'addPlugin'),
									   array('update' => '#add_plugin',
											 'before'=>$loadingShow,
											 'complete'=>$loadingHide));

				$this->Js->get('#plugin_add_new')->event('click', $event);

				?>
			</li>
			<li class="divider"></li>
			<li><?php
				//Ссылка  для создания конфига
				echo $this->Html->link('<i class="icon-file"></i> Конфигурационный файл', '#',
									array ('id'=>'config_add_new', 'escape' => false
									,'onClick'=>"$('#add_config').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 400});"

									));
				$effect = $this->Js->get('#add_config')->effect('slideIn');
				$event  = $this->Js->request(array('controller'=>'GameTemplates',
											 'action'=>'addConfig'),
									   array('update' => '#add_config',
											 'before'=>$loadingShow,
											 'complete'=>$loadingHide));

				$this->Js->get('#config_add_new')->event('click', $event);

				?>
			</li>
		</ul>
	</div>

	<div class="btn-group">
		<?php
		//Список всех карт
		echo $this->Html->link('<i class="icon-picture"></i> Список всех карт', '#',
							array ('id'=>'map_view_list',
									'escape' => false,
									'class' => 'btn',
									'onClick'=>"$('#map_list').dialog({modal: true,position: ['center',25], show: 'clip', hide: 'clip', width: 1100});"

							));
		$effect = $this->Js->get('#map_list')->effect('slideIn');
		$event  = $this->Js->request(array('controller'=>'GameTemplates',
									 'action'=>'commonMapList'),
							   array('update' => '#map_list',
									 'before'=>$loadingShow,
									 'complete'=>$loadingHide));

		$this->Js->get('#map_view_list')->event('click', $event);

		?>
	</div>
</div>


<div id="templates_list">

<h2>Список шаблонов серверов:</h2>

	<table class="intext" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<th width="250"></th>
			<th>ID</th>
			<th width="10%">Имя</th>
			<th>Моды</th>
			<th>Плагины</th>
		</tr>

	<?php
	//pr($gameTemplates);


		$i=1;
		if ( !empty($gameTemplates) ) {
			foreach ($gameTemplates as $gameTemplate):
			$id = $gameTemplate['GameTemplate']['id'];
			?>

			<tr>
				<td valign="top">
				<div class="btn-group">
						<?php
						//Иконка для редактирования параметров шаблона
						echo $this->Html->link('<i class="icon-wrench"></i>', '#',
											array ('id'=>'template_edit_'.$id,
													'escape' => false,
													'title' => 'Редактировать шаблон',
													'class' => 'btn',
													'onClick'=>"$('#edit_template').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});"

											));
						$effect = $this->Js->get('#edit_template')->effect('slideIn');
						$event  = $this->Js->request(array('controller'=>'GameTemplates',
													 'action'=>'edit', $id),
											   array('update' => '#edit_template',
													 'before'=>$loadingShow,
													 'complete'=>$loadingHide));

						$this->Js->get('#template_edit_'.$id)->event('click', $event);

						//Иконка для добавления модов и плагинов к шаблону
						echo $this->Html->link('<i class="icon-briefcase"></i>', '#',
											array ('id'=>'mod_add_'.$id,
													'escape' => false,
													'title' => 'Добавить моды к шаблону',
													'class' => 'btn',
													'onClick'=>"$('#link_template').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});"

											));
						$effect = $this->Js->get('#link_template')->effect('slideIn');
						$event  = $this->Js->request(array('controller'=>'GameTemplates',
													 'action'=>'linkModToTemplate', $id),
											   array('update' => '#link_template',
													 'before'=>$loadingShow,
													 'complete'=>$loadingHide));

						$this->Js->get('#mod_add_'.$id)->event('click', $event);


							//Иконка для добавления карт к шаблону
							echo $this->Html->link('<i class="icon-picture"></i>', '#',
												array ('id'=>'map_add_'.$id,
														'escape' => false,
														'title' => 'Добавить карты к шаблону',
														'class' => 'btn',
														'onClick'=>"$('#link_map').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 400});"

												));
							$effect = $this->Js->get('#link_map')->effect('slideIn');
							$event  = $this->Js->request(array('controller'=>'GameTemplates',
														 'action'=>'linkMapToTemplate', $id),
												   array('update' => '#link_map',
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide));

							$this->Js->get('#map_add_'.$id)->event('click', $event);

							//Иконка для добавления конфигов к шаблону
							echo $this->Html->link('<i class="icon-list-alt"></i>', '#',
												array ('id'=>'config_add_'.$id,
														'escape' => false,
														'title' => 'Добавить конфиги к шаблону',
														'class' => 'btn',
														'onClick'=>"$('#link_config').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 500});"

												));
							$effect = $this->Js->get('#link_config')->effect('slideIn');
							$event  = $this->Js->request(array('controller'=>'GameTemplates',
														 'action'=>'linkConfigToTemplate', $id),
												   array('update' => '#link_config',
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide));

							$this->Js->get('#config_add_'.$id)->event('click', $event);


							//Иконка для добавления услуг к шаблону
							echo $this->Html->link('<i class="icon-bookmark"></i>', '#',
												array ('id'=>'service_add_'.$id,
														'escape' => false,
														'title' => 'Добавить услуги к шаблону',
														'class' => 'btn',
														'onClick'=>"$('#link_service').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 500});"

												));
							$effect = $this->Js->get('#link_service')->effect('slideIn');
							$event  = $this->Js->request(array('controller'=>'GameTemplates',
														 'action'=>'linkServiceToTemplate', $id),
												   array('update' => '#link_service',
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide));

							$this->Js->get('#service_add_'.$id)->event('click', $event);

						//Иконка для удаления шаблона
						$confiremDeleteMessage = 'Вы уверены, что хотите удалить шаблон #'.$id.' - '.
												 $gameTemplate['GameTemplate']['longname'].'?'.
												 "\n<br/><br/>Это необратимая операция!" ;
						$event  = $this->Js->request(array('controller'=>'GameTemplates',
														 'action'=>'delete', $id),
												   array('update' => '#templates_list',
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide));
						echo $this->Html->link('<i class="icon-trash"></i>', '#',
												array ('id'=>'game_template_delete_'.$id,
														'escape' => false,
														'title' => 'Удалить шаблон',
														'class' => 'btn',
														'onClick'=>"$('#delete_confirm_".$id."').dialog({
																									resizable: false,
																									height:200,
																									width: 350,
																									modal: true,
																									buttons: {
																										'Удалить шаблон': function() {

																											window.location.href='delete/$id';
																											$(this).dialog('close');
																										},
																										Отмена: function() {

																											$(this).dialog('close');
																										}
																									}
																								});"

												));


						//$this->Js->get('#game_template_delete_'.$id)->event('click', $event);
						?>
				</div>

				<div id="delete_confirm_<?php echo $id; ?>" title="Подвердите удаление" style="display: none;">
						<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
							<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
							<?php echo $confiremDeleteMessage; ?>
						</div>
				</div>
				</td>
				<td valign="top"><?php echo $gameTemplate['GameTemplate']['id']; ?></td>
				<td valign="top"><?php echo '<strong>'.$gameTemplate['GameTemplate']['name'].'</strong><br/><small>('.$gameTemplate['GameTemplate']['longname'].')</small>'; ?></td>
				<td valign="top">
					<div style="display: inline;" class="icons">
					<ul class="ui-widget ui-helper-clearfix">

						<?php //pr($gameTemplate['Mod']); ?>

						<?php if ( !empty($gameTemplate['Mod']) ) {
									foreach ($gameTemplate['Mod'] as $mod): ?>

											<li>
												<div class="ui-widget">
													<div class="ui-state-highlight ui-corner-all" style="margin-top: 0px; padding: 2px 4px 1px 3px;">


														<?php
														//Иконка для добавления плагинов к моду
														echo $this->Html->link('<span class="ui-icon ui-icon-star" style="float: left; margin-right: .3em; margin-left: 0px; margin-top: 1px;"></span>'
																			.$mod['longname'].' '.$mod['version'], '#',
																			array ('id'=>$id.'-plugin_add_'.$mod['id'], 'escape' => false
																			,'onClick'=>"$('#link_mod').dialog({modal: true,position: ['center',130], show: 'clip', hide: 'clip', width:620});"

																			));
														$effect = $this->Js->get('#link_mod')->effect('slideIn');
														$event  = $this->Js->request(array('controller'=>'GameTemplates',
																					 'action'=>'linkPluginToMod', $mod['id']),
																			   array('update' => '#link_mod',
																					 'before'=>$loadingShow,
																					 'complete'=>$loadingHide));

														$this->Js->get('#'.$id.'-plugin_add_'.$mod['id'])->event('click', $event);

														?>

													</div>
												</div>
											</li>


									<?php
									endforeach;

							}
							?>



						</ul>
					</div>

				</td>
				<td valign="top">
					<div style="display: inline;" class="icons">
					<ul class="ui-widget ui-helper-clearfix">

						<?php //pr($gameTemplate['Mod']); ?>

						<?php if ( !empty($gameTemplate['Plugin']) ) {
									foreach ($gameTemplate['Plugin'] as $plugin): ?>

											<li>
												<div class="ui-widget">
													<div class="ui-state-highlight ui-corner-all" style="margin-top: 0px; padding: 2px 4px 1px 3px;">


														<?php
														//Иконка для добавления конфигов к плагину
														echo $this->Html->link('<span class="ui-icon ui-icon-star" style="float: left; margin-right: .3em; margin-left: 0px; margin-top: 1px;"></span>'
																			.$plugin['longname'].' '.$plugin['version'], '#',
																			array ('id'=>$id.'-config_add_'.$plugin['id'], 'escape' => false
																			,'onClick'=>"$('#link_plugin').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 620});"

																			));
														$effect = $this->Js->get('#link_plugin')->effect('slideIn');
														$event  = $this->Js->request(array('controller'=>'GameTemplates',
																					 'action'=>'linkConfigAndTagToPlugin', $plugin['id']),
																			   array('update' => '#link_plugin',
																					 'before'=>$loadingShow,
																					 'complete'=>$loadingHide));

														$this->Js->get('#'.$id.'-config_add_'.$plugin['id'])->event('click', $event);

														?>

													</div>
												</div>
											</li>


									<?php
									endforeach;

							}
							?>



						</ul>
					</div>

				</td>

			</tr>

			<?php
			endforeach;

		}
		?>
	</table>

</div>
<div id="link_template" style="display: none;" title="Привязка модов и плагинов"></div>
<div id="link_config" style="display: none;" title="Привязка конфигов к шаблону"></div>
<div id="link_service" style="display: none;" title="Привязка услуг к шаблону"></div>
<div id="link_mod" style="display: none;" title="Привязка плагинов и конфигов к моду"></div>
<div id="link_map" style="display: none;" title="Привязка карт к шаблону"></div>
<div id="link_plugin" style="display: none;" title="Привязка конфигов к плагину"></div>
<div id="add_map" style="display:none" title="Добавить карту"></div>
<div id="map_list" style="display:none" title="Список всех карт"></div>
<div id="add_mod" style="display:none" title="Добавить мод"></div>
<div id="add_plugin" style="display:none" title="Добавить плагин"></div>
<div id="add_type" style="display:none" title="Добавить тип"></div>
<div id="add_template" style="display:none" title="Добавить шаблон"></div>
<div id="add_config" style="display:none" title="Добавить конфигурационный файл"></div>
<div id="edit_template" style="display:none" title="Редактировать шаблон"></div>
