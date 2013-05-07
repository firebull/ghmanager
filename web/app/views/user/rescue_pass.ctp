<?php
/*
 * Created on 16.09.2010
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
 include('../loading_params.php');
?>
<div id="flash"><?php echo $session->flash(); ?></div>
<cake:nocache>
<?php echo $form->create('User', array('action' => 'rescuePass', 'class' => 'form-horizontal')); ?>
	<div class="modal-header" unselectable="on">
	    <a class="close" data-dismiss="modal">×</a>
	    <h3 class="brown">Восстановление пароля:</h3>
	</div>

	<div class="modal-body">
		<div class="control-group" id="login-group">
		    <label class="control-label" for="login">Логин:</label>
		    <div class="controls">
		      <div class="input-prepend">
		        <span class="add-on"><i class="icon-user"></i></span><?php 
		        	echo $form->input('User.username', array('div' => false, 'id' => 'login', 'label' => false )); ?>
		      </div>
		    </div>
		</div>
		
		<div class="control-group" id="email-group">
		    <label class="control-label" for="email">email</label>
		    <div class="controls">
		      <div class="input-prepend">
		        <span class="add-on"><i class="icon-envelope"></i></span><?php 
		        	echo $form->input('User.email', array('div' => false, 'id' => 'email', 'label' => false,
															 		'x-autocompletetype' => 'email' )); ?>
		      </div>
		      <p class="help-block" id="help-email">Введите e-mail, привязанный к логину</p>
		    </div>
		</div>

		<div class="control-group" id="captcha-image">
	    
	    <div class="controls">
		      <?php echo $html->image("captcha/".@$captcha_src); ?>
		    </div>
		</div>

		<div class="control-group" id="captcha-group">
			<label class="control-label" for="ver_code"></label>
		    <div class="controls">
		      <div class="input-prepend">
		        <span class="add-on"><i class="icon-check"></i></span><?php 
		        	echo $form->input('ver_code',array('size' => '25', 'div' => false, 'label' => false)); ?>
		      </div>
		      <p class="help-block" id="help-captcha">Подтвердите код на картинке</p>
		    </div>
		</div>

	</div>

	

	<div class="modal-footer">
		<?php
			//echo $form->submit ('Submit_form');
			echo $js->submit('Отправить',
										array(
											'url'=> array(
															'controller'=>'users',
															'action'=>'rescuePass'
											 ),
											'update' => '#rescue_password',
											'class' => 'btn btn-primary',
											'before' =>$loadingShow,
											'complete'=>$loadingHide,
											'buffer' => false,
											'div' => false,
											'label' => false));
		?>

		<a href="#" class="btn " data-dismiss="modal">Закрыть</a>

	</div>


<?php
	
	echo $form->end();
?>
</cake:nocache>