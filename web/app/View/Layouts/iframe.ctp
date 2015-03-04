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

