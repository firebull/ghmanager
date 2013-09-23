<?php
/*
 * Created on 22.05.2010
 *
 * Made fot project TeamServer
 * by bulaev
 */
  include('../loading_params.php');
  $sourceTvEnable = array(
  							'tf',
  							'css',
  							'cssv34',
  							'hl2mp',
  							'dods'
  							
  							);
  $gotvEnable = array( 'csgo',
  					   'csgo-t128'
  						);

  $hltvEnable = array(
  							'cs16',
  							'cs16-old',
  							'dmc',
  							'hl1'							
  							);
?>
<script type="text/javascript">	

		function GetStatuses () {
			  
			    $.getJSON('../servers/getStatus',
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
							$(td).attr('title','Сервер еще включается, либо запущен с параметром "sv_cheats 1", либо в процессе запуска возникла ошибка. Смотрите подробный статус и читайте логи.');
						}
					}

					$(tr).attr('onMouseover','this.className="'+style+'"')
					$(tr).attr('onMouseout','this.className="tbl_out"')
					
					
				});
			
			}																	
</script>
<div id="servers_list">
	<a href="#" class="btn btn-primary" id="opener" <?php 
			if ( empty($serversGrouped) ){
				echo 'style="display: none;';	
			}
	
		?>><i class="icon-plus icon-white"></i> Заказать сервер</a>
	<a href="#" class="btn btn-primary" id="news_opener" <?php 
			if ( empty($serversGrouped) ){
				echo 'style="display: none;';	
			}
	
		?>><i class="icon-info-sign icon-white"></i> Новости хостинга</a>
	<div id="news_loading" style="display: none;"><?php 
	echo $html->image('loading.gif', array('alt' => 'Загрузка новостей...', 
										   'width'  =>'16', 
                                           'height' => '16',
                                           'style'  => 'margin: 10px;'));
	?>
	</div>
<div class="ui-widget">
	<div> 
		<div id="links" class="ui-state-highlight ui-corner-all" <?php 
			if ( empty($serversGrouped) ){
				echo 'style="margin-top: 15px; padding: 0 .7em; display: block;"';	
			}
			else
			{
				echo 'style="margin-top: 15px; padding: 0 .7em; display: none;"';
			}
			?>>
			
			<table class="order">
				<tr>
					<td colspan="4" align="center">
						<h3>Заказать:</h3>
					</td>
				</tr>

				<tr>
					<td><?php
							//Ссылка  для заказа игрового сервера
							echo $html->link($html->image('bigicons/ico-game.png', 
															array('alt'=>'Заказать игровой сервер', 'title'=>'Заказать игровой сервер', 'width'=>'90', 'height'=>'90')), '#',
												array ('id'=>'game_server_add_new', 'escape' => false
												,'onClick'=>"$('#add_server').dialog({modal: true,position: ['center',100], show: 'clip', hide: 'clip', width: 620, minWidth: 620});"
												
												));
							$effect = $js->get('#add_server')->effect('slideIn');		
							$event  = $js->request(array('controller'=>'Orders',
														 'action'=>'add',1), 
												   array('update' => '#add_server',	  
														 'before'=>$effect.$loadingShow,
														 'complete'=>$loadingHide));
					
							$js->get('#game_server_add_new')->event('click', $event);
					
							?>
					
					</td>
					<td><?php
							//Ссылка  для заказа голосового сервера
							echo $html->link($html->image('bigicons/ico-voice.png', 
															array('alt'=>'Заказать госовой сервер', 'title'=>'Заказать госовой сервер', 'width'=>'90', 'height'=>'90')), '#',
												array ('id'=>'voice_server_add_new', 'escape' => false
												,'onClick'=>"$('#add_server').dialog({modal: true,position: ['center',100], show: 'clip', hide: 'clip', width: 620, minWidth: 620});"
												
												));
							$effect = $js->get('#add_server')->effect('slideIn');		
							$event  = $js->request(array('controller'=>'Orders',
														 'action'=>'add',2), 
												   array('update' => '#add_server',	  
														 'before'=>$effect.$loadingShow,
														 'complete'=>$loadingHide));
					
							$js->get('#voice_server_add_new')->event('click', $event);
					
							?>
					</td>
					<td>
					<?php
							//Ссылка  для заказа сервера EAC
							echo $html->link($html->image('bigicons/ico-eac.png', 
															array('alt'=>'Заказать сервер EAC', 'title'=>'Заказать сервер EAC', 'width'=>'90', 'height'=>'90')), '#',
												array ('id'=>'eac_server_add_new', 'escape' => false
												,'onClick'=>"$('#add_server').dialog({modal: true,position: ['center',100], show: 'clip', hide: 'clip', width: 620, minWidth: 620});"
												
												));
							$effect = $js->get('#add_server')->effect('slideIn');		
							$event  = $js->request(array('controller'=>'Orders',
														 'action'=>'addEac',8), 
												   array('update' => '#add_server',	  
														 'before'=>$effect.$loadingShow,
														 'complete'=>$loadingHide));
					
							$js->get('#eac_server_add_new')->event('click', $event);
					
					?>
					
					</td>
					
				</tr>
				<tr>
					<td align="center">Игровой сервер</td>
					<td align="center">Голосовой сервер</td>
					<td align="center">Сервер EAC</td>					
				</tr>
			</table>
		</div>			
	</div>
	
	<?php
	if (@$newOrderTemplate){
		$event  = $js->request(array('controller'=>'Orders',
									 'action'=>'add',$newOrderTemplate['Type'][0]['id'],$newOrderTemplate['GameTemplateType']['id']), 
							   array('update' => '#add_server',	  
									 'before'=>$effect.$loadingShow,
									 'complete'=>$loadingHide));
	?>
		<script type="text/javascript">
		$(function() {
			function openNewOrder() {
				
				$('#add_server').dialog({
										width: 620, 
										minWidth: 620,
										height: 770,
										modal: true,
										position: ['center',50]
									});	
				<?php echo $event; ?>
				
				}
				
			openNewOrder();
		});
		</script>
	<?php	
	}
	?>
