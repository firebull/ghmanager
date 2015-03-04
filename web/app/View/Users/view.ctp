<?php
/*
 * Created on 04.06.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
 //pr($userinfo);
 include('loading_params.php');

?>
<div id="flash"><?php echo $this->Session->flash(); ?></div>
<span class="highlight3" style="font-size: 14px; font-weight: bold;">
<?php
	echo $this->data['User']['username'] ;
	if (!empty($this->data['User']['steam_id'])){
		echo ' ('.$this->data['User']['steam_id'].')';
	}
?>
</span>
<?php
	if (!empty($this->data['User']['first_name']) or !empty($this->data['User']['second_name'])){
		echo '<br/>';
		if (!empty($this->data['User']['first_name'])){
			echo $this->data['User']['first_name']." ";
		}

		echo $this->data['User']['second_name'];

	}
?>
<br/>
<?php
if (!empty($this->data['User']['ftppassword'])){
		echo '<br/> Пароль FTP: '.$this->data['User']['ftppassword'];
	}
?>
<br/>
<?php
if (!empty($this->data['User']['guid'])){
		echo ' GUID: '.$this->data['User']['guid'];
	}
?>
<br/><br/>
<?php

	echo $this->Text->autoLinkEmails($this->data['User']['email']);

	if (!empty($this->data['User']['email_old']))
	{
		echo '<br/>'.$this->Text->autoLinkEmails($this->data['User']['email_old']).' (старый)';
	}

	if (!empty($this->data['User']['phone']))
	{
		echo '<br/><br/>'.$this->Common->parsePhoneNum($this->data['User']['phone']);
	}

	if (!empty($this->data['User']['phone_old']))
	{
		echo '<br/>'.$this->Common->parsePhoneNum($this->data['User']['phone_old']).' (старый)';
	}

?>
<br/>
<?php
if (!empty($this->data['User']['ftppassword'])){
		echo '<br/> Баланс: '.floatval($this->data['User']['money']).' руб.';
	}
?>
<br/>
<?php

	if ($this->data['User']['money'] > 0)
	{
		echo $this->Html->link('Обнулить баланс', '#',
					array ( 'id'=>'user_balance_empty_'.$this->data['User']['id'],
							'escape' => false,
							'class' => 'btn btn-primary',
							'title' => 'Нажмите, если перевели весь остаток клиенту.' ));

		$effect = $this->Js->get('#client_view')->effect('slideIn');
		$event  = $this->Js->request(array('controller'=>'Orders',
									 'action'=>'balanceEmpty', $this->data['User']['id']),
							   array('update' => '#client_view',
									 'before'=>$loadingShow,
									 'complete'=>$loadingHide.";$('#client_view').dialog({modal: true, position: ['center',180], width: 300});",
									 'buffer'=>false));

		$this->Js->get('#user_balance_empty_'.$this->data['User']['id'])->event('click', $event);
	}
	?>
<br/>
<?php
	if (@$this->data['User']['discount'] > 0){
		echo 'Скидка: '.$this->data['User']['discount'].'%';
	}
?>
<br/>
<?php

    if (strtolower($this->data['Group'][0]['name'])=='admin'){

			echo "Является администратором<br/>";

		}
?>
<br/>
<?php
if (!empty($this->data['User']['ftppassword'])){
		echo '<br/> Последний IP: '.$this->data['User']['last_ip'];
	}
?>
<br/>
<?php
if (!empty($this->data['User']['ftppassword'])){
		echo 'Последний вход: '.$this->Common->niceDate($this->data['User']['last_login']);
	}
?>
<br/>
<br/>
<?php
//Иконка для редактирования пользователей
echo $this->Html->link('Изменить данные клиента', '#',
					array ( 'id'=>'user_edit_'.$this->data['User']['id'],
							'escape' => false,
							'class' => 'btn btn-primary' ));

$effect = $this->Js->get('#client_view')->effect('slideIn');
$event  = $this->Js->request(array('controller'=>'Users',
							 'action'=>'editByAdmin', $this->data['User']['id']),
					   array('update' => '#client_view',
							 'before'=>$loadingShow,
							 'complete'=>$loadingHide.";$('#client_view').dialog({modal: true, position: ['center',180], width: 600});",
							 'buffer'=>false));

$this->Js->get('#user_edit_'.$this->data['User']['id'])->event('click', $event);
?>

<?php
			echo $this->Js->writeBuffer(); // Write cached scripts
?>
