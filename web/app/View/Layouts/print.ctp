<?php
/*
 * Created on 17.12.2010
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
?>
<!DOCTYPE
    html
    PUBLIC
    "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <?php echo $this->Html->charset(); ?>
    <title>
      <?php echo __('TeamServer: '); ?>
      <?php echo $title_for_layout; ?>
    </title>
    <?php
    	echo $this->Html-> meta('icon');
    	echo $this->Html->css(array (
			'js'
			,'ts-theme/jquery-ui.css'
		));
		echo $this->Html->script(array (
			'pass'
			,'jquery-1.4.4.min'
			,'jquery-ui-1.8.8.custom.min'
			));
    	echo $scripts_for_layout; ?>
  </head>
  <body onLoad="print();">
  <?php echo $content_for_layout; ?>
  </body>
