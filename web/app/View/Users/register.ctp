<div class="ui active inverted dimmer" id="loading" style="display: none;">
    <div class="ui text loader">Выполняю</div>
</div>
<div id="flash"><?php echo $this->Session->flash(); ?></div>

<?php
echo $this->Form->create(null, array('url' => '/users/register', 'class' => 'ui form'));
?>

	<div class="field" id="login-group">
	    <label for="login">Логин</label>
	    <div class="ui left icon input">
	    <i class="icon user"></i>
	    <?php
	        echo $this->Form->input('User.username', array('div' => false, 'id' => 'login', 'label' => false )); ?>
	      </div>
	      <label id="help-login">минимум 5 символов</label>
	</div>

	<div class="field" id="passwd1-group">
	    <label for="passwd_1">Пароль</label>
	    <div class="ui left icon input">
	        <i class="icon lock"></i>
	        <?php
	        	echo $this->Form->input('User.passwd', array('div' => false, 'id' => 'passwd_1', 'type'=>'password', 'label' => false )); ?>
	      </div>
	      <label id="help-passwd-1">от 7 до 32 символов</label>
	</div>

	<div class="field" id="passwd2-group">
	    <label for="passwd_2">Повторите пароль</label>
	    <div class="ui left icon input">
	        <i class="icon lock"></i>
	        <?php
	        	echo $this->Form->input('User.confirmpassword', array('div' => false, 'id' => 'passwd_2', 'type'=>'password', 'label' => false )); ?>
	      </div>
	      <label id="help-passwd-2"></label>
	</div>

	<div class="field" id="email-group">
	    <label for="email">email</label>
	    <div class="ui left icon input">
	    	<i class="icon envelope"></i>
	    	<?php
	        	echo $this->Form->input('User.email', array('div' => false, 'id' => 'email', 'label' => false,
														 		'x-autocompletetype' => 'email' )); ?>
	      </div>
	      <label id="help-email">Укажите настоящий email</label>
	</div>

	<div class="field" id="captcha-image">
	    <?php echo $this->Html->image("captcha/".@$captcha_src); ?>
	</div>

	<div class="field" id="captcha-group">
		<label for="ver_code">Подтвердите код на картинке</label>
	    <div class="ui left icon input">
	    	<i class="icon check"></i>
	    	<?php
	        echo $this->Form->input('ver_code',array('size' => '25', 'div' => false, 'label' => false)); ?>
	    </div>
	    <label id="help-captcha"></label>
	</div>

    <?php
		echo $this->Js->submit('Сохранить', [
											'url'=> [
														'controller'=>'Users',
														'action'=>'register'
											 		],
											'update' => '#loginModal .content .description',
											'id'     => 'progressSubmit',
											'class'  => 'ui fluid primary button',
											'before'   => '$("#loading").show();',
											'complete' => '$("#loading").hide();',
											'buffer' => false,
											'div'    => false,
											'label'  => false]);
	?>

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
				$('#passwd2-group').attr('class', 'field success');
				$('#help-passwd-2').text('Пароли совпадают');
			}
			else
			{
				$('#passwd2-group').attr('class', 'field error');
				$('#help-passwd-2').text('Пароли не совпадают');
			}
		}
		else
		{
			$('#passwd2-group').attr('class', 'field error');
			$('#help-passwd-2').text('Повторите пароль');
		}

	}

	$("#login").keyup(function() {
			var login = $('#login').val().toLowerCase().trim();
			$('#login').val(login);

			if (login.length > 0 && login.length < 5)
			{
				$('#login-group').attr('class', 'field error');
				$('#help-login').text('Логин минимум 5 символов');
			}
			else
			if(login.length >= 5)
			{
				$('#login-group').attr('class', 'field success');
				$('#help-login').text('Логин корректный');
			}
			else
			{
				$('#login-group').attr('class', 'field');
				$('#help-login').text('Минимум 5 символов');
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
					$('#email-group').attr('class', 'field success');
					$('#help-email').text('Email корректный, вышлю на него код.');
				}
				else
				{
					$('#email-group').attr('class', 'field error');
					$('#help-email').text('Это некорректный email');
				}
			}
			else
			{
				$('#email-group').attr('class', 'field');
				$('#help-email').text('Укажите настоящий email');
			}


			return false;
		});

});

</SCRIPT>
