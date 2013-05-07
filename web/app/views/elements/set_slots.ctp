<div id="action_positive"  style="height: 75px;">
	<?php echo $form->create('Server',array('action'=>'setSlots'));  ?>
	<table border="0" cellpadding="0" cellspacing="0" align="center" width="100%">
			<tr>
				<td width="5%"><h3>Слотов:</h3></td>
				<td width="10%">
				<div id="slotsDisabled" title="Перемещайте ползунок для выбора значения" style="margin-right: 3px;"></div>
				</td>
				<td width="85%"><div id="sliderSlots"></div></td>
			
			</tr>
			
			<tr>
				<td colspan="3"><small><div id="slotsMsg" style="height: 15px;"></div></small></td>
			</tr>
			<tr>
				
				<td align="center" colspan="3"><?php
				echo $form->input('id', array('type'=>'hidden', 'value' => $id));
				echo $form->input('slots',array (   'value' => $slots,
													'id'=>'newSlots',
													'div' => false, 
													'label' => false,
													'type' => 'hidden',
													'style' => 'font-weight: bold; color: #444; text-align: center;'));					
				
				
				 echo $form->submit('Изменить', array('class' => 'btn'));
			
			?></td>
			</tr>
		</table>
	<?php echo $form->end(); ?>	
</div>

<script type="text/javascript">
	$(function() {
		function rentLeft () {
			var currSlots = <?php echo $slots;?>;
			var rentLeft = <?php echo intval((strtotime($payedTill) - time()) / 3600 ); ?>;
			var newSlots = eval($("#sliderSlots").slider("value"));
			
			newRent = parseInt((currSlots / newSlots) * rentLeft);
			newDays = parseInt(newRent / 24);
			newHours = newRent - newDays*24;
			$('#slotsMsg').text('Аренда закончится через ~' + newDays + 'дн. ' + newHours + 'час.');
		};
			
		$("#sliderSlots").slider({
			range: "max",
			value: <?php echo $slots;?>,
			min: <?php echo $slots_min;?>,
			max: <?php echo $slots_max;?>,
			step: 1,
			slide: function(event, ui) {
				$("#newSlots").val(ui.value);
				$("#slotsDisabled").text(ui.value);
				rentLeft();
			}
		});
	
		$("#slotsDisabled").text($("#sliderSlots").slider("value"));
	});
</script>

<?php 
	echo $js->writeBuffer(); // Write cached scripts 
?>