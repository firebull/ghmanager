<?php
/*
 * Created on 06.04.2015
 *
 * Made for project GH Manager
 * by Nikita Bulaev
 */
?>
<div id="editProfileUser">
	<div class="ui active inverted dimmer" style="display: none;" id="userEditLoader">
		<div class="ui text loader">Сохраняю</div>
	</div>
	<div id="flash"><?php echo $this->Session->flash(); ?></div>

<?php
	if (!empty($this->data['Confirm']))
	{
		foreach ($this->data['Confirm'] as $confirm) {
			if (empty($confirm['server_id']))
			{

			if ($confirm['type'] == 'email')
			{

			echo $this->Form->create('User', ['action' => 'confirmByCode', 'class' => 'ui warning form segment']); ?>
			<div class="ui warning message">Введите код подтверждения для смены email</div>
			<div class="fields">
		        <?php

		        	echo $this->Form->input('Confirm.code', array(
		        										 'type'=> 'text',
		      											 'div' => 'field',
			                                             'label' => false,
			                                             'placeholder' => 'Код подтверждения',
			                                             'id' => 'code_email'));

			        echo $this->Form->input('Confirm.id', array('type' => 'hidden',
			        											'value' => $confirm['id']));
		    	?>

			    <?php
					echo $this->Js->submit('Проверить',
												array(
													'url'=> array(
																	'controller'=>'users',
																	'action'=>'confirmByCode',
																	'check',
																	'ver' => 2
													 ),
													'update' => '#topMenuModal .content .description',
													'div'    => false,
													'label'  => false,
													'class'  => 'ui orange button',
													'before' => '$("#userEditLoader").show();',
													'complete' => '$("#userEditLoader").hide();',
													'buffer' => false));
				?>

				<?php
					echo $this->Js->submit('Отменить запрос',
												array(
													'url'=> array(
																	'controller'=>'users',
																	'action'=>'confirmByCode',
																	'cancel',
																	'ver' => 2
													 ),
													'update' => '#topMenuModal .content .description',
													'div' => false,
													'label'  => false,
													'class'  => 'ui button',
													'before' => '$("#userEditLoader").show();',
													'complete'=> '$("#userEditLoader").hide();',
													'buffer' => false));
				?>
		    </div>
<?php
			echo $this->Form->end();
		}
		else
		if ($confirm['type'] == 'phone')
		{
			echo $this->Form->create('User', ['action' => 'confirmByCode', 'class' => 'ui warning form segment']); ?>
			<div class="ui warning message">Введите код подтверждения для смены номера телефона</div>
			<div class="fields">
		        <?php

		        	if (empty($this->data['User']['phone']))
		        	{
		        		$disabled = 'disabled';
		        	}
		        	else
		        	{
		        		$disabled = 'enabled'; // =))))))
		        	}

		        	echo $this->Form->input('Confirm.code', array(
		        										 'disabled' => $disabled,
		        										 'type'=> 'text',
		      											 'div' => 'field',
			                                             'label' => false,
			                                             'style' => 'width: 150px;',
			                                             'placeholder' => 'С текущего номера',
			                                             'id' => 'code_1'));

		    	?>

		    	<?php
		        	echo $this->Form->input('Confirm.code2', array( 'type'=> 'text',
		      											 'div' => 'field',
			                                             'label' => false,
			                                             'style' => 'width: 150px;',
			                                             'placeholder' => 'С нового номера',
			                                             'id' => 'code_2'));

			        echo $this->Form->input('Confirm.id', array('type' => 'hidden',
			        											'value' => $confirm['id']));
		    	?>

			    <?php
					echo $this->Js->submit('Проверить',
												array(
													'url'=> array(
																	'controller'=>'users',
																	'action'=>'confirmByCode',
																	'check'
													 ),
													'update' => '#edit_profile',
													'div' => false,
													'label' => false,
													'class' => 'ui orange button',
													'before' =>'$("#userEditLoader").show();',
													'complete'=>'$("#userEditLoader").hide();',
													'buffer' => false));
				?>

				<?php
					echo $this->Js->submit('Отменить запрос',
												array(
													'url'=> array(
																	'controller'=>'users',
																	'action'=>'confirmByCode',
																	'cancel'
													 ),
													'update' => '#topMenuModal .content .description',
													'div' => false,
													'label' => false,
													'class' => 'ui button',
													'before' =>'$("#userEditLoader").show();',
													'complete'=>'$("#userEditLoader").hide();',
													'buffer' => false));
				?>
		    </div>
<?php
			echo $this->Form->end();
		}

			}
		}
	}
