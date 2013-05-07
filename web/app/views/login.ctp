<?php
	$this->pageTitle = 'Представьтесь!';
	$this->layout = 'login';
	include('loading_params.php');
//	  	$this->loadModel('User');
//	  	$this->set('browser',$this->User->isCorrectBrowser($_SERVER['HTTP_USER_AGENT']));	
?>
<div id="title">
	<div id="logo"></div>
	<div id="title_text">Вход в панель администрирования</div>
</div>
<div id="login_form">
		
		

		<div id="loginform_header">Представьтесь!</div>
		<?php
				echo $form->create('DarkAuth', array (
					'url' => substr($this->here, strlen($this->base))
				));
		?>
		<?php   echo "\n";      ?>
		<div id="loginform_fieldname">
		<label for="username">логин:</label>
		</div>
		<div id="loginform_input">
		<?php   echo $form->input('DarkAuth.username', array('div' => false, 'label' => false, 'class' => 'loginform' ));     ?>
		</div>
		<div id="loginform_fieldname">
			<label for="username">

				<?php
				//Иконка для регистрации
				
				echo $html->link('Регистрация', '#',
											array ('id'=>'register_link', 'escape' => false,
											'onClick'=>"$('#register').dialog({modal: true,position: ['center',180]});"));
		
				$event  = $js->request(array('controller'=>'users',
											 'action'=>'register', 1), 
									   array('update' => '#register',	  
											 'before'=>$loadingShow,
											 'complete'=>$loadingHide));
	
				$js->get('#register_link')->event('click', $event);
				
				?>
				
			</label>

		</div>
		<?php   echo "\n";      ?>
		<br />
		<div id="loginform_fieldname">
		<label for="password">пароль:</label>
		</div>
		<div id="loginform_input">
			<?php   echo $form->password('DarkAuth.password', array('div' => false, 'label' => false, 'class' => 'loginform_passwd' ));  ?>
			<?php   echo "\n";      ?>
		</div>
		<div id="loginform_fieldname">
			<label for="password">
				<?php
				//Иконка для восстановления пароля
				
				echo $html->link('Забыли?', '#',
											array ('id'=>'rescue_password_link', 'escape' => false,
											'onClick'=>"$('#rescue_password').dialog({modal: true,position: ['center',180]});"));
		
				$event  = $js->request(array('controller'=>'users',
											 'action'=>'rescuePass', 1), 
									   array('update' => '#rescue_password',	  
											 'before'=>$loadingShow,
											 'complete'=>$loadingHide));
	
				$js->get('#rescue_password_link')->event('click', $event);
				
				?>
			</label>
		</div>
		<br />
		<div id="loginform_button">
		<input type="submit" value="войти" class="login_submit" id="login_button"/>
		</div>
			<?php   echo $form->end();     ?>
			<?php   echo "\n";      ?>

		</div>
		<div id="browsers_list" style="bottom: 20px;">
			<table class="browsers_list" cellpadding="2" cellspacing="0" border="0">
				<tr>
					<th colspan="4">
						Рекомендуем работать с панелью в следующих броузерах:
					</th>
				</tr>
				<tr class="bottom">
					<td width="26%">
					<?php
						echo $html->image('icons/operalogo.png', 
										array('alt'=>'Opera Logo', 'title'=>'Opera 10.2+', 'width'=>'84', 'height'=>'75','border'=>'0'));
					?>
					</td>
					<td width="25%">
					<?php
						echo $html->image('icons/mozillafirefox.png', 
										array('alt'=>'Mozilla Firefox Logo', 'title'=>'Mozilla Firefox 3.6+', 'width'=>'78', 'height'=>'75'));
					?>
					</td>
					<td width="24%">
					<?php
						echo $html->image('icons/chromelogo.png', 
										array('alt'=>'Google Chrome Logo', 'title'=>'Google Chrome 5+', 'width'=>'78', 'height'=>'75','border'=>'0'));
					?>
					</td>
					<td width="25%">
					<?php
						echo $html->image('icons/safari.png', 
										array('alt'=>'Apple Safari Logo', 'title'=>'Apple Safari 5+', 'width'=>'75', 'height'=>'80','border'=>'0'));
					?>
					</td>
					
					
				</tr>
				<tr class="top">
					<td width="26%"  style="padding-left: 20px;">
						Opera&nbsp;10.2+
					</td>
					<td width="25%">
						Mozilla Firefox&nbsp;3.6+
					</td>
					<td width="24%">
						Google Chrome&nbsp;5+
					</td>
					<td width="25%">
						Apple Safari&nbsp;5+
					</td>
									
				</tr>
			</table>
		</div>
	


<div id="register" style="display:none" class="ui-widget-content ui-corner-all" title="Регистрация нового клиента">
	<?php echo $html->image('loading.gif', array('alt'=>'Loading...', 'width'=>'16', 'height'=>'16'));?> 
	Загруза формы
	<a href="#" onclick="$('register').hide(); return false;">Отменить</a>
</div>
<div id="rescue_password" style="display:none" class="ui-widget-content ui-corner-all" title="Восстановление пароля.">
	<?php echo $html->image('loading.gif', array('alt'=>'Loading...', 'width'=>'16', 'height'=>'16'));?> 
	Загруза формы
	<a href="#" onclick="$('rescue_password').hide(); return false;">Отменить</a>
</div>
<?php 
			echo $js->writeBuffer(); // Write cached scripts 
?>



