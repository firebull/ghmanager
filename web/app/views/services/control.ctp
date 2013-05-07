<?php
/*
 * Created on 19.01.2011
 *
 * Made fot project TeamServer
 * by bulaev
 */
 
 include('../loading_params.php');
?>

<div id="add_service_about">
	<?php
	//Ссылка для создания новой услуги
	echo $html->link('+ Добавить новую услугу', '#',
						array ('id'=>'service_add_new', 'escape' => false
						,'onClick'=>"$('#add_service').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 500});"
						
						));
	$effect = $js->get('#add_service')->effect('slideIn');		
	$event  = $js->request(array('controller'=>'Services',
								 'action'=>'add'), 
						   array('update' => '#add_service',	  
								 'before'=>$effect.$loadingShow,
								 'complete'=>$loadingHide));

	$js->get('#service_add_new')->event('click', $event);

	?>
</div>

<div class="services list">

	<h2>Шаблоны услуг</h2>
	<table cellpadding="0" cellspacing="0" class="intext">
	<tr>
			<th style="width: 120px;"></th>
			<th><?php echo $this->Paginator->sort('#','id');?></th>
			<th style="width: 120px;"><?php echo $this->Paginator->sort('Кратко','name');?></th>
			<th style="width: 150px;"><?php echo $this->Paginator->sort('Полное название','longname');?></th>
			<th><?php echo $this->Paginator->sort('Описание','description');?></th>
			<th style="width: 120px;"><?php echo $this->Paginator->sort('Цена','price');?></th>			
	</tr>
	<?php
	foreach ($services as $service):
	$id = $service['Service']['id'];
	?>
	<tr>
	    <td>
	    <div class="icons">
			<ul id="icons_<?php echo $id;?>" class="ui-widget ui-helper-clearfix">
			<li class="ui-state-default ui-corner-all" title="Изменить параметры сервера">
			<?php
			//Иконка для редактирования параметров услуги
			echo $html->link('<span class="ui-icon ui-icon-wrench"></span>', '#',
								array ('id'=>'service_edit_'.$id, 'escape' => false,
								'onClick'=>"$('#edit_service').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});"));
			$effect = $js->get('#edit_service')->effect('slideIn');		
			$event  = $js->request(array('controller'=>'Services',
										 'action'=>'edit', $id), 
								   array('update' => '#edit_service',	  
										 'before'=>$loadingShow,
										 'complete'=>$loadingHide,
										 'buffer'=>false));

			$js->get('#service_edit_'.$id)->event('click', $event);
			?>
			</li>
			<li class="ui-state-default ui-corner-all" title="Удалить сервер">
			<?php
			//Иконка для удаления услуги
			$confiremDeleteMessage = 'Вы уверены, что хотите удалить услугу '.$service['Service']['longname'].' #'.$id.' ?'.
									 "\n<br/><br/>Это необратимая операция!" ;
			echo $html->link('<span class="ui-icon ui-icon-trash"></span>', '#',
									array ('id'=>'service_delete_'.$id, 'escape' => false
									,'onClick'=>"$('#delete_confirm_".$id."').dialog({
																						resizable: false,
																						height:300,
																						width: 400,
																						modal: true,
																						buttons: {

																								'Удалить услугу': function() {
																								window.location.href='/services/delete/$id';
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
			</li>
			</ul>
	    
	    
	    </td>
		<td><?php echo $service['Service']['id']; ?>&nbsp;</td>
		<td><?php echo $service['Service']['name']; ?>&nbsp;</td>
		<td><?php echo $service['Service']['longname']; ?>&nbsp;</td>
		<td><?php echo $service['Service']['description']; ?>&nbsp;</td>
		<td><?php echo $service['Service']['price']." руб./мес."; ?>&nbsp;</td>
	</tr>
<?php endforeach; ?>
	</table>
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
</div>
<?php 
			echo $js->writeBuffer(); // Write cached scripts 
?>
    <div id="add_service" style="display:none;"   title="Добавить новую услугу"></div>
	<!-- Контейнер для создания диалога редактирования параметров услуги -->
	<div id="edit_service" style="display:none;" title="Редактировать услугу"></div>