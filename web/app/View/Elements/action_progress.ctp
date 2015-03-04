<style>
	.ui-progressbar-value { background-image: url(/css/ts-theme/images/pbar-ani-orange.gif); }
	.ui-widget-header {border: 1px solid #777;}
</style>
<div style="position: relative; margin: 5px;" id="progress_<?php echo $id; ?>">
	<div style="float: left; left: 21%; width: 68%; " id="progressBar_<?php echo $id; ?>"></div>
	<div style="position: relative; float: left; left: 0px; width: 30%; padding-top: 3px; padding-left: 5px;" id="progressBar_<?php echo $id; ?>_Text">Готово: 0%</div>
	
</div>
<script>
	$(function() {
		
		$("#progressBar_<?php echo $id; ?>").progressbar({value: 0});

	});
</script>
<div id="clear"></div>