</div>

<?php $js->get('#news_opener')->event('click', 'newsShow();'); ?>
<div id="news"></div>
<script type="text/javascript">
<?php 
	$newsInitLoader  = $js->request(array('controller' => 'Messages',
												 'action'=>'init'), 
										   array('update' => '#news'));
	$newsLoader      = $js->request(array('controller' => 'Messages',
												 'action'=>'index'), 
										   array('update' => '#news'));
	echo $newsInitLoader;
?>
</script>
<?php 
/*
 * Тут выводим подряд на одну страницу несколько таблиц для каждого типа серверов.
 * 
 * Перебираем входной массив, который уже отсортирован по типам. 
 * С помощью switch определяем заголовки, дальше перебором выводим серверы.
 * 
 * Алгоритм дурацкий, честно говоря... А главное, совершенно ненаглядный.
 * Попытался максимально закомментить и разделить на блоки
 * Надо переделать как-нить по-человечески.
 * 
 */
// Обнулить переменные
$lastType = '';
$i=1;
$now=$time->fromString('now');
if ( !empty($serversGrouped) ) { // Если есть список
	
	foreach ( $serversGrouped as $type => $servers ) {
	       switch ($type) {
	/***********************************************************************************
	 * Игровые серверы заголовок - начало
	 ***********************************************************************************/ 
	       		case 'Game':
					?>
				<div class="list_border">
					<h2>Мои игровые серверы:</h2>
					
					<table class="intext"  border="0" cellpadding="0" cellspacing="0">
						<tr>
							
							<th></th>
							<th>ID</th>
							<th style="width: 30px;"></th>
							<th style="width: 130px;">Сервер</th>
							<th style="min-width: 255px !important">Настройка</th>
							<th>Слотов</th>
							<th width="130">Адрес</th>
							<th width="140">Оплачено до</th>
						</tr>
					<?php
					?>
					
					
	<?php       
	       			break;
	/***********************************************************************************
	 * Игровые серверы заголовок - конец
	 ***********************************************************************************/
	 /***********************************************************************************
	 * Радио серверы  заголовок - начало
	 ***********************************************************************************/     			
	       		case 'Eac':
					?>
					
				<div class="list_border">
					<h2>Мои серверы EAC:</h2>
					
					<table class="intext" border="0" cellpadding="0" cellspacing="0">
						<tr>
							
							<th></th>
							<th>ID</th>
							<th></th>
							<th></th>
							<th width="120">Настройка</th>
							<th></th>
							<th>Подключенный сервер</th>
							<th width="140">Оплачено до</th>
						</tr>
					<?php
					?>
					
	<?php       
	       			break;
	/***********************************************************************************
	 * Радио серверы заголовок - конец
	 ***********************************************************************************/
	/***********************************************************************************
	 * Голосовые серверы  заголовок - начало
	 ***********************************************************************************/     			
	       		case 'Voice':
					?>
					
				<div class="list_border">
					<h2>Мои голосовые серверы:</h2>
					
					<table class="intext" border="0" cellpadding="0" cellspacing="0">
						<tr>
							
							<th></th>
							<th>ID</th>
							<th></th>
							<th>Сервер</th>
							<th width="120">Настройка</th>
							<th>Слотов</th>
							<th>Адрес</th>
							<th  width="140">Оплачено до</th>
						</tr>
					<?php
					?>
					
	<?php       
	       			break;
	/***********************************************************************************
	 * Голосовые серверы заголовок - конец
	 ***********************************************************************************/         
	       } // switch
	
	/*****************************
	 * Теперь выводим сами серверы 
	 *****************************/
	
	foreach ( $servers as $server ) {		
			if ($server['Server']['action'] == 'delete')
	//Если сервер установлен на удаление:
	//**********************************************************************************
			{
			?>
		<tr id="opener_<?php echo $server['Server']['id']; ?>" class="tbl_out_hide" onmouseout="this.className='tbl_out_hide'" onmouseover="this.className='tbl_hover_red'">
			<td class="status"><div class="processing" title="Сервер будет удалён в ближайшее время"></div></td>
			<td class="id"><?php echo "#".$server['Server']['id']; ?></td>
			<td  class="left"><?php echo $html->image('icons/servers/'.$server['GameTemplate'][0]['name'].'.png', 
														array('alt'=>$server['GameTemplate'][0]['longname'], 'width'=>'24', 'height'=>'24')); ?></td>
			<td class="left">
				<div class="cuttext">
					<?php 
						if (empty($server['Server']['name'])){
							echo $server['GameTemplate'][0]['longname']; 
						}
						else
						{
							echo $server['Server']['name'];
						}
					?>
				</div>
			</td>
			<td><?php echo $server['Server']['slots']; ?></td>
			<td>
				<?php echo $server['Server']['address'].":".$server['Server']['port']; ?>
			</td>
			<td colspan="2">
			Сервер отмечен для удаления
			</td>
			
			<?php
			}
			else if (($now < $time->fromString($server['Server']['payedTill'])) && ($server['Server']['initialised'] == 1 ))
		//Если сервер оплачен и инициализирован:
		//**********************************************************************************
			{
				
				
			?>
		<tr class="tbl_out" id="server_string_<?php echo $server['Server']['id']; ?>">
			<td class="status">
				<?php if ($type == 'Game' or $type == 'Voice') {?>
				<div id='server_status_<?php echo $server['Server']['id']; ?>' class="stoped" title="Сервер выключен"></div>
				<?php } ?>
			</td>
			<td class="id"><?php echo "#".$server['Server']['id']; ?></td>
			<td width="30px">
			<?php echo $html->image('icons/servers/'.$server['GameTemplate'][0]['name'].'.png', 
														array('alt'=>$server['GameTemplate'][0]['longname'], 
														'title'=>$server['GameTemplate'][0]['longname'],
														'style' => 'width: 24px !important;'.
																   'height: 24px !important;'.
																   'max-width: 24px !important;'
															)); 
														?>
			</td>
			<td class="left">
				<div class="cuttext" style="max-width: 125px;">
					<?php 
						if (empty($server['Server']['name'])){
							$serverName = $server['GameTemplate'][0]['longname']; 
						}
						else
						{
							$serverName = $server['Server']['name'];
						}

						// Ссылка для смены имени сервера
						echo $html->link($serverName, '#',
													array ( 'id' => 'server_name_'.$server['Server']['id'], 
															'title' => 'Изменить имя сервера, отображаемое в панели',
															'escape' => false,
													'onClick'=>"$('#name_server').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 400});"));
						$effect = $js->get('#name_server')->effect('slideIn');		
						$event  = $js->request(array('controller'=>'Servers',
													 'action'=>'changeName', $server['Server']['id']), 
											   array('update' => '#name_server',	  
													 'before'=>$loadingShow,
													 'complete'=>$loadingHide));
			
						$js->get('#server_name_'.$server['Server']['id'])->event('click', $event);
			
					
					?>
				</div>
			</td>
			<td align="center"><?php 

			if (in_array($server['GameTemplate'][0]['id'], array(37,38)))
			{
				echo $this->element('icons_client_eac', array(
																	'id'   => $server['Server']['id'],
																	'name' => $server['GameTemplate'][0]['name'],
																	'longname' => $server['GameTemplate'][0]['longname'],
																	'viewLink' => 'true',
																	'initialised' => true,
																	'type' => strtolower($type),
																	'serverType' => $server['Type'][0]['name'],
																	'state' => @$eacStatus[$server['Server']['id']]
																	 ));
			}
			else
			{
				echo $this->element('icons_client_server', array(
																	'id'   => $server['Server']['id'],
																	'name' => $server['GameTemplate'][0]['name'],
																	'longname' => $server['GameTemplate'][0]['longname'],
																	'viewLink' => 'true',
																	'initialised' => true,
																	'type' => strtolower($type),
																	'serverType' => $server['Type'][0]['name']
																	 ));
			}
			
			?></td>
			
			<td><?php 

					if ($server['Type'][0]['name'] != 'eac')
					{
						echo $server['Server']['slots']; 
					}

			?></td>
			<td>
				<?php 
					
					if (!empty($server['Server']['address']))
					{
						echo $server['Server']['address'].":".$server['Server']['port']; 
						if ( in_array($server['GameTemplate'][0]['name'], $sourceTvEnable) )
						{
							echo '<br/>Порт STV: '.(intval($server['Server']['port']) + 1015);
						}
						else
						if ( in_array($server['GameTemplate'][0]['name'], $gotvEnable) )
						{
							echo '<br/>Порт GOTV: '.(intval($server['Server']['port']) + 1015);
						}
						else
						if ( in_array($server['GameTemplate'][0]['name'], $hltvEnable) )
						{
							echo '<br/>Порт HLTV: '.(intval($server['Server']['port']) + 1015);
						}
					}
					else
					{
						echo "-";
					}
						
						
				?>
			</td>
			<td><?php 
				
				if ($server['Server']['scaleTime'] > 0.15 and $server['Server']['scaleTime'] < 0.3){
					$scaleColor = '#DD1500';
					$dateColor = '#000';
					$scaleTitle = 'До окончания срока аренды осталось '.round($server['Server']['scaleTime']*30, 0).' дней';				
				}
				else
				if ($server['Server']['scaleTime'] >= 0 and $server['Server']['scaleTime'] <= 0.15){				
					$scaleColor = '#970405';
					$dateColor = '#DD1500';	
					
					if (($time->toUnix($server['Server']['payedTill']) - time()) < 86400){
						$scaleTitle = 'До окончания срока аренды осталось менее одного дня!';
					}
					else
					{
						$scaleTitle = 'До окончания срока аренды осталось менее недели.';
					}
					
				}
				else
				{
					$scaleColor = '#bbb';
					$dateColor = '#000';
					if ($server['Server']['scaleTime'] == 1){
						$scaleTitle = 'До окончания срока аренды более месяца.';
					}
					else
					{
						$scaleTitle = 'До окончания срока аренды осталось '.round($server['Server']['scaleTime']*30, 0).' дней';
					}
				}
				
				echo $html->tag('small', $this->Common->niceDate($server['Server']['payedTill']),
										array ( 'style' => 'color: '.$dateColor.';'));
				echo "<br/>";
				if (!empty($server['Server']['giftDays'])
						and
					strtotime($server['Server']['giftExpires']) > time())
					{
						
						echo $html->link( 'Из них '.$server['Server']['giftDays']." в подарок!", 'http://forum.teamserver.ru/index.php?topic=75.0',
										   array( 'style' => 'font-size: smaller;',
										   		  'target' => '_blank'));
												 
					}
			?>
			<div style="width: 130px; height: 6px; padding: 0px; border: 1px solid #777;">
			<?php
				// Нарисовать линейку - сколько осталось
				
				echo $html->tag('div', '', array( 'style' => 'background-color: '.$scaleColor.'; ' .
															 'width: '.($server['Server']['scaleTime']*100).'%;' .
															 'height: 6px;',
												  'title' => $scaleTitle));
			
			?>
			</div></td>
		</tr>
		<tr id="opener_<?php echo $server['Server']['id']; ?>">
			<td colspan="8" class="server_more">
				<div id="server_more_<?php echo $server['Server']['id']; ?>" style="display:none; text-align:center; width:100%;">
				Загрузка текущей информации...

				<a href="#" onclick="$('#server_more_<?php echo $server['Server']['id']; ?>').hide(); return false;">Скрыть</a>
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
		<tr id="opener_<?php echo $server['Server']['id']; ?>" style="border-bottom: 2px solid grey;" class="tbl_out double_border" onmouseout="this.className='tbl_out'" onmouseover="this.className='tbl_hover_yellow'">
			<td class="status"><div class="processing" title="Ожидание инициализации"></div></td>
			<td class="id"><?php echo "#".$server['Server']['id']; ?></td>
			<td>
			<?php echo $html->image('icons/servers/'.$server['GameTemplate'][0]['name'].'.png', 
														array('alt'=>$server['GameTemplate'][0]['longname'], 'width'=>'24', 'height'=>'24')); ?>
			</td>
			<td class="left">
				<?php 
					if (empty($server['Server']['name'])){
						$serverName = $server['GameTemplate'][0]['longname']; 
					}
					else
					{
						$serverName = $server['Server']['name'];
					}
				?>
				
				<div class="cuttext" style="width: 160px;" title="<?php echo $serverName; ?>">
					<?php echo $serverName; ?>
				</div>
			</td>
			<td><?php echo $this->element('icons_client_server', array(
																	'id'=>$server['Server']['id'],
																	'initialised' => false
																	 )); ?></td>			
			<td><?php echo $server['Server']['slots']; ?></td>
			<td class="center">
				Ожидание инициализации
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
			<?php echo $html->image('icons/servers/'.$server['GameTemplate'][0]['name'].'.png', 
														array('alt'=>$server['GameTemplate'][0]['longname'], 'width'=>'24', 'height'=>'24')); ?>
			</td>
			<td class="left">
				
				<?php 
					if (empty($server['Server']['name'])){
						$serverName = $server['GameTemplate'][0]['longname']; 
					}
					else
					{
						$serverName = $server['Server']['name'];
					}
				?>

				<div class="cuttext" style="width: 160px;" title="<?php echo $serverName; ?>">
					<?php echo $serverName; ?>
				</div>
			</td>
			<td><?php echo $this->element('icons_client_server', array(
																	'id'=>$server['Server']['id'],
																	'initialised' => true
																	 )); ?></td>			
			<td><?php echo $server['Server']['slots']; ?></td>
			<td class="center">
				<?php 
					
					echo $server['Server']['address'].":".$server['Server']['port']; 
					if ( in_array($server['GameTemplate'][0]['name'], $sourceTvEnable) ){
							echo '<br/>Порт STV: '.(intval($server['Server']['port']) + 1015);
					}
					else
					if ( in_array($server['GameTemplate'][0]['name'], $hltvEnable) ){
							echo '<br/>Порт HLTV: '.(intval($server['Server']['port']) + 1015);
						}
						
						
				?>	
			</td>
			<td>Ожидание оплаты</td>
			
		</tr>
			<?php
			}
			else
		//Если сервер не оплачен, и не инициализирован:
		//**********************************************************************************
			{
			?>
		<tr id="opener_<?php echo $server['Server']['id']; ?>" style="border-bottom: 2px solid grey;" class="tbl_out double_border" onmouseout="this.className='tbl_out double_border'" onmouseover="this.className='tbl_hover_red double_border'">
			<td class="status"><div class="warning"  title="Ожидание оплаты"></div></td>
			<td class="id"><?php echo "#".$server['Server']['id']; ?></td>
			<td>
			<?php echo $html->image('icons/servers/'.$server['GameTemplate'][0]['name'].'.png', 
														array('alt'=>$server['GameTemplate'][0]['longname'], 'width'=>'24', 'height'=>'24')); ?>
			</td>
			<td class="left">
				<?php 
					if (empty($server['Server']['name'])){
						$serverName = $server['GameTemplate'][0]['longname']; 
					}
					else
					{
						$serverName = $server['Server']['name'];
					}
				?>
				
				<div class="cuttext" style="width: 160px;" title="<?php echo $serverName; ?>">
					<?php echo $serverName; ?>
				</div>
			</td>
			<td><?php echo $this->element('icons_client_server', array(
																	'id'=>$server['Server']['id'],
																	'initialised' => false
																	 )); ?></td>
			<td><?php echo $server['Server']['slots']; ?></td>
			<td></td>
			<td>
			Ожидание оплаты
			</td>
		</tr>	
			<?php
			}
			    
	}
		
	// Закрываем таблицу
	?>
					</table>
				</div>
	<?php
	
	}// foreach для Типов

} // ^^^^^ Если есть список, начало сверху

