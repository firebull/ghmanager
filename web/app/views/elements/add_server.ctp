<?php
/*
 * Created on 27.05.2010
 *
 */
 include('../loading_params.php');
?>

	<?php
	echo $form->create('Server');
	echo $form->input('type',array ('options'=>array (
									'srcds'=>'Source Dedicated'
	//								,'other'=>'Другой тип'
	)));
	echo $form->input('game', array('options' => array(
									'l4d' => 'Left 4 Dead',
									'l4d2' => 'Left 4 Dead',
									'cs16' => 'Counter Strike 1.6',
									'css' => 'Counter Strike:Source'
	)));
	echo $form->input('slots', array('options'=>range(0,32,1)));
	echo $js->submit('Отправить',
								array(
									'url'=> array(
													'controller'=>'Servers',
													'action'=>'add'
									 ),
									'update' => '#servers_list',
									'class' => 'ui-button ui-widget ui-state-default ui-corner-all ui-button-text-only',
									'before' =>$loadingShow,
									'complete'=>$loadingHide,
									'buffer' => false));
							
	echo $form->end();
?>
