<?php

 include('../loading_params.php');

?>
<div id="flash"><?php echo $session->flash(); ?></div>

Укажите сумму, на которую вы бы хотели пополнить баланс Личного счёта:<br/><br/>

<?php echo $form->create('Order', array('class' => 'form-inline')); ?>
<div class="control-group" style="margin-left: 25px;">
    <div class="controls">
        <div class="input-append">				        
		<?php

			echo $form->input('amount', array(  'div' => false,
												'label' => false,
												'id' => 'depositAmount',
												'placeholder' => 100,
												'class' => 'span1'));
			?><span class="add-on">руб.</span>
		</div>

		<?php
			
			echo $js->submit('Пополнить',   array(
														'url'=> array(
																		'controller'=>'Orders',
																		'action'=>'makeDeposit'
														 ),
														'div' => false,
														'label' => false,
														'id' => 'depositConfirm',
														'update' => '#deposit',
														'class' => 'btn',
														'before' =>$loadingShow,
														'complete'=>$loadingHide.";$('#deposit').dialog( 'close' ).dialog({position: ['center',180], show: 'highlight', hide: 'highlight', width: 700});",
														'buffer' => true));
		?>
	</div>
</div>
<div>
<?php echo $form->end(); ?>

<script type="text/javascript">	
				
	$(function() {
			
			function depositCheckAmount()
			{
				var amount = eval($('#depositAmount').val());

				if (amount > 0)
				{
					$('#depositConfirm').removeAttr('disabled');
				}
				else
				{
					$('#depositConfirm').attr('disabled', 'disabled');
				}

			}

			$("#depositAmount").keyup(function() {
							depositCheckAmount();
						});
			
			depositCheckAmount();

			});
</script>

<?php 
			
			echo $js->writeBuffer(); // Write cached scripts 
?>
