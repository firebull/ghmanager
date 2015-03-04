<?php
/*
 * Created on 11.09.2010
 *
 * Made for project TeamServer
 * by bulaev
 */


foreach ($userinfo['Group'] as $group) {
	if (in_array($group['id'], array('1', '2')))
	{
		$isAdmin = true;
		break;
	}
	else
	{
		$isAdmin = false;
	}
}

include('loading_params.php');
$status = array(
					'read' => $this->Html->image('icons/check_grey.gif'),
					'unread' => '<span style="color: #DD1500;">Новое!</span>'
					);

?>
<div id="flash"><?php echo $this->Session->flash(); ?></div>
<div class="well">
<?php
if( count(@$thread)>0 ){
	foreach ( $thread as $message ) {
	?>

		<div class="list_border" style="border: 1px solid #446805; background-color: #fff; text-align: left;">
		<strong>
		<?php
				echo $status[$message['readstatus']];
				if (@$message['answerBy'] == 'support' and $isAdmin === false){
					echo ' <span style="color: #970405;">Ответ Техподдержки.';
				}
				else
				if (@$message['answerBy'] == 'support' and $isAdmin === true){
					echo ' <span style="color: #970405;">Ответ Техподдержки ('.$message['answerByName'].').';
				}
				else
				{
					echo ' <span>';
				}

		?>

			Отправлено <?php echo $this->Common->niceDate($message['created']).'</span>'; ?>
		</strong>
		<hr noshade style="background-color: #446805; "/>
		<?php echo $this->Text->autoLink(str_replace("\n", "<br/>", $message['text'])); ?>
		</div>


<?php
	}
}
else
{
	?>
	<div class="list_border" style="border: 1px solid #446805; background-color: #fff;">
	Нет новых сообщений
	</div>
<?php
}
?>

	<div class="form-actions" style="margin-bottom: 0px;">
		<?php
		/*
		 * Выводить Ответ и закрытие тикета, только если сам тикет открыт
		 */

		if ($ticketStatus != 'closed')
		{
		//Ссылка  для ответа
		echo $this->Html->link('<i class="icon-comment"></i> Написать ответ','#',
							array ('id'=>'new_message_'.$id,
									'escape' => false,
									'div' => false,
									'label' => false,
									'class' => 'btn',
								    'onClick'=>"$('#new_message_write_".$id."').show('blind');"

							));
		$event  = $this->Js->request(array('controller'=>'Supports',
									 'action'=>'add',$id),
							   array('update' => '#new_message_write_'.$id,
									 'before'=>$loadingShow,
									 'complete'=>$loadingHide));

		$this->Js->get('#new_message_'.$id)->event('click', $event);

		?>

	<?php

		} // if
	?>

		<?php
		echo $this->Html->link('<i class="icon-list-alt"></i> Показать всю переписку', '#',
							 			  array('Title' => 'Вывести все сообщения из ветки',
							 			  	    'escape' => false,
												'div' => false,
												'label' => false,
												'class' => 'btn',
												'id' => 'getFullThread_'.$id));


		?>

		<?php
		if ($ticketStatus != 'closed')
		{
		echo $this->Html->link('<i class="icon-thumbs-up"></i> Закрыть тикет', array('action' => 'closeTicket', $id),
							 			  array('Title' => 'Закрыть тикет',
							 			  	    'escape' => false,
												'div' => false,
												'label' => false,
												'class' => 'btn'));
		}

		?>

		<a href="#" onclick="$('#ticket_more_<?php echo $id; ?>').hide('highlight'); return false;" class="btn" title="Скрыть текущую переписку"><i class="icon-minus-sign"></i> Скрыть тикет</a>

	</div>
