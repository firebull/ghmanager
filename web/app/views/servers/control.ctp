<?php
/*
 * Created on 20.06.2010
 *
 * To change the template for this generated file go to
 * Made fot project TeamServer
 * by bulaev
 */
//pr($servers);
include('../loading_params.php');
$sourceTvEnable = array(
  							'tf',
  							'css',
  							'cssv34',
  							'hl2mp',
  							'dods'

  							);
$hltvEnable = array(
  							'cs16',
  							'cs16-old',
  							'dmc'
  							);
?>
<script type="text/javascript">

		function GetStatuses () {

			    $.getJSON('/servers/getStatus',
		                  {id: "<?php echo $serversIds; ?>"},
		                  function(tmps) {
		                    if(tmps !== null) {
		                      SetStatuses(tmps);
		                    }
		        		  }
		                );
			}

		function SetStatuses (tmps) {
				var tr = '';
				var style = '';

				$.each(tmps, function(index, status) {
					tr = '#server_string_' + index;
					td = '#server_status_' + index;
					style = 'tbl_hover_' + status;

					if (!$(td).hasClass(status)){
						$(td).removeClass().addClass(status);

						if (status == 'stoped'){
							$(td).attr('title','Сервер выключен');
						}
						else if (status == 'running')
						{
							$(td).attr('title','Сервер включён и работает');
						}
						else if (status == 'updating')
						{
							$(td).attr('title','Сервер обновляется, текущее состояние отображается в логах.');
						}
						else if (status == 'error'){
							$(td).attr('title','Сервер еще включается либо в процессе запуска возникла ошибка. Смотрите подробный статус и читайте логи.');
						}
					}

					$(tr).attr('onMouseover','this.className="'+style+'"')
					$(tr).attr('onMouseout','this.className="tbl_out"')


				});

			}
</script>
<div id="servers_list">
<?php

		// Статусы //
		$statusList = array('exec_success' => 'Запущен',
							'exec_error'   => 'Ошибка запуска',
							'update_started' => 'Обновляется',
							'stopped' => 'Выключен',
							'all' => 'Все состояния'
							);
?>
<div id="searchButton" style="margin-bottom: 5px;">
	<?php
			$searchText = 'Поиск';
			$searchParam = '';
			$isSearch = false;

			if (!is_null(@$locationId) and @$locationId != 'all'){
				$searchParam = 'Локация: '.@$locationsList[$locationId].'; ';
				$isSearch = true;
			}

			if (!is_null($statusChoise) and @$statusChoise != 'all'){
				$searchParam .= 'Статус: '.@$statusList[$statusChoise].'; ';
				$isSearch = true;
			}

			if (!is_null(@$searchUserName) and @$searchUserName != 'all'){
				$searchParam .= 'Клиент: '.@$searchUserName.'; ';
				$isSearch = true;
			}

			if (!is_null(@$searchServerId) and @$searchServerId != 'all'){
				$searchParam .= 'ID сервера: #'.@$searchServerId.'; ';
				$isSearch = true;
			}

			if (!is_null(@$searchServerIp) and @$searchServerIp != 'all'){
				$searchParam .= 'IP сервера: '.@$searchServerIp;
				$isSearch = true;
			}

			if (!is_null(@$searchServerPort) and @$searchServerPort != 'all'){
				if (@$searchServerIp != 'all'){
					$searchParam .= ':'.$searchServerPort;
				}
				else
				{
					$searchParam .= 'Порт сервера: #'.$searchServerPort.'; ';
				}

				$isSearch = true;
			}

			if (@$searchServerIp != 'all'){
				$searchParam .= '; ';
			}

			if ($isSearch === true){
				echo $html->tag('strong', $searchParam, array('style' => 'color: #970405;'));
				$searchText = 'Изменить условия поиска';
			}

			echo $html->link($searchText, '#', array('onClick' => " $('#searchButton').hide(); $('#searchForm').show('highlight');"));



	?>
