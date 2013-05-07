<?php


/*
 * Created on 15.05.2009
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */
$class = array(
				'servers' => 'nav',
				'supportTickets' => 'nav',
				'orders' => 'nav',
				'administration' => 'nav');
				

if ($this->params['action'] == 'control'){
	$class['administration']='active';
}
else
{
	$class[$this->params['controller']]='active';
}



?>

<ul class="menu_servers_left">
	<li class="<?php echo $class['servers']; ?>">
	<?php

	echo $html->link('Серверы', array (
		'controller' => 'servers', 'action' => 'index'));
	?>
	</li>
	<li class="<?php echo $class['supportTickets']; ?>">

	<?php

	echo $html->link('Техническая поддержка', array (
		'controller' => 'supportTickets', 'action' => 'index'));
	?>

	</li>
	<li class="<?php echo $class['orders']; ?>">
	<?php

	echo $html->link('Заказы', array (
		'controller' => 'orders', 'action' => 'index'));
	?>
	</li>
<?php  
/*
 * Выводить ниже только административную часть
 */
if (strtolower($userinfo['Group'][0]['name'])=='admin'
	or
	strtolower($userinfo['Group'][0]['name'])=='gameadmin'){ ?>
	<li class="<?php echo $class['administration']; ?>">

	<?php

	echo $html->link('Администрирование', array (
		'controller' => 'administration'));
	?>

	</li>
<?php } ?>
</ul>
