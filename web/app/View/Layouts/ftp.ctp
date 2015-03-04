<?php
header('Cache-control: private, no-cache, must-revalidate');
header('Expires: 0');
?>
<!DOCTYPE
    html
    PUBLIC
    "-//W3C//DTD XHTML 1.0 Transitional//EN"
    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">

<html xmlns="http://www.w3.org/1999/xhtml">
  <head>
    <title>
      <?php echo __('TeamServer: '); ?>
      <?php echo $title_for_layout; ?>
    </title>
    <?php
    	echo $this->Html-> meta('icon');
	?>

  </head>
  <body>

		<?php print $content_for_layout; ?>


  </body>
</html>
