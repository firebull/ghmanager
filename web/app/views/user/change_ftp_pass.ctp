<?php
/*
 * Created on 25.08.2010
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
 include('../loading_params.php');
?>

<cake:nocache>
<div id="flash"><?php echo $session->flash(); ?></div>
	<center>
		<h3 title="Логин" class="highlight2"><?php echo $ftpLogin; ?></h3>
		<h3 title="Пароль" class="highlight2" style="cursor:pointer;" id="pass" onClick="Confirm()"><?php echo $ftpPassword; ?></h3>
		<p>
			<small>
			* Щелкните по паролю, чтобы сгенерировать новый
			</small>
		</p>
		<p>Для доступа по ftp, используйте IP адрес своего сервера (без порта) и логин/пароль указанные выше.</p>
	</center>

<?php
// Подтверждение смены пароля
$confirmMessage = 'Вы уверены, что хотите сменить FTP-пароль?'.
						 "\n<br/><br/>Это необратимая операция!" ;		
$event  = $js->request(array('controller'=>'Users',
							 'action'=>'changeFtpPass','change'), 
					   array('update' => '#ftp_pass',	  
							 'before'=>$loadingShow,
							 'complete'=>$loadingHide,
							 'buffer'=>false));

//$js->get('#pass')->event('click', $event);
?>
<div id="confirm" title="Подвердите смену пароля" style="display: none;">
		<div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"> 
			<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span> 
			<?php echo $confirmMessage; ?>								
		</div>
</div>
<script type="text/javascript">


	function Confirm() {
		
		$('#confirm').dialog({
								resizable: false,
								height:200,
								width: 350,
								modal: true,
								buttons: {

										'Изменить пароль': function() {
										<?php echo $event;?>;
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


