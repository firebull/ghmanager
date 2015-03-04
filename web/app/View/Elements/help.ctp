<?php
/*
 * Created on 04.03.2011
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
 $loadingShow = $this->Js->get('#loading')->effect('fadeIn');
 $loadingHide = $this->Js->get('#loading')->effect('fadeOut');

?>

<div class="helper" style="margin-top: 10px;">
	<table border="0" cellpadding="3">
	<?php
		foreach ( $helpers as $id => $help ) {
	       ?>

			<tr>
				<td>
					<small>
				<?php
					$link=$this->Html->link($help, '#', array('id' => 'help_'.$id, 'escape' => false));

					$effect = $this->Js->get('#help_'.$id)->effect('slideIn');
					$event  = $this->Js->request(array( 'controller'=>'helps',
												  'action'=>'view', $id),
											array('update' => '#view_help',
												  'before'=>$effect.$loadingShow,
												   'complete'=>$loadingHide.";$('#view_help').dialog({modal: true,position: ['center',100], show: 'clip', hide: 'clip', width: 600});"));

					$this->Js->get('#help_'.$id)->event('click', $event);

					echo $this->Html->tag('div', $link, array('style' => ' '));
				?>
					</small>
				</td>
				<td>
					<span class="ui-icon ui-icon-circle-arrow-e" style="margin-right: 5px;" title=""></span>
				</td>
			</tr>
	       <?php
		}

	?>

	</table>
</div>
<div id="view_help" style="display:none;" title="Справка" class="ui-widget-content ui-corner-all"></div>
