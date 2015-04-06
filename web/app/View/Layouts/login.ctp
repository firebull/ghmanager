<!DOCTYPE html>
<html>
<head>
	<?php echo $this->Html->charset(); ?>

	<?php
		echo $this->Html->meta(array('property' => 'og:locale',
							 		 'content' => 'ru_RU'));
	?>

	<title><?php echo Configure::read('Panel.vendor.name').': '.$title_for_layout; ?></title>

	<?php
		echo $this->Html->meta('icon');

		echo $this->Html->css(array('semantic.1.11.6',
								    'jquery-ui-1.11.3.css',
								    'ghmanager.main.css?1'));
	?>

	<?php
		echo $this->Html->meta(array('property' => 'og:site_name',
							 		 'content'  => Configure::read('Panel.vendor.name')));

		$this->startIfEmpty('meta');

		echo $this->Html->meta(array('property' => 'og:description',
							 		 'content'  => Configure::read('Panel.vendor.meta')));

		echo $this->Html->meta(array('property' => 'og:title',
							 		 'content'  => Configure::read('Panel.vendor.name')));
		/*
		echo $this->Html->meta(array('property' => 'og:url',
							 		 'content' => "http://ghmanager.com"));

		echo $this->Html->meta(array('property' => 'og:image',
							 		 'content' => "http://ghmanager.com/img/personage01.png"));
		*/

		$this->end();

		echo $this->fetch('meta');
		echo $this->fetch('css');

		echo $this->Html->script(array(
			'jquery-2.1.3.js',
			//'jquery-ui.1.11.3.js',
			'jquery.password_strength',
			'knockout-3.3.0.js',
			'semantic.1.11.6.js',
			'moment-with-locales'
		));

		echo $this->fetch('script');

	?>
	<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		<script src="/js/html5.js"></script>
	<![endif]-->
	<style type="text/css">
		body{
			padding-top: 10% !important;
		}

	</style>
</head>
<body >
<div class="ui middle aligned four column centered doubling grid" style="width:99.9% !important;">
	<div class="row">
		<div class="column">
			<?php echo $this->Session->flash(); ?>
			<?php
            	echo $this->Html->image(Configure::read('Panel.vendor.wideLogo'));
            ?>
			<div class="ui segment">
				<div class="ui center aligned small header">Вход в панель управления</div>
				    <?php
						echo $this->Form->create('DarkAuth', array (
							'url' => substr($this->here, strlen($this->base)),
							'class' => 'ui form'
						));
					?>
					<div class="field">
						<label>Логин</label>
						<div class="ui left icon input">
							<i class="user icon"></i>
							<?php
							echo $this->Form->input('DarkAuth.username', [  'div' => false,
																			'label' => false,
																			'style' => ''
																			]);     ?>
						</div>
					</div>
					<div class="field">
						<label>Пароль</label>
						<div class="ui left icon input">
							<i class="lock icon"></i>
							<?php   echo $this->Form->password('DarkAuth.password', [
																'div' => false,
																'label' => false,
																'style' => ''
																]);     ?>
						</div>
					</div>
					<button class="ui fluid green button">Войти</button>
				<?php   echo $this->Form->end();     ?>
					<center>
						<a href="#" data-bind="event: {click: showModal.bind(true, 'small', 'Восстановить пароль', '/users/rescuePass/1')}">Забыли пароль?</a>
					</center>

					<div class="ui horizontal divider">Или</div>

					<button data-bind="event: {click: showModal.bind(true, 'small', 'Регистрация', '/users/register/1')}" class="ui fluid	button">Регистрация</button>
			</div>
			<center>
				<small>©2009-2015
    				<a href="https://github.com/firebull/ghmanager">Nikita Bulaev</a>
				</small>
			</center>
		</div>
	</div>
</div>
<div class="ui small modal" id="loginModal">
    <i class="close icon"></i>
    <div class="header"></div>
    <div class="content"><div class="description"></div></div>
    <div class="actions">
        <div class="ui button">Отмена</div>
    </div>
</div>

<script type="text/javascript">

	var loginViewModel = function(){

		var self = this;

		this.loading = ko.observable(false);
		this.errors  = ko.observableArray();


		this.showModal = function(size, title, bodyUrl, data){
                var self = this;

                $('#loginModal').removeClass('small large fullscreen').addClass(size);
                $('#loginModal .header').html(title);


                self.loading(true);

                $.get( bodyUrl )
                 .done(
                        function(data){
                            $('#loginModal .content .description').empty();
                            $('#loginModal .content .description').html(data);
                            $('#loginModal').modal('show').modal('refresh');

                            self.loading(false);
                        })
                 .fail( function(data, status, statusText) {
                    answer = "HTTP Error: " + statusText;
                    self.errors.push(answer);
                    self.loading(false);
                 });

            }.bind(this);
	};

	ko.applyBindings(new loginViewModel());
</script>
</body>
</html>
