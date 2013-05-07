<?php
/*
 * Created on 04.06.2010
 *
 * Made for project TeamServer
 * by bulaev
 */

 include('../loading_params.php');
 //pr($this->data);
?>
<div id="flash"><?php echo $session->flash(); ?></div>

<?php
	if (!empty($this->data['Confirm']))
	{
		foreach ($this->data['Confirm'] as $confirm) {
			if (empty($confirm['server_id']))
			{
?>
	<div class="well" style="float: left; margin-left: 0px;">
		<div class="control-group"  style="float: left;">
<?php
		if ($confirm['type'] == 'email')
		{
?>
			<label class="control-label" for="code_email">
				Введите код подтверждения для смены email:
			</label>
			<?php echo $form->create('User', array('action' => 'confirmByCode', 'class' => 'form-inline')); ?>
			<div class="controls">
		        <?php 		        	

		        	echo $form->input('Confirm.code', array(
		        										 'type'=> 'text',
		      											 'div' => false, 
			                                             'label' => false, 
			                                             'class' => 'span3',
			                                             'placeholder' => 'Код подтверждения',
			                                             'id' => 'code_email')); 

			        echo $this->Form->input('Confirm.id', array('type' => 'hidden',
			        											'value' => $confirm['id']));
		    	?>

			    <?php
					echo $js->submit('Проверить',
												array(
													'url'=> array(
																	'controller'=>'users',
																	'action'=>'confirmByCode',
																	'check'
													 ),
													'update' => '#edit_profile',
													'div' => false,
													'label' => false,
													'class' => 'btn btn-primary',
													'before' =>$loadingShow,
													'complete'=>$loadingHide,
													'buffer' => false));
				?>

				<?php
					echo $js->submit('Отменить запрос',
												array(
													'url'=> array(
																	'controller'=>'users',
																	'action'=>'confirmByCode',
																	'cancel'
													 ),
													'update' => '#edit_profile',
													'div' => false,
													'label' => false,
													'class' => 'btn',
													'before' =>$loadingShow,
													'complete'=>$loadingHide,
													'buffer' => false));
				?>	
		    </div>
<?php
			echo $form->end();
		}
		else
		if ($confirm['type'] == 'phone')
		{
?>
			<label class="control-label" for="code_1">
				Введите код подтверждения для смены номера телефона:
			</label>
			<?php echo $form->create('User', array('action' => 'confirmByCode', 'class' => 'form-inline')); ?>
			<div class="controls">
		        <?php 

		        	if (empty($this->data['User']['phone']))
		        	{
		        		$disabled = 'disabled';
		        	}
		        	else
		        	{
		        		$disabled = 'enabled'; // =))))))
		        	}

		        	echo $form->input('Confirm.code', array(  
		        										 'disabled' => $disabled,
		        										 'type'=> 'text',
		      											 'div' => false, 
			                                             'label' => false, 
			                                             'style' => 'width: 150px;',
			                                             'placeholder' => 'С текущего номера',
			                                             'id' => 'code_1')); 

		    	?>

		    	<?php 
		        	echo $form->input('Confirm.code2', array( 'type'=> 'text',
		      											 'div' => false, 
			                                             'label' => false, 
			                                             'style' => 'width: 150px;',
			                                             'placeholder' => 'С нового номера',
			                                             'id' => 'code_2')); 

			        echo $this->Form->input('Confirm.id', array('type' => 'hidden',
			        											'value' => $confirm['id']));
		    	?>

			    <?php
					echo $js->submit('Проверить',
												array(
													'url'=> array(
																	'controller'=>'users',
																	'action'=>'confirmByCode',
																	'check'
													 ),
													'update' => '#edit_profile',
													'div' => false,
													'label' => false,
													'class' => 'btn btn-primary',
													'before' =>$loadingShow,
													'complete'=>$loadingHide,
													'buffer' => false));
				?>

				<?php
					echo $js->submit('Отменить запрос',
												array(
													'url'=> array(
																	'controller'=>'users',
																	'action'=>'confirmByCode',
																	'cancel'
													 ),
													'update' => '#edit_profile',
													'div' => false,
													'label' => false,
													'class' => 'btn',
													'before' =>$loadingShow,
													'complete'=>$loadingHide,
													'buffer' => false));
				?>	
		    </div>
<?php
			echo $form->end();
		}
?>

		</div>
	</div>

<?php
			}
		}
	}
