<?php
/*
 * Created on 08.02.2011
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
?>
<div id="flash"><?php echo $this->Session->flash(); ?></div>

<div class="services form">
	<?php echo $this->Form->create(array('controller'=>'Promo'));?>
	<table border="0" cellpadding="0" cellspacing="3" width="95%">
		<tr>
			<td align="right">Тип:</td>
			<td align="left">
			<?php echo $this->Form->input('Promo.type',array (
												  'options' => $types,
												  'selected' => 'code',
												  'id' => 'typesPromo',
												  'div' => false,
												  'label' => false));?></td>
		</tr>
		<tr>
			<td align="right">Код:</td>
			<td align="left">
			<?php echo $this->Form->input('PromoCode.0.code',array (
												  'div' => false,
												  'id' => 'simpleCode',
												  'label' => false));?></td>
		</tr>
		<tr>
			<td align="right">Количество кодов:</td>
			<td align="left">
			<?php echo $this->Form->input('number',array (
												  'div' => false,
												  'disabled'=>'disabled',
												  'id' => 'numberPromo',
												  'label' => false));?></td>
		</tr>
		<tr>
			<td align="right">Описание акции:</td>
			<td align="left">
			<?php echo $this->Form->input('Promo.description',array (
												  'type' => 'textfield',
												  'div' => false,
												  'label' => false));?></td>
		</tr>
		<tr>
			<td align="right">Скидка, %:</td>
			<td align="left">
			<?php echo $this->Form->input('Promo.discount',array (
												  'div' => false,
												  'label' => false));?></td>
		</tr>
		<tr>
			<td align="right">Срок окончания:</td>
			<td align="left">
			<?php echo $this->Form->input('Promo.valid_through',array (
												  'type' => 'text',
												  'id' => 'validTrough',
												  'value' => date('Y-m-d 23:59:59', strtotime('+1 month')),
												  'div' => false,
												  'label' => false));?></td>
		</tr>

		<tr>
			<td></td>
			<td align="left">
			<?php echo $this->Form->submit('Добавить', array(
															'class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
															));
			?>
			</td>
		</tr>
	</table>
</div>

<script type="text/javascript">
		$(function() {
			$("#validTrough").datepicker({ dateFormat: 'yy-mm-dd 23:59:59' });

			$("#typesPromo").change(function() {
					var type = $("#typesPromo").val();
					if ( type == 'code'){
						$('#numberPromo').attr('disabled','disabled');
						$('#simpleCode').removeAttr('disabled');
					}
					else
					{
						$('#simpleCode').attr('disabled','disabled');
						$('#numberPromo').removeAttr('disabled');
					}
			});

		});
</script>
