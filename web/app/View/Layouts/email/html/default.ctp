<?php
/*
 * Created on 16.09.2010
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
?>
<!DOCTYPE html>
<html>
    <?php
	echo $this->Html->charset();
	echo "\n\n";
	?>
	<body>
		Здравствуйте!

		<?php echo $content_for_layout; ?>
		<p>
		С уважением,
		TeamServer.ru
		</p>
	</body>
</html>
