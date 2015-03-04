<?php
/*
 * Created on 01.06.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
 //pr(@$locations);
 include('loading_params.php');
?>

	<div id="root_servers_list">
	<?php foreach ($locations as $location): ?>
	<div class="list_border">
	<h3><?php echo $location['Location']['name'];?></h3>
		<table class="intext">
			<tr>
				<th></th>
				<th>ID</th>
				<th>Имя</th>
				<th>Слотов Максимально</th>
				<th>Слотов Куплено</th>
			</tr>
		<?php
		foreach ($location['RootServer'] as $rootServer):
		$id = $rootServer['id'];
		?>
			<tr style="background: #fff;">
				<td>
					<div class="btn-group">
							<?php
							//Иконка для редактирования параметров физического сервера
							echo $this->Html->link('<i class="icon-wrench"></i>', '#',
												array ( 'id'=>'server_edit_'.$id,
													    'escape' => false,
													    'class' => 'btn',
													    'title' => 'Редактировать основные параметры',
														'onClick'=>"$('#edit_root_server_container').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 400});"

												));
							$effect = $this->Js->get('#edit_root_server_container')->effect('slideIn');
							$event  = $this->Js->request(array('controller'=>'rootServers',
														 'action'=>'edit', $id),
												   array('update' => '#edit_root_server_container',
														 'before'=>$effect.$loadingShow,
														 'complete'=>$loadingHide));

							$this->Js->get('#server_edit_'.$id)->event('click', $event);


							//Иконка для удаления физического сервера
							$confiremDeleteMessage = 'Вы уверены, что хотите удалить сервер #'.$id.' - '.
													  $rootServer['name'].'?'.
													 "\n<br/><br/>Это необратимая операция!" ;
							echo $this->Html->link('<i class="icon-trash"></i>', '#',
													array ( 'id'=>'root_server_delete_'.$id,
														    'escape' => false,
													    	'class' => 'btn',
													    	'title' => 'Удалить сервер',
															'onClick'=>"$('#delete_confirm_".$id."').dialog({
																										resizable: false,
																										height:200,
																										width: 350,
																										modal: true,
																										buttons: {

																												'Удалить сервер': function() {
																												window.location.href='/rootServers/delete/$id';
																												$(this).dialog('close');
																											},
																											Отмена: function() {
																												$(this).dialog('close');
																											}
																										}
																									});"

													));
							?>
							<div id="delete_confirm_<?php echo $id; ?>" title="Подвердите удаление" style="display: none;">
									<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
										<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
										<?php echo $confiremDeleteMessage; ?>
									</div>
							</div>
							<?php
							//Иконка для просмотра статистики физического сервера
							echo $this->Html->link('<i class="icon-time"></i>', '#',
												array ( 'id'=>'server_stat_'.$id,
														'escape' => false,
													    'escape' => false,
												    	'class' => 'btn',
												    	'title' => 'Просмотр статистики',
														'onClick'=>"$('#stat').dialog({modal: true,position: ['center',50], show: 'clip', hide: 'clip', width: 900});"

												));
							$effect = $this->Js->get('#stat')->effect('slideIn');
							$event  = $this->Js->request(array('controller'=>'stats',
														 'action'=>'rootServerStat', $id),
												   array('update' => '#stat',
														 'before'=>'$("#stat").empty();'.$effect.$loadingShow,
														 'complete'=>$loadingHide));

							$this->Js->get('#server_stat_'.$id)->event('click', $event);

							?>
						</li>
						</ul>
					</div>
				</td>
				<td>#<?php echo $rootServer['id']; ?></td>
				<td>
					<?php

					//Ссылка  для просмотра списка IP, привязанных на физ.сервер
					echo $this->Html->link( $rootServer['name'], '#',
										array ('id'=>'ip_list_link_'.$id,
												'escape' => false,
												'title' => "Щелкните, чтобы посмотреть IP сервера."
												,'onClick'=>""

										));
					$effect = $this->Js->get('#ip_list_'.$id)->effect('slideIn');
					$event  = $this->Js->request(array('controller'=>'rootServers',
												 'action'=>'viewRootServerIp', $id),
										   array('update' => '#ip_list_'.$id,
												 'before'=>$effect.$loadingShow,
												 'complete'=>$loadingHide.";$('#ip_list_$id').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 500});"));

					$this->Js->get('#ip_list_link_'.$id)->event('click', $event);

					?>

					<div id="ip_list_<?php echo $id; ?>" style="display:none" title="<?php echo $rootServer['name']; ?>"></div>

				</td>
				<td><?php echo $rootServer['slotsMax']; ?></td>
				<td><?php echo $rootServer['slotsBought']; ?></td>
			</tr>
		<?php
		endforeach;
		?>
		</table>
	<div id="link_to_location">
			<?php

			//Ссылка  для создания физического сервера
			echo $this->Html->link('+ Привязать физические серверы к локации', '#',
								array ('id'=>'link_to_location_'.$location['Location']['id'], 'escape' => false
								,'onClick'=>"$('#link_rootserver_to_location').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 500});"

								));
			$effect = $this->Js->get('#link_rootserver_to_location')->effect('slideIn');
			$event  = $this->Js->request(array('controller'=>'locations',
										 'action'=>'linkRootserverToLocation', $location['Location']['id']),
								   array('update' => '#link_rootserver_to_location',
										 'before'=>$effect.$loadingShow,
										 'complete'=>$loadingHide));

			$this->Js->get('#link_to_location_'.$location['Location']['id'])->event('click', $event);

			?>

		</div>
	</div>
	<?php
	endforeach;
	?>
		<div id="view_stat">
			<?php

			//Ссылка  для создания физического сервера
			echo $this->Html->link('+ Общая статистика', '#',
								array ('id'=>'view_stat_link', 'escape' => false
								,'onClick'=>"$('#stat').dialog({modal: true,position: ['center',50], show: 'clip', hide: 'clip', width: 900});"

								));
			$effect = $this->Js->get('#stat')->effect('slideIn');
			$event  = $this->Js->request(array('controller'=>'stats',
										 'action'=>'rootServerStat', 'sum'),
								   array('update' => '#stat',
										 'before'=>'$("#stat").empty();'.$effect.$loadingShow,
										 'complete'=>$loadingHide));

			$this->Js->get('#view_stat_link')->event('click', $event);

			?>

		</div>
		<div id="add_location_about">
			<?php

			//Ссылка  для создания физического сервера
			echo $this->Html->link('+ Добавить новую локацию', '#',
								array ('id'=>'location_add_new', 'escape' => false
								,'onClick'=>"$('#add_location').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 500});"

								));
			$effect = $this->Js->get('#add_location')->effect('slideIn');
			$event  = $this->Js->request(array('controller'=>'locations',
										 'action'=>'add'),
								   array('update' => '#add_location',
										 'before'=>$effect.$loadingShow,
										 'complete'=>$loadingHide));

			$this->Js->get('#location_add_new')->event('click', $event);

			?>

		</div>
		<div id="add_root_server_about">
			<?php

			//Ссылка  для создания физического сервера
			echo $this->Html->link('+ Добавить новый сервер', '#',
								array ('id'=>'root_server_add_new', 'escape' => false
								,'onClick'=>"$('#add_root_server').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 400});"

								));
			$effect = $this->Js->get('#add_root_server')->effect('slideIn');
			$event  = $this->Js->request(array('controller'=>'rootServers',
										 'action'=>'add'),
								   array('update' => '#add_root_server',
										 'before'=>$effect.$loadingShow,
										 'complete'=>$loadingHide));

			$this->Js->get('#root_server_add_new')->event('click', $event);

			?>

		</div>
			<div id="add_root_server_about">

			<?php

			//Ссылка  для добавления IP
			echo $this->Html->link('+ Добавить IP', '#',
								array ('id'=>'ip_add_new', 'escape' => false
								,'onClick'=>"$('#add_ip').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 400});"

								));
			$effect = $this->Js->get('#add_ip')->effect('slideIn');
			$event  = $this->Js->request(array('controller'=>'rootServerIps',
										 'action'=>'add'),
								   array('update' => '#add_ip',
										 'before'=>$effect.$loadingShow,
										 'complete'=>$loadingHide));

			$this->Js->get('#ip_add_new')->event('click', $event);

			?>

		</div>
	</div>


<div id="stat" style="display:none;"  class="ui-widget-content ui-corner-all" title="Статистика">
	Загрузка данных...
</div>
<!-- Контейнер для создания диалога создания локации -->
<div id="add_location" style="display:none;"  class="ui-widget-content ui-corner-all" title="Добавить локацию">
	Загрузка данных...
</div>
<!-- Контейнер для создания диалога редактирования локации -->
<div id="edit_root_server" style="display:none;"  class="ui-widget-content ui-corner-all" title="Редактировать локацию">
		Загрузка данных...
</div>
<!-- Контейнер для привязки серверов к локации -->
<div id="link_rootserver_to_location" style="display:none;"  class="ui-widget-content ui-corner-all" title="Привязать серверы">
	Загрузка данных...
</div>
<!-- Контейнер для создания диалога создания физического сервера -->
<div id="add_root_server" style="display:none;"  class="ui-widget-content ui-corner-all" title="Добавить физический сервер">
	Загрузка данных...
</div>
<!-- Контейнер для создания диалога добавления IP -->
<div id="add_ip" style="display:none;"  class="ui-widget-content ui-corner-all" title="Добавить IP">
	Загрузка данных...
</div>
<!-- Контейнер для создания диалога редактирования параметров физического сервера -->
<div id="edit_root_server_container" style="display:none;"  class="ui-widget-content ui-corner-all" title="Редактировать параметры">
	<div id="edit_root_server">
		Загрузка данных...
	</div>
</div>
<?php
			echo $this->Js->writeBuffer(); // Write cached scripts
?>
