<?php
/*
 * Created on 24.05.2011
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
 include('../loading_params.php');
 $rand_id = rand('1', '100000000000');
 echo $html->link($name, '#',
							array ( 'id'=>'client_view_'.$id.'_'.$rand_id, 
									'escape' => false,
									'title' => 'Просмотр данных клиента #'.$id,
									'onClick'=>"$('#client_view').dialog({  modal: true," .
																		  " position: ['center',150], " .
																		  " title: 'Данные клиента #".$id."'," .
																		  " show: 'clip', " .
																		  " hide: 'clip', " .
																		  " width: 300});"
							
							));
		$effect = $js->get('#client_view')->effect('slideIn');		
		$event  = $js->request(array('controller'=>'Users',
									 'action'=>'view', $id), 
							   array('update' => '#client_view',	  
									 'before'=>$effect.$loadingShow,
									 'complete'=>$loadingHide));

		$js->get('#client_view_'.$id.'_'.$rand_id)->event('click', $event);
?>