if (@$paginate === true){
?>
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
<?php
}

	// Журнал событий
	if (!empty($iptablesLog))
	{
?>
<div class="list_border">
	<h2>Журнал <?php echo count($iptablesLog); ?> последних предположительных атак</h2>

    <table class="intext" style="background-color: white;">
        <tr>
            <th>Время</th>
            <th>Источник</th>
            <th>Назначение</th>
            <th>Тип атаки</th>
        </tr>
    <?php

        arsort($iptablesLog);
        $i = 0;
        foreach ($iptablesLog as $log) {
            if (!empty($log))
            {
                echo '<tr>';
                echo $html->tag('td', $this->Common->niceDate($log[0], 'full', 'unix'));

                echo '<td>';
                //Ссылка  для просмотра журнала атакуещего IP
                echo $html->link($log[1], '#',
                                    array ('id'=>'view_attacker_'.$i, 'escape' => false
                                    ,'onClick'=>"$('#view_more').dialog({modal: true,position: ['center',50], show: 'clip', hide: 'clip', width: 900});"
                                    
                                    ));
                $effect = $js->get('#view_more')->effect('slideIn');     
                $event  = $js->request(array('controller'=>'stats',
                                             'action'=>'getAttackerIpInfo', $log[1]), 
                                       array('update' => '#view_more',     
                                             'before'=>'$("#view_more").empty();'.$effect.$loadingShow,
                                             'complete'=>$loadingHide));
        
                $js->get('#view_attacker_'.$i)->event('click', $event);

                echo '</td>';
                echo '<td>';
                //Ссылка  для просмотра журнала атакуемого IP
                $dst = preg_split('/\:/', $log[2]);

                echo $html->link($log[2], '#',
                                    array ('id'=>'view_dst_'.$i, 'escape' => false
                                    ,'onClick'=>"$('#view_more').dialog({modal: true,position: ['center',50], show: 'clip', hide: 'clip', width: 900});"
                                    
                                    ));
                $effect = $js->get('#view_more')->effect('slideIn');     
                $event  = $js->request(array('controller'=>'stats',
                                             'action'=>'getDestinationIpInfo', $dst[0], $dst[1]), 
                                       array('update' => '#view_more',     
                                             'before'=>'$("#view_more").empty();'.$effect.$loadingShow,
                                             'complete'=>$loadingHide));
        
                $js->get('#view_dst_'.$i)->event('click', $event);

                echo '</td>';

                echo $html->tag('td', $log[3]);
                echo '</tr>';
            }
            
            $i++;
        }
    ?>
    </table>
</div>
<div id="view_more" title="Подробная информация об IP" style="display: none;"></div>
<?php

	}