</div>
<div id="clear"></div>
<div id="searchForm" style="display: none;">
	<?php

	echo $form->create('Server', array('class' => 'form-inline'));
	echo $form->input('User.username', array(	'type' => 'hidden',
												'value' => 'all'));
	echo $form->input('Server.id', array(
										'type'=>'hidden',
										'value' => 'all'));
	echo $form->input('Server.address', array(
										'type'=>'hidden',
										'value' => 'all'));
	echo $form->input('Server.port', array(
										'type'=>'hidden',
										'value' => 'all'));
	?>
	<fieldset>
		<div class="control-group">
			<div class="input-prepend">
				<span class="add-on">Локация</span><?php 	echo $form->input('Location.id', array('options' => @$locationsList,
										'selected'=>@$locationId,
										'div' => false,
										'id'  => 'locations',
										'label' => false)); ?>
			</div>

			<div class="input-prepend">
				<span class="add-on">Статус</span><?php 	echo $form->input('Server.status', array('options' => $statusList,
										'selected'=> @$statusChoise,
										'div' => false,
										'id'  => 'statuses',
										'label' => false)); ?>
			</div>
			<?php echo $form->submit('Отобрать', array('class' => 'btn btn-primary', 'div' => false));
			?>

		</div>
	</fieldset>
	<?php echo $form->end();

	echo $form->create('Server', array('class' => 'form-inline'));
	echo $form->input('Location.id', array(
										'type'=>'hidden',
										'value' => 'all'));
	echo $form->input('Server.status', array(
										'type'=>'hidden',
										'value' => 'all'));
	echo $form->input('Server.id', array(
										'type'=>'hidden',
										'value' => 'all'));
	echo $form->input('Server.address', array(
										'type'=>'hidden',
										'value' => 'all'));
	echo $form->input('Server.port', array(
										'type'=>'hidden',
										'value' => 'all'));

	?>
	<fieldset>
		<div class="control-group">
			<div class="input-prepend input-append">
				<span class="add-on">Клиент</span><?php echo $form->input('User.username',
										array(	'id' => 'usernameSort',
												'value' => @$searchUserName,
												'div' => false,
												'label' => false,
												'style' => 'width: 150px;'));

				echo $form->submit('Искать', array('class' => 'btn btn-primary', 'div' => false));?>
			</div>
		</div>
	</fieldset>
	<?php echo $form->end();

	echo $form->create('Server', array('class' => 'form-inline'));
	echo $form->input('Location.id', array(
										'type'=>'hidden',
										'value' => 'all'));
	echo $form->input('Server.status', array(
										'type'=>'hidden',
										'value' => 'all'));
	echo $form->input('User.username', array(	'type' => 'hidden',
												'value' => 'all'));
	echo $form->input('Server.address', array(
										'type'=>'hidden',
										'value' => 'all'));
	echo $form->input('Server.port', array(
										'type'=>'hidden',
										'value' => 'all'));

	?>
	<fieldset>
		<div class="control-group">
			<div class="input-prepend input-append">
				<span class="add-on">&nbsp;&nbsp;&nbsp;ID #</span><?php echo $form->input('Server.id',
										array(	'id' => 'searchByServerId',
												'type' => 'text',
												'value' => @$searchServerId,
												'div' => false,
												'label' => false,
												'style' => 'width: 150px;'));

				echo $form->submit('Искать', array('class' => 'btn btn-primary', 'div' => false));?>
			</div>
		</div>
	</fieldset>

	<?php echo $form->end();

	echo $form->create('Server', array('class' => 'form-inline'));
	echo $form->input('Location.id', array(
										'type'=>'hidden',
										'value' => 'all'));
	echo $form->input('Server.status', array(
										'type'=>'hidden',
										'value' => 'all'));
	echo $form->input('User.username', array(	'type' => 'hidden',
												'value' => 'all'));

	?>
	<fieldset>
		<div class="control-group">

			<div class="input-prepend">
				<span class="add-on">IP</span><?php

					echo $form->input('Server.address',
								array(	'id' => 'searchByServerIp',
										'type' => 'text',
										'value' => @$searchServerIp,
										'div' => false,
										'label' => false,
										'style' => 'width: 150px;'));?>
			</div>

			<div class="input-prepend">
				<span class="add-on">Порт</span><?php echo $form->input('Server.port',
								array(	'id' => 'searchByServerPort',
										'type' => 'text',
										'value' => @$searchServerPort,
										'div' => false,
										'label' => false,
										'style' => 'width: 100px;'));?>

			</div>
			<?php echo $form->submit('Искать', array('class' => 'btn btn-primary', 'div' => false));?>


		</div>
	</fieldset>
	<?php echo $form->end();

	?>
