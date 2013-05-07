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
		<?php echo $html->link( 'Локации и серверы', 
										array (
										'controller'=>'locations',
										'action'=>'control'
										)
		
								);
		?>
		</li>
		<li class="<?php echo $class['servers']; ?>">
		<?php echo $html->link( 'Клиентские серверы', 
										array (
										'controller'=>'servers',
										'action'=>'control'
										)
		
								);
		?>
		</li>
		<li class="<?php echo $class['users']; ?>">
		<?php echo $html->link( 'Клиенты', 
										array (
										'controller'=>'users',
										'action'=>'control'
										)
		
								);
		?>
		</li>
		<li class="<?php echo $class['supporttickets']; ?>">
		<?php echo $html->link( 'Техподдержка ('.intval(@$openTickets).')', 
										array (
										'controller'=>'supportTickets',
										'action'=>'control'
										)
		
								);
		?>
		</li>
		<li class="<?php echo $class['orders']; ?>">
		<?php echo $html->link( 'Заказы', 
										array (
										'controller'=>'orders',
										'action'=>'control'
										)
		
								);
		?>
		</li>
		<li class="<?php echo $class['gametemplates']; ?>">
		<?php echo $html->link( 'Шаблоны', 
										array (
										'controller'=>'gameTemplates',
										'action'=>'control'
										)
		
								);
		?>
		</li>
		<li class="<?php echo $class['services']; ?>">
		<?php echo $html->link( 'Услуги', 
										array (
										'controller'=>'services',
										'action'=>'control'
										)
		
								);
		?>
		</li>
		<li class="<?php echo $class['promos']; ?>">
		<?php echo $html->link( 'Промо', 
										array (
										'controller'=>'promos',
										'action'=>'control'
										)
		
								);
		?>
		</li>
	</ul>
</div>