// Журнал событий
if (!empty($journal)){

$statusNice = array( 'ok'    => 'OK',
					 'warn'  => 'Внимание',
					 'error' => 'Ошибка');

$userNice = array(  'user'    => 'Клиент',
					'userOut' => 'По токену',
					'admin'   => 'Администратор',
					'script'  => 'Скрипт');

?>	

<div class="list_border">
	<h2>Журнал событий:</h2>
	
	<table class="intext"  border="0" cellpadding="0" cellspacing="0">
		<tr>
			<th>ID</th>
			<th style='min-width: 400px;'>Описание</th>
			<th>Инициатор</th>
			<th>IP</th>
			<th>Дата и время</th>
			<th>Статус</th>
		</tr>
<?php

	foreach ($journal as $journalAction) {
?>
		<tr  <?php 
			
			if ($journalAction['status'] == 'ok')
			{
				echo 'style="background-color: #fff;"';
			}
			else
			if ($journalAction['status'] == 'warn')
			{
				echo 'style="background-color: #ffc;"';
			}
			else 
			{
				echo 'style="background-color: #fcc;"';
			}

		?>>
			<td>
				<?php 
					
					$format= '#%1$08d';
					printf($format, $journalAction['id']);

				?>
			</td>
			<td style="text-align: left;"><?php echo $journalAction['action']; ?></td>
			<td><?php echo $html->tag('small', @$userNice[$journalAction['creator']]); ?></td>
			<td>
				<?php 

					if ($journalAction['creator'] == 'user'){
						echo $journalAction['ip'];
					}
				?>
			</td>
			<td><?php echo $this->Common->niceDate($journalAction['created']);?></td>
			<td><?php echo @$statusNice[$journalAction['status']]; ?></td>
		</tr>
<?php
	}

?>
	</table>
</div>

<?php
}

