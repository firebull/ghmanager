<?php
/*
 * Created on 16.09.2010
 * Rewrittent on 05.04.2015
 *
 * Made for project GH Manager
 * by Nikita Bulaev
 */
?>
<div class="ui active inverted dimmer" id="loading" style="display: none;">
    <div class="ui text loader">Выполняю</div>
</div>
<div id="flash"><?php echo $this->Session->flash(); ?></div>
<cake:nocache>
<?php
	echo $this->Form->create('User', ['action' => 'rescuePass', 'class' => 'ui form']); ?>

		<div class="field" id="login-group">
		    <label for="login">Логин:</label>
		    <div class="ui left icon input">
		        <i class="icon user"></i>
		        <?php
		        	echo $this->Form->input('User.username', array('div' => false, 'id' => 'login', 'label' => false )); ?>
		      </div>
		</div>

		<div class="field" id="email-group">
		    <label for="email">email</label>
		    <div class="ui left icon input">
		        <i class="icon envelope"></i>
		        <?php
		        	echo $this->Form->input('User.email', array('div' => false, 'id' => 'email', 'label' => false,
															 		'x-autocompletetype' => 'email' )); ?>
		      </div>
		      <label>Введите e-mail, привязанный к логину</label>
		</div>

		<div class="control-group" id="captcha-image">

	    <div class="controls">
		      <?php echo $this->Html->image("captcha/".@$captcha_src); ?>
		    </div>
		</div>

		<div class="field" id="captcha-group">
			<label for="ver_code"></label>
		    <div class="ui left icon input">
		    <i class="icon check"></i>
		    <?php
		        echo $this->Form->input('ver_code',array('size' => '25', 'div' => false, 'label' => false)); ?>
		    </div>
		    <label id="help-captcha">Подтвердите код на картинке</label>
		</div>

		<?php

			echo $this->Js->submit('Отправить',
										array(
											'url'=> array(
															'controller'=>'users',
															'action'=>'rescuePass'
											 ),
											'update' => '#loginModal .content .description',
											'class' => 'ui fluid button primary',
											'before' => '$("#loading").show();',
											'complete'=> '$("#loading").hide();',
											'buffer' => false,
											'div' => false,
											'label' => false));
		?>


<?php

	echo $this->Form->end();
?>
</cake:nocache>
