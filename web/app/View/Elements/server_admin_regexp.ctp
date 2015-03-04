<?php
/*
 * Created on 13.07.2011
 *
 * by Nikita Bulaev
 */
  include('loading_params.php');
?>
<?php echo $this->Form->create('Server',array('action'=>'setModAdmin'));  ?>
<table border="0" cellpadding="0" cellspacing="0" align="center">
	<tr>
		<td>&nbsp;</td>
		<td><h3>Добавить администратора:</h3></td>
		<td style="width: 5%;">
			<?php
				$link=$this->Html->link('(справка)', '#', array('id' => 'help_mod_amin', 'escape' => false));

				$effect = $this->Js->get('#help_mod_amin')->effect('slideIn');
				$event  = $this->Js->request(array( 'controller'=>'helps',
											  'action'=>'view', '5'),
										array('update' => '#view_help',
											  'before'=>$effect.$loadingShow,
											   'complete'=>$loadingHide.";$('#view_help').dialog({modal: true,position: ['center',100], show: 'clip', hide: 'clip', width: 600});"));

				$this->Js->get('#help_mod_amin')->event('click', $event);

				echo $this->Html->tag('small', $link, array('style' => ' '));
			?>

		</td>
	</tr>
	<tr>
		<td colspan="3">
	<?php

		echo $this->Form->input('id', array('type'=>'hidden'));

	?>
		<div class="control-group">
		    <div class="controls">
		        <div class="input-append input-prepend">
		        	<span class="add-on"><i class="icon-user"></i></span><?php

					echo $this->Form->input('admString',array (
													'id'=>'admString',
													'div' => false,
													'label' => false,
													//'size' => 25,
													'style' => 'text-align: center;'
													));

					echo $this->Js->submit('Создать',
										array(
											'url'=> array(
															'controller'=>'Servers',
															'action'=>'setModAdmin'
											 ),
											'id' => 'setModAdminButton',
											'update' => '#server_start_params_container',
											'class' => 'btn',
											'div'   => false,
											'label' => false,
											'before' =>$loadingShow,
											'complete'=>$loadingHide));


	?>
					<label for="admString" class="text-center">
						<small><div id="setAdminMsg"></div></small>
					</label>

				</div>
		    </div>
		</div>
		</td>
	</tr>

</table>
<?php echo $this->Form->end(); ?>

<script type="text/javascript">
	$(function() {

		function matchMsg(){
			var string = $('#admString').val().trim();

			var steamIdRegex = /^STEAM_[01]:[01]:[0-9]{4,11}$/;
			var usrPassRegex = /^\"[0-9a-zA-Z-_\$@\+\=\^\!\?]+\"\s+\"[0-9a-zA-Z-_\$@\+\=\^\!\?]+\"$/;
			var ipRegex  = /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}$/;
			var intRegex = /[0-9 -()+]+$/;

			var result = '';

			if (string.match(steamIdRegex)){
				result = 'Создать админа по Steam ID';
			}
			else
			if (string.match(ipRegex)){
				result = 'Создать админа по IP-адресу';
			}
			else
			if (string.match(usrPassRegex)){
				result = 'Создать админа по имени и паролю';
			}

			$('#setAdminMsg').text(result);

		}

		$("#admString").keyup(function() {
								matchMsg();
								return false;
		});

	});
</script>

<?php
	echo $this->Js->writeBuffer(); // Write cached scripts
?>
