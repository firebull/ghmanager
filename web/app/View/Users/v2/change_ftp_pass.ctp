<?php
/*
 * Created on 06.04.2015
 *
 * Made for project GH Manager
 * by Nikita Bulaev
 */
?>
<div class="ui active inverted dimmer" style="display: none;" id="userFtpPassLoader">
	<div class="ui text loader">Сохраняю</div>
</div>
<cake:nocache>

<div id="flash"><?php echo $this->Session->flash(); ?></div>

<div class="ui top attached block header">
	Логин
</div>
<div class="ui bottom attached segment">
	<b><?php echo $ftpLogin; ?></b>
</div>
<div class="ui top attached block header">
	Пароль
</div>
<div class="ui bottom attached segment">
	<b><?php echo $ftpPassword; ?></b>
</div>
<button class="ui fluid red button" id="ftpPassBtn">Изменить пароль</button>
<div class="ui icon message">
	<i class="info icon"></i>
	<div class="content">
		Для доступа по ftp, используйте IP адрес своего сервера (без порта) и логин/пароль указанные выше.
	</div>
</div>

<?php
// Подтверждение смены пароля
$event  = $this->Js->request(array('controller'=>'Users',
							 'action'=>'changeFtpPass','change', 'ver' => 2),
					   array('update'   => '#topMenuModal .content .description',
							 'before'   => '$("#userFtpPassLoader").show();',
							 'complete' => '$("#userFtpPassLoader").hide();',
							 'buffer'   => false));
?>
<script type="text/javascript">



		$('#ftpPassBtn').click(function(){

			var confirmText = "Подвердите смену пароля! \n\n Вы уверены, что хотите сменить FTP-пароль? Это необратимая операция!";

			if (!confirm(confirmText)){
				return false;
			} else {
				<?php echo $event; ?>
			}
		});
</script>
</cake:nocache>


