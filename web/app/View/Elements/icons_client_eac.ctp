<?php
/*
 * Created on 08.05.2012
 *
 * File created for project TeamServer
 * by nikita
 */
 include('loading_params.php');
?>

		<div class="btn-group">
			<?php if ($initialised === true){ // Продление только для инициализированного сервера ?>
			<?php
			//Иконка для продления оплаты игрового сервера
			echo $this->Html->link('<i class="icon-shopping-cart"></i>', '#',
								array ('id'=>'server_cart_'.$id,
									   'escape' => false,
									   'class'=>"btn",
									   'title' => "Продлить сервер",
										'onClick'=>"$('#prolongate_server').dialog({modal: true,position: ['center',160], show: 'clip', hide: 'clip', width: 700});"));
			$effect = $this->Js->get('#prolongate_server')->effect('slideIn');
			$event  = $this->Js->request(array('controller'=>'Orders',
										 'action'=>'prolongate', $id),
								   array('update' => '#prolongate_server',
										 'before'=>$loadingShow,
										 'complete'=>$loadingHide,
										 'buffer'=>false));

			$this->Js->get('#server_cart_'.$id)->event('click', $event);
			?>
			<?php
			//Иконка для редактирования параметров запуска
			echo $this->Html->link('<i class="icon-cog"></i>', '#',
								array('id'=>'start_params_'.$id,
									  'escape' => false,
									  'class'=>"btn",
									  'title' => 'Изменить параметры запуска',
								      'onClick'=>"$('#server_start_params_container').dialog({modal: true," .
																					   "position: ['center',50], " .
																					   "title: 'Изменить параметры сервера ".$longname." #".$id."'," .
																					   "show: 'clip', " .
																					   "hide: 'clip', " .
																					   "width: 900});"));
			$effect = $this->Js->get('#server_start_params_container')->effect('slideIn');
			$event  = $this->Js->request(array('controller'=>'Servers',
										 'action'=>'editStartParams', $id),
								   array('update' => '#server_start_params_container',
										 'before'=>$loadingShow,
										 'complete'=>$loadingHide));

			$this->Js->get('#start_params_'.$id)->event('click', $event);


				if ($state == '1')
				{
					$iconEac = 'stop';
					$titleEac = 'Отключить EAC';
				}
				else
				{
					$iconEac = 'play';
					$titleEac = 'Включить EAC';
				}


				//Иконка для отключения EAC
				echo $this->Html->link('<i class="icon-'.$iconEac.'" id="eac_switch_button_'.$id.'"></i>', '#',
									array('id'=>'eac_switch_'.$id,
										  'escape' => false,
										  'class'=>"btn",
										  'title' => $titleEac,
									      'onClick'=>"$('#common_result').empty();
												 $('#common_result').dialog({modal: true," .
																		   "position: ['center',100], " .
																		   "title: 'EAC #".$id."'," .
																		   "show: 'clip', " .
																		   "hide: 'clip', " .
																		   "width: 300});"

									));
				$effect = $this->Js->get('#server_control_'.$id)->effect('slideIn');
				$event  = $this->Js->request(array('controller'=>'servers',
											 'action'=>'switchEac', $id,'switch'),
									   array('update' => '#common_result',
											 'before'=>$loadingShow.";",
											 'complete'=>$loadingHide.";setButtonEac(".$id.");return false;"
											 ));

				$this->Js->get('#eac_switch_'.$id)->event('click', $event);

				?>
			<?php } ?>

		</div>

<?php
			echo $this->Js->writeBuffer(); // Write cached scripts
?>
