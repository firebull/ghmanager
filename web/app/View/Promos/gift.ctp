<?php
/*
 * Created on 21.12.2011
 *
 * File created for project TeamServer(Git)
 * by nikita
 */

 //pr($this->data);
 include('loading_params.php');
?>

<div id="flash"><?php echo $this->Session->flash(); ?></div>

<div class="form">
	<?php echo $this->Form->create(array('controller'=>'Promo', 'action' => 'gift'));?>
	<table border="0" cellpadding="0" cellspacing="3" width="95%">
		<tr>
			<td class="param_name">Добавить дней всем:</td>
			<td class="param_value">
			<?php echo $this->Form->input('Gift.common',array (
														  'div' => false,
														  'label' => false));?></td>
		</tr>
		<tr>
			<td class="param_name">Добавить дней за каждые 8 слотов:</td>
			<td class="param_value">
			<?php echo $this->Form->input('Gift.slots',array (
														  'div' => false,
														  'label' => false));?></td>
		</tr>
		<tr>
			<td class="param_name">Добавить дней за каждую неделю аренды:</td>
			<td class="param_value">
			<?php echo $this->Form->input('Gift.week',array (
														  'div' => false,
														  'label' => false));?></td>
		</tr>
		<tr>
			<td class="param_name">Добавить дней за каждые прошедшие две недели аренды:</td>
			<td class="param_value">
			<?php echo $this->Form->input('Gift.past',array (
														  'div' => false,
														  'label' => false));?></td>
		</tr>
		<tr>
			<td class="param_name"><strong>Да, я абсолютно уверен!</strong></td>
			<td class="param_value">
			<?php echo $this->Form->checkbox('Gift.confirm',array (
			 											  'value' => '1',
														  'div' => false,
														  'label' => false));?></td>
		</tr>
		<tr>
			<td></td>
			<td align="left">
			<?php echo $this->Js->submit('Вперёд!', array (   'id'      => 'gift_submit',
													    'onClick' => 'ConfirmGift();',
													    'class'   => 'button',
													    'style'   => 'margin-top: 10px;',
													    'update'  => '#gift_make',
														'class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
														'before' =>$loadingShow,
														'complete'=>$loadingHide,
														'buffer' => false));


			?>

			</td>
		</tr>
	</table>
</div>