?>

<div class="well" style="float: left;">
	<?php echo $form->create('User', array('action' => 'edit', 'class' => 'form-inline')); ?>
	<div class="control-group"  style="float: left; margin-right: 20px;">
	    <label class="control-label" for="firstName">Имя:</label>
	    <div class="controls">
	      <?php echo $form->input('first_name', array( 'div' => false, 
		                                             'label' => false, 
		                                             'class' => 'span3',
		                                             'placeholder' => 'Имя',
		                                             'id' => 'firstName')); ?>
	    </div>
	</div>
	<div class="control-group" style="float: left;">
	    <label class="control-label" for="firstName">Фамилия:</label>
	    <div class="controls">
	      <?php echo $form->input('second_name', array( 'div' => false, 
		                                             'label' => false, 
		                                             'class' => 'span3',
		                                             'placeholder' => 'Фамилия',
		                                             'id' => 'secondName')); ?>
	    </div>
	</div>

	<div class="control-group"  style="float: left; margin-right: 20px;">
	    <label class="control-label" for="steamId">Steam ID:</label>
	    <div class="controls">
	      <?php echo $form->input('steam_id', array( 'type'=> 'text',
	      											 'div' => false, 
		                                             'label' => false, 
		                                             'class' => 'span3',
		                                             'placeholder' => 'Steam ID',
		                                             'id' => 'steamId')); ?>
	    </div>
	</div>
	<div class="control-group" style="float: left;">
	    <label class="control-label" for="codGuid">COD GUID:</label>
	    <div class="controls">
	      <?php echo $form->input('guid', array( 'div' => false, 
		                                             'label' => false,
		                                             'div' => false, 
		                                             'class' => 'span3',
		                                             'placeholder' => 'GUID',
		                                             'id' => 'codGuid')); ?>
	    </div>
	</div>
	<div class="control-group"  style="float: left;">
	    <label class="control-label" for="email">email:</label>
	    <div class="controls">
	      <?php echo $form->input('email', array( 'type'=> 'text',
	      											 'div' => false, 
		                                             'label' => false, 
		                                             'class' => 'span3',
		                                             'placeholder' => 'email',
		                                             'id' => 'email')); ?>
	    </div>
	</div>
	<div class="control-group span3" style="float: left; margin-top: 20px;">
	    <div class="controls">
	      <label class="checkbox">
            <?php

				echo $this->Form->checkbox('mailing', array(  
															  'id' => 'mailing',
			      											  'div' => false, 
				                                              'label' => false,
															  'class' => 'tipTip',
															  'title' => 'Получать от нас рассылку с новостями, акциями и предложениями.'));


			?>
            Подписка на Email-рассылку
          </label>
	    </div>
	</div>
	<div id="clear"></div>
	<div class="control-group"  style="float: left;">
	    <label class="control-label" for="email">Мобильный телефон:</label>
	    <div class="controls">
	      <?php echo $form->input('phone', array(   'disabled' => 'disabled',
													'title' => 'Ваш номер телефона в формате 7 XXX XXXXXXX (ЕЩЕ НЕ РАБОТАЕТ!)',
	      											'type'=> 'text',
	      											'div' => false, 
		                                            'label' => false, 
		                                            'class' => 'span3',
		                                            'placeholder' => '7 999 0001122',
		                                            'id' => 'phone')); ?>
	    </div>
	</div>
	<div class="control-group span3" style="float: left; margin-top: 20px;">
	    <div class="controls">
	      <label class="checkbox">
            <?php

				echo $this->Form->checkbox('sms_news', array( 'disabled' => 'disabled',
															  'id' => 'sms_news',
			      											  'div' => false, 
				                                              'label' => false,
															  'class' => 'tipTip',
															  'title' => 'Получать от нас SMS-рассылку с новостями, акциями и предложениями. (ЕЩЕ НЕ РАБОТАЕТ!)'));


			?>
            Подписка на SMS-рассылку
          </label>
	    </div>
	</div>
	<div id="clear"></div>
	<div class="form-actions" style="padding-bottom: 0px;">
	    <?php
			echo $js->submit('Сохранить',
										array(
											'url'=> array(
															'controller'=>'users',
															'action'=>'edit'
											 ),
											'update' => '#edit_profile',
											'div' => false,
											'label' => false,
											'class' => 'btn btn-primary',
											'before' =>$loadingShow,
											'complete'=>$loadingHide,
											'buffer' => false));
		?>	
	</div>

