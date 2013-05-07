<?php
/*
 * Created on 10.03.2011
 *
 * Made for project TeamServer(Git)
 * by bulaev
 */
?>
<div style="text-align: left; max-width: 306px;">
<strong>#<?php echo $ticket['SupportTicket']['id']." ".$ticket['SupportTicket']['title']?>
<br/>
<?php
echo $ticket['User'][0]['username'];
?>
</strong>
<?php

	foreach ( $ticket['Support'] as $message ) {
    ?>
    	<div class="list_border" style="border: 1px solid #446805; margin-top: 3px; padding: 2px;text-align: left;">
		<strong>
		    <small>		
			Отправлено <?php echo $time->niceShort($message['created']); ?>
			</small>
		</strong>
		<hr noshade style="background-color: #446805; "/>
		<?php echo str_replace("\n", "<br/>", $message['text']); ?>
	
		</div>
    <?php   
	}
?>
<br/>
<strong>
    <small>
    Ответ:
    </small>
</strong>
<?php
echo $this->Form->create('Support', array('action' => 'add'));
echo $form->input('text',
	  						array('type'=>'textarea',
	  							  'style'=> 'width: 92%;  
	  							  			 height: 120px; 
								  			 padding-left: 15px;
											 margin-left: 0px;


											  ', 

								  'escape'=>false,
								  'div' => false, 	
								  'label' => false));
echo $form->input('SupportTicket.id', array('type'=>'hidden', 'value' => $ticket['SupportTicket']['id']));
echo $this->Form->submit('Отправить');

echo $this->Html->link('Вернуться к списку тикетов', array('action'=>'admPda'));

?>

</div>