</div>
<table class="intext" border="0" cellpadding="0" cellspacing="0">
	<tr>

		<th></th>
		<th><?php echo $paginator->sort('ID', 'id'); ?></th>
		<th>Клиент</th>
		<th>Игра</th>
		<th width="280"></th>
		<th>Слотов</th>
		<th><?php echo $paginator->sort('Адрес', 'address'); ?>/Cервер</th>
		<th width="160">Оплачено до</th>
	</tr>

	<!-- Here is where we loop through our servers array, printing out their info -->

	<?php
	$i=1;
	$now=$time->fromString('now');

	if ( !empty($servers) ) {
		foreach ($servers as $server):
			switch (@$server['Type'][0]['name']){
				case 'srcds':
				case 'hlds':
				case 'cod':
				case 'ueds':
				case 'game':
					$type = 'Game';
					break;
				case 'voice':
					$type = 'Voice';
					break;
				case 'chat':
					$type = 'Chat';
					break;
				case 'radio':
					$type = 'Radio';
					break;
				case 'eac':
					$type = 'Eac';
					break;
			}

			if ($server['Server']['action'] == 'delete')
	//Если сервер установлен на удаление:
	//**********************************************************************************
			{
			?>
		<tr id="opener_<?php echo $server['Server']['id']; ?>" class="tbl_out_hide" onmouseout="this.className='tbl_out_hide'" onmouseover="this.className='tbl_hover_red'">
			<td class="status"><div class="processing" title="Сервер будет удалён в ближайшее время"></div></td>
			<td>#<?php echo $server['Server']['id']; ?></td>
			<td><?php echo $this->element('client_view', array( 'id'   => @$server['User'][0]['id'],
																'name' => @$server['User'][0]['username'])); ?>
			</td>
			<td  class="left"><?php echo $html->image('icons/servers/'.@$server['GameTemplate'][0]['name'].'.png',
														array(  'alt' => @$server['GameTemplate'][0]['longname'],
																'width'  => '24',
																'height' => '24' )); ?></td>

			<td colspan="2">
			Сервер ожидает удаления
			</td>
			<td>

				<?php

					if (!empty($server['Server']['address']))
					{
						echo $server['Server']['address'].":".$server['Server']['port'];
					}
					else
					{
						echo "-";
					}


					if ( in_array($server['GameTemplate'][0]['name'], $sourceTvEnable) ){
							echo '<br/>STV: '.(intval($server['Server']['port']) + 1015);
						}
					else
					if ( in_array($server['GameTemplate'][0]['name'], $hltvEnable) ){
							echo '<br/>HLTV: '.(intval($server['Server']['port']) + 1015);
						}

				?>
				<br/>
				<small>
				<?php
						if (!empty($server['RootServer'][0]['id']))
						{
							echo '#'.$server['RootServer'][0]['id'].'&nbsp;"'.$server['RootServer'][0]['name'].'"';
						}
						else
						{
							echo "-";
						}
						 ?>
				</small>
			</td>
			<td></td>
			<?php
			}
			else if (($now < $time->fromString($server['Server']['payedTill'])) && ($server['Server']['initialised'] == 1 ))
	//Если сервер оплачен и инициализирован:
	//**********************************************************************************
			{
			?>
		<tr class="tbl_out" onmouseout="this.className='tbl_out'" onmouseover="this.className='tbl_hover_green'">

			<td class="status">
				<?php if ($type == 'Game' or $type == 'Voice') {?>
				<div id='server_status_<?php echo $server['Server']['id']; ?>' class="stoped" title="Сервер выключен"></div>
				<?php } ?>
			</td>
			<td>#<?php echo $server['Server']['id']; ?></td>

			<td><?php echo $this->element('client_view', array( 'id'   => @$server['User'][0]['id'],
																'name' => @$server['User'][0]['username'])); ?>
			</td>
			<td  class="left"><?php echo $html->image('icons/servers/'.$server['GameTemplate'][0]['name'].'.png',
														array(  'alt' => $server['GameTemplate'][0]['longname'],
																'width'  => '24',
																'height' => '24' )); ?></td>
			<td><?php echo $this->element('icons_control_server', array(
																	'id'=>$server['Server']['id'],
																	'name' => $server['GameTemplate'][0]['name'],
																	'longname'=>$server['GameTemplate'][0]['longname'],
																	'viewLink'=>'true',
																	'initialised' => true,
																	'type' => strtolower($type),
																	'serverType' => $server['Type'][0]['name']
																	 )); ?></td>
			<td><?php echo $server['Server']['slots']; ?></td>
			<td>

				<?php

					if (!empty($server['Server']['address']))
					{
						echo $server['Server']['address'].":".$server['Server']['port'];
					}
					else
					{
						echo "-";
					}


					if ( in_array($server['GameTemplate'][0]['name'], $sourceTvEnable) ){
							echo '<br/>STV: '.(intval($server['Server']['port']) + 1015);
						}
					else
					if ( in_array($server['GameTemplate'][0]['name'], $hltvEnable) ){
							echo '<br/>HLTV: '.(intval($server['Server']['port']) + 1015);
						}

				?>
				<br/>
				<small>
				<?php
						if (!empty($server['RootServer'][0]['id']))
						{
							echo '#'.$server['RootServer'][0]['id'].'&nbsp;"'.$server['RootServer'][0]['name'].'"';
						}
						else
						{
							echo "-";
						}
						 ?>
				</small>
			</td>
			<td><?php

				if ($server['Server']['scaleTime'] >= 0 and $server['Server']['scaleTime'] <= 0.2){
					$scaleColor = '#970405';
					$dateColor = '#DD1500';
				}
				else
				{
					$scaleColor = '#bbb';
					$dateColor = '#000';
				}

				echo $html->tag('small', $this->Common->niceDate($server['Server']['payedTill']));
				echo "<br/>";
				if (!empty($server['Server']['giftDays'])
						and
					strtotime($server['Server']['giftExpires']) > time())
					{

						echo $html->tag( 'small', 'Из них '.$server['Server']['giftDays']." в подарок");

					}
			?>
			<div style="width: 150px; height: 8px; padding: 0px; border: 1px solid #777;">
			<?php
				// Нарисовать линейку - сколько осталось
				echo $html->tag('div', '', array( 'style' => 'background-color: '.$scaleColor.'; ' .
															 'width: '.($server['Server']['scaleTime']*100).'%;' .
															 'height: 8px;'));

			?>
			</div>
			</td>
		</tr>
		<tr id="opener_<?php echo $server['Server']['id']; ?>">
			<td colspan="8" class="server_more">
				<div id="server_more_<?php echo $server['Server']['id']; ?>" style="display:none;text-align:center;width:100%;">
				Загрузка текущей информации...

				<a href="#" onclick="$('server_more_<?php echo $server['Server']['id']; ?>').hide(); return false;">Скрыть</a>
				</div>
				<div style="display: none; text-align: left;" title="Просмотр журнала сервера #<?php echo $server['Server']['id'];?>" id="journal_<?php echo $server['Server']['id']; ?>"></div>
			</td>
		</tr>
			<?php
			}
			else if (($now < $time->fromString($server['Server']['payedTill'])) && ($server['Server']['initialised'] == 0 ))
	//Если сервер оплачен, но не инициализирован:
	//**********************************************************************************
			{
			?>
		<tr id="opener_<?php echo $server['Server']['id']; ?>" class="tbl_out" onmouseout="this.className='tbl_out'" onmouseover="this.className='tbl_hover_yellow'">

			<td class="status"><div class="processing" title="Ожидание инициализации"></div></td>
			<td>#<?php echo $server['Server']['id']; ?></td>

			<td><?php echo $this->element('client_view', array( 'id'   => @$server['User'][0]['id'],
																'name' => @$server['User'][0]['username'])); ?></td>
			<td  class="left"><?php echo $html->image('icons/servers/'.$server['GameTemplate'][0]['name'].'.png',
														array(  'alt' => $server['GameTemplate'][0]['longname'],
																'width'  => '24',
																'height' => '24' )); ?></td>

			<td><?php echo $this->element('icons_control_server', array(
																	     'id'=>$server['Server']['id']
																	    )); ?>
			</td>
			<td><?php echo $server['Server']['slots']; ?></td>
			<td class="left">
				Ожидание инициализации сервера
			</td>
			<td><?php echo $this->Common->niceDate($server['Server']['payedTill']); ?></td>

		</tr>
			<?php
			}
			else if (($now > $time->fromString($server['Server']['payedTill'])) && ($server['Server']['initialised'] == 1 ))
		//Если сервер не оплачен, но инициализирован (окончилась оплата):
		//**********************************************************************************
			{
			?>
		<tr id="opener_<?php echo $server['Server']['id']; ?>" style="border-bottom: 2px solid grey;" class="tbl_out double_border" onmouseout="this.className='tbl_out double_border'" onmouseover="this.className='tbl_hover_red double_border'">
			<td class="status"><div class="warning" title="Ожидание оплаты"></div></td>
			<td class="id"><?php echo "#".$server['Server']['id']; ?></td>
			<td>
			<?php echo $this->element('client_view', array( 'id'   => @$server['User'][0]['id'],
																'name' => @$server['User'][0]['username'])); ?>
			</td>
			<td  class="left">
				<?php echo $html->image('icons/servers/'.$server['GameTemplate'][0]['name'].'.png',
														array(  'alt' => $server['GameTemplate'][0]['longname'],
																'width'  => '24',
																'height' => '24' )); ?>
			</td>
			<td><?php echo $this->element('icons_control_server', array(
																	'id'=>$server['Server']['id'],
																	'initialised' => true,
																	'name' => $server['GameTemplate'][0]['name'],
																	'longname'=>$server['GameTemplate'][0]['longname'],
																	'type' => strtolower($type),
																	'serverType' => $server['Type'][0]['name']
																	 )); ?></td>
			<td><?php echo $server['Server']['slots']; ?></td>
			<td>
				<?php

					echo $server['Server']['address'].":".$server['Server']['port'];
					if ( in_array($server['GameTemplate'][0]['name'], $sourceTvEnable) ){
							echo '<br/>Порт Source TV: '.(intval($server['Server']['port']) + 1015);
					}
					else
					if ( in_array($server['GameTemplate'][0]['name'], $hltvEnable) ){
							echo '<br/>Порт HLTV: '.(intval($server['Server']['port']) + 1015);
						}


				?>
				<br/>
				<small>#<?php echo @$server['RootServer'][0]['id'].'&nbsp;"'.@$server['RootServer'][0]['name'].'"'; ?>
				</small>
				</td>
			<td>Ожидание оплаты c<br/><?php echo $this->Common->niceDate($server['Server']['payedTill']); ?></td>

		</tr>
			<?php
			}

			else
	//Если сервер не оплачен, и не инициализирован:
	//**********************************************************************************
			{
			?>
		<tr id="opener_<?php echo $server['Server']['id']; ?>" class="tbl_out" onmouseout="this.className='tbl_out'" onmouseover="this.className='tbl_hover_red'">
			<td class="status"><div class="warning"  title="Ожидание оплаты"></div></td>
			<td>#<?php echo $server['Server']['id']; ?></td>

			<td><?php echo $this->element('client_view', array( 'id'   => @$server['User'][0]['id'],
																'name' => @$server['User'][0]['username'])); ?></td>
			<td  class="left"><?php echo $html->image('icons/servers/'.$server['GameTemplate'][0]['name'].'.png',
														array(  'alt' => $server['GameTemplate'][0]['longname'],
																'width'  => '24',
																'height' => '24' )); ?></td>
			<td><?php echo $this->element('icons_control_server', array(
																	     'id'=>$server['Server']['id']
																	   )); ?>
			</td>
			<td><?php echo $server['Server']['slots']; ?></td>
			<td colspan="2">
			Ожидание оплаты
			</td>

			<?php
			}
			?>
		</tr>
		<?php
		endforeach;

	}
	?>


