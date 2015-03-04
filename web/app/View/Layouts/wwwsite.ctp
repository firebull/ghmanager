<?php
/*
 * Created on 24.05.2010
 *
 * Made fot project TeamServer
 * by bulaev
 */
?>
<!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <?php
	echo $this->Html->charset();
	?>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<link rel="stylesheet" href="http://www.teamserver.ru/templates/webhost_plazza/css/joomla.css" type="text/css" />
	<link rel="stylesheet" href="http://www.teamserver.ru/templates/webhost_plazza/css/styleTS.css" type="text/css" />
	<link href="http://www.teamserver.ru/templates/webhost_plazza/favicon.ico" rel="shortcut icon" type="image/x-icon" />
 	<?php
 		echo $this->Html->css(array (
			'https://panel.teamserver.ru/css/ts-theme/external.css'
		));

	 	/*
	 	echo $this->Html->script(array (
			 'http://yandex.st/jquery/1.6.2/jquery.min.js'
			,'https://panel.teamserver.ru/js/jquery-ui-1.8.16.custom.min.js'
		));
		*/

    ?>
    <title>
      <?php echo __('TeamServer: '); ?>
      <?php echo $title_for_layout; ?>
    </title>
    <?php


echo $this->Html->meta('icon');
?>
<?php
echo $scripts_for_layout;

echo "\n\n";


?>

  </head>
  <body>
<?php echo $content_for_layout; ?>

  </body>
</html>

