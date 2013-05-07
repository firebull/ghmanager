<?php
/*
 * Created on 06.06.2010
 *
 */
 //pr($session);
?>
<script type="text/javascript">
	$(function() {
		
		$("#submit").click(function() { 
			javascript:history.go(0); 
			return false;		
		 });
	});
	</script>
<div style="position: relative; width: 90%; left: 5%;">
	<div id="flash"><?php echo $session->flash(); ?></div>
	<div align="right">
		<input type="submit" id="submit" class="btn" value="Закрыть"/>
	</div>
</div>

