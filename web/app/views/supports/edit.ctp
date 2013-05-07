<div class="supports form">
<?php echo $this->Form->create('Support');?>
	<fieldset>
 		<legend><?php __('Edit Support'); ?></legend>
	<?php
		echo $this->Form->input('id');
		echo $this->Form->input('text');
		echo $this->Form->input('SupportTicket');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit', true));?>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $this->Form->value('Support.id')), null, sprintf(__('Are you sure you want to delete # %s?', true), $this->Form->value('Support.id'))); ?></li>
		<li><?php echo $this->Html->link(__('List Supports', true), array('action' => 'index'));?></li>
		<li><?php echo $this->Html->link(__('List Support Tickets', true), array('controller' => 'support_tickets', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Support Ticket', true), array('controller' => 'support_tickets', 'action' => 'add')); ?> </li>
	</ul>
</div>