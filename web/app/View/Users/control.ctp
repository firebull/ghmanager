<?php
/*
 * Created on 20.06.2010
 *
 * To change the template for this generated file go to
 */
 include('loading_params.php');
 $i=1;
 //Вводим начальное значение сортировки
 if (@$pastOrder){
 	$order = @$pastOrder; // устанавливаем из предыдущей сессии
 }
 else
 {
 	$order = 'asc';
 }

 // Создаем массив инверсии сортиорки.
 // Это делается для того, чтобы при создании ссылки сортировки столбца,
 // каждый раз порядок сортировки был обратным предыдущему.
 $revertOrder = array (
 						'desc' => 'asc',
 						'asc' => 'desc'
 						);
?>
<div id="users_list">

	<h2>Список пользователей:</h2>

	<table class="intext" border="0" cellpadding="0" cellspacing="0">
		<tr>
			<th width="100"></th>
			<th>№ п/п</th>
			<th><?php echo $this->Paginator->sort('ID', 'id'); ?></th>
			<th><?php echo $this->Paginator->sort('Логин', 'username'); ?></th>
			<th>Права</th>
			<th>Имя</th>
			<th><?php echo $this->Paginator->sort('e-mail', 'email'); ?></th>
			<th><?php echo $this->Paginator->sort('Последний вход', 'last_login'); ?></th>
			<th><?php echo $this->Paginator->sort('Зарегистрирован', 'created'); ?></th>
		</tr>
<?php
		if ( !empty($users) ) {
			foreach ($users as $user):
?>
		<tr>
			<td>
			<div class="icons">
			<ul id="icons_<?php echo $user['User']['id'];?>" class="ui-widget ui-helper-clearfix">
			<li class="ui-state-default ui-corner-all" title="Изменить данные клиента">
			<?php
			//Иконка для редактирования пользователей
			echo $this->Html->link('<span class="ui-icon ui-icon-wrench"></span>', '#',
								array ('id'=>'user_edit_'.$user['User']['id'], 'escape' => false,
								'onClick'=>""));
			$effect = $this->Js->get('#edit_user')->effect('slideIn');
			$event  = $this->Js->request(array('controller'=>'Users',
										 'action'=>'editByAdmin', $user['User']['id']),
								   array('update' => '#edit_user',
										 'before'=>$loadingShow,
										 'complete'=>$loadingHide.";$('#edit_user').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});",
										 'buffer'=>false));

			$this->Js->get('#user_edit_'.$user['User']['id'])->event('click', $event);
			?>
			</li>
			<li class="ui-state-default ui-corner-all" title="Удалить клиента и все его данные">
			<?php
						//Иконка для удаления клиента
						$confiremDeleteMessage = 'Вы уверены, что хотите удалить клиента '.$user['User']['username'].' #'.$user['User']['id'].' ?'.
												 "\n<br/><br/>Будут удалены также все его данные:<br/>" .
												 "серверы, домашняя директория, логи!" .
												 "\n<br/><br/>Это необратимая операция!" ;
						echo $this->Html->link('<span class="ui-icon ui-icon-trash"></span>', '#',
												array ('id'=>'user_delete_'.$user['User']['id'], 'escape' => false
												,'onClick'=>"$('#delete_confirm_".$user['User']['id']."').dialog({
																									resizable: false,
																									height:300,
																									width: 400,
																									modal: true,
																									buttons: {

																											'Удалить': function() {
																											window.location.href='/users/delete/".$user['User']['id']."';
																											$(this).dialog('close');
																										},
																										Отмена: function() {
																											$(this).dialog('close');
																										}
																									}
																								});"

												));
						?>
			<div id="delete_confirm_<?php echo $user['User']['id']; ?>" title="Подвердите удаление клиента" style="display: none;">
								<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;">
									<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
									<?php echo $confiremDeleteMessage; ?>
								</div>
			</div>
			</li>
			</ul>

			</td>
			<td><?php echo $i++; ?></td>
			<td><?php echo $user['User']['id']; ?></td>
			<td><?php echo $user['User']['username']; ?></td>
			<td><?php echo @$user['Group']['0']['name']; ?></td>
			<td><?php echo $user['User']['second_name']."&nbsp;".$user['User']['first_name']; ?></td>
			<td><?php echo $this->Text->autoLinkEmails($user['User']['email']); ?></td>
			<td><?php
				if ($user['User']['last_login'] == '0000-00-00 00:00:00'){
					echo '-';
				}
				else
				{
					echo $this->Common->niceDate($user['User']['last_login']);
				}

				?>
			</td>
			<td><?php echo $this->Common->niceDate($user['User']['created']); ?></td>
		</tr>


<?php
		endforeach;

		}
?>
	</table>
	<center>
		<!-- Shows the next and previous links -->
		<?php
			echo $this->Paginator->prev('«««', null, null, array('class' => 'disabled'));
			echo '&nbsp;&nbsp;';
			echo $this->Paginator->numbers();
			echo '&nbsp;&nbsp;';
			echo $this->Paginator->next('»»»', null, null, array('class' => 'disabled'));

		?>
		<br/>
		<!-- prints X of Y, where X is current page and Y is number of pages -->
		<?php echo $this->Paginator->counter(array('format' => 'Страница %page% из %pages%')); ?>
	</center>
<?php
			echo $this->Js->writeBuffer(); // Write cached scripts
?>
</div>
<!-- Контейнер для создания диалога редкатирования параметров сервера -->
<div id="edit_user" style="display:none;" title="Редактировать данные клиента"></div>
