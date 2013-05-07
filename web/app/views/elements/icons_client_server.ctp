<?php
/*
 * Created on 21.06.2010
 *
 * File created for project TeamServer
 * by nikita
 */
 include('../loading_params.php');
?>
<div class="btn-group" id="icons_<?php echo $id;?>">
	<?php if ($initialised === true)
	{ 

		// Продление только для инициализированного сервера 
		//Иконка для продления оплаты игрового сервера
		echo $html->link('<i class="icon-shopping-cart"></i>', '#',
									array ('id'=>'server_cart_'.$id, 
										   'escape' => false,
										   'class'=>"btn",
										   'title' => "Продлить сервер",
										   'onClick'=>"$('#prolongate_server').dialog({modal: true,position: ['center',160], show: 'clip', hide: 'clip', width: 700});"));

		$effect = $js->get('#prolongate_server')->effect('slideIn');		
		$event  = $js->request(array('controller'=>'Orders',
									 'action'=>'prolongate', $id), 
							   array('update' => '#prolongate_server',	  
									 'before'=>$loadingShow,
									 'complete'=>$loadingHide,
									 'buffer'=>false));

		$js->get('#server_cart_'.$id)->event('click', $event);
    
    } 

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
								 'before'=>$effect.$loadingShow.";$('#server_log_view_$id').remove();",
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
 
    if (@$serverType != 'ueds' and @$type != 'eac') {?>
			<?php
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

    	if ( in_array($name, array( 'css', 'cssv34', 'dods', 'tf', 'cs16'))) 
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
	} 

	    if (@$type != 'voice' and @$type != 'eac') 
	    {
			//Иконка для вывода параметров доступа к серверу
			echo $html->link('<i class="icon-folder-open"></i>', '#',
								array( 'id'=>'access_info_'.$id, 
									   'escape' => false,
									   'class'=>"btn",
									   'title' => 'Виды доступа к серверу',
								       'onClick'=>"$('#access_info').dialog({modal: true,position: ['center',50], show: 'clip', hide: 'clip', width: 710});"
								
								));
			$effect = $js->get('#access_info')->effect('slideIn');		
			$event  = $js->request(array('controller'=>'servers',
										 'action'=>'accessInfo', $id), 
								   array('update' => '#access_info',	  
										 'before'=>$loadingShow,
										 'complete'=>$loadingHide));

			$js->get('#access_info_'.$id)->event('click', $event);

	    } 
	}
	?>
	
</div>