?>

	<?php
		echo $this->Form->create('User', ['action' => 'edit', 'class' => 'ui form']);
	?>
		<div class="two fields">
			<div class="field">
			    <label for="firstName">Имя:</label>
			      <?php echo $this->Form->input('first_name', ['div' => false,
				                                               'label' => false,
				                                               'placeholder' => 'Имя',
				                                               'id' => 'firstName']); ?>
			</div>
			<div class="field">
			    <label for="secondName">Фамилия:</label>
			      <?php echo $this->Form->input('second_name', ['div' => false,
				                                                'label' => false,
				                                                'placeholder' => 'Фамилия',
				                                                'id' => 'secondName']); ?>
			</div>
		</div>
		<div class="two fields">
			<div class="field">
			    <label for="steamId">Steam ID:</label>
			    <div class="ui left icon input">
					<i class="steam square icon"></i>
			        <?php echo $this->Form->input('steam_id', ['type'=> 'text',
			      											   'div' => false,
				                                               'label' => false,
				                                               'placeholder' => 'Steam ID',
				                                               'id' => 'steamId']); ?>
			    </div>
			</div>
			<div class="field">
			    <label for="codGuid">COD GUID:</label>
			      <?php echo $this->Form->input('guid', ['div' => false,
				                                         'label' => false,
				                                         'placeholder' => 'GUID',
				                                         'id' => 'codGuid']); ?>
			</div>
		</div>
		<div class="two fields">
			<div class="field">
			    <label for="email">email:</label>
			    <div class="ui left icon input">
					<i class="envelope icon"></i>
			        <?php echo $this->Form->input('email', [ 'type'=> 'text',
			      											 'div' => false,
				                                             'label' => false,
				                                             'placeholder' => 'email',
				                                             'id' => 'email']); ?>
			    </div>
			</div>
			<div class="field">
				<label>&nbsp;</label>
			    <div class="ui toggle checkbox">
			        <?php

						echo $this->Form->checkbox('mailing', array(
																	  'id' => 'mailing',
					      											  'div' => false,
						                                              'label' => false,
																	  'class' => 'tipTip',
																	  'title' => 'Получать от нас рассылку с новостями, акциями и предложениями.'));


					?>
			        <label>Подписка на Email-рассылку</label>
			    </div>
			</div>
		</div>
		<div class="two fields">
			<div class="disabled field">
			    <label for="phone">Мобильный телефон:</label>
			    <div class="ui left icon input">
					<i class="call icon"></i>
			      	<?php echo $this->Form->input('phone', ['disabled' => 'disabled',
															'title' => 'Ваш номер телефона в формате 7 XXX XXXXXXX (ЕЩЕ НЕ РАБОТАЕТ!)',
			      											'type'=> 'text',
			      											'div' => false,
				                                            'label' => false,
				                                            'placeholder' => '7 999 0001122',
				                                            'id' => 'phone']); ?>
			    </div>
			</div>
			<div class="disabled field">
			    <label>&nbsp;</label>
			    <div class="ui toggle checkbox">
			        <?php
						echo $this->Form->checkbox('sms_news', ['disabled' => 'disabled',
																'id' => 'sms_news',
				      											'div' => false,
					                                            'label' => false,
																'title' => 'Получать от нас SMS-рассылку с новостями, акциями и предложениями. (ЕЩЕ НЕ РАБОТАЕТ!)']);


					?>
			        <label>Подписка на SMS-рассылку</label>
			    </div>
			</div>
		</div>
	<?php
		echo $this->Js->submit('Сохранить',['url'=> [
														'controller'=>'users',
														'action'=>'edit',
														'ver' => 2
											 		],
											'update' => '#topMenuModal .content .description',
											'div'    => false,
											'label'  => false,
											'class'  => 'ui primary fluid button',
											'before'  => '$("#userEditLoader").show();',
											'complete'=> '$("#userEditLoader").hide();',
											'buffer'  => false]);

		echo $this->Form->end();
	?>
	<div class="ui horizontal divider">Изменить пароль</div>

	<?php echo $this->Form->create('User', ['action' => 'changePass', 'class' => 'ui form']); ?>
		<div class="three fields">
			<div class="field">
			    <label for="progressBar">Новый пароль:</label>
			    <div class="controls">
			      <?php echo $this->Form->input('User.newpasswd',
			      									  ['type'  => 'password',
													   'id'    => 'progressBar',
													   'div'   => false,
													   'label' => false,
													   'class' => 'loginform_passwd_validator',
													   'onKeyUp' => 'checkPassword(this.value)']); ?>
			    </div>
			</div>
			<div class="field">
			    <label for="progressBar">Повторите пароль:</label>
			    <div class="controls">
			      <?php echo $this->Form->input('User.confirmpasswd',
			      										  ['type'=>'password',
														   'id' => 'progressBar2',
														   'div' => false,
														   'label' => false,
														   'class' => 'loginform_passwd',
														   'onKeyUp' => 'verify.check()']); ?>
			    </div>
			    <label class="control-label" for="progressBar" id="password_check_result">Повторите пароль:</label>
			</div>
			<div class="field">
			<label>&nbsp;</label>
				<?php
				echo $this->Js->submit('Изменить пароль',
										   ['url'=> [
														'controller' => 'users',
														'action' => 'changePass',
														'ver' => 2
											 		],
											'update' => '#topMenuModal .content .description',
											'id' => 'progressSubmit',
											'class'    => 'ui primary fluid button',
											'before'   => '$("#userEditLoader").show();',
											'complete' => '$("#userEditLoader").hide();',
											'buffer'   => false]);
				?>
			</div>
		</div>
	<?php echo $this->Form->end(); ?>

