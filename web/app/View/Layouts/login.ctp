<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<?php echo $this->Html->charset(); ?>
	<title>
	  <?php echo __('GH Manager: '); ?>
	  Панель управления
	</title>
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<?php
		echo $this->Html-> meta('icon');
		echo $this->Html->css(array (
			'bootstrap/bootstrap.login',
			'ts-theme/teamserver.tiny'
		));
		echo $this->Html->script(array (
			'https://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js'
		));
		echo $scripts_for_layout; ?>
</head>
<body style="margin-top: 5%;">
	<div class="container">
		<div class="row-fluid show-grid">
			<div class="span3">&nbsp;</div>
			<div class="span6">&nbsp;<?php echo $this->Session->flash(); ?></div>
		</div>
		<div class="row-fluid show-grid">
			<div class="span12">
				&nbsp;
				<div class="row-fluid ">
					<div class="span4">&nbsp;</div>
			    	<div class="span4 offset4 pull-left">
			    		<?php echo $this->Html->image('ghmanager_272x42.png', array( 'width' => 272,
			    																'height' => 42,
			    																'class' => 'pull-left',
			    																'style' => 'margin-left: 15px;'));?>
			    	</div>
				</div>
				<div class="row-fluid">
					<div class="span4">&nbsp;</div>
			    	<div class="span4 offset4 border-grey round-border bg-white" style="padding: 0px;">
			    		<?php
										echo $this->Form->create('DarkAuth', array (
											'url' => substr($this->here, strlen($this->base)),
											'style' => 'padding-top: 25px;',
											//'class' => 'well'
										));
								?>
			    		<fieldset>
			    			<label>
			    				<h4 class="brown text-center">Вход в панель управления</h4>
			    			</label>
				    		<br/>
				    		<div class="row-fluid">
					    		<div class="span1">&nbsp;</div>
				    			<div class="span10">


									<div class="input-prepend">

						                <span class="add-on"><i class="icon-user"></i></span><?php   echo $this->Form->input('DarkAuth.username', array(	'div' => false,
																							'label' => false,
																							'style' => 'width: 205px; margin-bottom: 0px;'
																							));     ?>
						            	<label for="DarkAuthUsername" class="text-center">
										<?php
										//Иконка для регистрации

										echo $this->Html->link('Регистрация', '#',
																	array ( 'id'=>'register_link',
																			'escape' => false,
																			'onClick'=>"$('#register').modal('show').fade;"));

										$event  = $this->Js->request(array('controller'=>'users',
																	 'action'=>'register', 1),
															   array('update' => '#register'));

										$this->Js->get('#register_link')->event('click', $event);

										?>

										</label>
						            </div>

						            <div class="input-prepend">

						                <span class="add-on"><i class="icon-lock"></i></span><?php   echo $this->Form->password('DarkAuth.password', array(	'div' => false,
																							'label' => false,
																							'style' => 'width: 205px; margin-bottom: 0px;'
																							));     ?>
						            	<label for="DarkAuthPassword"  class="text-center">
											<?php
											//Иконка для восстановления пароля

											echo $this->Html->link('Забыли пароль?', '#',
																		array ('id'=>'rescue_password_link', 'escape' => false,
																		'onClick'=>"$('#rescue_password').modal('show').fade;"));

											$event  = $this->Js->request(array('controller'=>'users',
																		 'action'=>'rescuePass', 1),
																   array('update' => '#rescue_password'));

											$this->Js->get('#rescue_password_link')->event('click', $event);

											?>
										</label>

						            </div>


					            	<center>
					            		<input type="submit" value="Войти" class="btn-large btn-primary" id="login_button"/>
					            	</center>

			           	</fieldset>
			    		<?php   echo $this->Form->end();     ?>
				    	</div>
			    	</div>
					<div class="row-fluid show-grid" style="margin-top: 5px;">
			    		<div class="span4">&nbsp;</div>
			    		<div class="span4 border-grey round-border bg-white" style="padding-bottom: 5px;">
			    			<h5 class="brown text-center" style="margin-bottom: 5px;">Рекомендуем следующие броузеры:</h5>
			    			<div class="row-fluid" style="margin-bottom: 10px;">
			    				<div style="margin-right: 3px; float: left;"></div>
			    				<div class="span3" style="width: 22.5%">
			    					<center>
			    					<?php    echo $this->Html->image('icons/browser-chrome.png',
																	array(	'alt'=>'Chrome Browser',
																			'title'=>'Google Chrome 10+',
																			'width'=>'50',
																			'height'=>'50',
																			'border'=>'0',
																			'style' => 'margin-top: 1px;'));
												?>
									</center>
								</div>
					    		<div class="span3" style="width: 22.5%">
					    			<center>
					    				<?php    echo $this->Html->image('icons/browser-firefox.png',
																			array(	'alt'=>'Mozilla Firefox Browser',
																					'title'=>'Firefox 4+',
																					'width'=>'55',
																					'height'=>'53',
																					'border'=>'0',
																					'style' => 'margin-top: 0px;'));
														?>
									</center>
								</div>
								<div class="span3" style="width: 22.5%">
									<center>
										<?php    echo $this->Html->image('icons/browser-opera.png',
																			array(	'alt'=>'Opera Browser',
																					'title'=>'Opera 10.2+',
																					'width'=>'48',
																					'height'=>'52',
																					'border'=>'0',
																					'style' => 'margin-top: 1px;'));
														?>
									</center>
								</div>
								<div class="span3" style="width: 22.5%">
									<center>
										<?php    echo $this->Html->image('icons/browser-safari.png',
																			array('alt'=>'Safari Browser', 'title'=>'Apple Safari 6+', 'width'=>'52', 'height'=>'56','border'=>'0'));
														?>
									</center>
								</div>
			    			</div>

			    		</div>

					</div>

					<div class="row-fluid ">
						<div class="span4">&nbsp;</div>
				    	<div class="span4 offset4 text-center">
				    		<small class="small">©2009-2015
				    				<a href="http://wiki.ghmanager.com">Nikita Bulaev</a>
							</small>
				    	</div>
				    </div>
		    	</div>
			</div>
	    </div>
	</div>

</div>


<?php
// Скрипты в конец для более быстрой загрузки
echo $this->Html->script(array (
	'bootstrap-transition',
	'bootstrap-modal',
	'jquery.password_strength',
	'https://ajax.googleapis.com/ajax/libs/jqueryui/1.8.17/jquery-ui.min.js'
));
?>

<script type="text/javascript">
	$(function() {

	});
</script>

<div id="register" class="modal hide fade" title="Регистрация нового клиента">
<?php echo $this->Html->image('loading.gif', array('alt'=>'Loading...', 'width'=>'16', 'height'=>'16'));?>
Загруза формы
<a href="#" onclick="$('register').hide(); return false;">Отменить</a>
</div>
<div id="rescue_password" class="modal hide fade" title="Восстановление пароля.">
	<?php echo $this->Html->image('loading.gif', array('alt'=>'Loading...', 'width'=>'16', 'height'=>'16'));?>
	Загруза формы
	<a href="#" onclick="$('rescue_password').hide(); return false;">Отменить</a>
</div>
<?php
		echo $this->Js->writeBuffer(); // Write cached scripts
?>
</body>
</html>
