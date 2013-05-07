<?php
/*
 * Created on 31.08.2010
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
 include('../loading_params.php');
 //pr($this->data['RadioShoutcastParam'][0]);
?>
<cake:nocache>
<div id="radio_params">

	<div id="flash"><?php echo $session->flash();?></div>
	<div id="errors" style="color: red;">
	<?php
	if (@$errors){
		foreach ( @$errors as $field => $error ) {
	       echo "Ошибка в поле ".$field.": ".$error."<br/>";
		}
	}
	?>
	</div>
	<h3>Редактирование параметров сервера SHOUTcast ID #<?php echo $this->data['Server']['id']?></h3>
	<?php echo $form->create('Server', array('action' => 'RadioShoutcastParam')); ?>
			
	<table border="0" cellpadding="2" cellspacing="5">
	<tr>
		<td>RealTime</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.RealTime', 
									array(														
										'options'=>array(
														'1' => 'Да',
														'0'=> 'Нет'
														), 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .									
												'Если "Да", то статусная строка будет обновляться каждую ' .
												'секунду свежей информацией о потоке.'));?></td>
	</tr>
	<tr>
		<td>ShowLastSongs</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.ShowLastSongs', 
									array(														
										'size' => '10', 
										'div' => true,	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .
												'Указывает, сколько последних композиций будет ' .
												'показано на странице /played.html. Возможно ' .
												'установить значение от 1 до 20.'));?>
		
		</td>
	</tr>
	<tr>
		<td>W3CEnable</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.W3CEnable', 
									array(														
										'options'=>array(
														'Yes' => 'Да',
														'No'=> 'Нет'
														), 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .
												'W3CEnable turns on W3C Logging.  W3C logs contain httpd-like accounts ' .
												'of every track played for every listener, including byte counts those ' .
												'listeners took.  This data can be parsed with tools like Analog and ' .
												'WebTrends, or given to third parties like Arbitron and Measurecast ' .
												'for their reporting systems.'));?></td>
	</tr>
	<tr>
		<td>NameLookups</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.NameLookups', 
									array(														
										'options'=>array(
														'1' => 'Да',
														'0'=> 'Нет'
														), 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .
												'Преобразовавать ли IP-адреса в имена.'));?></td>
	</tr>
	<tr>
		<td>RelayPort</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.RelayPort', 
									array(														
										'options'=>array(
														'1' => 'Да',
														'0'=> 'Нет'
														), 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .
												'RelayPort and RelayServer specify that you want to be a relay server. ' .
												'Relay servers act as clients to another server, and rebroadcast. ' .
												'Set RelayPort to 0, RelayServer to empty to disable relay mode. '));?></td>
	</tr>
	<tr>
		<td>RelayServer</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.RelayServer', 
									array(														
										'size' => '20', 
										'div' => true,	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .
												'RelayPort and RelayServer specify that you want to be a relay server. ' .
												'Relay servers act as clients to another server, and rebroadcast. ' .
												'Set RelayPort to 0, RelayServer to empty to disable relay mode. '));?>
		
		</td>
	</tr>
	<tr>
		<td>AutoDumpUsers</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.AutoDumpUsers', 
									array(														
										'options'=>array(
														'1' => 'Да',
														'0'=> 'Нет'
														), 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .
												'Отключать ли слушателей, если отключается трасляция.'));?></td>
	</tr>
	<tr>
		<td>AutoDumpSourceTime</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.AutoDumpSourceTime', 
									array(														
										'size' => '10', 
										'div' => true,	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .
												'AutoDumpSourceTime specifies how long, in seconds, ' .
												'the source stream is allowed to be idle before the ' .
												'server disconnects it. 0 will let the source stream ' .
												'idle indefinately before disconnecting.'));?>
		
		</td>
	</tr>
	<tr>
		<td>ContentDir</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.ContentDir', 
									array(														
										'size' => '10', 
										'div' => true,	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .
												'Директория (только имя, без "/" в начале или конце), в которой ' .
												'хранится контент по запросу. Можно указывать поддиректории.'));?>
		
		</td>
	</tr>
	<tr>
		<td>IntroFile</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.IntroFile', 
									array(														
										'size' => '20', 
										'div' => true,	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .
												'IntroFile can specify a mp3 file that will be streamed ' .
												'to listeners right when they connect before they hear ' .
												'the live stream.'));?>
		
		</td>
	</tr>
	<tr>
		<td>BackupFile</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.BackupFile', 
									array(														
										'size' => '20', 
										'div' => true,	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .
												'BackupFile can specify a mp3 file that will be streamed ' .
												'to listeners over and over again when the source stream ' .
												'disconnects. AutoDumpUsers must be 0 to use this feature. ' .
												'When the source stream reconnects, the listeners are ' .
												'rejoined into the live broadcast.'));?>
		
		</td>
	</tr>
	<tr>
		<td>TitleFormat</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.TitleFormat', 
									array(														
										'size' => '20', 
										'div' => true,	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .
												'Формат строки заголовка, который будет отправлен ' .
												'слушателям. Можно использовать теги WinAmp.'));?>
		
		</td>
	</tr>
	<tr>
		<td>PublicServer</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.PublicServer', 
									array(														
										'options'=>array(
														'always'  => 'Всегда',
														'never'   => 'Никогда',
														'default' => 'По умолчанию'
														), 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .
												'Данное значение может быть Всегда, Никогда или По умолчанию. ' .
												'Т.е. сделать сервер публичным или нет.'));?></td>
	</tr>
	<tr>
		<td>AllowRelay</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.AllowRelay', 
									array(														
										'options'=>array(
														'Yes' => 'Да',
														'No'=> 'Нет'
														), 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .
												'AllowRelay determines whether or not other ' .
												'SHOUTcast servers will be permitted to ' .
												'relay this server.'));?></td>
	</tr>
	<tr>
		<td>AllowPublicRelay</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.AllowPublicRelay', 
									array(														
										'options'=>array(
														'Yes' => 'Да',
														'No'=> 'Нет'
														), 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .
												'AllowPublicRelay, when set to No, will tell ' .
												'any relaying servers not to list the server in ' .
												'the SHOUTcast directory (non-public), provided ' .
												'the relaying server\'s Public flag is set to default.'));?></td>
	</tr>
	<tr>
		<td>MetaInterval</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.MetaInterval', 
									array(														
										'size' => '10', 
										'div' => true,	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .
												'Определяет как часто, в байтах, отправляются Мета-данные.'));?>
		
		</td>
	</tr>
	<tr>
		<td>ListenerTimer</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.ListenerTimer', 
									array(														
										'size' => '10', 
										'div' => true,	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .
												'ListenerTimer is a value in minutes of ' .
												'maximum permitted time for a connected listener. ' .
												'If someone is connected for longer than this ' .
												'amount of time, in minutes, they are disconnected.'));?>
		
		</td>
	</tr>
	<tr>
		<td>BanFile</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.BanFile', 
									array(														
										'size' => '25', 
										'div' => true,	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .
												'BanFile is the text file sc_serv reads and ' .
												'writes to/from for the list of clients prohibited ' .
												'to connect to this server.  It\'s automatically ' .
												'generated via the web interface.'));?>
		
		</td>
	</tr>
	<tr>
		<td>RipFile</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.RipFile', 
									array(														
										'size' => '25', 
										'div' => true,	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .
												'RipFile is the text file sc_serv reads ' .
												'and writes to/from for the list of client ' .
												'IPs which are *ALWAYS* permitted to connect ' .
												'to this server (useful for relay servers). ' .
												'This file is automatically generated via ' .
												'the web interface.'));?>
		
		</td>
	</tr>
	<tr>
		<td>RIPOnly</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.RIPOnly', 
									array(														
										'options'=>array(
														'Yes' => 'Да',
														'No'=> 'Нет'
														), 
										'div' => true, 	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .
												'Если установлено в Да, то только с IP-адресов,' .
												'указанных в RipFile, будет возможно прослушивание ' .
												'и трансляция. Все остальные соединения будут сброшены.'));?></td>
	</tr>
	<tr>
		<td>Sleep</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.Sleep', 
									array(														
										'size' => '10', 
										'div' => true,	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .
												'Oпределяет степень детализации клиентских потоков ' .
												'для передачи данных. Сервер отправляет до 1024 байт ' .
												'данных на поток, а затем засыпает на указанное здесь ' .
												'значение, после чего процесс повторяется. Уменьшение ' .
												'этого значения увеличивает нагрузку на сервер, а ' .
												'увеличение может привезти к пропускам. Для 128кбит ' .
												'наиболее оптимальным значением является 833мс. ' .
												'Не рекомендуется устанавливать значение меньше ' .
												'100 и больше 1024.'));?>
		
		</td>
	</tr>
	<tr>
		<td>CleanXML</td>
		<td><?php echo $form->input('RadioShoutcastParam.0.CleanXML', 
									array(														
										'options'=>array(
														'Yes' => 'Да',
														'No'=> 'Нет'
														), 
										'div' => true,	
										'label' => false,
										'error' => true,
										'class' => 'param',
										'title'=>'Подсказка|' .
												'Удаляет пробелы и некоторы символы в строках, ' .
												'что может привезти к ошибках на некоторых ' .
												'XML-парсерах. Если у вас имеются XML-ошибки отображения, ' .
												'например в виджете на вашем сайте, попробуйте отключить ' .
												'эту функцию.'));?>
		
		</td>
	</tr>
	<tr>
		<td colspan="2" align="center">
		<?php
			echo $form->input('id', array('type'=>'hidden'));

			echo $js->submit('Сохранить',
										array(
											'url'=> array(
															'controller'=>'Servers',
															'action'=>'editParamsRadioShoutcast'
											 ),
											'update' => '#radio_params',
											'class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
											'before' =>$loadingShow,
											'complete'=>$loadingHide,
											'buffer' => false));
									
			echo $form->end();
		?>
		</td>
	</tr>
</table>

	
	
	<?php echo $form->end();?>
<script type="text/javascript">
	$(function() {
		$('input.param, textarea, select').cluetip({splitTitle: '|', arrows: true,});
	});
</script>
</div>
<?php //pr($this->data['VoiceMumbleParam'][0]); 
?>
</cake:nocache>