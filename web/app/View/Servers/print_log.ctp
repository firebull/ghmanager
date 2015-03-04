<?php
/*
 * Created on 08.09.2010
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
$logName = rtrim($logName,'.log');
?>
<cake:nocache>
	<div id="flash"><?php echo $this->Session->flash(); ?></div>
	<div class="console" id="log-<?php echo $logName; ?>">

	<?php

	$log = htmlspecialchars(@$log);

	echo "<pre>".$log."</pre>";
	echo "Текущее время: ".date("D M j G:i:s");

	?>

	</div>
	<small>* Последние строки выводятся снизу</small>
</cake:nocache>

<script type="text/javascript">
	$('#log-<?php echo $logName; ?>').attr({ scrollTop: $("#log-<?php echo $logName; ?>").attr("scrollHeight") });
</script>