</table>

	<div id="add_server_about">
		<?php
		//Ссылка  для создания физического сервера
		echo $html->link('+ Добавить новый сервер', '#',
							array ('id'=>'server_add_new', 'escape' => false
							,'onClick'=>"$('#add_server').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 400});"

							));
		$effect = $js->get('#add_server')->effect('slideIn');
		$event  = $js->request(array('controller'=>'Servers',
									 'action'=>'add'),
							   array('update' => '#add_server',
									 'before'=>$effect.$loadingShow,
									 'complete'=>$loadingHide));

		$js->get('#server_add_new')->event('click', $event);

		?>
	</div>



<center>
<!-- Shows the next and previous links -->
<?php
	echo $paginator->prev('«««', null, null, array('class' => 'disabled'));
	echo '&nbsp;&nbsp;';
	echo $paginator->numbers();
	echo '&nbsp;&nbsp;';
	echo $paginator->next('»»»', null, null, array('class' => 'disabled'));

?>
<br/>
<!-- prints X of Y, where X is current page and Y is number of pages -->
<?php echo $paginator->counter(array('format' => 'Страница %page% из %pages%')); ?>
</center>
<script type="text/javascript">
					$(function() {
						$("#edit_server").dialog("destroy");

						$("#usernameSort").autocomplete({
								source: "/users/autoComplete/",
								minLength: 1,
							});

						$("#searchByServerIp").autocomplete({
								source: "/rootServerIps/autoComplete/",
								minLength: 1,
							});
					});