</div>
	
<?php
	echo $form->end();
?>

<div class="well" style="float: left;">
	<?php echo $form->create('User', array('action' => 'changePass', 'class' => 'form-inline')); ?>

	<div class="control-group"  style="float: left; margin-right: 20px;">
	    <label class="control-label" for="progressBar">Новый пароль:</label>
	    <div class="controls">
	      <?php echo $form->input('User.newpasswd',array(  
														   'type'=>'password', 
														   'id' => 'progressBar', 
														   'div' => false, 'label' => false, 
														   'class' => 'loginform_passwd_validator span3', 
														   'onKeyUp' => 'checkPassword(this.value)')); ?>
	    </div>
	</div>
	<div class="control-group"  style="float: left; margin-right: 20px;">
	    <label class="control-label" for="progressBar">Повторите пароль:</label>
	    <div class="controls">
	      <?php echo $form->input('User.confirmpasswd',array('size' => '15','type'=>'password', 
																   'id' => 'progressBar2', 
																   'div' => false, 
																   'label' => false, 
																   'class' => 'loginform_passwd span3', 
																   'onKeyUp' => 'verify.check()')); ?>
	    </div>
	    <label class="control-label" for="progressBar" id="password_check_result">Повторите пароль:</label>
	</div>
	<div id="clear"></div>
	<div class="form-actions" style="padding-bottom: 0px; margin-top: 0px;">
		<?php
		echo $js->submit('Изменить пароль',
									array(
										'url'=> array(
														'controller'=>'users',
														'action'=>'changePass'
										 ),
										'update' => '#edit_profile',
										'id' => 'progressSubmit',
										'class' => 'btn btn-primary',
										'before' =>$loadingShow,
										'complete'=>$loadingHide,
										'buffer' => false));
		?>
	</div>
</div>
<div id="clear"></div>
<?php
	if (empty($this->data['User']['phone']))
	{
?>

<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
	<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
	<small>
	Если укажите здесь номер телефона, то ВСЕ коды подтверждения будут приходить в виде SMS на него!<br/>
	Вы не сможете сменить этот номер без доступа к нему! Т.к. мы не берём от вас паспортные данные
	и не храним их, даже техподдержка не сможет изменить этот номер в случае потери доступа к нему.
	<br/>Поэтому, если вы часто меняете номер, мы настоятельно рекомендуем предварительно сменить его здесь или 
	не указывать его вовсе.
	</small>
	</p>
</div>

<?php
	}
	else
	{
?>

<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
	<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
	<small>
	ВСЕ коды подтверждения будут приходить в виде SMS на указанный здесь номер!<br/>
	Вы не сможете сменить этот номер без доступа к нему! Т.к. мы не берём от вас паспортные данные
	и не храним их, даже техподдержка не сможет изменить этот номер в случае потери доступа к нему.
	<br/>Поэтому, если вы часто меняете номер, мы настоятельно рекомендуем предварительно сменить его здесь заранее! Иначе вам придется создать новый аккаунт!
	</small>
	</p>
</div>
	
<?php
	}
?>
<br/>
<div id="clear"></div>
<div class="ui-state-highlight ui-corner-all" style="padding: 0 .7em;"> 
	<p><span class="ui-icon ui-icon-info" style="float: left; margin-right: .3em;"></span> 
	<small>
	Если укажите здесь свой Steam ID и/или COD GUID, то вы будете автоматически добавляться
	в администраторы сервера при установке соответствующих модов.
	</small>
	</p>
</div>

<SCRIPT TYPE="text/javascript">
	<!--
	
	verify = new verifynotify();
	verify.field1 = document.getElementById('progressBar');;
	verify.field2 = document.getElementById('progressBar2');
	verify.result_id = "password_check_result";
	
	// Update the result message
	verify.check();
	
	// -->

</SCRIPT>