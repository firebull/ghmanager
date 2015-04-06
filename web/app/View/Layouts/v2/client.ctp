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
								    'ts-theme/teamserver.css',
								    'validationEngine.jquery',
								    'codemirror/codemirror',
								    'codemirror/monokai',
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
			'knockout-3.3.0.js',
			'semantic.1.11.6.js',
			'codemirror/codemirror',
			'codemirror/mode/clike',
			'moment-with-locales'
		));

		echo $this->fetch('script');

	?>
	<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
	<!--[if lt IE 9]>
		<script src="/js/html5.js"></script>
	<![endif]-->
</head>
<body>
	<!-- Top Menu start -->
	<?php echo $this->element('v2/top_menu'); ?>
	<!-- Top Menu end -->
	<div style="padding-top: 50px !important;"></div>
	<div id="flash"><?php echo $this->Session->flash(); ?></div>
	<?php echo $this->fetch('content'); ?>



	<?php
			echo $this->Js->writeBuffer(); // Write cached scripts
	?>
	<div class="ui small modal" id="confirmModal">
        <i class="close icon"></i>
        <div class="header"></div>
        <div class="content"><div class="description"></div></div>
        <div class="actions">
            <div class="ui red basic button">Отмена</div>
            <div class="ui green ok button">OK</div>
        </div>
    </div>
	<?php
	// Скрипты в конец для более быстрой загрузки

	echo $this->Html->script(array (
		'pass'
		,'https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.3/jquery-ui.min.js'
		,'jquery.tipTip.minified'
		,'jquery.showLoading.min.js'
		,'jquery.validationEngine.js'
		,'jquery.validationEngine-ru.js'
		,'jquery.showStatusTs'

	));

	echo "\n\n";

	?>
</body>
</html>
