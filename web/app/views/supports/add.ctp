<?php
/*
 * Created on 11.09.2010
 *
 * Made for project TeamServer
 * by bulaev
 */
 //pr($session);
  include('../loading_params.php');
?>

<div id="new_message_form" class="well" style="text-align: left;">
<?php echo $this->Form->create('Support');?>
	<fieldset>
 		<h2 class="highlight3" style="margin-top: 0px;">Написать сообщение в тикет</h2>
	<?php
		echo $form->input('text',
	  						array('type'  => 'textarea',
	  							  'style' => 'width: 97%;  
	  							  			 height: 200px; 
								  			 padding-left: 15px;
											 margin-left: 0px;


											  ', 

								  'escape' => false,
								  'div'    => false, 	
								  'label'  => false));
		echo $form->input('SupportTicket.id', array('type'=>'hidden'));
	?>
	<?php
					
							
		echo $js->submit('Отправить',
			array(
				'url' => array(
								'controller' => 'Supports',
								'action'     => 'add',@$id
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
