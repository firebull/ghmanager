<div class="supports view">
<h2><?php  __('Support');?></h2>
	<dl><?php $i = 0; $class = ' class="altrow"';?>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Id'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $support['Support']['id']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Text'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $support['Support']['text']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Created'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $support['Support']['created']; ?>
			&nbsp;
		</dd>
		<dt<?php if ($i % 2 == 0) echo $class;?>><?php __('Modified'); ?></dt>
		<dd<?php if ($i++ % 2 == 0) echo $class;?>>
			<?php echo $support['Support']['modified']; ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Support', true), array('action' => 'edit', $support['Support']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('Delete Support', true), array('action' => 'delete', $support['Support']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $support['Support']['id'])); ?> </li>
		<li><?php echo $this->Html->link(__('List Supports', true), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Support', true), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Support Tickets', true), array('controller' => 'support_tickets', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Support Ticket', true), array('controller' => 'support_tickets', 'action' => 'add')); ?> </li>
	</ul>
</div>
<div class="related">
	<h3><?php __('Related Support Tickets');?></h3>
	<?php if (!empty($support['SupportTicket'])):?>
	<table cellpadding = "0" cellspacing = "0">
	<tr>
		<th><?php __('Id'); ?></th>
		<th><?php __('Status'); ?></th>
		<th><?php __('Created'); ?></th>
		<th><?php __('Modified'); ?></th>
		<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
		$i = 0;
		foreach ($support['SupportTicket'] as $supportTicket):
			$class = null;
			if ($i++ % 2 == 0) {
				$class = ' class="altrow"';
			}
		?>
		<tr<?php echo $class;?>>
			<td><?php echo $supportTicket['id'];?></td>
			<td><?php echo $supportTicket['status'];?></td>
			<td><?php echo $supportTicket['created'];?></td>
			<td><?php echo $supportTicket['modified'];?></td>
			<td class="actions">
				<?php echo $this->Html->link(__('View', true), array('controller' => 'support_tickets', 'action' => 'view', $supportTicket['id'])); ?>
				<?php echo $this->Html->link(__('Edit', true), array('controller' => 'support_tickets', 'action' => 'edit', $supportTicket['id'])); ?>
				<?php echo $this->Html->link(__('Delete', true), array('controller' => 'support_tickets', 'action' => 'delete', $supportTicket['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $supportTicket['id'])); ?>
			</td>
		</tr>
	<?php endforeach; ?>
	</table>
<?php endif; ?>

	<div class="actions">
		<ul>
			<li><?php echo $this->Html->link(__('New Support Ticket', true), array('controller' => 'support_tickets', 'action' => 'add'));?> </li>
		</ul>
	</div>
</div>
