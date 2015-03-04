<?php
/*
 * Created on 04.06.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
 //pr($this->data);
 include('loading_params.php');

?>
<div id="flash"><?php echo $this->Session->flash(); ?></div>
<?php echo $this->Form->create('User', array('action' => 'editByAdmin')); ?>
<table align="center" border="0" width="96%">
<tr>
	<td>
			<div id="input_fieldname">
				<label for="first_name">Имя:</label>
			</div>
			<div id="input_center">
				<?php echo $this->Form->input('first_name',array('size' => '15', 'div' => false, 'label' => false, 'class' => 'loginform')); ?>
			</div>
	</td>
	<td>
			<div id="input_fieldname">
				<label for="second_name">Фамилия:</label>
			</div>
			<div id="input_center">
				<?php echo $this->Form->input('second_name',array('size' => '15', 'div' => false, 'label' => false, 'class' => 'loginform')); ?>
			</div>
	</td>
</tr>
<?php /*?>
<tr>
	<td>
			<div id="loginform_fieldname">
				<label for="passwd">Новый пароль:</label>
			</div>
			<div id="loginform_input">
				<?php echo $this->Form->input('User.newpasswd',array('size' => '15','type'=>'password', 'div' => false, 'label' => false, 'class' => 'loginform')); ?>
			</div>
	</td>
	<td>
			<div id="loginform_fieldname">
				<label for="confirmpasswd">Повторите пароль:</label>
			</div>
			<div id="loginform_input">
				<?php echo $this->Form->input('User.confirmpasswd',array('size' => '15','type'=>'password', 'div' => false, 'label' => false, 'class' => 'loginform')); ?>
			</div>

	</td>
</tr>
<?php */?>
<tr>
	<td>
		<div id="input_fieldname">
			<label for="email">email:</label>
		</div>
		<div id="input_center">
			<?php echo $this->Form->input('email',array('size' => '15', 'div' => false, 'label' => false, 'class' => 'loginform')); ?>
		</div>

	</td>
	<td>
		<div id="input_fieldname">
			<label for="steam_id">Steam ID:</label>
		</div>
		<div id="input_center">
			<?php echo $this->Form->input('steam_id',array('type'=> 'text', 'size' => '15', 'div' => false, 'label' => false, 'class' => 'loginform')); ?>
		</div>

	</td>
</tr>
<tr>
	<td>
		<div id="input_fieldname">
			<label for="discount"><strong>Персональные права:</strong></label>
		</div>
		<div id="input_center">
	    <?php echo $this->Form->input('Group', array(	'options' => $groupList,
												'multiple' => 'checkbox',
												'div' => false,
												'label' => false));?>
		</div>
	</td>
	<td valign="top">
		<div id="input_fieldname">
			<label for="discount">Персональная скидка, %:</label>
		</div>
		<div id="input_center">
			<?php echo $this->Form->input('discount',array('type'=> 'text',
													 'size' => '15',
													 'div' => false,
                                                     'label' => false,
													 'class' => 'loginform')); ?>
		</div>
	</td>
</tr>
<tr>
	<td>
	</td>
	<td>
	<?php
	echo $this->Js->submit('Сохранить',
								array(
									'url'=> array(
													'controller'=>'users',
													'action'=>'editByAdmin',
													$this->data['User']['id']
									 ),
									'update' => '#edit_user',
									'class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
									'before' =>$loadingShow,
									'complete'=>$loadingHide,
									'buffer' => false));
	?>
	</td>
</tr>
</table>

<?php
	echo $this->Form->end();
?>