?>

	<div id="add_server" style="display:none;"  title="Пожалуйста, заполните форму заказа"></div>
	<div id="prolongate_server" style="display:none;"  title="Пожалуйста, заполните форму заказа"></div>
	<div id="name_server" style="display:none;"  title="Смена имени сервера"></div>
	<!-- Контейнер для создания RCON-консоли -->
	<div id="server_rcon" style="display:none" title="RCON консоль"></div>
	<!-- Контейнер для создания готовых RCON-команд -->
	<div id="server_auto_rcon" style="display:none" title="Выполнение команд на включенном сервере"></div>
	<!-- Контейнер для создания окна установки плагинов -->
	<div id="plugin_install" style="display:none" title="Установить моды и плагины"></div>
	<!-- Контейнер для создания окна установки карт -->
	<div id="map_install" style="display:none" title="Установить карты"></div>
	<!-- Контейнер для создания окна изменения параметров запуска -->
	<div id="server_start_params_container" style="display:none" title="Изменить параметры запуска сервера"></div>
	<!-- Контейнер для создания окна изменения конфигов -->
	<div id="server_params_container" style="display:none" title="Изменить настройки сервера"></div>
	<!-- Контейнер для создания окна параметров доступа к серверу -->
	<div id="access_info" style="display:none" title="Доступ к серверу"></div>
	<div id="common_result" style="display:none" title="Результат выполнения"></div>
	</div>
