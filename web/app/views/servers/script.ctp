<?php
/*
 * Created on 24.07.2010
 *
 * File created for project TeamServer
 * by nikita
 */
 
 $event  = $js->request(array('controller' => 'Servers',
							  'action' => 'viewServer', $serverId), 
					    array('update' => '#server_more_'.$serverId,	  
							  'before' => "init_".$serverId."(); refresh_".$serverId."(); $('#server_more_".$serverId."').showLoading({'addClass': 'loading-indicator-bars'});",
							  'complete' => "$('#server_more_".$serverId."').hideLoading(); GetStatuses();" ));
 
?>


<div id="flash"><?php echo $session->flash(); ?></div>
<div id="console" style="background-color: black; color: white;">
<?php 
echo "Текущее время: ".date("D M j G:i:s");
echo "<pre>".@$result."</pre>";	
?>
</div>

<script type="text/javascript" language="javascript">
$(function() {
	
	function refreshStatus(){
		setTimeout(function() {
				<?php echo $event;?>;
				$('#journal_<?php echo $serverId;?>').dialog({modal: true,position: ['center',180], show: 'clip', hide: 'clip', width: 600});

		}, 200 );

	} 

	refreshStatus();
});
</script>