</script>
<script type="text/javascript">

	GetStatuses();
	setInterval(function(){GetStatuses();},60000);

</script>
<?php
			echo $js->writeBuffer(); // Write cached scripts
?>
    <div id="add_server" style="display:none;"   title="Пожалуйста, заполните форму заказа"></div>
	<!-- Контейнер для создания диалога редкатирования параметров сервера -->
	<div id="edit_server" style="display:none;" title="Редактировать сервер"></div>
	<!-- Контейнер для создания RCON-консоли -->
	<div id="server_rcon" style="display:none;" title="RCON консоль"></div>
	<!-- Контейнер для создания готовых RCON-команд -->
	<div id="server_auto_rcon" style="display:none" title="Выполнение команд на включенном сервере"></div>
	<!-- Контейнер для создания окна изменения параметров запуска -->
	<div id="server_start_params_container" style="display:none;" title="Изменить параметры запуска сервера"></div>
	<!-- Контейнер для создания окна изменения конфигов -->
	<div id="server_params_container" style="display:none;" title="Изменить настройки сервера"></div>
	<!-- Контейнер для создания окна установки плагинов -->
	<div id="plugin_install" style="display:none;" title="Установить плагины"></div>
	<!-- Контейнер для создания окна установки карт -->
	<div id="map_install" style="display:none;" title="Установить карты"></div>
	<!-- Контейнер для создания окна просмотра данных клиента -->
	<div id="client_view" style="display:none;" title="Данные клиента"></div>
</div>


