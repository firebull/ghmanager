<?php
/*
 * Created on 04.05.2012
 *
 * File created for project TeamServer
 * by nikita
 */

include('loading_params.php');
//pr($eacServer);
?>

<div id="server_params">
	<div id="flash"><?php echo $this->Session->flash(); ?></div>
	<?php echo $this->Form->create('Server', array('action' => 'editStartParamsEac',
											 'class' => 'form-inline')); ?>
	<div id="action_positive"  style="height: 135px;">

		<table width="100%">
			<tr>
				<td colspan="4"><h3>Подключить сервер:</h3></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<div class="control-group" style="float: left;">
					<?php


						echo $this->Form->input('ownServer', array(
													'options' => $serversForEac,
													'selected' => @intval($this->data['Server']['ownServer']),
													'id' => 'ownServer',
													'div' => false,
													'label' => false,
													'title'=>'Вы можете подключить свой сервер из списка. В этом случае, никаких дополнительных параметров (пароль и тип игры) вводить не нужно. Сервер будет подключен автоматически. Все параметры также будут настроены автоматически.',
													'class' => 'span4 tipTip',
													'style' => ''));
					?>
					</div>
					<div id="clear"></div>
				</td>
			</tr>
			<tr>
				<td>или</td>
				<td></td>
			</tr>
			<tr>
				<td></td>
				<td>
					<div class="control-group" style="float: left;">
					<?php

						if (@$eacServer['GameTemplate'][0]['id'] == 37) {
							$eacText = 'Вы можете подключить сервер <i>только</i> с нашего хостинга.';
						} elseif (@$eacServer['GameTemplate'][0]['id'] == 38) {
							$eacText = 'Вы можете подключить <i>любой</i> сервер с <i>любого</i> хостинга.';
						}

						echo $this->Form->input('connectedAddress', array (
														'id'    => 'connectedAddress',
														'value' => @$connectedAddress,
														'div'   => false,
														'label' => false,
														'class' => 'span3 tipTip',
														'title' => 'Укажите здесь IP и порт другого сервера, если вы подключаете не свой сервер. '.@$eacText,
														'placeholder' => "IP:Порт другого сервера",
														'style' => 'font-weight: bold; color: #444; text-align: center; margin-left: 0px;'));
					?>
					<?php
						echo $this->Form->input('rconPass', array (
														'id'    => 'rconPass',
														'value' => @$rconPassword,
														'div'   => false,
														'label' => false,
														'class' => 'span2 tipTip',
														'title' => 'Укажите пароль RCON этого сервера. Без него подключить сервер к EAC нельзя.',
														'placeholder' => "Пароль RCON",
														'style' => 'font-weight: bold; color: #444; text-align: center; margin-left: 0px;'));
					?>

						<select name="data[Server][gameType]" id="gameType" class='span3 tipTip' title="Обязательно укажите игру этого сервера.">
							<option value="0" <?php if (empty($this->data['Server']['gameType'])) { echo 'selected="selected"'; }?>>Укажите игру...</option>
							<option value="1" <?php if (@$this->data['Server']['gameType'] == 1) { echo 'selected="selected"'; }?>> HL1: Counter-Strike 1.6 & CZero</option>
							<option value="2" <?php if (@$this->data['Server']['gameType'] == 2) { echo 'selected="selected"'; }?>>HL2: CS:S & CS:GO & TF 2</option>
						</select>
					</div>
				</td>
			</tr>
		</table>

	</div>

	<div id="action_positive"  style="height: 120px;">
		<h3>Параметры EAC:</h3>

		<div class="controls">
		  <label class="checkbox inline">
		    <?php

				echo $this->Form->checkbox('eacPublic', array(  'id' => 'eacPublic',
																'class' => 'tipTip',
																'title' => 'Если вы хотите, чтобы на сервере могли играть игроки как с EAC, так и без него - включите этот параметр.'
																));


			?> Публичный сервер
		  </label>
		  <label class="checkbox inline">
		    <?php

				echo $this->Form->checkbox('eac32bit', array(
															  'id' => 'eac32bit',
															  'class' => 'tipTip',
															  'title' => 'Принуждать ли игроков включать 32bit цвет для правильного отображения дыма.'));


			?> 32bit цвет
		  </label>
		</div>

		<div class="controls" style="margin-top: 15px;">
			<?php


				echo $this->Js->submit('Сохранить и включить EAC',
					array(
						'url'=> array(
										'controller'=>'Servers',
										'action'=>'editStartParamsEac',
										$id
						 ),
						'update' => '#server_start_params_container',
						'class' => 'btn btn-primary',
						'id'=>'submit',
						'before' =>$loadingShow,
						'complete'=>$loadingHide,
						'buffer' => false));
			?>
		</div>

	</div>

	<?php echo $this->Form->end();?>
</div>

<script type="text/javascript">

	$(function() {

		$(".tipTip").tipTip({maxWidth: "350px", delay: 100, defaultPosition: 'top'});

		function checkType(eacType) {

			if (eacType == 'hl1') {
				$('#eac32bit').removeAttr('disabled');
			} else if (eacType == 'hl2') {
				$('#eac32bit').attr('disabled', 'disabled');
			}

		}

		$("#ownServer").change(function() {

			var serverId = $("#ownServer").val();

			<?php echo @$serversTypesForEac;?>

			checkType(eacType);
			return false;
		});

		$("#gameType").change(function() {

			var otherGameType = $("#gameType").val();

			checkType('hl' + otherGameType);
			return false;
		});

		$("#connectedAddress").keyup(function() {

			var address = $("#connectedAddress").val();
			var ipRegex  = /^\d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}\:\d{5}$/;

			if (address.match(ipRegex)) {
				$("#ownServer").val(0);
				$('#eac32bit').removeAttr('disabled');
			}

			return false;
		});

	});

</script>
