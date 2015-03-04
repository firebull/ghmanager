<div id="flash"><?php echo $this->Session->flash(); ?></div>

<?php
include('loading_params.php');
echo $this->Form->create(null, array('url' => '/users/register', 'class' => 'form-horizontal'));
?>

<div class="modal-header" unselectable="on">
    <a class="close" data-dismiss="modal">×</a>
    <h3 class="brown">Для регистрации заполните форму ниже:</h3>
</div>
<div class="modal-body">
	<div class="control-group" id="login-group">
	    <label class="control-label" for="login">Логин</label>
	    <div class="controls">
	      <div class="input-prepend">
	        <span class="add-on"><i class="icon-user"></i></span><?php
	        	echo $this->Form->input('User.username', array('div' => false, 'id' => 'login', 'label' => false )); ?>
	      </div>
	      <p class="help-block" id="help-login">Логин минимум 5 символов</p>
	    </div>
	</div>

	<div class="control-group" id="passwd1-group">
	    <label class="control-label" for="passwd_1">Пароль</label>
	    <div class="controls">
	      <div class="input-prepend">
	        <span class="add-on"><i class="icon-lock"></i></span><?php
	        	echo $this->Form->input('User.passwd', array('div' => false, 'id' => 'passwd_1', 'type'=>'password', 'label' => false )); ?>
	      </div>
	      <span class="help-block" id="help-passwd-1">Пароль от 7 до 32 символов</span>
	    </div>
	</div>

	<div class="control-group" id="passwd2-group">
	    <label class="control-label" for="passwd_2"></label>
	    <div class="controls">
	      <div class="input-prepend">
	        <span class="add-on"><i class="icon-lock"></i></span><?php
	        	echo $this->Form->input('User.confirmpassword', array('div' => false, 'id' => 'passwd_2', 'type'=>'password', 'label' => false )); ?>
	      </div>
	      <p class="help-block" id="help-passwd-2">Повторите пароль</p>
	    </div>
	</div>

	<div class="control-group" id="email-group">
	    <label class="control-label" for="email">email</label>
	    <div class="controls">
	      <div class="input-prepend">
	        <span class="add-on"><i class="icon-envelope"></i></span><?php
	        	echo $this->Form->input('User.email', array('div' => false, 'id' => 'email', 'label' => false,
														 		'x-autocompletetype' => 'email' )); ?>
	      </div>
	      <p class="help-block" id="help-email">Укажите настоящий email</p>
	    </div>
	</div>

	<div class="control-group" id="captcha-image">

	    <div class="controls">
	      <?php echo $this->Html->image("captcha/".@$captcha_src); ?>
	    </div>
	</div>

	<div class="control-group" id="captcha-group">
		<label class="control-label" for="ver_code"></label>
	    <div class="controls">
	      <div class="input-prepend">
	        <span class="add-on"><i class="icon-check"></i></span><?php
	        echo $this->Form->input('ver_code',array('size' => '25', 'div' => false, 'label' => false)); ?>
	      </div>
	      <p class="help-block" id="help-captcha">Подтвердите код на картинке</p>
	    </div>
	</div>


</div>
<div class="modal-footer">
    <div class="control-group">
	    <?php

	//		echo $this->Form->submit('Регистрация', array ('id' => 'progressSubmit'));
			echo $this->Js->submit('Сохранить',
									array(
										'url'=> array(
														'controller'=>'Users',
														'action'=>'register'
										 ),
										'update' => '#register',
										'id' => 'progressSubmit',
										'class' => 'btn btn-primary',
										'before' =>$loadingShow,
										'complete'=>$loadingHide,
										'buffer' => false,
										'div' => false,
										'label' => false));


			?>
	    <button class="btn " data-dismiss="modal">Закрыть</button>
    </div>
</div>
<?php echo $this->Form->end(); ?>
<SCRIPT TYPE="text/javascript">

$(function() {

	function comparePasswords() {
		var passwd1 = $('#passwd_1').val();
		var passwd2 = $('#passwd_2').val();

		if (passwd2.length > 0)
		{
			if (passwd1 == passwd2)
			{
				$('#passwd2-group').attr('class', 'control-group success');
				$('#help-passwd-2').text('Пароли совпадают');
			}
			else
			{
				$('#passwd2-group').attr('class', 'control-group error');
				$('#help-passwd-2').text('Пароли не совпадают');
			}
		}
		else
		{
			$('#passwd2-group').attr('class', 'control-group');
			$('#help-passwd-2').text('Повторите пароль');
		}

	}

	$("#login").keyup(function() {
			var login = $('#login').val().toLowerCase().trim();
			$('#login').val(login);

			if (login.length > 0 && login.length < 5)
			{
				$('#login-group').attr('class', 'control-group error');
				$('#help-login').text('Логин минимум 5 символов');
			}
			else
			if(login.length >= 5)
			{
				$('#login-group').attr('class', 'control-group success');
				$('#help-login').text('Логин корректный');
			}
			else
			{
				$('#login-group').attr('class', 'control-group');
				$('#help-login').text('Логин минимум 5 символов');
			}

			return false;
		});

	$('#passwd_1').password_strength({controller: '#passwd1-group', container: '#help-passwd-1'});

	$("#passwd_1, #passwd_2").keyup(function() {
			comparePasswords();

			return false;
		});

	$("#email").keyup(function() {
			var email = $('#email').val().toLowerCase().trim();
			var regex = /\b[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,4}\b/;

			if (email.length > 0)
			{
				if (email.match(regex))
				{
					$('#email-group').attr('class', 'control-group success');
					$('#help-email').text('Email корректный, вышлю на него код.');
				}
				else
				{
					$('#email-group').attr('class', 'control-group error');
					$('#help-email').text('Это некорректный email');
				}
			}
			else
			{
				$('#email-group').attr('class', 'control-group');
				$('#help-email').text('Укажите настоящий email');
			}


			return false;
		});

});

</SCRIPT>
