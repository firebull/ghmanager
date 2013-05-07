<?php
/*
 * Created on 16.08.2010
 *
 * File created for project TeamServer
 * by nikita
 */
 include('../loading_params.php');
 $id = $this->data['Server']['id'];
?>
<div id="server_params">
	<div id="flash"><?php echo $session->flash(); ?></div>

<div id="action_positive" style="text-align: left;">
	<h3>Редактировать конфигурационные файлы:</h3>
	<?php 
	//echo $configsType;
	?>
	<div  style="display: inline;" class="icons">
	<ul class="ui-widget ui-helper-clearfix">
	<li>
	<div class="ui-widget">
		<div class="ui-state-highlight ui-corner-all" style="margin-top: 0px; padding: 0px 4px 3px 3px;">
				<?php 
					if ($configsOwner == 'server' or !$configsOwner){
						?>
						<span class="ui-icon ui-icon-bullet" style="float: left; margin-right: .1em; margin-left: 0px; margin-top: 2px;"></span>				
						<?php
					}
					else
					{
						?>
						<span class="ui-icon ui-icon-radio-on" style="float: left; margin-right: .1em; margin-left: 0px; margin-top: 2px;"></span>				
						<?php
					}
				
				?>
		<?php
			       			echo $html->link('Сервера', '#',
										array ('id'=>'configs_for_server', 'escape' => false
										
										));
										$effect = $js->get('#server_params_container')->effect('slideIn');		
										$event  = $js->request(array('controller'=>'servers',
																	 'action'=>'editParamsSrcds',
																	 $id,
																	 'server'), 
															   array('update' => '#server_params_container',	  
																	 'before'=>$loadingShow,
																	 'complete'=>$loadingHide));
								
										$js->get('#configs_for_server')->event('click', $event);
			       			
				?>
		</div>
	</div>
	</li>
	<?php 
	if (@$server){
		if (!empty($server['Mod'])){
			foreach ( $server['Mod'] as $mod ) {
				if (!empty($mod['Config'])){
				?>
				<li>
				<div class="ui-widget">
					<div class="ui-state-highlight ui-corner-all" style="margin-top: 0px; padding: 0px 4px 3px 3px;">
						<?php 
							if ($configsOwner == $mod['name']){
								?>
								<span class="ui-icon ui-icon-bullet" style="float: left; margin-right: .1em; margin-left: 0px; margin-top: 2px;"></span>				
								<?php
							}
							else
							{
								?>
								<span class="ui-icon ui-icon-radio-on" style="float: left; margin-right: .1em; margin-left: 0px; margin-top: 2px;"></span>				
								<?php
							}
						
						?>
				<?php
			       			echo $html->link($mod['longname'], '#',
										array ('id'=>'configs_for_'.$mod['name'], 'escape' => false
										
										));
							$effect = $js->get('#server_params_container')->effect('slideIn');		
							$event  = $js->request(array('controller'=>'servers',
														 'action'=>'editParamsSrcds',
														 $id,
														 'mod',
														 $mod['id']), 
												   array('update' => '#server_params_container',	  
														 'before'=>$loadingShow,
														 'complete'=>$loadingHide));
					
							$js->get('#configs_for_'.$mod['name'])->event('click', $event);
			       			
				?>
				</li>
				<?php
				}
			}
		}
		
		if (!empty($server['Plugin'])){
			foreach ( $server['Plugin'] as $plugin ) {
				if (!empty($plugin['Config'])){
					?>
					<li>
					<div class="ui-widget">
						<div class="ui-state-highlight ui-corner-all" style="margin-top: 0px; padding: 0px 4px 3px 3px;">
							<?php 
								if ($configsOwner == $plugin['name']){
									?>
									<span class="ui-icon ui-icon-bullet" style="float: left; margin-right: .1em; margin-left: 0px; margin-top: 2px;"></span>				
									<?php
								}
								else
								{
									?>
									<span class="ui-icon ui-icon-radio-on" style="float: left; margin-right: .1em; margin-left: 0px; margin-top: 2px;"></span>				
									<?php
								}
							
							?>
					<?php
				       			echo $html->link($plugin['longname'], '#',
											array ('id'=>'configs_for_'.$plugin['name'], 'escape' => false
											
											));
											$effect = $js->get('#server_params_container')->effect('slideIn');		
											$event  = $js->request(array('controller'=>'servers',
																		 'action'=>'editParamsSrcds',
																		 $id,
																		 'plugin',
																		 $plugin['id']), 
																   array('update' => '#server_params_container',	  
																		 'before'=>$loadingShow,
																		 'complete'=>$loadingHide));
									
											$js->get('#configs_for_'.$plugin['name'])->event('click', $event);
				       			
					?>
					</li>
					<?php
				}
			}
		}
	}
	?>
	</ul>
	</div>
</div>
	<script type="text/javascript">
	$(function() {
		
		var loading = '<?php echo $html->image('loading.gif', array('alt'=>'Loading...', 'width'=>'16', 'height'=>'16')); ?> Подождите...'

		$("#tabs").tabs({spinner: loading, selected: '-1',
			ajaxOptions: {
				error: function(xhr, status, index, anchor) {
					$(anchor.hash).html("Ошибка загрузки файла. Попробуйте чуть позже.");
				}
			}
		});
	});
<?php if ( count($configs) > 3){
	// Если больше 4-х конфигов, то расположим их в столбик слева
	?>
	$(function() {
		$("#tabs").tabs().addClass('ui-tabs-vertical ui-helper-clearfix');
		$("#tabs li").removeClass('ui-corner-top').addClass('ui-corner-left');
	});
<?php }?>	
	
	</script> 

	<div class="config_files" style="margin-bottom: 20px;">
	
		<div id="tabs">
			<ul>
				<li><a href="#tabs-1">Помощь</a></li>
				<?php
				
				foreach ( $configs as $config ) {
				?>
	
				<li title="<?php echo 'Просмотр конфига по пути: '.$config['path']."/".$config['name'];?>">
				
				<?php 
				if (strlen($configsOwner) >= 15){
					$configsOwner = '*'.substr($configsOwner, strlen($configsOwner) - 10, strlen($configsOwner));
				}
	       		echo $html->link("<span>[".
	       						$configsOwner.
								"]/".$config['name']."</span>",
	       						array('id' => 'config_'.$config['id'],									  
									  'action'=>'editConfigCommon', 
	       							  $this->data['Server']['id'], 
	       							  $config['id'],'read'),
	       						array('escape' => false)
	       						
	       						);
	       		?>
	       		<script>
	       			$( <?php echo "'#config_".$config['id']."'"; ?> ).bind( "tabsremove", function(event, ui) {
					  	$('#textarea').empty();
					  	editor = null;
					  	JavaScriptMode = null;
					});
	       		</script>
	       		</li>
	       		<?php	
				}
				
				
				?>
				
			</ul>
			<div id="tabs-1">
				Щелкните по конфигу, который хотели бы просмотреть. 
				Если хотите отредактировать его, щелкните по его содержимому. 
				После редактирования, нажмите сохранить.
				Внимательно читайте комментарии  - в них содержится много важной информации.
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function() {
		$(".button, input:submit").button();
	});
</script>
<?php 
	echo $js->writeBuffer(); // Write cached scripts 
?>