<div class="ui green small icon message">
	<i class="info icon"></i>
	<div class="content">
		Если укажите здесь свой Steam ID и/или COD GUID, то вы будете автоматически добавляться в администраторы сервера при установке соответствующих модов.
	</div>
</div>

	<?php
		if (empty($this->data['User']['phone']))
		{
	?>

	<div class="ui warning small icon message">
		<i class="warning icon"></i>
		<div class="content">
			<div class="header">
			Если укажите номер телефона, то ВСЕ коды подтверждения будут приходить в виде SMS на него!
			</div>
			<p>
			Вы не сможете сменить этот номер без доступа к нему! Т.к. мы не берём от вас паспортные данные и не храним их, даже техподдержка не сможет изменить этот номер в случае потери доступа к нему.
			</p>
			<p>
			Поэтому, если вы часто меняете номер, мы настоятельно рекомендуем предварительно сменить его здесь или не указывать его вовсе.
			</p>
		</div>
	</div>

	<?php
		}
		else
		{
	?>

	<div class="ui warning small icon message">
		<i class="warning icon"></i>
		<div class="content">
			<div class="header">
				ВСЕ коды подтверждения будут приходить в виде SMS на указанный здесь номер!
			</div>
			<p>
			Вы не сможете сменить этот номер без доступа к нему! Т.к. мы не берём от вас паспортные данные
			и не храним их, даже техподдержка не сможет изменить этот номер в случае потери доступа к нему.
			</p>
			<p>
			Поэтому, если вы часто меняете номер, мы настоятельно рекомендуем предварительно сменить его здесь заранее! Иначе вам придется создать новый аккаунт!
			</p>
		</div>
	</div>

	<?php
		}
	?>
</div>
<SCRIPT TYPE="text/javascript">

	$('#editProfileUser .ui.checkbox').checkbox();

	verify = new verifynotify();
	verify.field1 = document.getElementById('progressBar');;
	verify.field2 = document.getElementById('progressBar2');
	verify.result_id = "password_check_result";

	// Update the result message
	verify.check();

</SCRIPT>
