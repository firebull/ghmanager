<?php
/*
 * Created on 21.06.2010
 *
 * File created for project TeamServer
 * by nikita
 */
 include('../loading_params.php');
?>
		<div class="btn-group">
			<?php
			//Иконка для редактирования параметров игрового сервера
			echo $html->link('<i class="icon-wrench"></i>', '#',
								array( 'id'=>'server_edit_'.$id, 
									   'escape' => false,
									   'class'=>"btn",
									   'title' => 'Изменить параметры сервера',
								       'onClick'=>"$('#edit_server').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});"));
			$effect = $js->get('#edit_server')->effect('slideIn');		
			$event  = $js->request(array('controller'=>'Servers',
										 'action'=>'edit', $id), 
								   array('update' => '#edit_server',	  
										 'before'=>$loadingShow,
										 'complete'=>$loadingHide,
										 'buffer'=>false));

			$js->get('#server_edit_'.$id)->event('click', $event);

			//Иконка для просмотра состояния игрового сервера
			if (@$viewLink){
				if (@$type != 'eac'){

                $effect = $js->get('#server_more_'.$id)->effect('slideIn');

				echo $html->link('<i class="icon-zoom-in"></i>', '#',
							array( 'id'=>'server_view_'.$id, 
								   'escape' => false,
								   'class'=>"btn",
								   'title' => 'Просмотр состояния сервера'
									));
				
				$event  = $js->request(array('controller'=>'Servers',
										 'action'=>'viewServer', $id), 
								   array('update' => '#server_more_'.$id,	  
										 'before'=>$effect.$loadingShow,
										 'complete'=>$loadingHide));
				
				$js->get('#server_view_'.$id)->event('click', $event);				
   
				}

				if (@$type != 'radio' && @$type != 'voice') {

					//Иконка для редактирования параметров запуска
				    echo $html->link('<i class="icon-cog"></i>', '#',
								array( 'id'=>'start_params_'.$id, 
									   'escape' => false,
										'class'=>"btn",
										'title' => 'Изменить параметры запуска',
										'onClick'=>"$('#server_start_params_container').dialog({modal: true," .
																					   "position: ['center',50], " .
																					   "title: 'Изменить параметры сервера ".$longname." #".$id."'," .
																					   "show: 'clip', " .
																					   "hide: 'clip', " .
																					   "width: 900});"));
					$effect = $js->get('#server_start_params_container')->effect('slideIn');		
					$event  = $js->request(array('controller'=>'Servers',
												 'action'=>'editStartParams', $id), 
										   array('update' => '#server_start_params_container',	  
												 'before'=>$loadingShow,
												 'complete'=>$loadingHide));

					$js->get('#start_params_'.$id)->event('click', $event);
                } 
            }
            
            if (@$initialised === true)
            {    
                if (@$serverType != 'ueds' and @$serverType != 'eac') 
                {
					//Иконка для редактора параметров сервера
					echo $html->link('<i class="icon-pencil"></i>', '#',
								array( 'id'=>'params_'.$id, 
									   'escape' => false,
									   'class'=>"btn",
									   'title' => 'Изменить настройки сервера',
								       'onClick'=>"$('#server_params_container').dialog({ modal: true," .
																				   "position: ['center',50], " .
																				   "title: 'Изменить настройки сервера ".$longname." #".$id."'," .
																				   "show: 'clip', " .
																				   "hide: 'clip', " .
																				   "width: 1050});"
								
								));
					$effect = $js->get('#server_params_container')->effect('slideIn');		
					$event  = $js->request(array('controller'=>'servers',
												 'action'=>'editParams', $id), 
										   array('update' => '#server_params_container',	  
												 'before'=>$loadingShow,
												 'complete'=>$loadingHide));
		
					$js->get('#params_'.$id)->event('click', $event);

				} 
			}
			if (@$viewLink){	
				if (@$type != 'radio' && @$type != 'voice' && @$type != 'eac') 
				{
					//Иконка для установки плагинов
					echo $html->link('<i class="icon-briefcase"></i>', '#',
								array( 'id'=>'plugin_'.$id, 
									   'escape' => false,
									   'class'=>"btn",
									   'title' => 'Установить моды и плагины',
								       'onClick'=>"$('#plugin_install').dialog({modal: true," .
																		 "position: ['center',50], " .
																		 "title: 'Установить моды и плагины на сервер ".$longname." #".$id."'," .
																		 "show: 'clip', " .
																		 "hide: 'clip', " .
																		 "width: 680});"
								
								));
					$effect = $js->get('#plugin_install')->effect('slideIn');		
					$event  = $js->request(array('controller'=>'servers',
												 'action'=>'pluginInstall', $id, 'rediscover'=>'1'), 
										   array('update' => '#plugin_install',	  
												 'before'=>$loadingShow,
												 'complete'=>$loadingHide));
		
					$js->get('#plugin_'.$id)->event('click', $event);

					if ( in_array($name, array( 'css', 'cssv34', 'cs16', 'dods', 'tf'))) 
					{
						//Иконка для установки карт
						echo $html->link('<i class="icon-picture"></i>', '#',
									array('id'=>'maps_'.$id, 
										  'escape' => false,
										  'class'=>"btn",
										  'title' => 'Установить карты',
										  'onClick'=>"$('#map_install').dialog({modal: true," .
																			 "position: ['center',20], " .
																			 "title: 'Установить карты на сервер ".$longname." #".$id."'," .
																			 "show: 'clip', " .
																			 "hide: 'clip', " .
																			 "width: 870});"
									
									));
						$effect = $js->get('#map_install')->effect('slideIn');		
						$event  = $js->request(array('controller'=>'servers',
													 'action'=>'mapInstall', $id), 
											   array('update' => '#map_install',	  
													 'before'=>$loadingShow,
													 'complete'=>$loadingHide));
			
						$js->get('#maps_'.$id)->event('click', $event);

					} 
				
				} ?>
			<?php
			}

			//Иконка для удаления физического сервера
			$confiremDeleteMessage = 'Вы уверены, что хотите удалить сервер #'.$id.' ?'.
									 "\n<br/><br/>Это необратимая операция!" ;
			echo $html->link('<i class="icon-trash"></i>', '#',
									array('id'=>'server_delete_'.$id, 
									'escape' => false,
									'title' => 'Удалить сервер',
									'class'=>"btn",
									'onClick'=>"$('#delete_confirm_".$id."').dialog({
																						resizable: false,
																						height:200,
																						width: 350,
																						modal: true,
																						buttons: {

																								'Удалить сервер': function() {
																								window.location.href='/servers/delete/$id';
																								$(this).dialog('close');
																							},
																							Отмена: function() {
																								$(this).dialog('close');
																							}
																						}
																					});"
									
									));
						?>
				
		</div>

		<div id="delete_confirm_<?php echo $id; ?>" title="Подвердите удаление" style="display: none;">
			<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
				<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
				<?php echo $confiremDeleteMessage; ?>								
			</div>
		</div>