<?php
/*
 * Created on 08.02.2011
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
 include('../loading_params.php');
?>

<div id="add_new_promo_link">
	<?php
	//Ссылка для создания новой услуги
	echo $html->link('+ Добавить новую промо-акцию', '#',
						array ('id'=>'promo_add_new', 'escape' => false
						,'onClick'=>"$('#add_promo').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 500});"
						
						));
	$effect = $js->get('#add_promo')->effect('slideIn');		
	$event  = $js->request(array('controller'=>'Promos',
								 'action'=>'add'), 
						   array('update' => '#add_promo',	  
								 'before'=>$effect.$loadingShow,
								 'complete'=>$loadingHide));

	$js->get('#promo_add_new')->event('click', $event);
	?>
	<br/>
	<?php
	// Ссылка для добавления подарков
	echo $html->link('+ Провести подарочную акцию', '#',
						array ('id'=>'gift_make_new', 'escape' => false
						,'onClick'=>"$('#gift_make').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 500});"
						
						));
	$effect = $js->get('#gift_make')->effect('slideIn');		
	$event  = $js->request(array('controller'=>'Promos',
								 'action'=>'gift'), 
						   array('update' => '#gift_make',	  
								 'before'=>$effect.$loadingShow,
								 'complete'=>$loadingHide));

	$js->get('#gift_make_new')->event('click', $event);

	?>
</div>
<table class="intext">
	<tr>
		<th>№</th>
		<th>ID</th>
		<th>Тип</th>
		<th>Описание</th>
		<th>Скидка</th>
		<th>Коды</th>		
		<th>Окончание</th>
	</tr>
<?php
	$i = 1;
	
	$promoType = array('code'  => 'Многоразовый код',
					   'token' => 'Одноразовые коды');
	 
	foreach ( $this->data as $promo ) {
?>
<tr>
<?php		
		echo $html->tag('td', $i++);
		echo $html->tag('td', '#'.$promo['Promo']['id']);
		echo $html->tag('td', $promoType[$promo['Promo']['type']]);
		echo $html->tag('td', $promo['Promo']['description']);
		echo $html->tag('td', $promo['Promo']['discount'].'%');
		echo $html->tag('td', $html->link('Показать', '#',
										  array ('id'=>'promo_view_'.$promo['Promo']['id'], 'escape' => false,
										  'onClick'=>"$('#view_promo_codes').dialog({modal: true,position: ['center',100], show: 'clip', hide: 'clip', width: 400});"
											
								)));
		$effect = $js->get('#view_promo_codes')->effect('slideIn');		
		$event  = $js->request(array('controller'=>'Promos',
									 'action'=>'viewCodes',
									 $promo['Promo']['id']), 
							   array('update' => '#view_promo_codes',	  
									 'before'=>$effect.$loadingShow,
									 'complete'=>$loadingHide));
	
		$js->get('#promo_view_'.$promo['Promo']['id'])->event('click', $event);
		
		
		echo $html->tag('td', $time->niceShort($promo['Promo']['valid_through']));
?>
</tr>
<?php       
	}

?>
</table>

<div id="add_promo" style="display:none;"   title="Добавить новую промо-акцию"></div>
<!-- Контейнер для создания диалога редактирования параметров промо -->
<div id="edit_promo" style="display:none;" title="Редактировать промо-акцию"></div>
<!-- Контейнер для создания диалога просмотра кодов промо -->
<div id="view_promo_codes" style="display:none;" title="Коды промо-акции"></div>
<!-- Контейнер для создания диалога подарков -->
<div id="gift_make" style="display:none;" title="Провести подарочную акцию"></div>