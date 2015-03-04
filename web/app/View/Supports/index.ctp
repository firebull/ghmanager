<div class="supports index">
	<h2><?php __('Supports');?></h2>
	<table cellpadding="0" cellspacing="0">
	<tr>
			<th><?php echo $this->Paginator->sort('id');?></th>
			<th><?php echo $this->Paginator->sort('text');?></th>
			<th><?php echo $this->Paginator->sort('created');?></th>
			<th><?php echo $this->Paginator->sort('modified');?></th>
			<th class="actions"><?php __('Actions');?></th>
	</tr>
	<?php
	$i = 0;
	foreach ($supports as $support):
		$class = null;
		if ($i++ % 2 == 0) {
			$class = ' class="altrow"';
		}
	?>
	<tr<?php echo $class;?>>
		<td><?php echo $support['Support']['id']; ?>&nbsp;</td>
		<td><?php echo $support['Support']['text']; ?>&nbsp;</td>
		<td><?php echo $support['Support']['created']; ?>&nbsp;</td>
		<td><?php echo $support['Support']['modified']; ?>&nbsp;</td>
		<td class="actions">
			<?php echo $this->Html->link(__('View', true), array('action' => 'view', $support['Support']['id'])); ?>
			<?php echo $this->Html->link(__('Edit', true), array('action' => 'edit', $support['Support']['id'])); ?>
			<?php echo $this->Html->link(__('Delete', true), array('action' => 'delete', $support['Support']['id']), null, sprintf(__('Are you sure you want to delete # %s?', true), $support['Support']['id'])); ?>
		</td>
	</tr>
<?php endforeach; ?>
	</table>
	<p>
	<?php
	echo $this->Paginator->counter(array(
	'format' => __('Page %page% of %pages%, showing %current% records out of %count% total, starting on record %start%, ending on %end%', true)
	));
	?>	</p>

	<div class="paging">
		<?php echo $this->Paginator->prev('<< ' . __('previous', true), array(), null, array('class'=>'disabled'));?>
	 | 	<?php echo $this->Paginator->numbers();?>
 |
		<?php echo $this->Paginator->next(__('next', true) . ' >>', array(), null, array('class' => 'disabled'));?>
	</div>
</div>
<div class="actions">
	<h3><?php __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('New Support', true), array('action' => 'add')); ?></li>
		<li><?php echo $this->Html->link(__('List Support Tickets', true), array('controller' => 'support_tickets', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Support Ticket', true), array('controller' => 'support_tickets', 'action' => 'add')); ?> </li>
	</ul>
</div>