</div>

<script type="text/javascript" language="javascript">
		$(function() {
			$('#opener').click(
				
				function() {
				  
					$('#links').show('clip');
					$('#opener').attr('disabled','disabled');

				}
			
			);

			$('#opener_close').click(
				
				function() {
				  
					$('#links').hide('clip');
					$('#opener').removeAttr('disabled');

				}
			
			);				
		
		});
		
		function newsHide(){
			$('#news').hide('blind');			
			$('#news_opener').removeAttr('disabled');			
		}
		
		function newsShow(){
			$('#news').html('<?php echo $html->image('loading.gif', array('alt'=>'Загрузка новостей...', 'width'=>'16', 'height'=>'16')); ?>');
			<?php echo $newsLoader; ?>
			$('#news_opener').attr('disabled','disabled');			
			$('#news').show('blind');			
		}
		
</script>
<script type="text/javascript">	

	GetStatuses();
	setInterval(function(){GetStatuses();},300000);

	function setButtonEac(serverId)
	{
		if ( $('#eac_switch_button_' + serverId).hasClass('ui-icon-stop'))
		{
			$('#eac_switch_button_' + serverId).removeClass('ui-icon-stop');
			$('#eac_switch_button_' + serverId).addClass('ui-icon-play');
			$('#eac_switch_button_' + serverId).attr('title', 'Включить EAC');
		}
		else
		if ( $('#eac_switch_button_' + serverId).hasClass('ui-icon-play'))
		{
			$('#eac_switch_button_' + serverId).removeClass('ui-icon-play');
			$('#eac_switch_button_' + serverId).addClass('ui-icon-stop');
			$('#eac_switch_button_' + serverId).attr('title', 'Отключить EAC');
		}
	}
											
</script>
<?php 
			echo $js->writeBuffer(); // Write cached scripts 
?>
