<div id="flash"><?php echo $session->flash(); ?></div>
<?php
	
	include('../loading_params.php');

	echo $form->create('Server', array('class' => 'form-inline'));

	echo $this->Html->tag('label', 'Выберите карту:');
	echo '<br/>';
	echo $form->input('maps', array('label' => false,
									'div' => false,
									'style' => 'width: 360px; margin-right: 15px;'));

	echo $js->submit('Сменить',
						array(
							'url'=> array(
											'controller'=>'Servers',
											'action'=>'setMapRcon',
											$id
							 ),
							'div' => false,
							'update' => '#server_auto_rcon',
							'class' => 'btn btn-primary',
							'escape' => false,
							'id'=>'submitRconMap',
							'before' =>$loadingShow,
							'complete'=>$loadingHide,
							'buffer' => false));	

?>
<p></p>
<div id='clear'></div>
<div class="ui-state-highlight ui-corner-all" style="margin-top: 8px; padding: 0 .7em;"> 
	<p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
	<small>
	Обратите внимание, что сменив тут карту, вы не измените карту, установленную по-умолчанию!<br/>
	Если вы хотите изменить карту, которая запускается при включении сервера, установите её в
	"Параметрах запуска", кнопка <i class="icon-cog"></i>
	
	</small>
	</p>
</div>


<?php

if (@$refresh === true) // Обновить статус сервера
{
	 $event  = $js->request(array('controller' => 'Servers',
							  'action' => 'viewServer', $id), 
					    array('update' => '#server_more_'.$id,	  
							  'before' => "init_".$id."(); refresh_".$id."(); $('#server_more_".$id."').showLoading({'addClass': 'loading-indicator-bars'});",
							  'complete' => "$('#server_more_".$id."').hideLoading(); GetStatuses();" ));

?>

<script type="text/javascript" language="javascript">
$(function() {
	
	function refreshStatus(){
		setTimeout(function() {
				<?php echo $event;?>;

		}, 2000 );

	} 

	refreshStatus();
});
</script>
<?php

}

?>