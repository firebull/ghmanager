<?php
/*
 * Created on 08.02.2011
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
 include('loading_params.php');
?>

<div id="add_new_promo_link">
	<?php
	//Ссылка для создания новой услуги
	echo $this->Html->link('+ Добавить новую промо-акцию', '#',
						array ('id'=>'promo_add_new', 'escape' => false
						,'onClick'=>"$('#add_promo').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 500});"

						));
	$effect = $this->Js->get('#add_promo')->effect('slideIn');
	$event  = $this->Js->request(array('controller'=>'Promos',
								 'action'=>'add'),
						   array('update' => '#add_promo',
								 'before'=>$effect.$loadingShow,
								 'complete'=>$loadingHide));

	$this->Js->get('#promo_add_new')->event('click', $event);
	?>
	<br/>
	<?php
	// Ссылка для добавления подарков
	echo $this->Html->link('+ Провести подарочную акцию', '#',
						array ('id'=>'gift_make_new', 'escape' => false
						,'onClick'=>"$('#gift_make').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 500});"

						));
	$effect = $this->Js->get('#gift_make')->effect('slideIn');
	$event  = $this->Js->request(array('controller'=>'Promos',
								 'action'=>'gift'),
						   array('update' => '#gift_make',
								 'before'=>$effect.$loadingShow,
								 'complete'=>$loadingHide));

	$this->Js->get('#gift_make_new')->event('click', $event);

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
		echo $this->Html->tag('td', $i++);
		echo $this->Html->tag('td', '#'.$promo['Promo']['id']);
		echo $this->Html->tag('td', $promoType[$promo['Promo']['type']]);
		echo $this->Html->tag('td', $promo['Promo']['description']);
		echo $this->Html->tag('td', $promo['Promo']['discount'].'%');
		echo $this->Html->tag('td', $this->Html->link('Показать', '#',
										  array ('id'=>'promo_view_'.$promo['Promo']['id'], 'escape' => false,
										  'onClick'=>"$('#view_promo_codes').dialog({modal: true,position: ['center',100], show: 'clip', hide: 'clip', width: 400});"

								)));
		$effect = $this->Js->get('#view_promo_codes')->effect('slideIn');
		$event  = $this->Js->request(array('controller'=>'Promos',
									 'action'=>'viewCodes',
									 $promo['Promo']['id']),
							   array('update' => '#view_promo_codes',
									 'before'=>$effect.$loadingShow,
									 'complete'=>$loadingHide));

		$this->Js->get('#promo_view_'.$promo['Promo']['id'])->event('click', $event);


		echo $this->Html->tag('td', $this->Time->niceShort($promo['Promo']['valid_through']));
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
