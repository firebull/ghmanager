<?php
/*
 * Created on 25.08.2010
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
 //pr($this->data);
 include('loading_params.php');
?>
<div class="ui-state-highlight ui-corner-all" style="margin-top: 8px; padding: 0 .7em;">
	<span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>
	<small>
	Мы еще отлаживаем процедуры. Обсуждение, пожелания и ошибки просьба публиковать на форуме.
	</small>
</div>
<br/>
<cake:nocache>
<div id="flash2"><?php echo $this->Session->flash(); ?></div>

<?php if ($canCreateHosting === true) {?>

<?php echo $this->Form->create('WebHosting', array('class' => 'form-inline')); ?>
<h4>Создание учётной записи для управления web-хостингом.</h4>
<table border="0" cellpadding="0" cellspacing="0" width="90%" align="center">
	<tr>
		<td align="center">
			<div id="action_positive">
				<div id="input_fieldname">
					Логин:
				</div>
				<strong><?php  echo $userinfo['User']['username']; ?></strong>
			</div>
		</td>
	</tr>
	<tr>
		<td>
			<div id="action_transparent" style="margin-top: 0px; margin-bottom:0px;">
				<div id="input_fieldname">
					<label for="newpasswd">Пароль:</label>
				</div>
				<?php echo $this->Form->input('User.newpasswd', array('size' => '15',
																   'type'=>'password',
																   'id' => 'progressBar',
																   'div' => false,
																   'label' => false,
																   'style' => 'z-index: 1010;',
																   'class' => 'loginform_passwd_validator',
																   'onKeyUp' => 'checkPassword(this.value)')); ?>
			</div>
			</td>

	</tr>

	<tr>
		<td>
			<div id="action_transparent" style="margin-top: 0px; margin-bottom:0px;">
				<div id="input_fieldname">
					<label for="confirmpasswd">Повторите пароль:</label>
				</div>

				<?php echo $this->Form->input('User.confirmpasswd', array('size' => '15','type'=>'password',
																   'id' => 'progressBar2',
																   'div' => false,
																   'label' => false,
																   'class' => 'loginform_passwd',
																   'onKeyUp' => 'verify.check()')); ?>
			</div>
		</td>
	</tr>
	<tr>
		<td align="center">
			<div id="password_check_result"></div>
		</td>
	</tr>
	<tr>
		<td>
			<div id="action_transparent" style="margin-top: 0px; margin-bottom:0px;">
				<div id="input_fieldname">
					<label for="email">email:</label>
				</div>
				<div id="input_center">
					<?php echo $this->Form->input('User.email', array(
															 'value' => $userinfo['User']['email'],
		                                                     'div' => false,
															 'label' => false,
		                                                     'class' => 'loginform' )); ?>
				</div>
			</div>
		</td>
	</tr>
		<tr>
		<td>
		<div class="action_neutral" id="domain_container">
		<div id="input_fieldname">
			<label for="domain_name">Домен:</label>
		</div>
		<div id="clear"></div>
		<div class="control-group">
			<?php echo $this->Form->input('domain', array(
													 //'style' => 'width: 230px;',
													 'id' => 'domain_name',
                                                     'div' => false,
													 'label' => false,
                                                     'class' => 'span2 tipTip',
                                                     'title'=>'' .
														'Имя сайта может содержать от 3 до 60 символов. Вы можете придумать его на основе ' .
														'наших доменов, приведённых ниже, либо укажите собственное. '.
														'В этом случае вам необходимо ' .
														'самостоятельно зарегистрировать домен у регистратора ' .
														'(мы рекомендуем R01.ru), либо, если он у вас уже есть, ' .
														'позже настроить домен на IP, который вы сможете узнать ' .
														'в ISP Manager.' )); ?>

			<?php echo $this->Form->input('domainList', array(
													 'options'=>array(
																	  'teambans.ru' => 'TeamBans.ru',
																	  'teamstat.ru' => 'TeamStat.ru',
																	  'teamnet.ru'  => 'TeamNet.ru'
																	 ),
													 'id' => 'domain_list',
													 'div'   => false,
													 'label' => false,
                                                     'class' => 'span2 tipTip',
                                                     'title'=>'' .
														'Выберите один из наших доменов. Если вы указали ' .
														'полное имя сайта слева, это меню не имеет значения.' )); ?>
		</div>
		<div id="clear"></div>
</tr>
<tr>
	<td>
	<div class="action_neutral" id="domain_result">Укажите имя сайта</div>
	</div>
	</td>
</tr>
<tr>

	<td align="center">
	<?php
	echo $this->Js->submit('Создать',
		array(
			'url'=> array(
							'controller'=>'Servers',
							'action'=>'webHosting'
			 ),
			'update' => '#web_hosting',
			'id' => 'progressSubmit',
			'class' => 'btn btn-primary',
			'before' =>$loadingShow,
			'complete'=>$loadingHide,
			'buffer' => false));
	echo $this->Form->end();
	?>
	</td>
</tr>
</table>

<script type="text/javascript">
	$(function() {

		$(".tipTip").tipTip({maxWidth: "250px", delay: 100, defaultPosition: 'top'});

		function domainResult() {
			var domain = $("#domain_name").val().toLowerCase();
			var domainOur = $("#domain_list").val();
			var domainRegex  = /^([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}$/;
			var domain3Level = /^[a-zA-Z0-9]{3,60}$/;
			var domain3LevelFull = /^[a-zA-Z0-9]{3,60}\.(teamstat\.ru|teambans\.ru|teamnet\.ru)$/;
			var loadingImg = '<div id="domain_check_loading" style="display: none; position: relative; float: right; width: 16px; height: 16px;"><img src="/img/loading.gif" alt="Loading..." width="16" height="16"></div>';
			var result = '';
			var fullDomain = '';



			if (domain.match(domain3Level) || domain.match(domain3LevelFull)) {
				// Да, смотрится глупо =(
				if (domain.match(domain3Level)) {
					resultLink = '<a href="#" id="checkDomain">Проверить: http://' + domain + '.' + domainOur + '</a>' + loadingImg;
				} else {
					var domainParts = domain.split('.');
					$("#domain_list [value='" + domainParts[1] + "." + domainParts [2] + "']").attr("selected", "selected");

					resultLink = '<a href="#" id="checkDomain">Проверить: http://' + domain + '</a>' + loadingImg;


				}


				$('#domain_container').removeClass().addClass('action_neutral');
				$('#domain_result').removeClass().addClass('action_neutral');
				$("#domain_result").text('');
				$("#domain_result").append(resultLink);

				$("#checkDomain").click(function() {
										CheckDomain();
										return false;
				});

			} elseif (domain.match(domainRegex)) {
				result = 'Имя сайта: http://' + domain;
				$('#domain_container').removeClass().addClass('action_positive');
				$('#domain_result').removeClass().addClass('action_positive');
				$("#domain_result").text(result);
			} else {
				result = 'Имя сайта не указано';
				$('#domain_container').removeClass().addClass('action_accent');
				$('#domain_result').removeClass().addClass('action_accent');
				$("#domain_result").text(result);
			}


		}

		function CheckDomain() {

			$('#domain_check_loading').show();

			var domain = $("#domain_name").val().toLowerCase();
			var domainOur = $("#domain_list").val();
			var domain3Level = /^[a-zA-Z0-9]{3,60}$/;
			var domain3LevelFull = /^[a-zA-Z0-9]{3,60}\.(teamstat\.ru|teambans\.ru|teamnet\.ru)$/;
			var fullDomain = '';


			if (domain.match(domain3Level)) {
				fullDomain = domain + '.' + domainOur;
			} elseif ( domain.match(domain3LevelFull)) {
				fullDomain = domain;
			} else {
				return true;
			}



			$.getJSON('/servers/webHostingCheckDomain/' + fullDomain, function(data) {
					var result = 'Не удалось проверить домен';

					if (data['error'] == '1' || data == null) {
						result = 'Не удалось проверить домен';
					} elseif (data['error'] == '0') {

						if (data['exists'] == '1') {
							result = 'Домен занят. Попробуйте другое имя.';
							$('#domain_container').removeClass().addClass('action_accent');
							$('#domain_result').removeClass().addClass('action_accent');
						} else {
							result = 'Имя свободно: http://' + fullDomain;
							$('#domain_container').removeClass().addClass('action_positive');
							$('#domain_result').removeClass().addClass('action_positive');
						}

					}

				  	$("#domain_result").text(result);
				});


		}

		$("#domain_name").keyup(function() {
								domainResult();
								return false;
		});

		$("#domain_list").change(function() {
								domainResult();
								return false;
		});



		domainResult();

	});
</script>

<SCRIPT TYPE="text/javascript">
	<!--

	verify = new verifynotify();
	verify.field1 = document.getElementById('progressBar');;
	verify.field2 = document.getElementById('progressBar2');
	verify.result_id = "password_check_result";

	// Update the result message
	verify.check();

	// -->

</SCRIPT>

<?php

		} elseif ($canCreateHosting === false and !empty($this->data)) {

?>
	<p>Управлять услугой вы можете по адресу:
	<?php echo $this->Text->autoLinkUrls('https://isp.teamserver.ru/manager', array('target' => '_blank')); ?>
	</p>
	Логин: <strong><?php  echo $this->data['Info']['name']; ?></strong><br/>
	<div id="clear"></div>
	<?php
		if (is_array($this->data['Info']['email']) and !empty($this->data['Info']['email'])) {
			echo $this->Html->tag('div','email: ', array('style' => 'float: left; position: relative; '));
			echo '<div style="float: left; position: relative;">';
			foreach ( $this->data['Info']['email'] as $email ) {
       			echo $email;
			}
			echo '</div>';
		} elseif (!empty($this->data['Info']['email'])) {
			echo 'email: '.$this->data['Info']['email'];
		}
	?>
	<div id="clear"></div>
	<?php
		if (is_array($this->data['Info']['domain']) and !empty($this->data['Info']['domain'])) {
			echo $this->Html->tag('div','Домены: ', array('style' => 'float: left; position: relative; '));
			echo '<div style="float: left; position: relative;">';
			foreach ( $this->data['Info']['domain'] as $domain ) {
       			echo $this->Text->autoLinkUrls('http://'.$domain, array('target' => '_blank'));
			}
			echo '</div>';
		} elseif (!empty($this->data['Info']['domain'])) {
			echo 'Домен: '.$this->Text->autoLinkUrls('http://'.$this->data['Info']['domain'], array('target' => '_blank'));
		}
	?>
	<div id="clear"></div>

<?php
		}

 ?>
<?php
	  echo $this->Js->writeBuffer(); // Write cached scripts
?>

</cake:nocache>