</div>
<?php
	if ($isAdmin === true)
	{
?>



<?php
	if (!empty($int_comments))
	{
?>
	<div class="well"  style="text-align: left !important;">
<?php
		echo $this->Html->tag('h2', 'Комментарии', array('class' => 'highlight3'));

		$pastCommenter = '';
		$i = 0;

		foreach ($int_comments as $comment)
		{

			if ($pastCommenter != $comment['by'] and $i > 0)
			{
				echo '</blockquote>';
				echo '<blockquote>';
				echo $this->Html->tag('span', $comment['by'], array('class' => 'highlight2'));
			}
			else
			if ($pastCommenter != $comment['by'] and $i == 0)
			{
				echo '<blockquote>';
				echo $this->Html->tag('span', $comment['by'], array('class' => 'highlight2'));
			}
?>

			<p><?php

			$time = $this->Common->niceDate($comment['time']).'<br/>';
			echo $this->Html->tag('small', $time, array('class' => 'highlight3'));
			echo $this->Text->autoLink(str_replace("\n", "<br/>",$comment['text'])); ?></p>
<?php
			$i++;

			$pastCommenter = $comment['by'];

		}
?>
	</div>

<?php
	}
?>
<div class="well">
<div class="btn-toolbar">
	<div class="btn-group">
		<button class="btn btn-primary"><i class="icon-share-alt icon-white"></i> Передать тикет</button>
		<button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"><span class="caret"></span></button>
		<ul class="dropdown-menu" style="text-align: left !important;">
<?php
			foreach ($admins as $admin) {
?>
				<li><?php
					//Ссылка  для создания типа шаблона
					if ($admin['name'] == $userinfo['User']['username'])
					{
						$adminName = 'Взять себе';
					}
					else
					{
						$adminName = $admin['name'].' ('.$admin['spec'].')';
					}

					echo $this->Html->link( $adminName,
									  array('controller' => 'supportTickets',
									    	'action' => 'linkToAdmin',
											 $id, $admin['id']),
									  array('id'=>'admin_'.$admin['id'],
											'escape' => false));

					?>
				</li>
<?php
			}

?>
		</ul>
	</div>
	<div class="btn-group">
<?php

	echo $this->Html->tag('button', '<i class="icon-comment icon-white"></i> Внутренний комментарий',
									array('class' => 'btn btn-primary',
										  'escape' => false,
										  'id' => 'show-int-comment-'.$id));
?>
	</div>
</div>

<div id="new_comment_form_<?php echo $id;?>" style="text-align: left; display: none;">
	<hr/>
	<br/>
<?php echo $this->Form->create('SupportTicket');?>
	<fieldset>
 		<h2 class="highlight3" style="margin-top: 0px;">Написать внутренний комментарий в тикет</h2>
	<?php
		echo $this->Form->input('text',
	  						array('type'  => 'textarea',
	  							  'style' => 'width: 97%;
	  							  			 height: 200px;
								  			 padding-left: 15px;
											 margin-left: 0px;


											  ',

								  'escape' => false,
								  'div'    => false,
								  'label'  => false));
		echo $this->Form->input('SupportTicket.id', array('type'=>'hidden'));
	?>
	<?php


		echo $this->Js->submit('Отправить',
			array(
				'url' => array(
								'controller' => 'SupportTickets',
								'action'     => 'addComment',@$id
				 ),
				'escape'   => false,
				'update'   => '#ticket_more_'.@$id,
				'class'    => 'btn btn-primary',
				'before'   => $loadingShow,
				'complete' => $loadingHide,
				'buffer'   => true));
	?>
	</fieldset>

</div>

</div>
<?php

	}

	$event  = $this->Js->request(array('controller'=>'SupportTickets',
							 'action'=>'view', $id, 'all'),
					   array('update' => '#ticket_more_'.$id,
							 'before'=>$loadingShow,
							 'complete'=>$loadingHide));

	$this->Js->get('#getFullThread_'.$id)->event('click', $event);
?>

<div id="new_message_write_<?php echo $id; ?>" style="display:none;"  title="Написать ответ в тикет"></div>
<?php
	echo $this->Js->writeBuffer(); // Write cached scripts
?>

<script type="text/javascript" language="javascript">
		$(function() {
			$('#show-int-comment-<?php echo $id;?>').click(

				function() {

					$('#new_comment_form_<?php echo $id;?>').show('highlight');
					$('#show-int-comment-<?php echo $id;?>').attr('disabled', 'disabled');

				}

			);

		});
</script>
