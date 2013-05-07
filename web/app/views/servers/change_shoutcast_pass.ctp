<?php
/*
 * Created on 08.09.2010
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
 include('../loading_params.php');
?>
<cake:nocache>
<div id="flash"><?php echo $session->flash(); ?></div>
	<center>
		<small>Пароль транслятора</small>
		<h3 title="Пароль транслятора" style="color: #446805;  cursor:pointer;" id="pass" onClick="ConfirmPass()"><?php echo @$passwords[0]; ?></h3>
		<small>Пароль администратора</small>
		<h3 title="Пароль администратора" style="color: #446805; cursor:pointer;" id="adminPass" onClick="ConfirmAdminPass()"><?php echo @$passwords[1]; ?></h3>
		<p>
			<small>
			* Щелкните по паролю, чтобы сгенерировать новый
			</small>
		</p>
	</center>

<?php
// Подтверждение смены пароля
$confirmMessage = 'Вы уверены, что хотите сменить пароль?' ;		
$eventPassword  = $js->request(array('controller'=>'Servers',
							 'action'=>'changeShoutcastPass',$id,'changePass'), 
					   array('update' => '#server_password_'.$id,	  
							 'before'=>$loadingShow,
							 'complete'=>$loadingHide,
							 'buffer'=>false));
$eventAdminPassword  = $js->request(array('controller'=>'Servers',
							 'action'=>'changeShoutcastPass',$id,'changeAdminPass'), 
					   array('update' => '#server_password_'.$id,	  
							 'before'=>$loadingShow,
							 'complete'=>$loadingHide,
							 'buffer'=>false));

//$js->get('#pass')->event('click', $event);
?>
<div id="confirm_<?php echo $id; ?>" title="Подвердите смену пароля" style="display: none;">
		<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
			<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
			<?php echo $confirmMessage; ?>								
		</div>
</div>
<script type="text/javascript">


	function ConfirmPass() {
		
		$('#confirm_<?php echo $id; ?>').dialog({
								resizable: false,
								height:200,
								width: 350,
								modal: true,
								buttons: {

										'Изменить пароль': function() {
										<?php echo $eventPassword;?>;
										$(this).dialog('close');
									},
									Отмена: function() {
										$(this).dialog('close');
									}
								}
							});	
		
		}
		
	function ConfirmAdminPass() {
		
		$('#confirm_<?php echo $id; ?>').dialog({
								resizable: false,
								height:200,
								width: 350,
								modal: true,
								buttons: {

										'Изменить пароль': function() {
										<?php echo $eventAdminPassword;?>;
										$(this).dialog('close');
									},
									Отмена: function() {
										$(this).dialog('close');
									}
								}
							});	
		
		}
	

</script>
</cake:nocache>