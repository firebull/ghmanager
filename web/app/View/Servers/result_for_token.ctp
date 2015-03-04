<?php
/*
 * Created on 01.09.2010
 *
 * File created for project TeamServer(Git)
 * by nikita
 */
?>
<div id="flash"><?php echo $this->Session->flash(); ?></div>
<div id="log" style="text-align: left; font-size: 9px;"><?php
	if (@$result) {
		$this->Text = split("\n", $result);
		foreach ( $this->Text as $str ) {
			echo $str."<br/>";

		}
	}
?></div>
<div id="link">
<?php
	if (@$token) {
		echo $this->Html->link("Вернуться к серверу",
						 array('controller' => 'servers',
						 	   'action' => 'controlByToken',
						 	   $token)
						 	   );
	}
?>
</div>

