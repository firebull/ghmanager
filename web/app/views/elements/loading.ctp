<?php
/*
 * Created on 09.06.2010
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
?>
<div id="loading" style="display: none; margin-top: 0px; top: 0px;" class="ui-state-highlight">
	<table border="0" cellpadding="0" cellspacing="5">
		<tr>
			<td valign="top">
				<?php 
				echo $html->image('loading_red.gif', array('alt'=>'Loading...', 'width'=>'16', 'height'=>'16'));
				?>
			</td>
			<td>
			Выполение операции, подождите, пожалуйста.
			</td>
		</tr>
	</table>
</div>
<script type="text/javascript">
	$(function() {
		
		$('#loading').position({
				of: '#container',
				my: 'top',
				at: 'top'
			});
		
		});
</script>
