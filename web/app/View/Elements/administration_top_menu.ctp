<?php
/*
 * Created on 19.06.2010
 *
 * To change the template for this generated file go to
 */

 $class = array(
				'servers' => 'nav',
				'locations' => 'nav',
				'users' => 'nav',
				'supporttickets' => 'nav',
				'orders'=>'nav',
				'gametemplates'=>'nav',
				'services'=>'nav',
				'promos'=>'nav');

 $class[strtolower($this->params['controller'])]='active';

?>

<div id="administration_top_menu">
	<ul class="top_menu">
		<li class="<?php echo $class['locations']; ?>">
		<?php echo $this->Html->link( 'Локации и серверы',
										array (
										'controller'=>'locations',
										'action'=>'control'
										)

								);
		?>
		</li>
		<li class="<?php echo $class['servers']; ?>">
		<?php echo $this->Html->link( 'Клиентские серверы',
										array (
										'controller'=>'servers',
										'action'=>'control'
										)

								);
		?>
		</li>
		<li class="<?php echo $class['users']; ?>">
		<?php echo $this->Html->link( 'Клиенты',
										array (
										'controller'=>'users',
										'action'=>'control'
										)

								);
		?>
		</li>
		<li class="<?php echo $class['supporttickets']; ?>">
		<?php echo $this->Html->link( 'Техподдержка ('.intval(@$openTickets).')',
										array (
										'controller'=>'supportTickets',
										'action'=>'control'
										)

								);
		?>
		</li>
		<li class="<?php echo $class['orders']; ?>">
		<?php echo $this->Html->link( 'Заказы',
										array (
										'controller'=>'orders',
										'action'=>'control'
										)

								);
		?>
		</li>
		<li class="<?php echo $class['gametemplates']; ?>">
		<?php echo $this->Html->link( 'Шаблоны',
										array (
										'controller'=>'gameTemplates',
										'action'=>'control'
										)

								);
		?>
		</li>
		<li class="<?php echo $class['services']; ?>">
		<?php echo $this->Html->link( 'Услуги',
										array (
										'controller'=>'services',
										'action'=>'control'
										)

								);
		?>
		</li>
		<li class="<?php echo $class['promos']; ?>">
		<?php echo $this->Html->link( 'Промо',
										array (
										'controller'=>'promos',
										'action'=>'control'
										)

								);
		?>
		</li>
	</ul>
</div>


