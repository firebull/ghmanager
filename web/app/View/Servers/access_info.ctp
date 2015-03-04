<cake:nocache>
<center>
<h3>
Доступ к серверу по FTP:
</h3>
<span>
<?php
	$serverId = $this->data['Server']['id'];
	$serverIp = $this->data['Server']['address'];
    $ftpUrl = "ftp://client".$userinfo['User']['id']."@".$serverIp;
	echo $this->Html->link($ftpUrl, $ftpUrl, array("target"=>"_blank"));
?>
</span>
<br/>
<small>
<br/>
*Пароль вы можете узнать щёлкнув "Пароль FTP" в левой части панели.
</small>

<?php
   if ( in_array($this->data['GameTemplate'][0]['name'], array( 'css',
   																'cssv34',
   																'dods',
   																'tf',
   																'hl2mp',
   																'cs16',
   																'cs16-old',
   																'dmc'))
	  ) {
	?>
<hr/>
<?php ?>
<h3>
Доступ к записанным демо по HTTP:
</h3>
<span>
<?php
    $demoUrl = "http://".$serverIp."/".$userinfo['User']['username']."/dems";
	echo $this->Html->link($demoUrl, $demoUrl, array("target"=>"_blank"));
?>
</span>
<br/>
<hr/>
<h3>
Закачать дополнительные карты по HTTP:
</h3>
<span>

	<?php



	$uploaderScript = "http://".$this->data['Server']['address']."/uploadMap/?" .
					  "id=".$this->data['Server']['id']."&" .
					  "token=".$this->data['User'][0]['tokenhash'];
	echo $this->Html->link('Загрузить свою карту на сервер', '#',
					array (
						   'onClick' => "window.open('".$uploaderScript."', 'newUploaderWin#".$this->data['Server']['id']."', 'Toolbar=yes, Location=no, Directories=no, Status=yes, Menubar=yes, Scrollbars=no, Resizable=yes, Width=550, Height=500')")
							);

?>
</span>
<?php
	} elseif ( in_array($this->data['GameTemplate'][0]['name'], array( 'cod4',
   																'cod4fixed',
   																'cod2'))
	  ) {
?>
<br/>
<hr/>
<h3>
Доступ к скриншотам PunkBuster:
</h3>
<span>
<?php
    $demoUrl = "http://".$serverIp."/".$userinfo['User']['username']."/pb";
	echo $this->Html->link($demoUrl, $demoUrl, array("target"=>"_blank"));
?>
</span>
<br/>
<hr/>
<?php
	}
?>

<br/>
<?php ?>
<?php if (@$this->data['Server']['controlByToken'] == 1) { ?>
<hr/>
<h3>
Контроль сервера без пароля:
</h3>
<span>
<?php
    $tockenUrl = "https://panel.teamserver.ru/servers/controlByToken/".@$this->data['Server']['controlToken'];
	echo $this->Html->link($tockenUrl,
								  array(
								  		'controller' => 'servers',
								  		'action' => 'controlByToken',
								  		@$this->data['Server']['controlToken']
								  		),
								  array(
								  		'target' => '_blank'
								  		));
?>
</span>
<br/>
<?php } ?>


</center>
</cake:nocache>
