<?php
/*
 * Created on 19.01.2011
 *
 */
 include('loading_params.php');
?>
<div id="flash"><?php echo $this->Session->flash(); ?></div>

<div class="services form">
	<?php echo $this->Form->create('Service');?>
	<table border="0" cellpadding="0" cellspacing="3" width="95%">

		<tr>
			<td align="right">Краткое название услуги:</td>
			<td align="left">
			<?php echo $this->Form->input('name',array (
												  'size' => '12',
												  'div' => false,
												  'label' => false));?></td>
		</tr>
		<tr>
			<td align="right">Полное название услуги:</td>
			<td align="left">
			<?php echo $this->Form->input('longname',array (
												  'size' => '30',
												  'div' => false,
												  'label' => false));?></td>
		</tr>
		<tr>
			<td align="right">Описание услуги:</td>
			<td align="left">
			<?php echo $this->Form->input('description',array (
												  'div' => false,
												  'label' => false));?></td>
		</tr>
		<tr>
			<td align="right">Стоимость руб./мес.:</td>
			<td align="left">
			<?php echo $this->Form->input('price',array (
												  'size' => '5',
												  'div' => false,
												  'label' => false));?></td>
		</tr>
		<tr>
			<td></td>
			<td align="left">
			<?php 	echo $this->Form->input('Service.id', array(
									'type' => 'hidden',
									'div' => false,
									'label' => false)); ?>
			<?php echo $this->Form->submit('Сохранить', array(
															'class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
															));
			?>
			</td>
		</tr>
	</table>
</div>